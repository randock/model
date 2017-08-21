<?php

declare(strict_types=1);

namespace Tests\Randock\Model\Mocks;

use Randock\Model\AbstractEventModel;
use Randock\ValueObject\AggregateRootId;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class OrderMock extends AbstractEventModel
{
    /**
     * @var MockUuid
     */
    private $uuid;
    /**
     * @var Collection
     */
    private $travelers;

    /**
     * @var \DateTime
     */
    private $arrivalDate;

    /**
     * @var string
     */
    private $email;

    /**
     * @var AddressMock
     */
    private $address;

    public function __construct(\DateTime $arrivalDate, string $email, AddressMock $addressMock)
    {
        $this->arrivalDate = $arrivalDate;
        $this->email = $email;
        $this->address = $addressMock;

        $this->uuid = new MockUuid();
        $this->travelers = new ArrayCollection();
    }

    /**
     * @return AggregateRootId
     */
    public function getUuid(): AggregateRootId
    {
        return $this->uuid;
    }

    /**
     * @param AggregateRootId $uuid
     */
    public function setUuid(AggregateRootId $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return Collection
     */
    public function getTravelers(): Collection
    {
        return $this->travelers;
    }

    /**
     * @param Collection $travelers
     */
    public function setTravelers(Collection $travelers)
    {
        $this->travelers = $travelers;
    }

    /**
     * @return \DateTime
     */
    public function getArrivalDate(): \DateTime
    {
        return $this->arrivalDate;
    }

    /**
     * @param \DateTime $arrivalDate
     */
    public function setArrivalDate(\DateTime $arrivalDate)
    {
        $this->arrivalDate = $arrivalDate;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * @param TravelerMock $traveler
     *
     * @return OrderMock
     */
    public function addTraveler(TravelerMock $traveler): OrderMock
    {
        if (!$this->travelers->contains($traveler)) {
            $this->travelers->add($traveler);
            $traveler->setOrder($this);
        }

        return $this;
    }

    /**
     * @param TravelerMock $traveler
     *
     * @return OrderMock
     */
    public function removeTraveler(TravelerMock $traveler): OrderMock
    {
        if ($this->travelers->contains($traveler)) {
            $this->travelers->removeElement($traveler);
        }

        return $this;
    }

    /**
     * @return AddressMock
     */
    public function getAddress(): AddressMock
    {
        return $this->address;
    }

    /**
     * @param AddressMock $address
     *
     * @return OrderMock
     */
    public function setAddress(AddressMock $address): OrderMock
    {
        $this->address = $address;

        return $this;
    }
}
