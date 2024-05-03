<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ComputerAuthorizedCountryRepository")
 * @ORM\Table(name="`ComputerAuthorizedCountry`")
 */
class ComputerAuthorizedCountry
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Computer", inversedBy="computerAuthorizedCountries")
     * @ORM\JoinColumn(name="computer_id", referencedColumnName="id", nullable=false)
     */
    private $computer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuthorizedCountry", inversedBy="computerAuthorizedCountries")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=false)
     */
    private $authorizedCountry;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComputer(): ?Computer
    {
        return $this->computer;
    }

    public function setComputer(?Computer $computer): self
    {
        $this->computer = $computer;
        return $this;
    }

    public function getAuthorizedCountry(): ?AuthorizedCountry
    {
        return $this->authorizedCountry;
    }

    public function setAuthorizedCountry(?AuthorizedCountry $authorizedCountry): self
    {
        $this->authorizedCountry = $authorizedCountry;
        return $this;
    }
}