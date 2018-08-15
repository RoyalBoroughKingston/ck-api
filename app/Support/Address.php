<?php

namespace App\Support;

class Address
{
    /**
     * @var string
     */
    public $addressLine1;

    /**
     * @var string|null
     */
    public $addressLine2;

    /**
     * @var string|null
     */
    public $addressLine3;

    /**
     * @var string
     */
    public $city;

    /**
     * @var string
     */
    public $county;

    /**
     * @var string
     */
    public $postcode;

    /**
     * @var string
     */
    public $country;

    /**
     * Address constructor.
     *
     * @param $address
     * @param string $city
     * @param string $county
     * @param string $postcode
     * @param string $country
     */
    public function __construct($address, string $city, string $county, string $postcode, string $country)
    {
        $this->addressLine1 = (array)$address[0];
        $this->addressLine2 = (array)$address[1] ?? null;
        $this->addressLine3 = (array)$address[2] ?? null;
        $this->city = $city;
        $this->county = $county;
        $this->postcode = $postcode;
        $this->country = $country;
    }

    /**
     * @param $address
     * @param string $city
     * @param string $county
     * @param string $postcode
     * @param string $country
     * @return \App\Support\Address
     */
    public static function create($address, string $city, string $county, string $postcode, string $country): Address
    {
        return new static($address, $city, $county, $postcode, $country);
    }
}
