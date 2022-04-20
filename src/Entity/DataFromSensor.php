<?php

namespace App\Entity;

use App\Repository\DataFromSensorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DataFromSensorRepository::class)]
class DataFromSensor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $sensorNbr;

    #[ORM\Column(type: 'float')]
    private $value;

    #[ORM\Column(type: 'datetime')]
    private $sendedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSensorNbr(): ?string
    {
        return $this->sensorNbr;
    }

    public function setSensorNbr(string $sensorNbr): self
    {
        $this->sensorNbr = $sensorNbr;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getSendedAt(): ?\DateTimeInterface
    {
        return $this->sendedAt;
    }

    public function setSendedAt(\DateTimeInterface $sendedAt): self
    {
        $this->sendedAt = $sendedAt;

        return $this;
    }
}
