<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\ReservationChange;
use App\Entity\SettingEmployer;
use App\Entity\SettingProject;
use App\Model\EvoDTO\FactDTO;
use App\Model\EvoDTO\HistoryDTO;
use App\Model\EvoDTO\PlanDTO;
use App\Model\SettingEmployerDTO;
use App\Model\SettingProjectDTO;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Tochka\Calendar\WorkCalendar;

class DataOperationService extends AbstractController
{
    private $doctrine;

    public function setDoctrine($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFactEmployerMonth($evoAnswer)
    {
        $projectsHours = [];
        $projectFact = [];

        if (!empty($evoAnswer) && is_array($evoAnswer['data'])) {
            foreach ($evoAnswer['data'] as $task) {
                if (empty($projectsHours[(int)$task['project_id']])) {
                    $projectsHours[(int)$task['project_id']] = (float)$task['time'];
                } else {
                    $projectsHours[(int)$task['project_id']] += (float)$task['time'];
                }
            }
        }
        foreach ($projectsHours as $projectId => $hours) {
            $projectFact[$projectId] = [
                'hours' => round($hours, 2),
            ];
        }
        return $projectFact;
    }

    public function getPlanEmployerMonth($reservations, $reservationChangeRepository)
    {
        $arReservations = [];
        foreach ($reservations as $reservation) {
            $changes = $reservationChangeRepository->findBy([
                'entry' => $reservation,
            ]);
            $history = [];
            if (!empty($changes) && is_array($changes)) {
                foreach ($changes as $reservationChange) {
                    $history[] = [
                        'new' => $reservationChange->getNewValue(),
                        'old' => $reservationChange->getOldValue(),
                        'date' => $reservationChange->getChangeTime()->format('d.m.Y H:i:s'),
                    ];
                }
            }
            $history = array_reverse($history);
            if (empty($arReservations[$reservation->getProjectId()])) {
                $arReservations[$reservation->getProjectId()] = [];
            }
            $arReservations[$reservation->getProjectId()][$reservation->getManager()->getId()] = [
                'percent' => $reservation->getPercent(),
                'history' => $history,
            ];
            if (empty($arReservations[$reservation->getProjectId()]['allPercent'])) {
                $arReservations[$reservation->getProjectId()]['allPercent'] = 0;
            }

            $arReservations[$reservation->getProjectId()]['allPercent'] += $reservation->getPercent();
        }
        return $arReservations;
    }

    public function getCalendar($from, $to, array $user)
    {

        return [];
    }

    public function getArrayPlanDTO($reservations)
    {
        $reservationChangeRepository = $this->doctrine->getRepository(ReservationChange::class);
        $planDTOArray = [];

        foreach ($reservations as $reservation) {
            $planDTO = new PlanDTO();
            $historyDTOArray = [];
            $changes = $reservationChangeRepository->findBy([
                'entry' => $reservation->getId(),
            ]);
            if (!empty($changes) && is_array($changes)) {
                foreach ($changes as $reservationChange) {
                    $historyDTO = new HistoryDTO();
                    $historyDTO->new = $reservationChange->getNewValue();
                    $historyDTO->old = $reservationChange->getOldValue();
                    $historyDTO->date = $reservationChange->getChangeTime()->format('d.m.Y H:i:s');
                    $historyDTOArray[] = $historyDTO;
                }
            }
            $historyDTOArray = array_reverse($historyDTOArray);

            $planDTO->percent = $reservation->getPercent();
            $planDTO->manager = $reservation->getManager()->getId();
            $planDTO->employer = $reservation->getEmployerId();
            $planDTO->project = $reservation->getProjectId();
            $planDTO->dateReserve = $reservation->getDateReservation()->format('d.m.Y H:i:s');
            $planDTO->history = $historyDTOArray;
            $planDTOArray[$reservation->getProjectId()] = $planDTO;
        }
        return $planDTOArray;
    }

    public function getAllReserveEmployer($employerId, $month, $year, $managerId, $projectId, $doctrine)
    {
        $reservationRepository = $doctrine->getRepository(Reservation::class);
        $reservations = $reservationRepository->findBy([
            'employerId' => $employerId,
            'month' => $month,
            'year' => $year,
        ]);
        $ReservePercentAll = [
            'allPercent' => 0,
            'managerReservePercent' => 0,
        ];
        foreach ($reservations as $reservation) {
            if ($reservation->getDateReservation() != null) {
                if ($reservation->getManager()->getId() == $managerId && $reservation->getProjectId() == $projectId) {
                    $ReservePercentAll['managerReservePercent'] += $reservation->getPercent();
                }
            }
            $ReservePercentAll['allPercent'] += $reservation->getPercent();
        }

        return $ReservePercentAll;
    }

    public function getFactMonthData($dataQuery)
    {
        $mass = [];
        $uniqEmployers = array_unique_key($dataQuery, 'employer_id');
        foreach ($uniqEmployers as $employer) {
            $mass[$employer] = [];
            $hoursEmployer = 0;
            foreach ($dataQuery as $taskEmployer) {
                if ($employer == $taskEmployer['employer_id']) {
                    $hoursEmployer += $taskEmployer['time'];
                }
            }
            $mass[$employer] = $hoursEmployer;
        }
        return $mass;
    }

    public function getPlanMonthData($projectId, $month, $year, $doctrine)
    {
        $reservationChangeRepository = $doctrine->getRepository(ReservationChange::class);
        $reservationRepository = $doctrine->getRepository(Reservation::class);

        $Reservations = [];
        $reservations = $reservationRepository->findBy([
            'projectId' => $projectId,
            'year' => $year,
            'month' => (int)$month,
        ]);
        foreach ($reservations as $reservation) {
            $history = [];
            $changes = $reservationChangeRepository->findBy([
                'entry' => $reservation->getId(),
            ]);
            if (!empty($changes) && is_array($changes)) {
                foreach ($changes as $reservationChange) {
                    $history[] = [
                        'new' => $reservationChange->getNewValue(),
                        'old' => $reservationChange->getOldValue(),
                        'date' => $reservationChange->getChangeTime()->format('d.m.Y H:i:s'),
                    ];
                }
            }
            $history = array_reverse($history);
            if (empty($Reservations[$reservation->getEmployerId()])) {
                $Reservations[$reservation->getEmployerId()] = [];
            }
            $Reservations[$reservation->getEmployerId()][$reservation->getManager()->getId()] = [
                'percent' => $reservation->getPercent(),
                'dateReserve' => $reservation->getDateReservation(),
                'history' => $history,
            ];
        }
        return $Reservations;
    }

    function getEmployerSetting($doctrine, $id)
    {
        $entityManager = $doctrine->getManager();
        $settingEmployerManager = $entityManager->getRepository(SettingEmployer::class);
        $settingEmployer = $settingEmployerManager->findOneBy([
            'employerId' => $id,
        ]);
        if (empty($settingEmployer)) {
            return null;
        }
        $settingEmployerDTO = new SettingEmployerDTO();
        $settingEmployerDTO->hoursWork = $settingEmployer->getHoursWork();

        return $settingEmployerDTO;
    }

    function getProjectLidId($doctrine, $id)
    {
        $entityManager = $doctrine->getManager();
        $settingProjectManager = $entityManager->getRepository(SettingProject::class);
        $settingProject = $settingProjectManager->findBy([
            'lidId' => $id,
        ]);
        $arrayResult = [];
        foreach ($settingProject as $settingProjectItem) {
            $settingProjectDTO = new SettingProjectDTO();
            $settingProjectDTO->id = $settingProjectItem->getId();
            $settingProjectDTO->projectId = $settingProjectItem->getProjectId();
            $settingProjectDTO->lidId = $settingProjectItem->getLidId();
            $arrayResult [] = $settingProjectDTO;
        }
        return $arrayResult;
    }

    public function getCountDaysPeriod($year, $month, string $dateStart, string $dateEnd)
    {
        $dateCurrent = new DateTime($year . '-' . $month . '-' . '01');
        $dateStart = new DateTime($dateStart);
        $dateEnd = new DateTime($dateEnd);
        if ($dateStart < $dateCurrent) {
            $dateStart = $dateCurrent;
        }
        if ($dateEnd >= $dateCurrent->modify('+1 month')) {
            $dateEnd = $dateCurrent->modify('-1 days');
        }
        $workingDays = 0;
        while ($dateEnd >= $dateStart) {
            $date = WorkCalendar::create($dateStart->format('Y'), $dateStart->format('m'), $dateStart->format('d'));
            $workingDays += $date->isWorkday();
            $dateStart->modify('+1 days');
        }

        return $workingDays;
    }

    public function percentOnHours($percent, $countDays, $workHourDay) {
        $workHoursFull = $workHourDay * $countDays;
        if ($percent > 0) {
            return (($workHoursFull * $percent) / 100);
        }
        return 0;
    }

    public function hoursOnPercent($hours, $countDays, $workHourDay) {
        $workHoursFull = $workHourDay * $countDays;
        if ($hours > 0) {
            return ((100 * $hours) / $workHoursFull);
        }
        return 0;
    }
}

function array_unique_key($array, $key)
{
    $tmp = $key_array = array();
    $i = 0;
    foreach ($array as $val) {
        if (!in_array($val[$key], $key_array)) {
            $key_array[$i] = $val[$key];
            $tmp[$i] = $val['employer_id'];
        }
        $i++;
    }
    return $tmp;
}
