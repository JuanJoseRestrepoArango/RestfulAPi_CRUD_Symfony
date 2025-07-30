<?php

namespace App\Controller\Api;

use App\DTO\RestauranteDTO;
use App\Helper\RestauranteResponse;
use App\Service\RestauranteService;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: 'Restaurantes',
    description: 'Operaciones sobre restaurantes'
)]
#[Route('/api/restaurantes')]


class RestauranteApiController extends AbstractController
{
    public function __construct(private RestauranteService $restauranteService, private ValidatorInterface $validacion) {}


    #[Route('', methods: ['GET'])]
    #[OA\Get(
        summary: 'Lista todos los restaurantes',
        description: 'Obtiene un listado completo de todos los restaurantes registrados',
        tags: ['Restaurantes']
    )]
    #[OA\Response(
        response: 200,
        description: 'Listado de restaurantes',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Restaurante')
        )
    )]
    #[OA\Response(response: 401, description: 'No autorizado')]
    public function listar(): JsonResponse
    {
        $restaurantes = $this->restauranteService->listar();
        $respuesta = array_map(fn($r) => RestauranteDTO::fromEntity($r)->toArray(), $restaurantes);
        return RestauranteResponse::exito($respuesta);
    }

    #[Route('', name: 'api_restaurantes_create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Crea un nuevo restaurante',
        description: 'Registra un nuevo restaurante en el sistema',
        tags: ['Restaurantes']
    )]
    #[OA\RequestBody(
        description: 'Datos del restaurante a crear',
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'nombre', type: 'string', example: 'Restaurante Ejemplo'),
                new OA\Property(property: 'direccion', type: 'string', example: 'Calle Principal 123'),
                new OA\Property(property: 'telefono', type: 'string', example: '+1234567890')
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Restaurante creado exitosamente',
        content: new OA\JsonContent(ref: '#/components/schemas/Restaurante')
    )]
    #[OA\Response(response: 400, description: 'Datos inválidos')]
    #[OA\Response(response: 401, description: 'No autorizado')]
    public function crear(Request $request,SerializerInterface $serializer): JsonResponse
    {
        $datos = $request->getContent();
        
        try {
            $restauranteDTO = $serializer->deserialize($datos, RestauranteDTO::class, 'json');
        } catch (Exception $e) {
            throw new ValidatorException((string) $e->getMessage());
        }

        $errores = $this->validacion->validate($restauranteDTO);
        if (count($errores) > 0) {
            throw new ValidatorException((string) $errores);
        }
        $restaurante = $this->restauranteService->crear($restauranteDTO);
        return RestauranteResponse::exito(RestauranteDTO::fromEntity($restaurante)->toArray(), 'Restaurante creado exitosamente');
    }

   

    #[Route('/{id}', methods: ['GET'])]
    #[OA\Get(
        summary: 'Obtiene un restaurante específico',
        description: 'Devuelve los detalles de un restaurante según su ID',
        tags: ['Restaurantes']
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'ID del restaurante',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Detalles del restaurante',
        content: new OA\JsonContent(ref: '#/components/schemas/Restaurante')
    )]
    #[OA\Response(response: 404, description: 'Restaurante no encontrado')]
    #[OA\Response(response: 401, description: 'No autorizado')]
    public function mostrar(int $id): JsonResponse
    {
        $restaurante = $this->restauranteService->mostrar($id);
        return RestauranteResponse::exito(RestauranteDTO::fromEntity($restaurante)->toArray());
    }

   

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        summary: 'Actualiza completamente un restaurante (PUT)',
        description: 'Reemplaza todos los datos del restaurante con los proporcionados',
        tags: ['Restaurantes']
    )]
    #[OA\Patch(
        summary: 'Actualiza parcialmente un restaurante (PATCH)',
        description: 'Actualiza solo los campos proporcionados del restaurante',
        tags: ['Restaurantes']
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'ID del restaurante a actualizar',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        description: 'Datos del restaurante a actualizar',
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'nombre', type: 'string', example: 'Restaurante Actualizado'),
                new OA\Property(property: 'direccion', type: 'string', example: 'Calle Nueva 456'),
                new OA\Property(property: 'telefono', type: 'string', example: '+987654321')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Restaurante actualizado exitosamente',
        content: new OA\JsonContent(ref: '#/components/schemas/Restaurante')
    )]
    #[OA\Response(response: 400, description: 'Datos inválidos')]
    #[OA\Response(response: 404, description: 'Restaurante no encontrado')]
    #[OA\Response(response: 401, description: 'No autorizado')]
    public function actualizar(int $id, Request $request,SerializerInterface $serializer): JsonResponse
    {
        $datos =$request->getContent();
        try{
            if($request->isMethod('PATCH')){
                $restauranteExistente = $this->restauranteService->mostrar($id);
                $datosArray = json_decode($datos, true); 
                
                $datosCombinados = $this->restauranteService->mergeDatosRestaurante($restauranteExistente, $datosArray);
                
                $restauranteDTO = $serializer->deserialize(json_encode($datosCombinados) , RestauranteDTO::class, 'json');
            }else{
                $restauranteDTO = $serializer->deserialize($datos, RestauranteDTO::class, 'json');
            }
        }catch(Exception $e) {
            throw new ValidatorException((string) $e->getMessage());
        }
        
        $errores = $this->validacion->validate($restauranteDTO);
        if (count($errores) > 0) {
            throw new ValidatorException((string) $errores);
        }
        $restaurante = $this->restauranteService->actualizar($id, $restauranteDTO);
        return RestauranteResponse::exito(RestauranteDTO::fromEntity($restaurante)->toArray(), 'Restaurante actualizado exitosamente');
    }

    
     #[Route('/{id}', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Elimina un restaurante',
        description: 'Elimina permanentemente un restaurante del sistema',
        tags: ['Restaurantes']
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'ID del restaurante a eliminar',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Restaurante eliminado exitosamente',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'status', type: 'boolean', example: true),
                new OA\Property(property: 'message', type: 'string', example: 'Restaurante eliminado exitosamente')
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Restaurante no encontrado')]
    #[OA\Response(response: 401, description: 'No autorizado')]
    public function eliminar(int $id): JsonResponse
    {
        $this->restauranteService->eliminar($id);
        return RestauranteResponse::exito(null, 'Restaurante eliminado exitosamente');
    }
}