<?php

namespace App\Controller;

use App\Entity\ApprovedPlan;
use App\Entity\Plan;
use App\Entity\Reservation;
use App\Entity\ReservationChange;
use App\Model\EvoDTO\CalendarDTO;
use App\Model\EvoDTO\FactDTO;
use App\Model\EvoDTO\PlanResourceDTO;
use App\Model\EvoDTO\ReserveDTO;
use App\Model\MockData;
use App\Model\Response\BadResponse;
use App\Service\ClientBitrix;
use App\Service\ClientEvo;
use App\Service\DataOperationService;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Annotations as OA;
use App\Model\Response\Evo\ReserveResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Model\Response\Evo\InfoReserveResponse;
use App\Model\Response\Evo\InfoEmployerResponse;
use App\Model\Response\Evo\ResourceResponse;

/**
 * @Route("/api/doc")
 */
class ResourceDataController extends AbstractController
{
    private $doctrine;

    private $clientEvo;

    private $clientBitrix;

    private $resourceController;

    private $mockData;

    /**
     * ResourceDataController constructor.
     * @param ManagerRegistry $doctrine
     * @param ClientEvo $clientEvo
     * @param ClientBitrix $clientBitrix
     * @param DataOperationService $resourceController
     * @param LoggerInterface $evoApiLogger
     * @param LoggerInterface $bitrixApiLogger
     * @param MockData $mockData
     */
    public function __construct(
        ManagerRegistry $doctrine,
        ClientEvo $clientEvo,
        ClientBitrix $clientBitrix,
        DataOperationService $resourceController,
        LoggerInterface  $evoApiLogger,
        LoggerInterface  $bitrixApiLogger,
        MockData $mockData
    ) {
        $this->doctrine = $doctrine;
        $this->clientEvo = $clientEvo;
        $this->clientBitrix = $clientBitrix;
        $this->resourceController = $resourceController;
        $this->resourceController->setDoctrine($this->doctrine);
        $this->clientEvo->setLogger($evoApiLogger);
        $this->clientBitrix->setLogger($bitrixApiLogger);
        $this->mockData = $mockData;
    }

    /**
     * Получение информации карточки сотрудника
     *
     * @Route("/info/employer", name="getEmployerCardInfo", methods={"GET"})
     * @OA\Tag(name="ResourceData")
     *
     * @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\JsonContent(ref=@Model(type=InfoEmployerResponse::class))
     * )
     * @OA\Parameter(
     *     name="employer",
     *     in="query",
     *     description="Employer id",
     *     @OA\Schema(type="int")
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
     * ),
     * @OA\Parameter(
     *     name="idB24",
     *     in="query",
     *     description="idB24",
     *     @OA\Schema(type="int")
     * ),
     * @OA\Parameter(
     *     name="settingHours",
     *     in="query",
     *     description="settingHours",
     *     @OA\Schema(type="int")
     * )
     * @param ManagerRegistry $doctrine
     * @return Response
     * @throws \Exception
     */
    public function getEmployerCardInfo(): Response
    {
        $request = Request::createFromGlobals();
        $reservationRepository = $this->doctrine->getRepository(Reservation::class);

        $maskBearer = 'Bearer ';
        $bearer = $request->headers->get('authorization');
        $bearerToken = str_replace($maskBearer, "", $bearer);

        $employerId = (int)$request->query->get('employer');
        $year = (int)$request->query->get('year');
        $month = (int)$request->query->get('month');
        $idB24 = (int)$request->query->get('idB24');
        $settingHours = (int)$request->query->get('settingHours');

        if (strlen((string)$month) === 1) {
            $month = '0' . $month;
        }

        $dateTo = (new \DateTime('01.' . $month . '.' . $year))->modify('+1 month');
        $dateTo->modify('-1 day');

        $parameters = [
            'token' => $bearerToken,
            'filter[date][from]' => '01.' . $month . '.' . $year,
            'filter[date][to]' => $dateTo->format('d.m.Y'),
            'filter[employer_id]' => $employerId,
            'limit' => 20000,
        ];

        //$evoAnswer = $this->clientEvo->getTask($parameters);
        //test
        $evoAnswer['data'] = $this->mockData->getTaskFilterInfo($month, $year, $employerId);
        //test

        $projectFact = $this->resourceController->getFactEmployerMonth($evoAnswer);

        $reservations = $reservationRepository->findBy([
            'employerId' => $employerId,
            'year' => $year,
            'month' => (int)$month,
        ]);
        $reservationChangeRepository = $this->doctrine->getRepository(ReservationChange::class);
        $arReservations = $this->resourceController->getPlanEmployerMonth($reservations, $reservationChangeRepository);

        $dateFrom = new DateTime($year . $month . '01');
        $dateTo = (new DateTime($year . $month . '01'))->modify('+1 month')->modify('-1 days');

        $workingDays = $this->resourceController->getCountDaysPeriod($year, $month, $dateFrom->format('Y-m-d'), $dateTo->format('Y-m-d'));


        /*if ($idB24 != 0) {
            $calendar = $this->resourceController->getCalendar($dateFrom->format('Y-m-d'), $dateTo->format('Y-m-d'), [$idB24]);
        }*/
        $reservationsData = $this->resourceController->getAllReserveEmployer($employerId, $month, $year, null, null, $this->doctrine);

        $absentDays = 0;

        if (isset($calendar)) {
            foreach ($calendar[$idB24] as $calendarItem) {
                $absentDays = $this->resourceController->getCountDaysPeriod($year, $month, $calendarItem->dateFrom->format('Y-m-d'), $calendarItem->dateTo->format('Y-m-d'));
            }
        }

        $persentAllMonth = $this->resourceController->hoursOnPercent($settingHours * ($workingDays - $absentDays), $workingDays, $settingHours) - $reservationsData['allPercent'];
        $hoursAllMonth = $this->resourceController->percentOnHours($persentAllMonth, $workingDays, $settingHours);

        if (isset($calendar)) {
            foreach ($calendar[$idB24] as $item) {
                $item->dateFrom = $item->dateFrom->format('d.m.Y');
                $item->dateTo = $item->dateTo->format('d.m.Y');
            }
            $calendar = $calendar[$idB24];
        } else {
            $calendar = [];
        }
        $dataResponse = [
            'workingDays' => $workingDays,
            'success' => true,
            'fact' => $projectFact,
            'plan' => $arReservations,
            'calendar' => $calendar,
            'freePercent' => $persentAllMonth,
            'freeHours' => $hoursAllMonth,

        ];
        return new JsonResponse($dataResponse, JsonResponse::HTTP_OK);

    }

    /**
     * Получение информации таблицы ресурсов
     *
     * @Route("/resource", name="getResource", methods={"GET"})
     * @OA\Tag(name="ResourceData")
     *
     * @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\JsonContent(ref=@Model(type=ResourceResponse::class))
     * )
     * @OA\Parameter(
     *     name="competence",
     *     in="query",
     *     description="Competence Name",
     *     @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     name="year",
     *     in="query",
     *     description="year",
     *     @OA\Schema(type="int")
     * )
     * @OA\Parameter(
     *     name="month",
     *     in="query",
     *     description="month",
     *     @OA\Schema(type="int")
     * )
     * @return JsonResponse
     * @throws \Exception
     */
    public function getResource(): JsonResponse
    {
        $request = Request::createFromGlobals();

        $maskBearer = 'Bearer ';
        $bearer = $request->headers->get('authorization');
        $bearerToken = str_replace($maskBearer, "", $bearer);

        $competence = $request->query->get('competence');
        $month = $request->query->get('month');
        $year = $request->query->get('year');

        $reservationRepository = $this->doctrine->getRepository(Reservation::class);

        $dateTo = (new \DateTime('01.' . $month . '.' . $year))->modify('+1 month');
        $dateTo->modify('-1 day');

        $parameters = [
            'token' => $bearerToken,
            'filter[date][from]' => '01.' . $month . '.' . $year,
            'filter[date][to]' => $dateTo->format('d.m.Y'),
            'filter[project_id]' => 25,
            'limit' => 20000,
        ];
        $evoAnswerTask = $this->clientEvo->getTask($parameters);

        if (!empty($evoAnswerTask['status'])) {
            return new JsonResponse(new BadResponse(false, $evoAnswerTask['status']), JsonResponse::HTTP_BAD_REQUEST);
        }

        $parameters = [
            'token' => $bearerToken,
        ];
        $evoAnswerEmployer = $this->clientEvo->getEmployer($parameters);

        if (!empty($evoAnswerTask['status'])) {
            return new JsonResponse(new BadResponse(false, $evoAnswerEmployer['status']), JsonResponse::HTTP_BAD_REQUEST);
        }

        $reserveArray['freeTime'] = [];
        $reserveArray['planPercent'] = [];
        $freeTimeArray = [];
        $planPercentArray = [];
        foreach ($evoAnswerEmployer['data'] as $employer) {
            if ($employer['competence'] == $competence) {
                $factDTO = new FactDTO();
                $factDTO->id = $employer['id'];

                $planResourceDTO = new PlanResourceDTO();
                $planResourceDTO->id = $employer['id'];

                $hour = 0;
                foreach ($evoAnswerTask['data'] as $task) {
                    if ($task['employer_id'] == $employer['id']) {
                        $hour += $task['time'];
                    }
                }
                $factDTO->hour = $hour;

                $reservations = $reservationRepository->findBy([
                    'employerId' => $employer['id'],
                    'year' => $year,
                    'month' => $month,
                ]);

                $percent = 0;
                foreach ($reservations as $reservation) {
                    $percent += $reservation->getPercent();
                }
                $planResourceDTO->percent = $percent;

                $freeTimeArray [] = $factDTO;
                $planPercentArray [] = $planResourceDTO;
            }
        }

        return new JsonResponse(new ResourceResponse(true, $freeTimeArray, $planPercentArray), JsonResponse::HTTP_OK);
    }

    /**
     * Получение информации таблицы резерва проекта
     *
     * @Route("/reserve", name="getReserveTable", methods={"GET"})
     * @OA\Tag(name="ResourceData")
     *
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *       @OA\JsonContent(
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="fact",
     *                  type="object",
     *                  @OA\Property(
     *                      property="134",
     *                      type="int",
     *                      example="35",
     *                  ),
     *                  @OA\Property(
     *                      property="135",
     *                      type="int",
     *                      example="35",
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="plan",
     *                  type="object",
     *                  @OA\Property(
     *                      property="134",
     *                      type="object",
     *                      @OA\Property(
     *                          property="4",
     *                          type="object",
     *                          @OA\Property(
     *                              property="percent",
     *                              type="int",
     *                              example="49",
     *                          ),
     *                          @OA\Property(
     *                              property="dateReserve",
     *                              type="string",
     *                              example="13.06.2022 21:45:43",
     *                          ),
     *                          @OA\Property(
     *                              property="history",
     *                              type="array",
     *                              @OA\Items(
     *                                  @OA\Property(
     *                                      property="new",
     *                                      type="int",
     *                                      example="20",
     *                                  ),
     *                                  @OA\Property(
     *                                      property="old",
     *                                      type="int",
     *                                      example="80",
     *                                  ),
     *                                  @OA\Property(
     *                                      property="date",
     *                                      type="string",
     *                                      example="25.07.2022 12:00:36",
     *                                  )
     *                              )
     *                          )
     *                      )
     *                  )
     *              )
     *       )
     *
     *   ),
     * @OA\Parameter(
     *     name="project",
     *     in="query",
     *     description="Project id",
     *     @OA\Schema(type="int")
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
    public function getReserveTable(): JsonResponse
    {
        $request = Request::createFromGlobals();
        $reservationRepository = $this->doctrine->getRepository(Reservation::class);

        $maskBearer = 'Bearer ';
        $bearer = $request->headers->get('authorization');
        $bearerToken = str_replace($maskBearer, "", $bearer);

        $projectId = (int)$request->query->get('project');
        $year = (int)$request->query->get('year');
        $month = (int)$request->query->get('month');

        if (strlen((string)$month) === 1) {
            $month = '0' . $month;
        }

        try {
            $dateTo = (new \DateTime('01.' . $month . '.' . $year))->modify('+1 month');
            $dateTo->modify('-1 day');
        } catch (\Exception $e) {
            return new JsonResponse(new BadResponse(false, 'Error dateTime param!'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $parameters = [
            'token' => $bearerToken,
            'filter[date][from]' => '01.' . $month . '.' . $year,
            'filter[date][to]' => $dateTo->format('d.m.Y'),
            'filter[project_id]' => $projectId,
            'limit' => 20000,
        ];

        //$evoAnswer = $this->clientEvo->getTask($parameters);
        $evoAnswer['data'] = $this->mockData->getTaskFilter($month, $year, $projectId);

        $projectFact = $this->resourceController->getFactMonthData($evoAnswer['data']);
        $ReservationsData = $this->resourceController->getPlanMonthData($projectId, $month, $year, $this->doctrine);

        $dataResponse = [
            'success' => true,
            'fact' => $projectFact,
            'plan' => $ReservationsData,
        ];
        return new JsonResponse($dataResponse, JsonResponse::HTTP_OK);
    }

    /**
     * Получение информации таблицы резерва проекта
     *
     * @Route("/reserve/plan", name="getReservePlan", methods={"GET"})
     * @OA\Tag(name="ResourceData")
     *
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *       @OA\JsonContent(
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="fact",
     *                  type="object",
     *                  @OA\Property(
     *                      property="134",
     *                      type="int",
     *                      example="35",
     *                  ),
     *                  @OA\Property(
     *                      property="135",
     *                      type="int",
     *                      example="35",
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="plan",
     *                  type="object",
     *                  @OA\Property(
     *                      property="134",
     *                      type="object",
     *                      @OA\Property(
     *                          property="4",
     *                          type="object",
     *                          @OA\Property(
     *                              property="percent",
     *                              type="int",
     *                              example="49",
     *                          ),
     *                          @OA\Property(
     *                              property="dateReserve",
     *                              type="string",
     *                              example="13.06.2022 21:45:43",
     *                          ),
     *                          @OA\Property(
     *                              property="history",
     *                              type="array",
     *                              @OA\Items(
     *                                  @OA\Property(
     *                                      property="new",
     *                                      type="int",
     *                                      example="20",
     *                                  ),
     *                                  @OA\Property(
     *                                      property="old",
     *                                      type="int",
     *                                      example="80",
     *                                  ),
     *                                  @OA\Property(
     *                                      property="date",
     *                                      type="string",
     *                                      example="25.07.2022 12:00:36",
     *                                  )
     *                              )
     *                          )
     *                      )
     *                  )
     *              )
     *       )
     *
     *   ),
     * @OA\Parameter(
     *     name="project",
     *     in="query",
     *     description="Project id",
     *     @OA\Schema(type="int")
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
    public function getReservePlan(): JsonResponse
    {
        $request = Request::createFromGlobals();
        $approvedPlanRepository = $this->doctrine->getRepository(ApprovedPlan::class);
        $planRepository = $this->doctrine->getRepository(Plan::class);
        $maskBearer = 'Bearer ';
        $bearer = $request->headers->get('authorization');
        $bearerToken = str_replace($maskBearer, "", $bearer);

        $projectId = (int)$request->query->get('project');
        $year = (int)$request->query->get('year');
        $month = (int)$request->query->get('month');

        $plans = $planRepository->findBy(['project' => $projectId]);
        foreach ($plans as $plan) {
            if ($plan->getStatus() == 'time') {

            }
        }


        if (strlen((string)$month) === 1) {
            $month = '0' . $month;
        }

        try {
            $dateTo = (new \DateTime('01.' . $month . '.' . $year))->modify('+1 month');
            $dateTo->modify('-1 day');
        } catch (\Exception $e) {
            return new JsonResponse(new BadResponse(false, 'Error dateTime param!'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $parameters = [
            'token' => $bearerToken,
            'filter[date][from]' => '01.' . $month . '.' . $year,
            'filter[date][to]' => $dateTo->format('d.m.Y'),
            'filter[project_id]' => $projectId,
            'limit' => 20000,
        ];
        $plan = $planRepository->findOneBy(['project' => $projectId]);
        if ($plan->getStatus() == 'time') {

        }
        //$evoAnswer = $this->clientEvo->getTask($parameters);
        $evoAnswer['data'] = $this->mockData->getTaskFilter($month, $year, $projectId);

        $projectFact = $this->resourceController->getFactMonthData($evoAnswer['data']);
        $ReservationsData = $this->resourceController->getPlanMonthData($projectId, $month, $year, $this->doctrine);

        $dataResponse = [
            'success' => true,
            'fact' => $projectFact,
            'plan' => $ReservationsData,
        ];
        return new JsonResponse($dataResponse, JsonResponse::HTTP_OK);
    }

    /**
     * Получение информации таблицы резерва проекта!
     *
     * @Route("/reserve1", name="getReserveTable1", methods={"GET"})
     * @OA\Tag(name="ResourceData")
     *
     * @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\JsonContent(ref=@Model(type=ReserveResponse::class))
     * )
     * @OA\Parameter(
     *     name="project",
     *     in="query",
     *     description="Project id",
     *     @OA\Schema(type="int")
     * ),
     * @OA\Parameter(
     *     name="year",
     *     in="query",
     *     description="Year",
     *     @OA\Schema(type="string", example="[2022,2022]")
     * ),
     * @OA\Parameter(
     *     name="month",
     *     in="query",
     *     description="Month number",
     *     @OA\Schema(type="string", example="[11,12]")
     * )
     * @return JsonResponse
     * @throws \Exception
     */
    public function getReserveTable1(): JsonResponse
    {
        $request = Request::createFromGlobals();
        $reservationRepository = $this->doctrine->getRepository(Reservation::class);

        $maskBearer = 'Bearer ';
        $bearer = $request->headers->get('authorization');
        $bearerToken = str_replace($maskBearer, "", $bearer);

        $projectId = (int)$request->query->get('project');
        $yearArray = $request->query->get('year');
        $monthArray = $request->query->get('month');
        $monthArray = json_decode($monthArray);
        $yearArray = json_decode($yearArray);
        try {
            $arrayMonthReserve = [];
            foreach ($monthArray as $index => $month) {
                $year = $yearArray[$index];

                if (strlen((string)$month) === 1) {
                    $month = '0' . $month;
                }

                $dateTo = (new \DateTime('01.' . $month . '.' . $year))->modify('+1 month');
                $dateTo->modify('-1 day');

                $parameters = [
                    'token' => $bearerToken,
                    'filter[date][from]' => '01.' . $month . '.' . $year,
                    'filter[date][to]' => $dateTo->format('d.m.Y'),
                    'filter[project_id]' => $projectId,
                    'limit' => 20000,
                ];

                $evoAnswer = $this->clientEvo->getTask($parameters);

                if (!empty($evoAnswer['status'])) {
                    return new JsonResponse(new BadResponse(false, $evoAnswer['status']), JsonResponse::HTTP_BAD_REQUEST);
                }

                $reservations = $reservationRepository->findBy([
                    'projectId' => $projectId,
                    'year' => $year,
                    'month' => (int)$month,
                ]);

                $fact = $this->resourceController->getFactMonthData($evoAnswer['data']);
                $plan = $this->resourceController->getArrayPlanDTO($reservations);

                $reserveDTOArray = new ReserveDTO();
                $reserveDTOArray->month = $month;
                $reserveDTOArray->year = $year;
                $reserveDTOArray->fact = $fact;
                $reserveDTOArray->plan = $plan;
                $arrayMonthReserve[] = $reserveDTOArray;
            }
        } catch (\Exception $e) {
            return new JsonResponse(new BadResponse(true, $e->getMessage()), JsonResponse::HTTP_BAD_REQUEST);
        }
        return new JsonResponse(new ReserveResponse(true, $arrayMonthReserve), JsonResponse::HTTP_OK);
    }

    /**
     * Получение информации карточки заполнения брони
     *
     * @Route("/info/reserve", name="getInfoReserve", methods={"GET"})
     * @OA\Tag(name="ResourceData")
     *
     * @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\JsonContent(ref=@Model(type=InfoReserveResponse::class))
     * )
     * @OA\Parameter(
     *     name="manager",
     *     in="query",
     *     description="manager id",
     *     @OA\Schema(type="int")
     * ),
     * @OA\Parameter(
     *     name="project",
     *     in="query",
     *     description="project id",
     *     @OA\Schema(type="int")
     * ),
     * @OA\Parameter(
     *     name="employer",
     *     in="query",
     *     description="employer id",
     *     @OA\Schema(type="int")
     * ),
     * @OA\Parameter(
     *     name="year",
     *     in="query",
     *     description="year",
     *     @OA\Schema(type="int")
     * ),
     * @OA\Parameter(
     *     name="month",
     *     in="query",
     *     description="month",
     *     @OA\Schema(type="int")
     * ),
     * @OA\Parameter(
     *     name="idB24",
     *     in="query",
     *     description="idB24",
     *     @OA\Schema(type="int")
     * ),
     * @OA\Parameter(
     *     name="settingHours",
     *     in="query",
     *     description="settingHours",
     *     @OA\Schema(type="int")
     * )
     * @return JsonResponse
     * @throws \Exception
     */
    public function getInfoSetReserve(): JsonResponse
    {
        //$resourceController = new DataOperationController();
        $request = Request::createFromGlobals();

        $month = $request->query->get('month');
        $year = $request->query->get('year');
        $idB24 = $request->query->get('idB24');
        $employerId = $request->query->get('employer');
        $managerId = $request->query->get('manager');
        $projectId = $request->query->get('project');
        $settingHours = $request->query->get('settingHours');

        $date = new DateTime($year . '-' . $month . '-' . '01');
        $date->modify('+1 month');
        $date->modify('-1 days');

        $workingDays = $this->resourceController->getCountDaysPeriod($year, $month, $year . '-' . $month . '-' . '01', $date->format('Y-m-d'));

        $calendar = $this->resourceController->getCalendar($year . '-' . $month . '-' . '01', $date->format('Y-m-d'), [$idB24]);

        $reservationsData = $this->resourceController->getAllReserveEmployer($employerId, $month, $year, $managerId, $projectId, $this->doctrine);


        $absentDays = 0;
        /*foreach ($calendar[$idB24] as $calendarItem) {
            $absentDays = $this->resourceController->getCountDaysPeriod($year, $month, $calendarItem->dateFrom->format('Y-m-d'), $calendarItem->dateTo->format('Y-m-d'));
        }*/
        //print_r('res');
        $persentAllMonth = $this->resourceController->hoursOnPercent($settingHours * ($workingDays - $absentDays), $workingDays, $settingHours) - $reservationsData['allPercent'];
        $hoursAllMonth = $this->resourceController->percentOnHours($persentAllMonth, $workingDays, $settingHours);

       /* foreach ($calendar[$idB24] as $item) {
            $item->dateFrom = $item->dateFrom->format('d.m.Y');
            $item->dateTo = $item->dateTo->format('d.m.Y');
        }*/
//print_r('res');
        $dataResponse = [
            'success' => true,
            'calendar' => '',
            'absentDays' => $absentDays,
            'freePercent' => $persentAllMonth,
            'freeHours' => $hoursAllMonth,
            'allReservePercent' => $reservationsData['allPercent'] ?? null,
            'managerReservePercent' => $reservationsData['managerReservePercent']
        ];
        return new JsonResponse($dataResponse,JsonResponse::HTTP_OK);
    }

    /**
     * Получение рабочих дней
     *
     * @Route("/workday", name="workday", methods={"GET"})
     * @OA\Tag(name="ResourceData")
     *
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *       @OA\JsonContent(
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="workingDays",
     *                  type="string"
     *              ),
     *       )
     *
     *   ),
     * @OA\Parameter(
     *     name="month",
     *     in="query",
     *     description="month",
     *     @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     *     name="year",
     *     in="query",
     *     description="year",
     *     @OA\Schema(type="string")
     * )
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function getWorkDate(Request $request): JsonResponse
    {
        $month = $request->query->get('month');
        $year = $request->query->get('year');

        if ((int)$month < 10) {
            $month = '0' . $month;
        }

        $dateFrom = new DateTime($year . $month . '01');
        $dateTo = (new DateTime($year . $month . '01'))->modify('+1 month')->modify('-1 days');

        $workingDays = $this->resourceController->getCountDaysPeriod($year, $month, $dateFrom->format('Y-m-d'), $dateTo->format('Y-m-d'));

        $dataResponse = [
            'success' => true,
            'workingDays' => $workingDays,
        ];
        return new JsonResponse($dataResponse, JsonResponse::HTTP_OK);
    }

    /**
     * Получение задач!
     *
     * @Route("/task", name="task", methods={"GET"})
     * @OA\Tag(name="ResourceData")
     *
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *       @OA\JsonContent(
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="workingDays",
     *                  type="string"
     *              ),
     *       )
     *
     *   ),
     * @OA\Parameter(
     *     name="month",
     *     in="query",
     *     description="month",
     *     @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     *     name="year",
     *     in="query",
     *     description="year",
     *     @OA\Schema(type="string")
     * )
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function getTask(Request $request): JsonResponse
    {
        $maskBearer = 'Bearer ';
        $bearer = $request->headers->get('authorization');
        $bearerToken = str_replace($maskBearer, "", $bearer);

        $project= $request->query->get('project');
        $task = $request->query->get('task');
        $filters = $request->query->get('filter');

        $parameters = [
            'token' => $bearerToken,
            'filter[date][from]' => $filters['dateFrom'],
            'filter[date][to]' => $filters['dateTo'],
            'filter[project_id]' => $project,
            'filter[task_id]' => $task,
            'limit' => 20000,
        ];
        $evoAnswerTask = $this->clientEvo->evoApiRequest('GET', 'task', $parameters);
        $evoAnswerTask = json_decode($evoAnswerTask, true);

        if (empty($evoAnswerTask['data'])) {
            return new JsonResponse(new BadResponse(false, $evoAnswerTask['status']), JsonResponse::HTTP_BAD_REQUEST);
        }
        $dataResponse = [
            'success' => true,
            'workingDays' => '$workingDays',
        ];
        return new JsonResponse($dataResponse, JsonResponse::HTTP_OK);
    }

    public function getCalendarEmployerId($calendar)
    {
        $calendarDTOArray = [];
        foreach ($calendar as $item) {
            $calendarDTO = new CalendarDTO();
            $calendarDTO->id = $item->id;
            $calendarDTO->dateFrom = $item->dateFrom->format('d.m.Y');
            $calendarDTO->dateTo = $item->dateTo->format('d.m.Y');
            $calendarDTO->accessibilty = $item->accessibilty;
            $calendarDTOArray[] = $calendarDTO;
        }
        return $calendarDTOArray;
    }
}
