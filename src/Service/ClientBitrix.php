<?php

namespace App\Service;

use phpDocumentor\Reflection\Types\This;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Vasyag07\Bitrix24Client\Client;

class ClientBitrix extends AbstractController
{
    use LoggerAwareTrait;

    private $client;

    public function __construct()
    {
        //$this->client = new Client($_ENV['BITRIX24_BASE_URL'], $_ENV['BITRIX24_ID_MANAGER'], $_ENV['BITRIX24_API_KEY']);
    }

    public function getEmployers() {
        //return $this->bitrixApiRequest($this->client->user->getAll(), __METHOD__);
    }

    public function getCalendar($from, $to, array $user) {
        //return $this->bitrixApiRequest($this->client->calendar->getAccessibilityList(new \DateTime($from), new \DateTime($to), $user), __METHOD__);
    }

    public function bitrixApiRequest($response, $method)
    {
        $this->log(sprintf(
            'Request. Place: %s. Method: %s.',
            __CLASS__,
            $method),
            ['response' => $response]
        );
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
