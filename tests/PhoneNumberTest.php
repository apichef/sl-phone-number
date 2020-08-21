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

        $result = $slPhoneNumber->getData();

        $this->assertEquals(['number' => $formated], $result);
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
            ['0000000000', true, '0000000000'], // 10 digits
            ['1000000000', false, null], // 10 digits NOT starting with 0
            ['000000000', false, null], // 9 digits
            ['00000000000', false, null], // 11 digits

            ['+94000000000', true, '0000000000'], // +94 and 9 digits
            ['-94000000000', false, null], // -94 and 9 digits
            ['+9400000000', false, null], // +94 and 8 digits
            ['+940000000000', false, null], // +94 and 10 digits

            ['0094000000000', true, '0000000000'], // 0094 and 9 digits
            ['0096000000000', false, null], // 0096 and 9 digits
            ['009400000000', false, null], // 0094 and 8 digits
            ['00940000000000', false, null], // 0094 and 10 digits
        ];
    }
}
