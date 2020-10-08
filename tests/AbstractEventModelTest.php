<?php

declare(strict_types=1);

namespace Tests\Randock\Model;

use PHPUnit\Framework\TestCase;
use Randock\ValueObject\DynamicObjectStorage;
use Randock\Model\Exception\NotPatchableException;
use Tests\Randock\Model\Mocks\OrderMock;
use Tests\Randock\Model\Mocks\AddressMock;
use Tests\Randock\Model\Mocks\TravelerMock;

/**
 * Class AbstractEventModelTests.
 */
class AbstractEventModelTest extends TestCase
{
    public function testSimplePatch()
    {
        $traveler = new TravelerMock('Sebas', self::getDynamicObject());

        $data = new \stdClass();

        $data->surName = 'modif';
        $data->travelerDetails = new \stdClass();
        $data->travelerDetails->nationality = 'XX';

        $result = $traveler->patch($data);

        self::assertEmpty($result);
        self::assertSame($data->surName, $traveler->getSurName());
        self::assertSame($data->travelerDetails->nationality, $traveler->getTravelerDetails()->getNationality());
    }

    public function testAdvancedPatch()
    {
        $order = $this->getNewOrderWithTraveler();

        $arrayOrder = [
           'arrivalDate' => '2017-05-27',
           'email' => 'jop@randock.com',
           'travelers' => [
               [
                   'surname' => 'fuck yeah',
                   'travelerDetails' => [
                       'nationality' => 'ES',
                       'allFirstNames' => 'ES',
                       'allLastNames' => 'ES',
                       'dateOfBirth' => '1985-05-27',
                       'placeOfBirth' => 'Ubeda',
                       'mothersName' => 'ES',
                       'fathersName' => 'ES',
                       'passportNumber' => '75111458E',
                       'passportIssueDate' => '2016-05-27',
                       'passportExpiryDate' => '2021-05-27',
                   ],
               ],
            ],
       ];

        $arrayOrder = json_decode(json_encode($arrayOrder));

        $result = $order->patch($arrayOrder);

        self::assertEmpty($result);
        self::assertSame($arrayOrder->email, $order->getEmail());
        self::assertSame($arrayOrder->arrivalDate, $order->getArrivalDate()->format('Y-m-d'));
        self::assertSame($arrayOrder->travelers[0]->travelerDetails->placeOfBirth, $order->getTravelers()[0]->getTravelerDetails()->getPlaceOfBirth());
    }

    public function testNotPatchableCollectionException()
    {
        $order = $this->getNewOrderWithTraveler();

        $arrayOrder = [
            'arrivalDate' => '2017-05-27',
            'email' => 'jop@randock.com',
            'travelers' => null,
        ];

        $arrayOrder = json_decode(json_encode($arrayOrder));
        self::expectException(NotPatchableException::class);

        $order->patch($arrayOrder);
    }

    public function testNotPatchableException()
    {
        $order = $this->getNewOrderWithTraveler();

        $arrayOrder = [
            'arrivalDate' => '2017-05-27',
            'email' => 'jop@randock.com',
            'address' => null,
        ];

        $arrayOrder = json_decode(json_encode($arrayOrder));
        self::expectException(NotPatchableException::class);

        $order->patch($arrayOrder);
    }

    private static function getDynamicObject()
    {
        return new DynamicObjectStorage(json_decode(json_encode([
            'surname' => 'fuck yeah',
            'travelerDetails' => [
                'nationality' => 'ES',
                'allFirstNames' => 'ES',
                'allLastNames' => 'ES',
                'dateOfBirth' => '1985-05-27',
                'placeOfBirth' => 'Ubeda',
                'mothersName' => 'ES',
                'fathersName' => 'ES',
                'passportNumber' => '75111458E',
                'passportIssueDate' => '2016-05-27',
                'passportExpiryDate' => '2021-05-27',
            ],
        ])));
    }

    private function getNewOrderWithTraveler()
    {
        $address = new AddressMock('callecita');
        $order = new OrderMock(new \DateTime('tomorrow'), 'juan@demarco.com', $address);

        return $order->addTraveler(new TravelerMock('Sebas', self::getDynamicObject()));
    }
}
