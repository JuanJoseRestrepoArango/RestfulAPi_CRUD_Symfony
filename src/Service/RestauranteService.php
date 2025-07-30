<?php

namespace App\Service;

use Exception;
use App\DTO\RestauranteDTO;
use App\Entity\Restaurante;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\RestauranteNoEncontrado;

class RestauranteService{

    public function __construct(private EntityManagerInterface $entityManager) {}

    public function listar(): array
    {
        return $this->entityManager->getRepository(Restaurante::class)->findAll();
    }

    public function crear(RestauranteDTO $restauranteDTO): Restaurante
    {

        $duplicado = $this->entityManager->getRepository(Restaurante::class)->buscarPorTelefono($restauranteDTO->telefono);
        if ($duplicado) {
            throw new Exception('Ya existe un restaurante con este teléfono: ' . $restauranteDTO->telefono);
        }

        $restaurante = new Restaurante();
        $restaurante->setNombre($restauranteDTO->nombre);
        $restaurante->setDireccion($restauranteDTO->direccion);
        $restaurante->setTelefono($restauranteDTO->telefono);

        

        $this->entityManager->persist($restaurante);
        $this->entityManager->flush();

        return $restaurante;
    }

    public function mostrar(int $id): Restaurante
    {
        $restaurante = $this->entityManager->getRepository(Restaurante::class)->find($id);
        if (!$restaurante) {
            throw new RestauranteNoEncontrado();
        }
        return $restaurante;
    }
    public function mergeDatosRestaurante(Restaurante $existente, array $datos): RestauranteDTO
    {
        $combinado = array_merge([
            'nombre' => $existente->getNombre(),
            'direccion' => $existente->getDireccion(),
            'telefono' => $existente->getTelefono()
        ], $datos);

        return RestauranteDTO::fromArray($combinado);
    }

    public function actualizar(int $id, RestauranteDTO $restauranteDTO): Restaurante
    {
        $restaurante = $this->mostrar($id);
        $restaurante->setNombre($restauranteDTO->nombre);
        $restaurante->setDireccion($restauranteDTO->direccion);
        $restaurante->setTelefono($restauranteDTO->telefono);

        $this->entityManager->flush();

        return $restaurante;
    }

    public function eliminar(int $id): void
    {
        $restaurante = $this->mostrar($id);
        $this->entityManager->remove($restaurante);
        $this->entityManager->flush();
    }
}