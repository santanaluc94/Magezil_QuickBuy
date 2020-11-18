<?php

namespace Magezil\QuickBuy\Block\Listing;

class QuickBuyButton extends \Magento\Framework\View\Element\Template
{
    public function __construct()
    {
        $this->obj = \Magento\Framework\App\ObjectManager::getInstance();
    }

    public function getCurrentProduct()
    {
    }
}
