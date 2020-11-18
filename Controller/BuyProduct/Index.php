<?php

namespace Magezil\QuickBuy\Controller\BuyProduct;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Message\ManagerInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Reports\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\Quote\Model\QuoteFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Model\QuoteRepository as QuoteRepository;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Quote\Model\Quote\AddressFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Customer\Model\CustomerFactory;

use Magento\Quote\Model\Quote;
use Magento\Customer\Model\Data\Customer;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\AuthorizationException;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

    public function __construct(
        Context $context
    ) {
        $this->obj = \Magento\Framework\App\ObjectManager::getInstance();
        $this->messageManager = $this->obj->get(ManagerInterface::class);
        $this->checkoutSession = $this->obj->get(CheckoutSession::class);
        $this->productRepository = $this->obj->get(ProductRepositoryInterface::class);
        $this->quoteCollectionFactory = $this->obj->get(QuoteCollectionFactory::class);
        $this->quoteFactory = $this->obj->get(QuoteFactory::class);
        $this->storeManager = $this->obj->get(StoreManagerInterface::class);
        $this->quoteRepository = $this->obj->get(QuoteRepository::class);
        $this->customerSession = $this->obj->get(CustomerSession::class);
        $this->customerRepository = $this->obj->get(CustomerRepositoryInterface::class);
        $this->addressRepository = $this->obj->get(AddressRepositoryInterface::class);
        $this->addressFactory = $this->obj->get(AddressFactory::class);
        $this->customerFactory = $this->obj->get(CustomerFactory::class);
        $this->productFactory = $this->obj->get(ProductFactory::class);
        parent::__construct($context);
    }

    public function execute(): Redirect
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        echo '<pre>';
        try {
            if ($this->getRequest()->getParam('product_id') === null) {
                throw new NotFoundException(__('You must pass a product id to quick buy a product.'));
            }

            $productId = $this->getRequest()->getParam('product_id');

            $product = $this->productRepository->getById($productId);

            if (!$product->isInStock() || !$product->isSaleable()) {
                throw new AuthorizationException(__('The product is invalid to buy in this moment.'));
            }

            $quote = $this->createQuote();

            $currentQuote = $this->checkoutSession->getQuote();
            $currentQuote->delete()
                ->removeAllItems()
                ->setTotalsCollectedFlag(false)
                ->collectTotals()
                ->save();

            $this->checkoutSession->replaceQuote($quote);
            var_dump(get_class_methods($this->checkoutSession));
            die;
            $quoteCollectionFactory = $this->quoteCollectionFactory->create();

            // Check has product in cart
            if ($this->checkoutSession->hasQuote()) {
                $quote = $this->quoteRepository->get($this->checkoutSession->getQuoteId());
                $quoteFactory = $this->quoteFactory->create()->load($this->checkoutSession->getQuoteId());
                $quote->setIsActive(0);
                $this->quoteRepository->save($quote);
            }
            var_dump(get_class_methods($this->checkoutSession->hasQuote()));
            die;

            // Check has payment method
            // Check has shipping address
            // Check has billing address
            // Buy Product
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }

    private function createQuote(): Quote
    {
        $customer = $this->customerRepository->getById($this->customerSession->getCustomer()->getId());
        $product = $this->productFactory->create()->load($this->getRequest()->getParam('product_id'));

        $billingAddress = $this->mapAddress($customer, (int) $customer->getDefaultBilling());
        $shippingAddress = $this->mapAddress($customer, (int) $customer->getDefaultShipping());

        $quote = $this->quoteFactory->create();
        $quote->setStore($this->getStoreManager())->assignCustomer($customer);
        $quote->addProduct($product, 1);
        $quote->getBillingAddress()->addData($billingAddress);
        $quote->getShippingAddress()->addData($shippingAddress);
        $quote->save();

        return $quote;
    }

    private function mapAddress(Customer $customer, int $addressId): array
    {
        $address = $this->addressRepository->getById($addressId);

        $addressMapped = [
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname(),
            'street' => $address->getStreet(),
            'city' => $address->getCity(),
            'country_id' => $address->getCountryId(),
            'region' => $address->getRegionId(),
            'postcode' => $address->getPostcode(),
            'telephone' => $address->getTelephone(),
            'fax' => $address->getFax(),
            'save_in_address_book' => 1
        ];

        return $addressMapped;
    }

    private function getStoreManager()
    {
        return $this->storeManager->getStore();
    }
}
