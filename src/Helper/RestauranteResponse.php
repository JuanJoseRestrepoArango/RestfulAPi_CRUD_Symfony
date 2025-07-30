<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\JsonResponse;

class RestauranteResponse{
    public static function exito($data, $message = 'Operacion Exitosa'): JsonResponse
    {
        return new JsonResponse([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], 200);
    }

    public static function error($message = 'Error en la operación', $code = 400): JsonResponse
    {
        return new JsonResponse([
            'status' => false,
            'message' => $message,
        ], $code);
    }
}