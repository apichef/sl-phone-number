<?php

declare(strict_types=1);

namespace APiChef\SlPhoneNumber;

use PHPUnit\Framework\TestCase;

class PhoneNumberTest extends TestCase
{
    /**
     * @dataProvider phoneNumbersWithValidity
     */
    public function test_getDetails_throws_an_exception_when_the_starting_digits_or_length_is_invalid(string $number, bool $validity)
    {
        $slPhoneNumber = new PhoneNumber($number);

        if (! $validity) {
            $this->expectException(\InvalidArgumentException::class);
        }

        $result = $slPhoneNumber->getData();

        $this->assertIsArray($result);
    }

    /**
     * @dataProvider phoneNumbersWithValidity
     */
    public function test_it_validates_phone_number_by_starting_digits_and_length(string $number, bool $validity)
    {
        $slPhoneNumber = new PhoneNumber($number);

        $this->assertEquals($validity, $slPhoneNumber->isValid());
    }

    // 0{000000000} **
    // +94{000000000}
    // 0094{000000000}

    public function phoneNumbersWithValidity()
    {
        return [
            ['0000000000', true], // 10 digits
            ['1000000000', false], // 10 digits NOT starting with 0
            ['000000000', false], // 9 digits
            ['00000000000', false], // 11 digits
        ];
    }
}
