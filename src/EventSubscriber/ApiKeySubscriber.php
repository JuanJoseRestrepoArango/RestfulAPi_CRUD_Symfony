<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ApiKeySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo();

         if (str_starts_with($path, '/api/doc') ||str_starts_with($path, '/api/doc')) {
            return;
        }

        if(str_starts_with($request->getPathInfo(), '/api')) {
            
            $apiKey = $request->headers->get('X-API-KEY');
            $keyValida = $_ENV['API_KEY'];
    
            if ($apiKey !== $keyValida ) {
                $event->setResponse(new JsonResponse([
                    'status' => false,
                    'message' => 'Api Key no válida',
                ], 401));
            }
        }
    }
} 