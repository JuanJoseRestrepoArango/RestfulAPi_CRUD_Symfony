<?php

namespace App\DTO;

use App\Entity\Restaurante;
use Symfony\Component\Validator\Constraints as Assert;

class RestauranteDTO
{
   
    public function __construct(
        public readonly ?int $id, 
        #[Assert\NotBlank(message: 'El nombre es obligatorio')]
        #[Assert\Type(type: 'string', message: 'El nombre debe ser un texto')]
        #[Assert\Length(max: 255, maxMessage: 'Máximo 255 caracteres')]
        public readonly string $nombre,

         #[Assert\NotBlank(message: 'La dirección es obligatoria')]
        #[Assert\Type(type: 'string', message: 'La dirección debe ser un texto')]
        #[Assert\Length(max: 255, maxMessage: 'Máximo 255 caracteres')]
        public readonly string $direccion,  

         #[Assert\NotBlank(message: 'El teléfono es obligatorio')]
        #[Assert\Type(type: 'string', message: 'El teléfono debe ser un texto')]
        #[Assert\Length(max: 20, maxMessage: 'Máximo 20 caracteres')]
        public readonly string $telefono,
    )
    {}

    public static function fromArray(array $data): self
    {
        return new self(
             $data['id'] ?? null,
            $data['nombre'] ?? '',
            $data['direccion'] ?? '',
            $data['telefono'] ?? ''
        );
    }


    public static function fromEntity(Restaurante $restaurante): self
    {
        return new self(
            $restaurante->getId(),
            $restaurante->getNombre(),
            $restaurante->getDireccion(),
            $restaurante->getTelefono()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'direccion' => $this->direccion,
            'telefono' => $this->telefono,
        ];
    }
}