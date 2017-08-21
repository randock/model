<?php

declare(strict_types=1);

namespace Tests\Randock\Model\Mocks;

use Randock\Model\AbstractEventModel;
use Randock\ValueObject\DynamicObjectStorage;

class TravelerMock extends AbstractEventModel
{
    /**
     * @var MockUuid
     */
    private $uuid;
    /**
     * @var OrderMock
     */
    private $order;

    /**
     * @var string
     */
    private $surName;

    /**
     * @var DynamicObjectStorage
     */
    private $travelerDetails;

    public function __construct(string $surname, DynamicObjectStorage $travelerDetails)
    {
        $this->surName = $surname;
        $this->travelerDetails = $travelerDetails;

        $this->uuid = new MockUuid();
    }

    public function patch(\stdClass $data)
    {
        $this->travelerDetails = new DynamicObjectStorage($this->travelerDetails->toJsonObject());

        return parent::patch($data);
    }

    /**
     * @return OrderMock
     */
    public function getOrder(): OrderMock
    {
        return $this->order;
    }

    /**
     * @param OrderMock $order
     */
    public function setOrder(OrderMock $order)
    {
        $this->order = $order;
    }

    /**
     * @return string
     */
    public function getSurName(): string
    {
        return $this->surName;
    }

    /**
     * @param string $surName
     */
    public function setSurName(string $surName)
    {
        $this->surName = $surName;
    }

    /**
     * @return DynamicObjectStorage
     */
    public function getTravelerDetails(): DynamicObjectStorage
    {
        return $this->travelerDetails;
    }

    /**
     * @param DynamicObjectStorage $travelerDetails
     */
    public function setTravelerDetails(DynamicObjectStorage $travelerDetails)
    {
        $this->travelerDetails = $travelerDetails;
    }

    /**
     * @return MockUuid
     */
    public function getUuid(): MockUuid
    {
        return $this->uuid;
    }
}
