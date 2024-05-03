<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuthorizedCountryRepository")
 * @ORM\Table(name="`AuthorizedCountry`")
 */
class AuthorizedCountry
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=500, name="country_name")
     */
    private $countryName;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ComputerAuthorizedCountry", mappedBy="authorizedCountry")
     */
    private $computerAuthorizedCountries;

    public function __construct()
    {
        $this->computerAuthorizedCountries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountryName(): ?string
    {
        return $this->countryName;
    }

    public function setCountryName(string $countryName): self
    {
        $this->countryName = $countryName;
        return $this;
    }

    /**
     * @return Collection|ComputerAuthorizedCountry[]
     */
    public function getComputerAuthorizedCountries(): Collection
    {
        return $this->computerAuthorizedCountries;
    }

    public function __toString(): string
    {
        return $this->countryName;
    }


}