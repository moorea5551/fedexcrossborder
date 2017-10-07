<?php

namespace FedExCrossBorder\Checkout\Entity;

use FedExCrossBorder\Common\BaseModel;

/**
 * Class Country
 *
 * @package FedExCrossBorder\Checkout\Entity
 *
 * @property string $country_code
 * @property string $country_name
 * @property string $country_phone_code
 */
class Country extends BaseModel
{
    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->country_code;
    }

    /**
     * @param string $country_code
     * @return $this
     */
    public function setCountryCode($country_code)
    {
        $this->country_code = $country_code;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountryName()
    {
        return $this->country_name;
    }

    /**
     * @param string $country_name
     * @return $this
     */
    public function setCountryName($country_name)
    {
        $this->country_name = $country_name;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountryPhoneCode()
    {
        return $this->country_phone_code;
    }

    /**
     * @param string $country_phone_code
     * @return $this
     */
    public function setCountryPhoneCode($country_phone_code)
    {
        $this->country_phone_code = $country_phone_code;

        return $this;
    }
}
