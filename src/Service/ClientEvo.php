<?php

namespace App\Service;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ClientEvo extends AbstractController
{
    use LoggerAwareTrait;

    private $evoApiHost;

    public function __construct()
    {
        $this->evoApiHost = $_ENV['EVO_URL'];
    }

    public function auth($parameters) {
        $evoAnswer =  $this->evoApiRequest('POST', 'auth', $parameters);
        return json_decode($evoAnswer, true);
    }

    public function getEmployer($parameters, $limit = 1000) {

        $evoAnswer = $this->evoApiRequest('GET', 'employee', array_merge(
            $parameters, ['limit' => $limit]
        ));
        return json_decode($evoAnswer, true);
    }

    public function getTask($parameters, $limit = 20000)
    {
        $evoAnswer= $this->evoApiRequest('GET', 'task', array_merge(
            $parameters, ['limit' => $limit]
        ));
        return json_decode($evoAnswer, true);
    }

    public function getProject($parameters)
    {
        $evoAnswer = $this->evoApiRequest('GET', 'project', $parameters);
        return json_decode($evoAnswer, true);
    }

    public function evoApiRequest($requestType, $apiMethod, $data)
    {
        try {
            if ($requestType == 'POST') {
                $ch = curl_init($this->evoApiHost . $apiMethod);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            } elseif ($data) {
                $ch = curl_init($this->evoApiHost . $apiMethod . '?' . http_build_query($data));
            } else {
                $ch = curl_init($this->evoApiHost . $apiMethod);
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $response = curl_exec($ch);
            curl_close($ch);
            unset($data['password']);
            $this->log(sprintf(
                'Request. Place: %s. Method: %s.',
                __CLASS__,
                $apiMethod),
                ['params' => $data, 'response' => $response]
            );
            return $response;
        } catch (Exception $e) {
            $this->log(sprintf(
                '%s error. Place: %s. Line: %s. Method: %s. Message: %s. Status: %s',
                get_class($e),
                __CLASS__,
                $e->getLine(),
                $apiMethod,
                $e->getMessage(),
                $e->getCode()
            ));
            return $e;
        }
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