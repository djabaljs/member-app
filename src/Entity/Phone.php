<?php

namespace App\Entity;

use App\Repository\PhoneRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PhoneRepository::class)
 */
class Phone
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @ORM\ManyToOne(targetEntity=UtilNumber::class, inversedBy="phones")
     * @ORM\JoinColumn(nullable=false)
     */
    private $utilNumber;

  

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getUtilNumber(): ?UtilNumber
    {
        return $this->utilNumber;
    }

    public function setUtilNumber(?UtilNumber $utilNumber): self
    {
        $this->utilNumber = $utilNumber;

        return $this;
    }

    public function __toString()
    {
        return $this->getNumber();
    }
}
