<?php

declare(strict_types=1);

namespace APiChef\SlPhoneNumber;

use PHPUnit\Framework\TestCase;

class PhoneNumberTest extends TestCase
{
    /**
     * @dataProvider phoneNumbersWithValidity
     */
    public function test_getDetails_throws_an_exception_when_the_starting_digits_or_length_is_invalid(string $number, bool $validity, string $formated = null)
    {
        $slPhoneNumber = new PhoneNumber($number);

        if (! $validity) {
            $this->expectException(\InvalidArgumentException::class);
        }

        /** @var PhoneNumberDetails $result */
        $result = $slPhoneNumber->getDetails();

        $this->assertEquals($formated, $result->getNumber());
    }

    /**
     * @dataProvider phoneNumbersWithValidity
     */
    public function test_it_validates_phone_number_by_starting_digits_and_length(string $number, bool $validity)
    {
        $slPhoneNumber = new PhoneNumber($number);

        $this->assertEquals($validity, $slPhoneNumber->isValid());
    }

    public function phoneNumbersWithValidity()
    {
        return [
            ['0770000000', true, '0770000000'], // 10 digits
            ['0112000000', true, '0112000000'], // 10 digits
            ['1000000000', false, null], // 10 digits NOT starting with 0
            ['000000000', false, null], // 9 digits
            ['00000000000', false, null], // 11 digits

            ['+94770000000', true, '0770000000'], // +94 and 9 digits
            ['+94112000000', true, '0112000000'], // +94 and 9 digits
            ['-94000000000', false, null], // -94 and 9 digits
            ['+9400000000', false, null], // +94 and 8 digits
            ['+940000000000', false, null], // +94 and 10 digits

            ['0094770000000', true, '0770000000'], // 0094 and 9 digits
            ['0094112000000', true, '0112000000'], // 0094 and 9 digits
            ['0096000000000', false, null], // 0096 and 9 digits
            ['009400000000', false, null], // 0094 and 8 digits
            ['00940000000000', false, null], // 0094 and 10 digits
        ];
    }

    public function test_get_details_returns_phone_number_details_object()
    {
        $details = (new PhoneNumber('0710000000'))->getDetails();

        $this->assertInstanceOf(PhoneNumberDetails::class, $details);
        $this->assertEquals('Mobitel', $details->getOperator());

        $fixedDetails = (new PhoneNumber('0112000000'))->getDetails();

        $this->assertInstanceOf(PhoneNumberDetails::class, $fixedDetails);
        $this->assertEquals('Sri Lanka Telecom', $fixedDetails->getOperator());
        $this->assertEquals('Western', $fixedDetails->getProvince());
        $this->assertEquals('Colombo', $fixedDetails->getDistrict());
        $this->assertEquals('Colombo', $fixedDetails->getArea());
    }

    public function test_it_throws_a_logic_exception_when_trying_to_get_province_from_mobile_number()
    {
        $details = (new PhoneNumber('0710000000'))->getDetails();
        $this->expectException(\LogicException::class);
        $details->getProvince();
    }

    public function test_it_throws_a_logic_exception_when_trying_to_get_district_from_mobile_number()
    {
        $details = (new PhoneNumber('0710000000'))->getDetails();
        $this->expectException(\LogicException::class);
        $details->getDistrict();
    }

    public function test_it_throws_a_logic_exception_when_trying_to_get_area_from_mobile_number()
    {
        $details = (new PhoneNumber('0710000000'))->getDetails();
        $this->expectException(\LogicException::class);
        $details->getArea();
    }

    /**
     * @dataProvider validityPhoneNumbers
     */
    public function test_can_get_local_format(string $number)
    {
        $localFormat = (new PhoneNumber($number))->toLocalFormat();
        $this->assertEquals('0710000000', $localFormat);
    }

    /**
     * @dataProvider validityPhoneNumbers
     */
    public function test_can_get_IDD_format(string $number)
    {
        $localFormat = (new PhoneNumber($number))->toIDDFormat();
        $this->assertEquals('+94710000000', $localFormat);
    }

    public function validityPhoneNumbers(): array
    {
        return [
            ['0710000000'],
            ['+94710000000'],
            ['0094710000000'],
        ];
    }
}
