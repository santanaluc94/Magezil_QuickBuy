<?php

namespace Magezil\QuickBuy\Block\Product;

use Magento\Framework\View\Element\Template\Context;
use Magezil\QuickBuy\Model\Config\Settings;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

use Magento\Catalog\Model\Product;

class QuickBuyButton extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        Context $context,
        array $data = []
    ) {
        $this->obj = \Magento\Framework\App\ObjectManager::getInstance();
        $this->quickBuyConfig = $this->obj->get(Settings::class);
        $this->registry = $this->obj->get(Registry::class);
        $this->storeManager = $this->obj->get(StoreManagerInterface::class);
        parent::__construct($context, $data);
    }

    public function getCurrentProduct(): Product
    {
        return $this->registry->registry('current_product');
    }

    public function getQuickBuyConfig(): Settings
    {
        return $this->quickBuyConfig;
    }

    public function getUrlQuickBuy(): string
    {
        return $this->storeManager->getStore()->getUrl('/') . 'quickBuy/buyProduct/index?product_id=' . $this->getCurrentProduct()->getId();
    }
}
