<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AlertRepository")
 * @ORM\Table(name="`Alert`")
 */
class Alert
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AddressIp")
     * @ORM\JoinColumn(name="id_address_ip", referencedColumnName="id", nullable=false)
     */
    private $addressIp;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Computer")
     * @ORM\JoinColumn(name="id_computer", referencedColumnName="id", nullable=false)
     */
    private $computer;

    /**
     * @ORM\Column(type="integer", name="level_alert")
     */
    private $levelAlert;

    /**
     * @ORM\Column(type="datetime", name="alert_datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $alertDatetime;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuthorizedCountry")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=false)
     */
    private $country;

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

    public function getComputer(): ?Computer
    {
        return $this->computer;
    }

    public function setComputer(?Computer $computer): self
    {
        $this->computer = $computer;
        return $this;
    }

    public function getLevelAlert(): ?int
    {
        return $this->levelAlert;
    }

    public function setLevelAlert(int $levelAlert): self
    {
        $this->levelAlert = $levelAlert;
        return $this;
    }

    public function getAlertDatetime(): ?\DateTimeInterface
    {
        return $this->alertDatetime;
    }

    public function setAlertDatetime(?\DateTimeInterface $alertDatetime): self
    {
        $this->alertDatetime = $alertDatetime;
        return $this;
    }

    public function getCountry(): ?AuthorizedCountry
    {
        return $this->country;
    }

    public function setCountry(?AuthorizedCountry $country): self
    {
        $this->country = $country;
        return $this;
    }
}