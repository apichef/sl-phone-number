<?php

namespace APiChef\SlPhoneNumber;

class PhoneNumber
{
    const TYPE_MOBILE = 'mobile';
    const TYPE_FIXED = 'fixed';

    private $areaCodes = [
        '011' => ['province' => 'Western', 'district' => 'Colombo', 'area' => 'Colombo'],
        '021' => ['province' => 'Northern', 'district' => 'Jaffna', 'area' => 'Jaffna'],
        '023' => ['province' => 'Northern', 'district' => 'Mannar', 'area' => 'Mannar'],
        '024' => ['province' => 'Northern', 'district' => 'Vavuniya', 'area' => 'Vavuniya'],
        '025' => ['province' => 'North Central', 'district' => 'Anuradhapura', 'area' => 'Anuradhapura'],
        '026' => ['province' => 'Eastern', 'district' => 'Trincomalee', 'area' => 'Trincomalee'],
        '027' => ['province' => 'North Central', 'district' => 'Polonnaruwa', 'area' => 'Polonnaruwa'],
        '031' => ['province' => 'Western', 'district' => 'Gampaha', 'area' => 'Negombo'],
        '032' => ['province' => 'North Western', 'district' => 'Puttalam', 'area' => 'Chilaw'],
        '033' => ['province' => 'Western', 'district' => 'Gampaha', 'area' => 'Gampaha'],
        '034' => ['province' => 'Western', 'district' => 'Kalutara', 'area' => 'Kalutara'],
        '035' => ['province' => 'Sabaragamuwa', 'district' => 'Kegalle', 'area' => 'Kegalle'],
        '036' => [
            'province' => 'Western,Sabaragamuwa',
            'district' => 'Colombo,Rathnapura,Kegalle',
            'area' => 'Avissawella'
        ],
        '037' => ['province' => 'North Western', 'district' => 'Kurunegala', 'area' => 'Kurunegala'],
        '038' => ['province' => 'Western', 'district' => 'Kalutara', 'area' => 'Panadura'],
        '041' => ['province' => 'Southern', 'district' => 'Matara', 'area' => 'Matara'],
        '045' => ['province' => 'Sabaragamuwa', 'district' => 'Ratnapura', 'area' => 'Ratnapura'],
        '047' => ['province' => 'Southern', 'district' => 'Hambantota', 'area' => 'Hambantota'],
        '051' => ['province' => 'Central', 'district' => 'Nuwara Eliya', 'area' => 'Hatton'],
        '052' => ['province' => 'Central', 'district' => 'Nuwara Eliya', 'area' => 'Nuwara Eliya'],
        '054' => ['province' => 'Central', 'district' => 'Kandy', 'area' => 'Nawalapitiya'],
        '055' => ['province' => 'Uva', 'district' => 'Monaragala,Badulla', 'area' => 'Monaragala,Badulla'],
        '057' => ['province' => 'Uva', 'district' => 'Badulla', 'area' => 'Bandarawela'],
        '063' => ['province' => 'Eastern', 'district' => 'Ampara', 'area' => 'Ampara'],
        '065' => ['province' => 'Eastern', 'district' => 'Batticaloa', 'area' => 'Batticaloa'],
        '066' => ['province' => 'Central', 'district' => 'Matale', 'area' => 'Matale'],
        '067' => ['province' => 'Eastern', 'district' => 'Ampara', 'area' => 'Kalmunai'],
        '081' => ['province' => 'Central', 'district' => 'Kandy', 'area' => 'Kandy'],
        '091' => ['province' => 'Southern', 'district' => 'Galle', 'area' => 'Galle'],
    ];

    private $operatorCodes = [
        '0' => ['name' => 'Lanka Bell', 'type' => 'Fixed LTE'],
        '2' => ['name' => 'Sri Lanka Telecom', 'type' => 'Fixed Fibre or Copper'],
        '3' => ['name' => 'Sri Lanka Telecom', 'type' => 'Fixed CDMA or LTE'],
        '4' => ['name' => 'Dialog', 'type' => 'Fixed LTE'],
        '5' => ['name' => 'Lanka Bell', 'type' => 'Fixed CDMA'],
        '7' => ['name' => 'Dialog', 'type' => 'Fixed LTE'],
        '9' => ['name' => 'Tritel', 'type' => 'Public Payphones'],
    ];

    private $mobileOperatorCodes = [
        '070' => ['name' => 'Mobitel'],
        '071' => ['name' => 'Mobitel'],
        '072' => ['name' => 'Hutch'],
        '074' => ['name' => 'Dialog'],
        '075' => ['name' => 'Airtel'],
        '076' => ['name' => 'Dialog'],
        '077' => ['name' => 'Dialog'],
        '078' => ['name' => 'Hutch'],
    ];

    /** @var string */
    private $number;

    public function __construct(string $number)
    {
        $this->number = $number;
    }

    public function getData(): array
    {
        if ($this->isFormatValid()) {
            $number = $this->toLocalFormat();
            $data = [
                'number' => $number,
            ];
            $prefix  = substr($number, 0, 3);

            if (array_key_exists($prefix, $this->mobileOperatorCodes)) {
                $data['type'] = self::TYPE_MOBILE;
                $data['operator'] = $this->mobileOperatorCodes[$prefix];

                return $data;
            }

            if (array_key_exists($prefix, $this->areaCodes)) {
                $operatorCode = substr($number, 3, 1);
                if (array_key_exists($operatorCode, $this->operatorCodes)) {
                    $data['type'] = self::TYPE_FIXED;
                    $data['operator'] = $this->operatorCodes[$operatorCode];
                    $data['location'] = $this->areaCodes[$prefix];

                    return $data;
                }
            }
        }

        throw new \InvalidArgumentException('Invalid phone number.');
    }

    public function isValid(): bool
    {
        try {
            $this->getData();

            return true;
        } catch (\InvalidArgumentException $exception) {
            return false;
        }
    }

    private function isFormatValid(): bool
    {
        return preg_match('/^0[0-9]{9}$/', $this->number) ||
            preg_match('/^\+94[0-9]{9}$/', $this->number) ||
            preg_match('/^0094[0-9]{9}$/', $this->number);
    }

    private function toLocalFormat(): string
    {
        $number = $this->number;

        if (substr($this->number, 0, 3) === '+94') {
            $number = '0' . substr($this->number, 3);
        }

        if (substr($this->number, 0, 4) === '0094') {
            $number = '0' . substr($this->number, 4);
        }

        return $number;
    }
}
