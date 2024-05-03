<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ComputerRepository")
 * @ORM\Table(name="`Computer`")
 */
class Computer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="hostname")
     */
    private $hostname;

    /**
     * @ORM\Column(type="boolean", name="all_locations_allow", options={"default" : false})
     */
    private $allLocationsAllow;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AddressIp", mappedBy="computer")
     */
    private $addressIps;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ComputerAuthorizedCountry", mappedBy="computer")
     */
    private $computerAuthorizedCountries;

    public function __construct()
    {
        $this->addressIps = new ArrayCollection();
        $this->computerAuthorizedCountries = new ArrayCollection();
    }

    /**
     * @return Collection|AddressIp[]
     */
    public function getAddressIps(): Collection
    {
        return $this->addressIps;
    }

    /**
     * @return Collection|ComputerAuthorizedCountry[]
     */
    public function getComputerAuthorizedCountries(): Collection
    {
        return $this->computerAuthorizedCountries;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHostname(): ?string
    {
        return $this->hostname;
    }

    public function setHostname(?string $hostname): self
    {
        $this->hostname = $hostname;
        return $this;
    }

    public function getAllLocationsAllow(): ?bool
    {
        return $this->allLocationsAllow;
    }

    public function setAllLocationsAllow(bool $allLocationsAllow): self
    {
        $this->allLocationsAllow = $allLocationsAllow;
        return $this;
    }
}