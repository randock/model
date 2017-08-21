<?php

declare(strict_types=1);

namespace Tests\Randock\Model\Mocks;

use Randock\Model\AbstractEventModel;

class AddressMock extends AbstractEventModel
{
    private $uuid;

    private $street;

    public function __construct(string $street)
    {
        $this->street;
        $this->uuid = new MockUuid();
    }

    /**
     * @return MockUuid
     */
    public function getUuid(): MockUuid
    {
        return $this->uuid;
    }

    /**
     * @return mixed
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param mixed $street
     *
     * @return AddressMock
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }
}
