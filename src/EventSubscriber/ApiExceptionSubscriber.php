<?php

namespace App\EventSubscriber;


use App\Exception\RestauranteNoEncontrado;
use Doctrine\ORM\Query\QueryException;
use Exception;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $excepcion = $event->getThrowable();
        $estado = 500;
        $mensaje = 'Error interno del servidor' . ' ' . $excepcion->getMessage();

        if ($excepcion instanceof RestauranteNoEncontrado) {
            $estado = 404;
            $mensaje = $excepcion->getMessage();
        } elseif ($excepcion instanceof QueryException) {
            $estado = 500;
            $mensaje = 'Error en base de datos: ' . $excepcion->getMessage();
        } elseif ($excepcion instanceof BadRequestHttpException) {
            $estado = 400;
            $mensaje = 'Petición mal formada: ' . $excepcion->getMessage();
        } elseif ($excepcion instanceof HttpExceptionInterface) {
            $estado = $excepcion->getStatusCode();
            $mensaje = $excepcion->getMessage();
        } elseif ($excepcion instanceof ValidatorException) {
            $estado = 422;
            $mensaje = $excepcion->getMessage();
        }elseif ($excepcion instanceof Exception) {
            $estado = 422;
            $mensaje = $excepcion->getMessage();
        }

        $response = new JsonResponse([
            'status' => false,
            'message' => $mensaje,
        ], $estado);

        $event->setResponse($response);
    }
}

