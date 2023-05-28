<?php

namespace App\EventListener;

use App\Entity\Manager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestSignListener implements EventSubscriberInterface
{
    private ManagerRegistry $doctrine;

    public function __construct(
        ManagerRegistry $doctrine
    ) {
        $this->doctrine = $doctrine;
    }

    /**
     * Проверка
     */
    public function onRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        if ('/api/doc' === substr($request->getPathInfo(), 0, 8)) {
            if (
                '/api/doc/login' != substr($request->getPathInfo(), 0, 16) &&
                strlen($request->getPathInfo()) > 8
            ) {
                if (!$this->isRequest($request)) {
                    $event->setResponse(new JsonResponse('Access denied!', JsonResponse::HTTP_FORBIDDEN));
                }
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onRequest',
        ];
    }

    private function isRequest(Request $request): bool
    {
        $maskBearer = 'Bearer ';
        $bearer = $request->headers->get('authorization');
        $bearerToken = str_replace($maskBearer, "", $bearer);
        $response = true;
        $managerRepository = $this->doctrine->getRepository(Manager::class);
        if (empty($bearerToken)) {
            $response = false;
        }
        if (empty($bearerToken)) {
            $response = false;
        } else {
            $manager = $managerRepository->findOneBy([
                'token' => $bearerToken,
            ]);
            if (empty($manager)) {
                $response = false;
            }
        }
        return $response;
    }
}
