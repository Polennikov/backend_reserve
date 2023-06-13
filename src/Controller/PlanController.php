<?php

namespace App\Controller;

use App\Entity\ApprovedPlan;
use App\Entity\Plan;
use App\Model\Response\ApprovedPlanResponse;
use App\Model\Response\ManagerResponse;
use App\Model\Response\PlanResponse;
use App\Model\Response\SettingManagerResponse;
use App\Model\Response\SuccessResponse;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Model\Request\PlanRequest;

/**
 * @Route("/api/doc")
 */
class PlanController extends AbstractController
{
    private $doctrine;

    private $validator;

    public function __construct(
        ManagerRegistry $doctrine,
        ValidatorInterface  $validator
    ) {
        $this->doctrine = $doctrine;
        $this->validator = $validator;
    }

    /**
     * Получение всех неджеров из БД
     *
     * @Route("/plan", name="getPlan", methods={"GET"})
     * @OA\Tag(name="Plan")
     *
     * @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\JsonContent(ref=@Model(type=ManagerResponse::class))
     * )
     * @OA\Parameter(
     *     name="project",
     *     in="query",
     *     description="project",
     *     @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     name="month",
     *     in="query",
     *     description="month",
     *     @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     name="year",
     *     in="query",
     *     description="year",
     *     @OA\Schema(type="string")
     * )
     * @return JsonResponse
     */
    public function getPlan(): JsonResponse
    {
        $request = Request::createFromGlobals();

        $planRepository = $this->doctrine->getRepository(Plan::class);
        $approvedPlanRepository = $this->doctrine->getRepository(ApprovedPlan::class);

        $project = (string)$request->query->get('project');
        $month = (string)$request->query->get('month');
        $year = (string)$request->query->get('year');

        $plan = $planRepository->findOneBy([
            'project' => $project,
            'month' => $month,
            'year' => $year
        ]);
//print_r($plan);
        $planResponse = [];
        if (isset($plan)) {
            $planResponse = new PlanResponse();
            $planResponse->year = $plan->getYear();
            $planResponse->status = $plan->getStatus();
            $planResponse->month = $plan->getMonth();
            $planResponse->project = $plan->getProject();

            $approvedPlanResponseArray = [];
            $approvedPlan = $approvedPlanRepository->findBy([
                'plan' => $plan,
            ]);
//print_r($approvedPlan);
            foreach ($approvedPlan as $item) {
                $approvedPlanResponse = new ApprovedPlanResponse();
                $approvedPlanResponse->value = $item->getCheckValue();
                $approvedPlanResponse->competence = $item->getCompetence();
                $approvedPlanResponse->plan = $item->getPlan()->getId();
                $approvedPlanResponseArray[] = $approvedPlanResponse;
            }
            $planResponse->approvedPlan = $approvedPlanResponseArray;

        }

        return new JsonResponse($planResponse, JsonResponse::HTTP_OK);
    }

    /**
     * Получение настроек менеджера
     *
     * @Route("/plan", name="setPlan", methods={"POST"})
     * @OA\Tag(name="Plan")
     * @OA\RequestBody(
     *     required=true,
     *     description="Данные",
     *     @OA\JsonContent(ref=@Model(type=PlanRequest::class))
     * )
     * @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\JsonContent(ref=@Model(type=SettingManagerResponse::class))
     * )
     * @return JsonResponse
     */
    public function setPlan(): JsonResponse
    {
        $planRepository = $this->doctrine->getRepository(Plan::class);
        $approvedPlanRepository = $this->doctrine->getRepository(ApprovedPlan::class);

        $entityManager = $this->doctrine->getManager();

        $request = Request::createFromGlobals();

        $request = json_decode($request->getContent(), true);

        $project= (string)$request['project'];
        $projectName= (string)$request['projectName'];
        $status= (string)$request['status'];
        $month= (string)$request['month'];
        $year= (string)$request['year'];
        $currentCompetence= $request['currentCompetence'];

        $plan = $planRepository->findOneBy([
            'project' => $project,
            'month' => $month,
            'year' => $year
        ]);

        if (!isset($plan)) {
            $plan = new Plan();
        }
        $competence = [];
        $approvedPlanResponseArray = [];
        if ($status == 'time') {
                foreach ($currentCompetence as $item) {
                $approvedPlanResponse = new ApprovedPlanResponse();
                    $approvedPlan = $approvedPlanRepository->findOneBy([
                        'plan' => $plan->getId(),
                        'competence' => $item
                    ]);
                    if (empty($approvedPlan)) {
                        $approvedPlan = new ApprovedPlan();
                        $approvedPlan->setCheckValue('false');
                        $approvedPlan->setCompetence($item);
                        $approvedPlan->setPlan($plan);
                        $entityManager->persist($approvedPlan);
                    }
                $approvedPlanResponse->value = false;
                $approvedPlanResponse->competence = $item;

                $approvedPlanResponse->plan = $plan->getId();
                $approvedPlanResponseArray[] = $approvedPlanResponse;
            }
        }
        $plan->setProject($project);
        $plan->setProjectName($projectName);
        $plan->setStatus($status);
        $plan->setMonth($month);
        $plan->setYear($year);
        //print_r($plan);
        $planResponse = new PlanResponse();
        $planResponse->project = $plan->getProject();
        $planResponse->projectName = $plan->getProjectName();
        $planResponse->status = $plan->getStatus();
        $planResponse->month = $plan->getMonth();
        $planResponse->year = $plan->getYear();
        $planResponse->approvedPlan = $approvedPlanResponseArray;
        //print_r($planResponse);
        $entityManager->persist($plan);
        $entityManager->flush();
        return new JsonResponse($planResponse, JsonResponse::HTTP_OK);
    }

    /**
     * Получение настроек менеджера
     *
     * @Route("/plan/confirmed", name="setPlanConfirmed", methods={"POST"})
     * @OA\Tag(name="Plan")
     * @OA\RequestBody(
     *     required=true,
     *     description="Данные",
     *     @OA\JsonContent(ref=@Model(type=PlanRequest::class))
     * )
     * @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\JsonContent(ref=@Model(type=SettingManagerResponse::class))
     * )
     * @return JsonResponse
     */
    public function setPlanConfirmed(): JsonResponse
    {
        $planRepository = $this->doctrine->getRepository(Plan::class);
        $approvedPlanRepository = $this->doctrine->getRepository(ApprovedPlan::class);

        $entityManager = $this->doctrine->getManager();

        $request = Request::createFromGlobals();

        $request = json_decode($request->getContent(), true);

        $project= (string)$request['project'];
        $month= (string)$request['month'];
        $year= (string)$request['year'];
        $currentCompetence= (string)$request['competence'];
        $value = (string)$request['value'];
//print_r($value);
        $plan = $planRepository->findOneBy([
            'project' => $project,
            'month' => $month,
            'year' => $year
        ]);
        $approvedPlan = 'false';
        if (!empty($plan)) {
            $approvedPlan = $approvedPlanRepository->findOneBy([
                'plan' => $plan,
                'competence' => $currentCompetence,
            ]);
            if (!empty($approvedPlan)) {
                $approvedPlan->setCheckValue($value == 1 ? 'true' : 'false');
                $entityManager->persist($approvedPlan);
                $entityManager->flush();
            }
        }

        return new JsonResponse($approvedPlan, JsonResponse::HTTP_OK);
    }

    /**
     * Получение всех неджеров из БД
     *
     * @Route("/plan/approved", name="getPlanApproved", methods={"GET"})
     * @OA\Tag(name="Plan")
     *
     * @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\JsonContent(ref=@Model(type=ManagerResponse::class))
     * )
     * @OA\Parameter(
     *     name="project",
     *     in="query",
     *     description="project",
     *     @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     name="month",
     *     in="query",
     *     description="month",
     *     @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     name="year",
     *     in="query",
     *     description="year",
     *     @OA\Schema(type="string")
     * )
     * @return JsonResponse
     */
    public function getPlanApproved(): JsonResponse
    {
        $request = Request::createFromGlobals();

        $planRepository = $this->doctrine->getRepository(Plan::class);
        $approvedPlanRepository = $this->doctrine->getRepository(ApprovedPlan::class);

        $competence = $request->query->get('competence');
//print_r($competence);
        $plans = $planRepository->findAll();
        $planResponseArray = [];
        foreach ($plans as $item) {
            if ($item->getStatus() == 'time') {
                $approvedPlan = $approvedPlanRepository->findBy(['plan' => $item]);
                if (!empty($approvedPlan)) {
                    foreach ($approvedPlan as $itemApproved) {
                        if ($competence == $itemApproved->getCompetence() && $itemApproved->getCheckValue() !== true) {
                            $planResponse = new PlanResponse();
                            $planResponse->year = $item->getYear();
                            $planResponse->status = $item->getStatus();
                            $planResponse->month = $item->getMonth();
                            $planResponse->project = $item->getProject();
                            $planResponseArray[] = $planResponse;
                        }
                    }
                }
            }
        }

        return new JsonResponse($planResponseArray, JsonResponse::HTTP_OK);
    }
}
