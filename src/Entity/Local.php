<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\LocalRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: LocalRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read:dataFromSensor']]
)]
class Local
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $local;

    #[ORM\Column(type: 'string', length: 255)]
    private $campus;

    #[ORM\OneToMany(mappedBy: 'local', targetEntity: Sensor::class)]
    private $sensors;

    #[ORM\OneToMany(mappedBy: 'local', targetEntity: DataFromSensor::class)]
    private $dataFromSensors;

    public function __construct()
    {
        $this->sensors = new ArrayCollection();
        $this->dataFromSensors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocal(): ?string
    {
        return $this->local;
    }

    public function setLocal(string $local): self
    {
        $this->local = $local;

        return $this;
    }

    public function getCampus(): ?string
    {
        return $this->campus;
    }

    public function setCampus(string $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection<int, Sensor>
     */
    public function getSensors(): Collection
    {
        return $this->sensors;
    }

    public function addSensor(Sensor $sensor): self
    {
        if (!$this->sensors->contains($sensor)) {
            $this->sensors[] = $sensor;
            $sensor->setLocal($this);
        }

        return $this;
    }

    public function removeSensor(Sensor $sensor): self
    {
        if ($this->sensors->removeElement($sensor)) {
            // set the owning side to null (unless already changed)
            if ($sensor->getLocal() === $this) {
                $sensor->setLocal(null);
            }
        }

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
            $dataFromSensor->setLocal($this);
        }

        return $this;
    }

    public function removeDataFromSensor(DataFromSensor $dataFromSensor): self
    {
        if ($this->dataFromSensors->removeElement($dataFromSensor)) {
            // set the owning side to null (unless already changed)
            if ($dataFromSensor->getLocal() === $this) {
                $dataFromSensor->setLocal(null);
            }
        }

        return $this;
    }
    public function __toString() : string
    {
        return $this->local;
    }
}