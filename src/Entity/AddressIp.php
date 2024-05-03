<?php

namespace App\Entity;

use App\Service\HectorApi;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AddressIpRepository")
 * @ORM\Table(name="`AddressIp`")
 */
class AddressIp
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Computer", inversedBy="addressIps")
     * @ORM\JoinColumn(name="computer_id", referencedColumnName="id", nullable=true)
     */
    private ?Computer $computer;

    /**
     * @ORM\Column(type="string", length=90, nullable=false, name="ip_address")
     */
    private string $ipAddress;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuthorizedCountry")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=false)
     */
    private $country;

    /**
     * @ORM\Column(type="datetime", name="collection_datetime", nullable=false)
     */
    private \DateTimeInterface $collectionDateTime;

    /**
     * @ORM\Column(type="boolean", name="is_proxy", nullable=false)
     */
    private bool $isProxy;

    /**
     * @var HectorApi
     */
    private $hectorApi;

    public function __construct(HectorApi $hectorApi)
    {
        $this->hectorApi = $hectorApi;
    }
    public function getId(): int
    {
        return $this->id;
    }

    public function getComputer(): ?Computer
    {
        return $this->computer;
    }


    public function getTag(): ?string
    {
        if ($this->computer === null) {
            return null;
        }

        $hostname = $this->computer->getHostname();
        return $this->hectorApi->getAccountFromHostname($this->hectorApi->getAccessToken(), $hostname);
    }


    public function setComputer(?Computer $computer): self
    {
        $this->computer = $computer;

        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(?string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

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

    public function getCollectionDateTime(): ?\DateTimeInterface
    {
        return $this->collectionDateTime;
    }

    public function setCollectionDateTime(?\DateTimeInterface $collectionDateTime): self
    {
        $this->collectionDateTime = $collectionDateTime;

        return $this;
    }

    public function getIsProxy(): bool
    {
        return $this->isProxy;
    }

    public function setIsProxy(bool $isProxy): self
    {
        $this->isProxy = $isProxy;

        return $this;
    }

}
