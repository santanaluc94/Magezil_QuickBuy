<?php

namespace Magezil\QuickBuy\Controller\BuyProduct;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Message\ManagerInterface;
use Magento\Checkout\Model\Session as CheckoutSession;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Redirect;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

    public function __construct(
        Context $context
    ) {
        $this->obj = \Magento\Framework\App\ObjectManager::getInstance();
        $this->messageManager = $this->obj->get(ManagerInterface::class);
        $this->checkoutSession = $this->obj->get(CheckoutSession::class);
        parent::__construct($context);
    }

    public function execute(): Redirect
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        // Check has product in cart
        if ($this->checkoutSession->hasQuote()) {

        }

        // Check has payment method
        // Check has shipping address
        // Check has billing address
        // Buy Product

        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
