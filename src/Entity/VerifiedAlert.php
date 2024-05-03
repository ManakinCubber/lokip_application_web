<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VerifiedAlertRepository")
 * @ORM\Table(name="`VerifiedAlert`")
 */
class VerifiedAlert
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="idAlerts")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AddressIp")
     * @ORM\JoinColumn(name="id_address_ip", referencedColumnName="id")
     */
    private $addressIp;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddressIp(): ?AddressIp
    {
        return $this->addressIp;
    }

    public function setAddressIp(?AddressIp $addressIp): self
    {
        $this->addressIp = $addressIp;

        return $this;
    }
}
