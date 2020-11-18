<?php

namespace Magezil\QuickBuy\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Settings
{
    const MODULE_ENABLE = 'magezil_quick_buy/general/enable';
    const BUTTON_LABEL = 'magezil_quick_buy/general/label';
    const ENABLE_IN_PRODUCT_PAGE = 'magezil_quick_buy/general/current_product';
    const ENABLE_IN_LISTING_PRODUCT = 'magezil_quick_buy/general/endpoint';

    protected $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::MODULE_ENABLE, ScopeInterface::SCOPE_WEBSITE);
    }

    public function hasButtonLabel(): bool
    {
        return $this->scopeConfig->isSetFlag(self::BUTTON_LABEL, ScopeInterface::SCOPE_WEBSITE);
    }

    public function getButtonLabel(): string
    {
        return $this->scopeConfig->getValue(self::BUTTON_LABEL, ScopeInterface::SCOPE_WEBSITE);
    }

    public function isEnableInProductPage(): bool
    {
        return $this->scopeConfig->isSetFlag(self::ENABLE_IN_PRODUCT_PAGE, ScopeInterface::SCOPE_WEBSITE);
    }

    public function isEnableInListingProduct(): bool
    {
        return $this->scopeConfig->isSetFlag(self::ENABLE_IN_LISTING_PRODUCT, ScopeInterface::SCOPE_WEBSITE);
    }
}
