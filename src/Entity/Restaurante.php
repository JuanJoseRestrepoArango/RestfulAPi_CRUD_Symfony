<?php

namespace App\Entity;

use App\Repository\RestauranteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RestauranteRepository::class)]
class Restaurante
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El nombre es obligatorio')]
    #[Assert\Length(max: 255, maxMessage: 'Máximo 255 caracteres')]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La dirección es obligatoria')]
    #[Assert\Length(max: 255, maxMessage: 'Máximo 255 caracteres')]
    private ?string $direccion = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: 'El telefono es obligatoria')]
    #[Assert\Length(max: 20, maxMessage: 'Máximo 20 caracteres')]
    private ?string $telefono = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(string $direccion): static
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): static
    {
        $this->telefono = $telefono;

        return $this;
    }
}
