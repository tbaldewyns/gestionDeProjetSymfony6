<?php

namespace App\Entity;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\DataFromSensorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DataFromSensorRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:dataFromSensor']]
)]
class DataFromSensor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:dataFromSensor'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read:dataFromSensor'])]
    private $sensorNbr;

    #[ORM\Column(type: 'float')]
    #[Groups(['read:dataFromSensor'])]
    private $value;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['read:dataFromSensor'])]
    private $sendedAt;

    #[ORM\ManyToOne(targetEntity: Local::class, inversedBy: 'dataFromSensors')]
    #[Groups(['read:dataFromSensor'])]
    private $local;

    #[ORM\ManyToOne(targetEntity: DataType::class, inversedBy: 'dataFromSensors')]
    #[Groups(['read:dataFromSensor'])]
    private $type;

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

    public function getLocal(): ?Local
    {
        return $this->local;
    }

    public function setLocal(?Local $local): self
    {
        $this->local = $local;

        return $this;
    }

    public function getType(): ?DataType
    {
        return $this->type;
    }

    public function setType(?DataType $type): self
    {
        $this->type = $type;

        return $this;
    }
}