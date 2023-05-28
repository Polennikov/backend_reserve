<?php

namespace App\Service;

use App\Entity\Report;
use App\Entity\Spending;
use App\Entity\Task;
use App\Model\RmDTO\ReportDTO;
use App\Model\RmDTO\SpendingDTO;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

class ReportService
{
    private LoggerInterface  $logger;

    private ManagerRegistry $doctrine;

    private ClientEvo $clientEvo;

    public function __construct(
        ManagerRegistry $doctrine,
        LoggerInterface  $redmineApiLogger,
        ClientEvo $clientEvo
    ) {
        $this->doctrine = $doctrine;
        $this->logger = $redmineApiLogger;
        $this->clientEvo = $clientEvo;
    }

    /* *
     * @return Report
     */
    /*public function createReport(ReportFilterService $reportFilterService, User $user, Register $register): Report
    {
        $this->fromDateFilter = $reportFilterService->getFromDateFilter();
        $this->toDateFilter = $reportFilterService->getToDateFilter();
        $timesEntriesByIssue = $reportFilterService->getTimeFromRedmine();
        $tasksFromEvoByIssue = $reportFilterService->getTimeFromEvo();

        // сортируем задачи по фильтру указанным пользователем
        $reportFilterService->sortIssues();

        $this->report = new Report();
        $this->report->setFromDate($this->fromDateFilter);
        $this->report->setToDate($this->toDateFilter);

        $statuses = [];
        foreach ($reportFilterService->getIssuesFromRedmine() as $issueFromRedmine) {
            $timesEntries = [];
            $tasksFromEvo = [];
            $issueId = $issueFromRedmine->getId();
            if (isset($timesEntriesByIssue[$issueId])) {
                $timesEntries = $timesEntriesByIssue[$issueId];
            }
            if (isset($tasksFromEvoByIssue[$issueId])) {
                $tasksFromEvo = $tasksFromEvoByIssue[$issueId];
            }
            $task = $this->getTask($issueFromRedmine, $timesEntries, $tasksFromEvo);
            $status = $task->getStatus();
            // чтобы избавить от одинаковых статусов, записываем в ключ
            $statuses[$status] = $status;

            $this->report->addTask($task);
        }
        // удаляем задачи без времени если для этого включен фильтр
        $reportFilterService->removeTasksWithoutTime($this->report);

        $this->report->setStatuses($this->getStringStatusesFromArray($statuses));
        $this->report->setCreatedAt(new \DateTime());
        $this->report->setProject(Project::createProject($reportFilterService->getProjectFromEvo(), $reportFilterService->getProjectFromRedmine()));
        $this->report->setUser($user);
        $this->report->setRegister($register);

        $this->report->setFilterSetting(Report::DISPLAY_TOTAL_TIME_CODE, $reportFilterService->getDisplayTotalTime());
        $this->report->setFilterSetting(Report::DISPLAY_TOTAL_TIME_FOR_TASK_WITHOUT_REDMINE_CODE, $reportFilterService->getDisplayTotalTimeForTaskWithoutRedmine());

        return $this->report;
    }*/

   /* private function getStringStatusesFromArray(array $statuses): string
    {
        sort($statuses);
        // Удаляем статусы со значением null
        $keyFromNull = array_search(null, $statuses);
        if (false !== $keyFromNull) {
            unset($statuses[$keyFromNull]);
        }

        return implode('; ', $statuses);
    }*/

    /**
     * @param $token
     * @param $manager
     * @param $projectRm
     * @param $filters
     * @return array
     * @throws \Redmine\Exception\ClientException
     */
    public function getTask($token, $manager, $projectRm, $filters): array
    {
        $clientRm = new ClientRm($manager->getTokenRm());
        $clientRm->setLogger($this->logger);

        $reportRepository = $this->doctrine->getRepository(Report::class);
        $taskRepository = $this->doctrine->getRepository(Task::class);
        $entityManager = $this->doctrine->getManager();

        $filterIssueForRedmine['project_id'] = $projectRm;

        if (isset($filters->dateFrom) && isset($filters->dateTo)) {
            $date = '><' . (new \DateTime($filters->dateFrom))->format('Y-m-d') . '|' . (new \DateTime($filters->dateTo))->format('Y-m-d');
            $filterIssueForRedmine['updated_on'] = $date;
            $filterIssueForRedmine = array_merge($filterIssueForRedmine, [ 'updated_on' => $date]);
        }
        if (isset($filters->statuses)) {
            $filterIssueForRedmine = array_merge($filterIssueForRedmine, [ 'statuses_id' => $filters->statuses]);
        }
        if (isset($filters->monthPaid)) {
            $filterIssueForRedmine = array_merge($filterIssueForRedmine, [ 'cf_25' => $filters->monthPaid]);
        }

        $taskList = $clientRm->getIssuesWithArrayStatusAndWithoutLimit($filterIssueForRedmine);

        $report = $reportRepository->findOneBy([
            'manager' => $manager->getId(),
            'projectRm' => $projectRm,
        ]);
        if (!empty($report)) {
            $entityManager->remove($report);
            $entityManager->flush();
        }
        $report = new Report();
        $report->setManager($manager);
        $report->setProjectRm($projectRm);
        $report->setProjectEvo($this->getEvoProject($token, $projectRm, $clientRm));
        $report->setToDate(new \DateTime($filters->dateTo));
        $report->setFromDate(new \DateTime($filters->dateFrom));
        $dateTo = new \DateTime($filters->dateTo);
        $dateFrom = new \DateTime($filters->dateFrom);

        unset($filters->dateTo);
        unset($filters->dateFrom);

        $arrayIdIssues = $this->getArrayIdIssues($taskList);
        $filters = get_object_vars($filters);
        $filters['issues'] = $arrayIdIssues;

        $report->setFilterSettings($filters);
        $report->setCreatedAt(new \DateTime());
        $entityManager->persist($report);

        $taskDTOArray = [];
        foreach ($taskList as $taskItem) {
            $task = $taskRepository->findOneBy([
                'report' => $report->getId(),
                'taskId' => $taskItem['id']
            ]);
            if (empty($task)) {
                $task = new Task();
            }
            $task->setTitle($taskItem['subject']);
            $task->setRedmineEstimate($taskItem['estimated_hours']);
            $task->setRedmineId($taskItem['project']['id']);
            $task->setTaskId($taskItem['id']);
            $task->setReport($report);
            $task->setStatus($taskItem['status']['id']);
            $task->setToDate($dateTo);
            $task->setFromDate($dateFrom);
            $entityManager->persist($task);
            $taskDTOArray[] = $taskItem;
        }
        return $taskDTOArray;
    }

    public function getTaskTime($token, $manager, $projectRm, $report, $evoAnswerEmployer): bool
    {
        $entityManager = $this->doctrine->getManager();
        $taskRepository = $this->doctrine->getRepository(Task::class);
        $spendingRepository = $this->doctrine->getRepository(Spending::class);
        $clientRm = new ClientRm($manager->getTokenRm());
        $clientRm->setLogger($this->logger);

        $dateFrom = $report->getFromDate();
        $dateTo = $report->getToDate();

        $filterIssueForRedmine = [
            'project_id' => $projectRm,
            'from' => $dateFrom->format('Y-m-d'),
            'to' => $dateTo->format('Y-m-d'),
        ];
        $taskRm = $clientRm->getTimeEntriesWithoutLimit($filterIssueForRedmine);

        $parametersGetTaskEvo = [
            'token' => $token,
            'filter[date][from]' =>  $dateFrom->format('d.m.Y'),
            'filter[date][to]' =>  $dateTo->format('d.m.Y'),
        ];
        if ($report->getProjectRm() != $report->getProjectEvo()) {
            $parametersGetTaskEvo['filter[project_id]'] = $report->getProjectEvo();
        }
        $evoTaskAnswer = $this->clientEvo->getTask($parametersGetTaskEvo);

        $dateFrom = new Carbon($report->getFromDate());
        $dateTo = new Carbon($report->getToDate());
        $dateInterval = $this->getMonthListFromDate($dateFrom, $dateTo);


        $taskEmployers = [];
        foreach ($report->getFilterSettings()['issues'] as $issue) {
            foreach ($taskRm as $time) {
                if ((string)$time['issue']['id'] == (string)$issue) {
                    $employerIdEvo = $this->getIdEmployerEvo($time['user']['name'], $evoAnswerEmployer);
                    $taskEmployers[$employerIdEvo] = $time['user']['id'];
                }
            }
        }
        foreach ($report->getFilterSettings()['issues'] as $issue) {
            foreach ($dateInterval as $dateIntervalItem) {
                foreach ($taskEmployers as $keyTaskEmployer => $taskEmployer){
                    $timeTaskRm = 0;
                    foreach ($taskRm as $timeRm) {
                        $dateTime = new \DateTime($timeRm['spent_on']);
                        if ($timeRm['issue']['id'] == $issue && $timeRm['user']['id'] == $taskEmployer
                            && $dateTime->format('m') == $dateIntervalItem['month']
                            && $dateTime->format('Y') == $dateIntervalItem['year'])
                        {
                            $timeTaskRm += $timeRm['hours'];
                        }
                    }
                    $timeTaskEvo = 0;
                    foreach ($evoTaskAnswer['data'] as $timeEvo) {
                        $dateTime = new \DateTime($timeEvo['date_update']);
                        if ($timeEvo['employer_id'] == $keyTaskEmployer && $timeEvo['task_id'] == $issue
                            && $dateTime->format('m') == $dateIntervalItem['month']
                            && $dateTime->format('Y') == $dateIntervalItem['year'])
                        {
                            $timeTaskEvo += (float)$timeEvo['time'];
                        }
                    }
                    if ($timeTaskEvo != 0 || $timeTaskRm != 0) {
                        $issueTask = $taskRepository->findOneBy([
                            'taskId' => $issue,
                            'report' => $report->getId(),
                        ]);
                        $spending = $spendingRepository->findOneBy([
                            'task' => $issueTask->getId()
                        ]);
                        if (empty($spending)) {
                            $spending = new Spending();
                        }

                        $spending->setMonth($dateIntervalItem['month']);
                        $spending->setYear($dateIntervalItem['year']);
                        $spending->setEmployerId($taskEmployer);
                        $spending->setTask($issueTask);
                        $spending->setTimeRedmine($timeTaskRm);
                        $spending->setTimeEvo($timeTaskEvo);
                        $entityManager->persist($spending);
                    }
                }
            }
        }
        $entityManager->flush();

        return true;
    }

    public function getReportData($arrayTask, $report): array {
        $dateFrom = new Carbon($report->getFromDate());
        $dateTo = new Carbon($report->getToDate());
        $dateInterval = $this->getMonthListFromDate($dateFrom, $dateTo);
        $reportArrayDTO = [];
        foreach ($arrayTask as $task) {
            $reportDTO = new ReportDTO();
            $reportDTO->idTask = $task->getTaskId();
            $reportDTO->redmineEstimate = $task->getRedmineEstimate();
            $reportDTO->dateFrom = $task->getFromDate()->format('Y-m-d');
            $reportDTO->dateTo = $task->getToDate()->format('Y-m-d');

            $resultSpending['hoursAllRm'] = 0;
            $resultSpending['hoursAllEvo'] = 0;
            $resultSpending['employerTaskArrayDTO'] = [];
            foreach ($task->getSpendings() as $spending) {
                $spendingDTO = new SpendingDTO();
                $spendingDTO->year = $spending->getYear();
                $spendingDTO->month = $spending->getMonth();
                $spendingDTO->hoursRm = $spending->getTimeRedmine();
                $spendingDTO->hoursEvo = $spending->getTimeEvo();
                $spendingDTO->employerId = $spending->getEmployerId();
                $resultSpending['hoursAllRm'] += $spendingDTO->hoursRm;
                $resultSpending['hoursAllEvo'] += $spendingDTO->hoursEvo;
                $resultSpending['employerTaskArrayDTO'][] = $spendingDTO;
            }
            $reportDTO->hoursAllRm = $resultSpending['hoursAllRm'];
            $reportDTO->hoursAllEvo = $resultSpending['hoursAllEvo'];
            $reportDTO->employerTask = $resultSpending['employerTaskArrayDTO'];
            $reportArrayDTO [] = $reportDTO;
        }
        return [
          'report' => $reportArrayDTO,
          'date' => $dateInterval,
        ];
    }

    public function getProject($manager): array
    {
        $clientRm = new ClientRm($manager->getTokenRm());
        $clientRm->setLogger($this->logger);
        return $clientRm->getProjects();
    }

    public function getStatuses($manager): array
    {
        $clientRm = new ClientRm($manager->getTokenRm());
        $clientRm->setLogger($this->logger);
        return $clientRm->getIssueStatuses();
    }

    private function getMonthListFromDate(Carbon $start, Carbon $end)
    {
        foreach (CarbonPeriod::create($start, '1 month', $end) as $month) {
            $months[$month->format('m-Y')] = $month->format('F Y');
        }
        $dateIntervalArray = [];
        foreach ($months as $key => $monthItem) {
            $myArray = explode('-', $key);
            $dateInterval = [
                'month' => $myArray[0],
                'year' => $myArray[1]
            ];
            $dateIntervalArray[] = $dateInterval;
        }
        return $dateIntervalArray;
    }

    private function getIdEmployerEvo(string $employerName, $evoAnswerEmployer)
    {
        foreach ($evoAnswerEmployer['data'] as $data) {
            if (isset($data['title'])) {
                $employerNameRm = $this->handleNameEmployee($employerName);
                $employerNameEvo = $this->handleNameEmployee($data['title']);
                if (stristr($employerNameEvo, $employerNameRm) || stristr($employerNameRm, $employerNameEvo)) {
                    return $data['id'];
                }
            }
        }
    }

    private function handleNameEmployee(string $name): string
    {
        // замена букв требуется чтобы из разных систем символы возвращались одинаковые
        return strtoupper(str_replace(['ё', 'й'], ['е', 'и'], $name));
    }

    private function getEvoProject($token, $projectRm, $clientRm)
    {
        $parameters = [
            'token' => $token
        ];
        $evoAnswer = $this->clientEvo->getProject($parameters);
        $rmAnswer = $clientRm->getProjects();
        foreach ($rmAnswer as $itemRmAnswer) {
            if ($itemRmAnswer['id'] == $projectRm) {
                foreach ($evoAnswer['data'] as $itemEvoAnswer) {
                    if (stristr($itemRmAnswer['name'], $itemEvoAnswer['title']) || stristr($itemEvoAnswer['title'], $itemRmAnswer['name'])) {
                        return $itemEvoAnswer['id'];
                    }
                }
            }
        }
        return $projectRm;
    }

    public function getArrayIdIssues($dataIssues)
    {
        $arrayIdIssue = [];
        foreach ($dataIssues as $itemIssues) {
            $arrayIdIssue[] = $itemIssues['id'];
        }
        return $arrayIdIssue;
    }
}