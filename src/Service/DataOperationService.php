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
use Vasyag07\Bitrix24Client\Client;

class DataOperationService extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function setDoctrine($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFactEmployerMonth($evoAnswer)
    {
        $projectsHours = [];
        $factDTOArray = [];

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
            $factDTO = new FactDTO();
            $factDTO->id = $projectId;
            $factDTO->hour = round($hours, 2);
            $factDTOArray[] = $factDTO;
        }
        return $factDTOArray;
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
            $planDTOArray[] = $planDTO;
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
        $factDTOArray = [];
        $uniqEmployers = array_unique_key($dataQuery, 'employer_id');
        foreach ($uniqEmployers as $employer) {
            $hoursEmployer = 0;
            $factDTO = new FactDTO();
            $factDTO->id = $employer;
            foreach ($dataQuery as $taskEmployer) {
                if ($employer == $taskEmployer['employer_id']) {
                    $hoursEmployer += $taskEmployer['time'];
                }
            }
            $factDTO->hour = $hoursEmployer;
            $factDTOArray[] = $factDTO;
        }
        return $factDTOArray;
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
