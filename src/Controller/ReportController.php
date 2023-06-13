<?php

namespace App\Controller;

use App\Entity\Manager;
use App\Entity\Report;
use App\Entity\Spending;
use App\Entity\Task;
use App\Model\Request\Rm\IssueTimeRmRequest;
use App\Model\Response\Rm\TaskResponse;
use App\Model\Api\Request\OrderRequest;
use App\Model\Response\BadResponse;
use App\Model\Response\Rm\ReportDataResponse;
use App\Service\ClientEvo;
use App\Service\ClientRm;
use App\Service\ReportService;
use JMS\Serializer\SerializerBuilder;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Annotations as OA;
use App\Model\Response\Rm\StatusesResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Model\Request\Rm\TaskEditRequest;
use App\Model\Response\Rm\ProjectResponse;
use App\Model\Request\Rm\IssuesRmRequest;

/**
 * @Route("/api/doc")
 */
class ReportController extends AbstractController
{
    private $doctrine;

    private $clientEvo;

    private $translator;

    private $validator;

    private $reportService;

    private $logger;

    public function __construct(
        TranslatorInterface $translator,
        ManagerRegistry $doctrine,
        ClientEvo $clientEvo,
        ValidatorInterface  $validator,
        LoggerInterface  $redmineApiLogger
    ) {
        $this->doctrine = $doctrine;
        $this->clientEvo = $clientEvo;
        $this->translator = $translator;
        $this->validator =  $validator;
        $this->logger = $redmineApiLogger;
        $this->reportService = new ReportService($doctrine, $redmineApiLogger,
         $clientEvo);
    }

    /**
     * Получение статусов задач (фильтры)
     *
     * @Route("/redmine/statuses", name="getRedmineStatuses", methods={"GET"})
     * @OA\Tag(name="Redmine")
     *
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *        @OA\JsonContent(ref=@Model(type=StatusesResponse::class))
     * )
     * @param Request $request
     * @return JsonResponse
     * @throws \Redmine\Exception\ClientException
     */
    public function getRedmineStatuses(Request $request): JsonResponse
    {
        $managerRepository = $this->doctrine->getRepository(Manager::class);

        $maskBearer = 'Bearer ';
        $bearer = $request->headers->get('authorization');
        $bearerToken = str_replace($maskBearer, "", $bearer);

        $manager = $managerRepository->findOneBy([
            'token' => $bearerToken,
        ]);

        if (empty($manager->getTokenRm())) {
            return new JsonResponse(new BadResponse(false, 'Manager not login Rm!'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $statuses = $this->reportService->getStatuses($manager);

        return new JsonResponse(new StatusesResponse(true, $statuses), JsonResponse::HTTP_OK);
    }

    /**
     * Получение сотрудников!
     *
     * @Route("/redmine/employer", name="getRedmineEmployer", methods={"GET"})
     * @OA\Tag(name="Redmine")
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Redmine\Exception\ClientException
     */
    public function getRedmineEmployer(Request $request): JsonResponse
    {
        $managerRepository = $this->doctrine->getRepository(Manager::class);

        $maskBearer = 'Bearer ';
        $bearer = $request->headers->get('authorization');
        $bearerToken = str_replace($maskBearer, "", $bearer);

        $manager = $managerRepository->findOneBy([
            'token' => $bearerToken,
        ]);

        if (empty($manager->getTokenRm())) {
            return new JsonResponse(new BadResponse(false, 'Manager not login Rm!'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $clientRm = new ClientRm($manager->getTokenRm());
        $projects = '';//$clientRm->get();

        return new JsonResponse($projects, JsonResponse::HTTP_OK);
    }

    /**
     * Получение проектов РМ
     *
     * @Route("/redmine/project", name="getRedmineProject", methods={"GET"})
     * @OA\Tag(name="Redmine")
     *
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(ref=@Model(type=ProjectResponse::class))
     * )
     * @param Request $request
     * @return JsonResponse
     * @throws \Redmine\Exception\ClientException
     */
    public function getRedmineProject(Request $request): JsonResponse
    {
        $serializer = SerializerBuilder::create()->build();
        $managerRepository = $this->doctrine->getRepository(Manager::class);

        $maskBearer = 'Bearer ';
        $bearer = $request->headers->get('authorization');
        $bearerToken = str_replace($maskBearer, "", $bearer);

        $manager = $managerRepository->findOneBy([
            'token' => $bearerToken,
        ]);

        if (empty($manager->getTokenRm())) {
            return new JsonResponse(new BadResponse(false, 'Manager not login Rm!'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $projects = $this->reportService->getProject($manager);

        $projectsDTOArray = $serializer->fromArray(
            $projects,
            'array<App\Model\RmDTO\ProjectDTO>',
        );

        return new JsonResponse(new ProjectResponse(true, $projectsDTOArray), JsonResponse::HTTP_OK);
    }

    /**
     * Формирование списка задач
     *
     * @Route("/redmine/issues", name="getRedmineIssues", methods={"POST"})
     * @OA\Tag(name="Redmine")
     *
     * @OA\RequestBody(
     *     required=true,
     *     description="Данные для поиска задачи",
     *     @OA\JsonContent(ref=@Model(type=IssuesRmRequest::class))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(ref=@Model(type=TaskResponse::class))
     * )
     * @OA\Parameter(
     *     name="token",
     *     in="query",
     *     description="Token auth",
     *     @OA\Schema(type="string")
     * )
     * @param Request $request
     * @return JsonResponse
     * @throws \Redmine\Exception\ClientException
     */
    public function getRedmineIssues(Request $request): JsonResponse
    {
        $maskBearer = 'Bearer ';
        $bearer = $request->headers->get('authorization');
        $bearerToken = str_replace($maskBearer, "", $bearer);

        $serializer = SerializerBuilder::create()->build();
        $entityManager = $this->doctrine->getManager();
        $managerRepository = $this->doctrine->getRepository(Manager::class);

        $orderRequest = $serializer->deserialize($request->getContent(), IssuesRmRequest::class, 'json');

        $errors = $this->validator->validate($orderRequest);

        if (count($errors) > 0) {
            return new JsonResponse(new BadResponse(false, 'Valid param error'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $projectRm = $orderRequest->project;
        $filters = $orderRequest->filter;

        $manager = $managerRepository->findOneBy([
            'token' => $bearerToken,
        ]);

        if (empty($manager->getTokenRm())) {
            return new JsonResponse(new BadResponse(false, 'Manager not login Rm!'), JsonResponse::HTTP_BAD_REQUEST);
        }

        if (empty($projectRm)) {
            return new JsonResponse(new BadResponse(false, 'Project param error!'), JsonResponse::HTTP_BAD_REQUEST);
        }

        try{
            $taskDTOArray = $this->reportService->getTask($bearerToken, $manager, $projectRm, $filters);
        } catch (\Exception $e) {
            return new JsonResponse(new BadResponse(false, 'Query param ' . $e->getMessage()), JsonResponse::HTTP_BAD_REQUEST);
        }

        $entityManager->flush();
        $taskDTOArray = $serializer->fromArray(
            $taskDTOArray,
            'array<App\Model\RmDTO\TaskDTO>',
        );

        return new JsonResponse(new TaskResponse(true, $taskDTOArray), JsonResponse::HTTP_OK);
    }

    /**
     * Получение заполнение задач RM Evo
     *
     * @Route("/redmine/time", name="getRedmineTime", methods={"GET"})
     * @OA\Tag(name="Redmine")
     *
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *        @OA\JsonContent(ref=@Model(type=ReportDataResponse::class))
     * )
     * @OA\Parameter(
     *     name="project",
     *     in="query",
     *     description="Project id RM",
     *     @OA\Schema(type="string")
     * )
     * @param Request $request
     * @return JsonResponse
     * @throws \Redmine\Exception\ClientException
     */
    public function getRedmineTime(Request $request): JsonResponse
    {
        $managerRepository = $this->doctrine->getRepository(Manager::class);
        $reportRepository = $this->doctrine->getRepository(Report::class);
        $serializer = SerializerBuilder::create()->build();

        /** @var IssueTimeRmRequest $catalogProductsRequest */
        $catalogProductsRequest = $serializer->fromArray($request->query->all(), IssueTimeRmRequest::class);

        $errors = $this->getValidatorErrors($catalogProductsRequest, $this->validator);
        if (!empty($errors)) {
            return new JsonResponse(new BadResponse(false, 'Valid param error'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $maskBearer = 'Bearer ';
        $bearer = $request->headers->get('authorization');
        $bearerToken = str_replace($maskBearer, "", $bearer);

        $projectRm = $request->query->get('project');

        $manager = $managerRepository->findOneBy([
            'token' => $bearerToken,
        ]);
        if (empty($manager->getTokenRm())) {
            return new JsonResponse(new BadResponse(false, 'Manager not login Rm!'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $report = $reportRepository->findOneBy([
            'manager' => $manager->getId(),
            'projectRm' => $projectRm,
        ]);
        if (empty($report)) {
            return new JsonResponse(new BadResponse(false, 'Report not exist in DB!'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $parameters = [
            'token' => $bearerToken,
        ];
        $evoAnswerEmployer = $this->clientEvo->getEmployer($parameters);
        if (!empty($evoAnswerEmployer['status'])) {
            return new JsonResponse(new BadResponse(false, $evoAnswerEmployer['status']), JsonResponse::HTTP_BAD_REQUEST);
        }

        try{
            $this->reportService->getTaskTime($bearerToken, $manager, $projectRm, $report, $evoAnswerEmployer);
        } catch (\Exception $e) {
            return new JsonResponse(new BadResponse(false, 'Query param ' . $e->getMessage()), JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(new BadResponse(true, 'ok'), JsonResponse::HTTP_OK);

        // Делаем запись в файл
        /*  $filename = 'generate/table' . $projectRm . '-' . $token . '.json';
          if ($responseTime) {
              file_put_contents($filename, json_encode($responseTime, JSON_FORCE_OBJECT));
          }*/
    }

    /**
     * Изменение периода задачи
     *
     * @Route("/redmine/time", name="getRedmineTimeEdit", methods={"POST"})
     * @OA\Tag(name="Redmine")
     *
     * @OA\RequestBody(
     *     required=true,
     *     description="Данные пользователя для изменения",
     *     @OA\JsonContent(ref=@Model(type=TaskEditRequest::class))
     * )
     * @param Request $request
     * @return JsonResponse
     * @throws \Redmine\Exception\ClientException
     * @throws \Exception
     */
    public function getRedmineTimeEdit(Request $request): JsonResponse
      {
          $serializer = SerializerBuilder::create()->build();
          $entityManager = $this->doctrine->getManager();
          $taskRepository = $this->doctrine->getRepository(Task::class);
          //$spendingRepository = $this->doctrine->getRepository(Spending::class);
          $managerRepository = $this->doctrine->getRepository(Manager::class);

          $maskBearer = 'Bearer ';
          $bearer = $request->headers->get('authorization');
          $bearerToken = str_replace($maskBearer, "", $bearer);

          $manager = $managerRepository->findOneBy([
              'token' => $bearerToken,
          ]);
          if (empty($manager)) {
              return new JsonResponse(new BadResponse(false, 'Manager not exist!'), JsonResponse::HTTP_BAD_REQUEST);
          }

          $clientRm = new ClientRm($manager->getTokenRm());

          $taskEditRequest = $serializer->deserialize($request->getContent(), TaskEditRequest::class, 'json');
          $errors = $this->validator->validate($taskEditRequest);
          if (count($errors) > 0) {
              foreach ($errors as $error) {
                  return new JsonResponse(new BadResponse(false, $error->getMessage()), JsonResponse::HTTP_BAD_REQUEST);
              }
          }

          try {
              $dateFromNew = new \DateTime($taskEditRequest->dateFrom);
              $dateToNew = new \DateTime($taskEditRequest->dateTo);
          } catch (\Exception $e) {
              return new JsonResponse(new BadResponse(false, $e->getMessage()), JsonResponse::HTTP_BAD_REQUEST);
          }

          $task = $taskRepository->findOneBy([
              'taskId' => $taskEditRequest->taskId,
              'report' => $taskEditRequest->reportId,
          ]);
          if (empty($task)) {
              return new JsonResponse(new BadResponse(false, 'Task not exist!'), JsonResponse::HTTP_BAD_REQUEST);
          }

          $report = $task->getReport();
          if (empty($report)) {
              return new JsonResponse(new BadResponse(false, 'Report not exist!'), JsonResponse::HTTP_BAD_REQUEST);
          }

          foreach ($task->getSpendings() as $spending) {
              //$task->removeSpendings($spending);
          }
          $task->setFromDate($dateFromNew);
          $task->setToDate($dateToNew);
          $entityManager->persist($task);

          $filterIssueForRedmine = [
              'project_id' => $task->getRedmineId(),
              'from' => $dateFromNew->format('Y-m-d'),
              'to' => $dateToNew->format('Y-m-d'),
          ];

          $taskRm = $clientRm->getTimeEntriesWithoutLimit($filterIssueForRedmine);

          $parametersGetTaskEvo = [
              'token' => $manager->getToken(),
              'filter[date][from]' =>  $dateFromNew->format('Y-m-d'),
              'filter[date][to]' => $dateToNew->format('Y-m-d'),
              'filter[project_id]' => $taskEditRequest->projectEvo,
          ];
          $evoTaskAnswer = $this->clientEvo->getTask($parametersGetTaskEvo);
          if (!empty($evoTaskAnswer['status'])) {
              return new JsonResponse(new BadResponse(false, $evoTaskAnswer['status']), JsonResponse::HTTP_BAD_REQUEST);
          }

          $parameters = [
              'token' => $bearerToken,
          ];
          $evoAnswerEmployer = $this->clientEvo->getEmployer($parameters);
          if (!empty($evoAnswerEmployer['status'])) {
              return new JsonResponse(new BadResponse(false, $evoAnswerEmployer['status']), JsonResponse::HTTP_BAD_REQUEST);
          }

          $result = $this->spendingCreate($task, $evoAnswerEmployer, $taskRm, $evoTaskAnswer);

          $entityManager->flush();
          return new JsonResponse($result, JsonResponse::HTTP_OK);

      }

    /**
     * Получение данных по отчету
     *
     * @Route("/redmine/report", name="getRedmineReport", methods={"GET"})
     * @OA\Tag(name="Redmine")
     *
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *        @OA\JsonContent(ref=@Model(type=ReportDataResponse::class))
     * )
     * @OA\Parameter(
     *     name="project",
     *     in="query",
     *     description="id Проекта из RM",
     *     required=true,
     *     @OA\Schema(type="int")
     * )
     * @OA\Parameter(
     *     name="filter[task]",
     *     in="query",
     *     description="Задачи для выгрузки: [123, 345, 567]",
     *     required=false,
     *     @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     name="filter[quantity]",
     *     in="query",
     *     description="Способ для выгрузки: all|null",
     *     required=false,
     *     @OA\Schema(type="string")
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function getRedmineReport(Request $request): JsonResponse
    {
        $reportRepository = $this->doctrine->getRepository(Report::class);
        $managerRepository = $this->doctrine->getRepository(Manager::class);

        $maskBearer = 'Bearer ';
        $bearer = $request->headers->get('authorization');
        $bearerToken = str_replace($maskBearer, "", $bearer);

        $projectId = $request->query->get('project');
        $filter = json_decode($request->query->get('filter'), true);

        $manager = $managerRepository->findOneBy([
            'token' => $bearerToken,
        ]);
        if (empty($manager)) {
            return new JsonResponse('Manager not exist!', JsonResponse::HTTP_BAD_REQUEST);
        }

        $report = $reportRepository->findOneBy([
            'manager' => $manager,
            'projectRm' => $projectId,
        ]);
        if (empty($report)) {
            return new JsonResponse('Report not exist!', JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            if (isset($filter['quantity'])) {
                if (isset($filter['issue']) && $filter['quantity'] == 'null') {
                    $arrayTask = [];
                    foreach ($report->getTasks() as $task) {
                        if (in_array($task->getTaskId(), $filter['issue'])) {
                            $arrayTask[] = $task;
                        }
                    }
                } elseif ($filter['quantity'] == 'all') {
                    $arrayTask = $report->getTasks();
                } else {
                    throw new \Exception('Param Query error!');
                }
            } else {
               throw new \Exception('Param Query error!');
            }
        } catch (\Exception $e) {
            return new JsonResponse(new BadResponse(false, $e->getMessage()), JsonResponse::HTTP_BAD_REQUEST);
        }

        try{
            $result = $this->reportService->getReportData($arrayTask, $report);
            $dateInterval = $result['date'];
            $reportArrayDTO = $result['report'];
        } catch (\Exception $e) {
            return new JsonResponse(new BadResponse(false, 'Query param ' . $e->getMessage()), JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(new ReportDataResponse(true, $reportArrayDTO, $dateInterval), JsonResponse::HTTP_OK);
    }

    public function getDateInterval($dateFrom, $dateTo)
    {
        $datesForColumns = [];
        $fromDate = new \DateTime($dateFrom->format('Y-m'));
        $toDate = new \DateTime($dateTo->format('Y-m'));
        print_r($fromDate);
        $interval = $toDate->diff($fromDate);
        $months = $interval->y * 12 + $interval->m;
        if (0 >= $months) {
            $period = [$fromDate];
        } else {
            $period = new \DatePeriod($fromDate, new \DateInterval('P1M'), $months);
        }

        foreach ($period as $date) {
            $month = $date->format('F');
            $datesForColumns[$date->format('Y')][$date->format('m')] = $month;
        }

        return $datesForColumns;
    }

    /**
     * @param object $object
     * @param ValidatorInterface $validator
     * @return string[]
     */
    private function getValidatorErrors(object $object, ValidatorInterface $validator): array
    {
        $errors = [];
        /** @var ConstraintViolation $error */
        foreach ($validator->validate($object) as $error) {
            $errors[] = $error->getMessage();
        }

        return $errors;
    }
}
