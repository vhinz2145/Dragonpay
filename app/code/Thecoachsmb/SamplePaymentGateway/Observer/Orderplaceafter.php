<?php

namespace Thecoachsmb\SamplePaymentGateway\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class Orderplaceafter implements ObserverInterface
{
    protected $_responseFactory;
    protected $_url;

    public function __construct(
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {    
        $url = 'https://gw.dragonpay.ph/Pay.aspx?procid=GCSH&';

        $url_parts = parse_url($url);
        // If URL doesn't have a query string.
        if (isset($url_parts['query'])) { // Avoid 'Undefined index: query'
        parse_str($url_parts['query'], $params);
        } else 
        {
        $params = array(
            'merchantid' => COMWORKS,
            'txnid' => $order->getOrderIncrementId(),
            'amount' => '1',
            'ccy' => $order->getCurrencyCode(),
            'description' => 'My order description.',
            'email' => $address->getEmail()
        );
        }
            $this->_redirect($url,$params);
        }}
        ?>
        