<?php

namespace App\Controller;

use App\Entity\Manager;
use App\Entity\Reservation;
use App\Entity\ReservationChange;
use App\Model\EvoDTO\HistoryDTO;
use App\Model\Response\SetReserveResponse;
use App\Model\Response\SuccessResponse;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Annotations as OA;
use App\Model\Request\Evo\ReserveRequest;
use App\Model\Response\BadResponse;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * @Route("/api/doc")
 */
class ReserveController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(
        ManagerRegistry $doctrine
    ) {
        $this->doctrine = $doctrine;
    }

    /**
     * Добавление/редактирование брони
     *
     * @Route("/reserve", name="reserve-create", methods={"POST"})
     * @OA\Tag(name="Reserve")
     *
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(ref=@Model(type=SetReserveResponse::class))
     * )
     * @OA\RequestBody(
     *     required=true,
     *     description="Данные пользователя",
     *     @OA\JsonContent(ref=@Model(type=ReserveRequest::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Ошибка выполнения",
     *     @OA\JsonContent(ref=@Model(type=BadResponse::class))
     * )
     */
    public function setReserve(Request $request): JsonResponse
    {
        $serializer = SerializerBuilder::create()->build();
        $entityManager = $this->doctrine->getManager();
        $reservationRepository = $this->doctrine->getRepository(Reservation::class);
        $managerRepository = $this->doctrine->getRepository(Manager::class);

        $reserve = $serializer->deserialize($request->getContent(), ReserveRequest::class, 'json');

        $managers = $managerRepository->findOneBy(['id' => $reserve->manager]);
        if (empty($managers)) {
            return new JsonResponse(new BadResponse(false, 'Manager not exist!'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $reservation = $reservationRepository->findOneBy([
            'manager' => $reserve->manager,
            'projectId' => $reserve->project,
            'employerId' => $reserve->employer,
            'year' => $reserve->year,
            'month' => $reserve->month,
        ]);

        $reservationChange = new ReservationChange();

        if (empty($reservation)) {
            $reservation = new Reservation();
            $reservation->setManager($managers);
            $reservation->setProjectId($reserve->project);
            $reservation->setEmployerId($reserve->employer);
            $reservation->setYear($reserve->year);
            $reservation->setMonth($reserve->month);
            $reservationChange->setOldValue(0);
        } elseif ($reservation->getPercent() === $reserve->percent) {
            return new JsonResponse(new BadResponse(false, 'same_percent'), JsonResponse::HTTP_BAD_REQUEST);
        } else {
            $reservationChange->setOldValue($reservation->getPercent());
        }

        $reservation->setDateReservation(new \DateTime());
        $reservation->setPercent($reserve->percent);

        $entityManager->persist($reservation);
        $entityManager->flush();

        $reservationChange->setNewValue($reservation->getPercent());
        $reservationChange->setChangeTime(new \DateTime());
        $reservationChange->setEntry($reservation);

        $entityManager->persist($reservationChange);
        $entityManager->flush();

        $reservation->addReservationChange($reservationChange);

        $history = [];
        foreach ($reservation->getReservationChanges() as $reservationChange) {
            $historyDTO = new HistoryDTO();
            $historyDTO->new = $reservationChange->getNewValue();
            $historyDTO->old = $reservationChange->getOldValue();
            $historyDTO->date = $reservationChange->getChangeTime()->format('d.m.Y H:i:s');
            $history[] = $historyDTO;
        }
        $history = array_reverse($history);

        return new JsonResponse(new SetReserveResponse(true, $reservation->getPercent(), $history), JsonResponse::HTTP_OK);
    }

    /**
     * Удаление брони
     *
     * @Route("/reserve/delete", name="reserve-delete", methods={"DELETE"})
     * @OA\Tag(name="Reserve")
     *
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *        @OA\JsonContent(ref=@Model(type=SuccessResponse::class))
     * )
     * @OA\RequestBody(
     *          required=true,
     *          description="Данные пользователя",
     *          @OA\JsonContent(ref=@Model(type=ReserveRequest::class))
     * )
     * @OA\Response(
     *     response=400,
     *     description="Ошибка выполнения",
     *     @OA\JsonContent(ref=@Model(type=BadResponse::class))
     * )
     */
    public function deleteReserve(Request $request): JsonResponse
    {
        $serializer = SerializerBuilder::create()->build();
        $entityManager = $this->doctrine->getManager();
        $reservationRepository = $this->doctrine->getRepository(Reservation::class);
        $managerRepository = $this->doctrine->getRepository(Manager::class);
        $reserve = $serializer->deserialize($request->getContent(), ReserveRequest::class, 'json');

        $managers = $managerRepository->findOneBy(['id' => $reserve->manager]);
        if (empty($managers)) {
            return new JsonResponse(new BadResponse(false, 'Manager not exist!'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $reservation = $reservationRepository->findOneBy([
            'manager' => $reserve->manager,
            'projectId' => $reserve->project,
            'employerId' => $reserve->employer,
            'year' => $reserve->year,
            'month' => $reserve->month,
        ]);

        if (empty($reservation)) {
            return new JsonResponse(new BadResponse(false, 'Reserve data not exist!'), JsonResponse::HTTP_BAD_REQUEST);
        }

        foreach ($reservation->getReservationChanges() as $reservationChangeItem) {
            $entityManager->remove($reservationChangeItem);
            $entityManager->flush();
        }

        $entityManager->remove($reservation);
        $entityManager->flush();

        return new JsonResponse(new SuccessResponse(), JsonResponse::HTTP_OK);
    }
}
