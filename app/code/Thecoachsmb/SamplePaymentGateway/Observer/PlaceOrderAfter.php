<?php

namespace Thecoachsmb\SamplePaymentGateway\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;

class PlaceOrderAfter implements ObserverInterface
{
    protected $_redirect;
    protected $_response;
    protected $_urlInterface;
    protected $orderRepository;

    public function __construct(
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\App\Response\Http $response,
        \Magento\Framework\App\ActionFlag $redirect,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\UrlInterface $url,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->_response = $response;
        $this->_redirect = $redirect;
        $this->_urlInterface = $urlInterface;
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
        $this->orderRepository = $orderRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
        {       
            $event = $observer->getEvent();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $_checkoutSession = $objectManager->create('\Magento\Checkout\Model\Session');
            $_quoteFactory = $objectManager->create('\Magento\Quote\Model\QuoteFactory');
        
            $order = $_checkoutSession->getLastRealOrder();
            $quote = $_quoteFactory->create()->loadByIdWithoutStore($order->getQuoteId());
        if  ($quote->getId()) {

            define('ENV_TEST', 0);
            define('ENV_LIVE', 1);
            $environment = ENV_LIVE;
            $errors = array();
            $is_link = false;

            $params = array(
                'merchantid' => 'COMWORKS',
                'txnid' => $order->getIncrementId(),
                'amount' => $order->getGrandTotal(),
                'ccy' => 'PHP',
                'description' => 'My order description.',
                'email' => $order->getCustomerEmail()
            );

        if (!is_numeric($params['amount'])) {
            $errors[] = 'Amount should be a number.';
            }
        else if ($params['amount'] <= 0) {
            $errors[] = 'Amount should be greater than 0.';
            }
        if (empty($errors)) {
            $params['amount'] = number_format($params['amount'], 2, '.', '');
            $params['key'] = 'P2f3w9@C';
            $digest_string = implode(':', $params);
            unset($params['key']);
            $params['digest'] = sha1($digest_string);
                    $url = 'https://gw.dragonpay.ph/Pay.aspx?procid=GCSH&';
            }
	 
        if ($environment == ENV_TEST) {
            $url = 'http://test.dragonpay.ph/Pay.aspx?procid=GCSH&';
            }
            $url .= http_build_query($params, '', '&');
        if ($is_link) {
            echo '<br><a href="' . $url . '">' . $url . '</a>';
            }
        else {
            header("Location: $url");
            }
            }
            $quote->setIsActive(1)->setReservedOrderId(null)->save();
            $_checkoutSession->replaceQuote($quote);
            $url = $this->_url->getUrl($url);
            $this->_responseFactory->create()->setRedirect($url)->sendResponse();
            die();
            
        }
    }
?>