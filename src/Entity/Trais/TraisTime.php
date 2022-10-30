<?php

namespace App\Entity\Trais;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

trait TraisTime
{
    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $modifAt = null;

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getModifAt(): ?\DateTimeImmutable
    {
        return $this->modifAt;
    }

    public function setModifAt(\DateTimeImmutable $modifAt): self
    {
        $this->modifAt = $modifAt;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setDateCreateUpdate()
    {
        if ($this->getCreateAt() == null)
        {
            $this->setCreateAt(new DateTimeImmutable());
        }
        $this->setModifAt(new DateTimeImmutable());
    }
}