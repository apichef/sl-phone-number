<?php

namespace APiChef\SlPhoneNumber;

class PhoneNumberDetails
{
    /** @var string */
    private $number;

    /** @var string */
    private $type;

    /** @var string */
    private $operator;

    /** @var string */
    private $province = null;

    /** @var string */
    private $district = null;

    /** @var string */
    private $area = null;

    public function __construct(string $number, string $type, string $operator, array $location = null)
    {
        $this->number = $number;
        $this->type = $type;
        $this->operator = $operator;

        if ($location !== null) {
            $this->province = $location['province'];
            $this->district = $location['district'];
            $this->area = $location['area'];
        }
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function getProvince(): string
    {
        $this->checkLocationDetails();

        return $this->province;
    }

    public function getDistrict(): string
    {
        $this->checkLocationDetails();

        return $this->district;
    }

    public function getArea(): string
    {
        $this->checkLocationDetails();

        return $this->area;
    }

    private function checkLocationDetails(): void
    {
        if ($this->province === null) {
            throw new \LogicException('Can not read location details form mobile number');
        }
    }
}
