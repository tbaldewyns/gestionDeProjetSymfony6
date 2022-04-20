<?php

namespace App\Entity;


class DataSearch{

    /**
     * @var string|null
     */
    
    private $type;

    /** 
     * @var string|null
    */
    
    private $local;
    
    /** 
     * @var string|null
    */

    private $frequence;


     public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
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
    public function getFrequence(): ?string
    {
        return $this->frequence;
    }

    public function setFrequence(string $frequence): self
    {
        $this->frequence = $frequence;

        return $this;
    }
    
}
?>