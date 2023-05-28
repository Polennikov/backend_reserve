<?php

namespace App\Service;

use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializerBuilder;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Redmine\Exception\ClientException as RedmineException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ClientRm
{
    use LoggerAwareTrait;

    /**
     * @var \Redmine\Client\NativeCurlClient
     */
    private $client;

    /**
     * @var SerializerInterface
     */
    private $jmsSerializer;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(string $apikeyOrUsername, string $password = null)
    {
        $this->client = new \Redmine\Client\NativeCurlClient($_ENV['RM_URL'], $apikeyOrUsername, $password);
        $this->jmsSerializer = SerializerBuilder::create()->build();
    }

    /**
     * @throws RedmineException
     */
    public function getCurrentUser()
    {
        $response = $this->requestHandler('user','getCurrentUser');

        return $response['user'];
    }

    /**
     * @return ProjectFromRedmine[]
     * @throws RedmineException
     */
    public function getProjectsWithoutLimit(array $filter = []): array
    {
        $limit = 100;
        $offset = 0;
        $projects = [];
        while (true) {
            $response = $this->requestHandler('project','all', array_merge(
                $filter, ['limit' => $limit, 'offset' => $offset]
            ));

            $projects = array_merge($projects, $response['projects']);
            $totalCount = $response['total_count'];
            $offset += $limit;
            if ($offset >= $totalCount) {
                break;
            }
        }

        return $this->jmsSerializer->fromArray(
            $projects,
            'array<App\Dto\Redmine\ProjectFromRedmine>'
        );
    }

    /**
     * @return ProjectFromRedmine[]
     * @throws RedmineException
     */
    public function getProjects(array $filter = [], int $limit = 100, int $offset = 0): array
    {
        $response = $this->requestHandler('project','all', array_merge(
            $filter, ['limit' => $limit, 'offset' => $offset]
        ));

        return $response['projects'];
    }

    /**
     * @throws RedmineException
     */
    public function getIssueStatuses(array $filter = [], int $limit = 100, int $offset = 0): array
    {
        $response = $this->requestHandler('issue_status','all', array_merge(
            $filter, ['limit' => $limit, 'offset' => $offset]
        ));
        return $response['issue_statuses'];
    }

    /**
     * @return IssueFromRedmine[]
     * @throws RedmineException
     */
    public function getIssues(array $filter = [], int $limit = 100, int $offset = 0): array
    {
        $response = $this->requestHandler('issue','all', array_merge(
            $filter, ['limit' => $limit, 'offset' => $offset]
        ));
        return $response['issues'];
    }

    /**
     * @return IssueFromRedmine[]
     * @throws RedmineException
     */
    public function getIssuesWithoutLimit(array $filter = []): array
    {
        $limit = 100;
        $offset = 0;
        $issues = [];
        while (true) {
            $response = $this->requestHandler('issue','all', array_merge(
                $filter, ['limit' => $limit, 'offset' => $offset]
            ));

            $issues = array_merge($issues, $response['issues']);
            $totalCount = $response['total_count'];
            $offset += $limit;
            if ($offset >= $totalCount) {
                break;
            }
        }

        return $issues;
    }

    /**
     * @return IssueFromRedmine[]
     * @throws RedmineException
     */
    public function getIssuesWithArrayStatusAndWithoutLimit(array $filter): array
    {
        $allIssues = [];
       if (!empty($filter['statuses_id'])) {
           foreach ($filter['statuses_id'] as $statusId) {
               $newFilter = $filter;
               unset($newFilter['statuses_id']);
               $newFilter['status_id'] = $statusId;
               $issuesResponse = $this->getIssuesWithoutLimit($newFilter);
               $allIssues = array_merge($allIssues, $issuesResponse);
           }
       } else {
           $issuesResponse = $this->getIssuesWithoutLimit($filter);
           $allIssues = $issuesResponse;
       }

        return $allIssues;
    }

    /**
     * @return TimeEntriesFromRedmine[]
     * @throws RedmineException
     */
    public function getTimeEntriesWithoutLimit(array $filter)
    {
        $limit = 1000;
        $offset = 0;
        $timeEntries = [];
        while (true) {
            $response = $this->requestHandler('time_entry','all', array_merge(
                $filter, ['limit' => $limit, 'offset' => $offset]
            ));
            $timeEntries = array_merge($timeEntries, $response['time_entries']);
            $totalCount = $response['total_count'];
            $offset += $limit;
            if ($offset >= $totalCount) {
                break;
            }
        }

        return $timeEntries;
    }

    /**
     * @return mixed
     * @throws RedmineException
     */
    public function requestHandler(string $nameClass, string $method, array $params = [])
    {
        try {
            $response = call_user_func([$this->client->getApi($nameClass), $method], $params);
            $this->log(sprintf(
                'Request. Place: %s. NameClassMethod: %s. Method: %s.',
                __CLASS__,
                $nameClass,
                $method),
                ['params' => $params, 'response' => $response]
            );

            if (isset($response['errors'])) {
                $errMsg = '';
                foreach ($response['errors'] as $key => $itemError) {
                    $errMsg .= sprintf(' %s: %s', $key, $itemError);
                }
                throw new RedmineException($errMsg);
            }
        } catch (RedmineException | \Throwable $e) {
            $this->log(sprintf(
                '%s error. Place: %s. Line: %s. NameClassMethod: %s. Method %s. Message: %s. Status: %s',
                get_class($e),
                __CLASS__,
                $e->getLine(),
                $nameClass,
                $method,
                $e->getMessage(),
                $e->getCode()
            ));
            throw $e;
        }
        return $response;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    private function log(string $message, array $context = [])
    {
        if (!$this->logger) {
            return;
        }

        $this->logger->log(LogLevel::DEBUG, $message, $context);
    }
}