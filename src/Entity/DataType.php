<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\DataTypeRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: DataTypeRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:dataFromSensor']]
)]
class DataType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $value;

    #[ORM\OneToMany(mappedBy: 'type', targetEntity: DataFromSensor::class)]
    private $dataFromSensors;

    #[ORM\Column(type: 'float', nullable: true)]
    private $minAlarm;

    #[ORM\Column(type: 'float', nullable: true)]
    private $maxAlarm;

    public function __construct()
    {
        $this->dataFromSensors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return Collection<int, DataFromSensor>
     */
    public function getDataFromSensors(): Collection
    {
        return $this->dataFromSensors;
    }

    public function addDataFromSensor(DataFromSensor $dataFromSensor): self
    {
        if (!$this->dataFromSensors->contains($dataFromSensor)) {
            $this->dataFromSensors[] = $dataFromSensor;
            $dataFromSensor->setType($this);
        }

        return $this;
    }

    public function removeDataFromSensor(DataFromSensor $dataFromSensor): self
    {
        if ($this->dataFromSensors->removeElement($dataFromSensor)) {
            // set the owning side to null (unless already changed)
            if ($dataFromSensor->getType() === $this) {
                $dataFromSensor->setType(null);
            }
        }

        return $this;
    }

    public function __toString() : string
    {
        return $this->value;
    }

    public function getMinAlarm(): ?float
    {
        return $this->minAlarm;
    }

    public function setMinAlarm(?float $minAlarm): self
    {
        $this->minAlarm = $minAlarm;

        return $this;
    }

    public function getMaxAlarm(): ?float
    {
        return $this->maxAlarm;
    }

    public function setMaxAlarm(?float $maxAlarm): self
    {
        $this->maxAlarm = $maxAlarm;

        return $this;
    }
}