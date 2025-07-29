<?php

namespace App\DTO;
class RetauranteDTO
{
   
    public function __construct(
        public readonly string $nombre,
        public readonly string $direccion,  
        public readonly string $telefono,
    )
    {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['nombre'] ?? '',
            $data['direccion'] ?? '',
            $data['telefono'] ?? ''
        );
    }

    public static function fromPatch($datosViejos,$datosNuevos):self
    {
        $datos = array_merge($datosViejos, $datosNuevos);
        return new self(
            $datos['nombre'],
            $datos['direccion'],
            $datos['telefono']
        );
    }

    public function toArray(): array
    {
        return [
            'nombre' => $this->nombre,
            'direccion' => $this->direccion,
            'telefono' => $this->telefono,
        ];
    }
}