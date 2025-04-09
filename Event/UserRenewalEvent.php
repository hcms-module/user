<?php

namespace App\Application\User\Event;

use App\Application\Shop\Model\ShopOrder;

class UserRenewalEvent
{
    protected ShopOrder $order;

    /**
     * @param ShopOrder $order
     */
    public function __construct(ShopOrder $order) { $this->order = $order; }

    public function getOrder(): ShopOrder
    {
        return $this->order;
    }
}