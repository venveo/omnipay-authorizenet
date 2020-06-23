<?php
/**
 * AuthorizeNet Item
 */

namespace Omnipay\AuthorizeNet;

use Omnipay\Common\Item;

class AuthorizeNetItem extends Item
{
    public function getItemId()
    {
        return $this->getParameter('itemId');
    }

    public function setItemId($value)
    {
        return $this->setParameter('itemId', $value);
    }

    public function getTaxable()
    {
        return $this->getParameter('taxable');
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setTaxable($value)
    {
        return $this->setParameter('taxable', $value);
    }
}
