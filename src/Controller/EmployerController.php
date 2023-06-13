<?php

namespace App\Controller;

use App\Model\EvoDTO\EmployerDTO;
use App\Model\MockData;
use App\Model\Response\SettingEmployerResponse;
use App\Model\SettingEmployerDTO;
use App\Service\ClientBitrix;
use App\Service\DataOperationService;
use App\Entity\SettingEmployer;
use App\Model\Response\BadResponse;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\ClientEvo;
use App\Entity\Reservation;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Model\Response\Evo\EmployerResponse;

/**
 * @Route("/api/doc")
 */
class EmployerController extends AbstractController
{
    private $doctrine;

    private $clientEvo;

    private $clientBitrix;

    private $resourceController;

    private $evoApiLogger;

    private $bitrixApiLogger;

    private $mockData;

    public function __construct(
        ManagerRegistry $doctrine,
        ClientEvo $clientEvo,
        ClientBitrix $clientBitrix,
        DataOperationService $resourceController,
        LoggerInterface  $evoApiLogger,
        LoggerInterface  $bitrixApiLogger,
        MockData  $mockData
    ) {
        $this->doctrine = $doctrine;
        $this->clientEvo = $clientEvo;
        $this->clientBitrix = $clientBitrix;
        $this->resourceController = $resourceController;
        $this->evoApiLogger = $evoApiLogger;
        $this->bitrixApiLogger = $bitrixApiLogger;
        $this->mockData = $mockData;
        $this->clientEvo->setLogger($this->evoApiLogger);
        $this->clientBitrix->setLogger($this->bitrixApiLogger);
    }

    /**
     * Получение всех сотрудников
     *
     * @Route("/employer", name="getEmployers", methods={"GET"})
     * @OA\Tag(name="Employer")
     *
     * @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\JsonContent(ref=@Model(type=EmployerResponse::class))
     * )
     * @return JsonResponse
     */
    public function getEmployers(): JsonResponse
    {
        $request = Request::createFromGlobals();
        $maskBearer = 'Bearer ';
        $bearer = $request->headers->get('authorization');
        $bearerToken = str_replace($maskBearer, "", $bearer);

        $parameters = [
            'token' => $bearerToken,
        ];

        $evoAnswer = $this->clientEvo->getEmployer($parameters);

        if (empty($evoAnswer['data'])) {
            return new JsonResponse(new BadResponse(false, $evoAnswer['status']), JsonResponse::HTTP_BAD_REQUEST);
        }

        $listEmployers = $evoAnswer['data'];
        // Выгрузка
        /*   $fp = fopen("employer.csv", "w");
           foreach ($listEmployers as $line)
           {fputcsv($fp, // The file pointer
               $line, // The fields
           ',' // The delimiter
               );
           }
           fclose($fp);*/

        $listEmployerB24 = $this->clientBitrix->getEmployers();
        if ($listEmployerB24) {
            foreach ($listEmployerB24 as $B24) {
                $arr[$B24->lastName . ' ' . $B24->firstName . ' ' . $B24->partonymic] = $B24->id;
            }
            foreach ($arr as $key => $value) {
                foreach ($listEmployers as &$data) {
                    if (stristr($data['title'], $key) || stristr($key, $data['title'])) {
                        $data['idB24'] = $arr[$key];
                    }
                }
            }
        }
        $employersArray = [];
        //test
        $listEmployers = $this->mockData->getEmployer();
        //test
        foreach ($listEmployers as $data) {
            $employerDto = new EmployerDTO();
            $employerDto->id = $data['id'];
            $employerDto->title = $data['title'];
            $employerDto->company = $data['company'];
            $employerDto->competence = $data['competence'];
            $employerDto->employment = $data['employment'];
            $employerDto->grade = $data['grade'];
            $employerDto->intaroHours = $data['intaroHours'];
            $employerDto->dateEnd = $data['dateEnd'];
            $employerDto->idB24 = $data['idB24'] ?? null;
            $employerDto->settings = $this->resourceController->getEmployerSetting($this->doctrine, $data['id']) ?? null;
            $employerDto->lidProjectId = $this->resourceController->getProjectLidId($this->doctrine, $data['id']) ?? null;
            $employersArray[] = $employerDto;
        }

        $responseArray = new EmployerResponse(true);
        $responseArray->employers = $employersArray;

        return new JsonResponse($responseArray, JsonResponse::HTTP_OK);
    }

    /**
     * Получение всех сотрудников одной компетенции
     *
     * @Route("/employer/competence", name="getEmployerCompetence", methods={"GET"})
     * @OA\Tag(name="Employer")
     *
     * @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\JsonContent(ref=@Model(type=EmployerResponse::class))
     * )
     * @OA\Parameter(
     *     name="competence",
     *     in="query",
     *     description="Competence Name",
     *     @OA\Schema(type="string")
     * )
     * @return JsonResponse
     */
    public function getEmployerCompetence(): JsonResponse
    {
        $request = Request::createFromGlobals();

        $maskBearer = 'Bearer ';
        $bearer = $request->headers->get('authorization');
        $bearerToken = str_replace($maskBearer, "", $bearer);

        $parameters = [
            'token' => $bearerToken,
        ];

        $evoAnswer = $this->clientEvo->getEmployer($parameters);

        $competence = (string)$request->query->get('competence');

        if (empty($evoAnswer['data'])) {
            return new JsonResponse(new BadResponse(false, $evoAnswer['status']), JsonResponse::HTTP_BAD_REQUEST);
        }

        $employersArray = [];
        //test
        $evoAnswer['data'] = $this->mockData->getEmployer();
        //test
        foreach ($evoAnswer['data'] as $employer) {
            if ($employer['competence'] == $competence) {
                $employerDto = new EmployerDTO();
                $employerDto->id = $employer['id'];
                $employerDto->title = $employer['title'];
                $employerDto->company = $employer['company'];
                $employerDto->competence = $employer['competence'];
                $employerDto->employment = $employer['employment'];
                $employerDto->grade = $employer['grade'];
                $employerDto->intaroHours = $employer['intaroHours'];
                $employerDto->dateEnd = $employer['dateEnd'];
                $employersArray[] = $employerDto;
            }
        }
        $responseArray = new EmployerResponse(true);
        $responseArray->employers = $employersArray;

        return new JsonResponse($responseArray, JsonResponse::HTTP_OK);
    }

    /**
     * Получение сотрудников работавших на проекте за указанный месяц
     *
     * @Route("/employer/project", name="getEmployerProject", methods={"GET"})
     * @OA\Tag(name="Employer")
     *
     * @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\JsonContent(ref=@Model(type=EmployerResponse::class))
     * )
     * @OA\Parameter(
     *     name="project",
     *     in="query",
     *     description="Project id",
     *     @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     *     name="year",
     *     in="query",
     *     description="Year",
     *     @OA\Schema(type="int")
     * ),
     * @OA\Parameter(
     *     name="month",
     *     in="query",
     *     description="Month number",
     *     @OA\Schema(type="int")
     * )
     * @return JsonResponse
     * @throws \Exception
     */
    public function getEmployerProject(): JsonResponse
    {
        $request = Request::createFromGlobals();
        $reservationRepository = $this->doctrine->getRepository(Reservation::class);

        $projectId = (int)$request->query->get('project');
        $year = (int)$request->query->get('year');
        $month = (int)$request->query->get('month');

        $maskBearer = 'Bearer ';
        $bearer = $request->headers->get('authorization');
        $bearerToken = str_replace($maskBearer, "", $bearer);

        $parameters = [
            'token' => $bearerToken,
        ];

        $evoAnswerEmployer = $this->clientEvo->getEmployer($parameters);

        if (!empty($evoAnswerEmployer['status'])) {
            return new JsonResponse(new BadResponse(false, $evoAnswerEmployer['status']), JsonResponse::HTTP_BAD_REQUEST);
        }

        $listEmployerB24 = $this->clientBitrix->getEmployers();

        /*foreach ($listEmployerB24 as $B24) {
            $arr[$B24->lastName . ' ' . $B24->firstName . ' ' . $B24->partonymic] = $B24->id;
        }*/
       /* foreach ($arr as $key => $value) {
            foreach ($evoAnswerEmployer['data'] as &$data) {
                if (stristr($data['title'], $key) || stristr($key, $data['title'])) {
                    $data['idB24'] = $arr[$key];
                }
            }
        }*/

        //test
        $evoAnswerEmployer['data'] = $this->mockData->getEmployer();
        //test

       foreach ($evoAnswerEmployer['data'] as &$data) {
            $data['settings'] = $this->resourceController->getEmployerSetting($this->doctrine, $data['id']) ?? null;
            $data['lidProjectId'] = $this->resourceController->getProjectLidId($this->doctrine, $data['id']) ?? null;
        }

        $employers = [];

        $dateTo = (new \DateTime('01.' . $month . '.' . $year))->modify('+1 month');
        $dateTo->modify('-1 day');

        $parameters = [
            'token' => $bearerToken,
            'filter[date][from]' => '01.' . $month . '.' . $year,
            'filter[date][to]' => $dateTo->format('d.m.Y'),
            'filter[project_id]' => (string)$projectId,
            'limit' => 20000,
        ];

        $evoAnswerTask = $this->clientEvo->getTask($parameters);

        //test
        $evoAnswerTask['data'] = $this->mockData->getTaskFilter($month, $year, $projectId);
        //test

        if (!empty($evoAnswerTask['status'])) {
            return new JsonResponse(new BadResponse(false, $evoAnswerTask['status']), JsonResponse::HTTP_BAD_REQUEST);
        }

        foreach ($evoAnswerTask['data'] as $task) {
            if (!in_array((int)$task['employer_id'], $employers)) {
                $employers [] = (int)$task['employer_id'];
            }
        }

        $reservations = $reservationRepository->findBy([
            'projectId' => $projectId,
            'year' => $year,
            'month' => (int)$month,
        ]);

        foreach ($reservations as $reservation) {
            if ($reservation->getPercent() > 0) {
                if (!in_array((int)$reservation->getEmployerId(), $employers)) {
                    $employers [] = $reservation->getEmployerId();
                }
            }
        }

        $employersArray = [];

        foreach ($employers as $employer) {
            foreach ($evoAnswerEmployer['data'] as $dataEmployer) {
                if ($dataEmployer['id'] == $employer) {
                    $employerDto = new EmployerDTO();
                    $employerDto->id = $dataEmployer['id'];
                    $employerDto->title = $dataEmployer['title'];
                    $employerDto->company = $dataEmployer['company'];
                    $employerDto->competence = $dataEmployer['competence'];
                    $employerDto->employment = $dataEmployer['employment'];
                    $employerDto->grade = $dataEmployer['grade'];
                    $employerDto->intaroHours = $dataEmployer['intaroHours'];
                    $employerDto->dateEnd = $dataEmployer['dateEnd'];
                    $employerDto->idB24 = $dataEmployer['idB24'] ?? null;
                    $employerDto->settings = $dataEmployer['settings'];
                    $employerDto->lidProjectId = $dataEmployer['lidProjectId'];
                    $employersArray[] = $employerDto;
                }
            }
        }
        $responseArray = new EmployerResponse(true);
        $responseArray->employers = $employersArray;

        return new JsonResponse($responseArray, JsonResponse::HTTP_OK);
    }

    /**
     * Изменение настроек сотрудника
     *
     * @Route("/employer/setting/{id}", name="editSettingEmployer", methods={"POST"})
     * @OA\Tag(name="Employer")
     *
     * @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="workHours",
     *                  type="string",
     *                  example="..."
     *              )
     *          )
     *     )
     * @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\JsonContent(ref=@Model(type=SettingEmployerResponse::class))
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="employer id",
     *     @OA\Schema(type="string")
     * )
     * @param int $id
     * @return Response
     */
    public function editSettingEmployer(int $id): Response
    {
        $entityManager = $this->doctrine->getManager();
        $settingEmployerManager = $this->doctrine->getRepository(SettingEmployer::class);
        $request = Request::createFromGlobals();

        $request = json_decode($request->getContent(), true);

        $hoursWork = (int)$request['hoursWork'];

        if ($hoursWork < 1 || $hoursWork > 9) {
            return new JsonResponse(new BadResponse(false, 'Error hoursWork number!'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $settingEmployer = $settingEmployerManager->findOneBy([
            'employerId' => $id,
        ]);

        if (empty($settingEmployer)) {
            $settingEmployer = new settingEmployer();
        }

        try {
            $settingEmployer->setEmployerId((integer)$id);
            $settingEmployer->setHoursWork((integer)$hoursWork);

            $entityManager->persist($settingEmployer);
            $entityManager->flush();

        } catch (\Exception $e) {
            return new JsonResponse(new BadResponse(false, $e->getMessage()), JsonResponse::HTTP_BAD_REQUEST);
        }
        $settingEmployerDTO = new SettingEmployerDTO();
        $settingEmployerDTO->hoursWork = $settingEmployer->getHoursWork();

        return new JsonResponse(new SettingEmployerResponse(true, $settingEmployerDTO), JsonResponse::HTTP_OK);
    }
}
