<?php

namespace FedExCrossBorder\Checkout\Entity;

use FedExCrossBorder\Common\BaseModel;

/**
 * Class AvailableLanguages
 *
 * @package FedExCrossBorder\Checkout\Entity
 *
 * @property string $confirmation_uri
 * @property boolean $merchant_direct
 */
class MerchantAttributes extends BaseModel
{
    /**
     * @return string
     */
    public function getConfirmationUri()
    {
        return $this->confirmation_uri;
    }

    /**
     * @param string $confirmation_uri
     * @return $this
     */
    public function setConfirmationUri($confirmation_uri)
    {
        $this->confirmation_uri = $confirmation_uri;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isMerchantDirect()
    {
        return $this->merchant_direct;
    }

    /**
     * @param boolean $merchant_direct
     * @return $this
     */
    public function setMerchantDirect($merchant_direct)
    {
        $this->merchant_direct = $merchant_direct;

        return $this;
    }
}
