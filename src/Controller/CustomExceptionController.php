<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class CustomExceptionController extends AbstractController
{
    public function show(FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        return $this->json([
            'success' => false,
            'status' =>$exception->getStatusText(),
            'error' => $exception->getMessage(),
        ], $exception->getStatusCode());
    }
}
