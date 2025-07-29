<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RestauranteNoEncontrado extends NotFoundHttpException
{
    public function __construct(string $message = 'Restaurante no encontrado')
    {
        parent::__construct($message);
    }
}