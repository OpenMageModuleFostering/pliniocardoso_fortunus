<?php
/**
 * Class PlinioCardoso_Fortunus_Model_Payment_Boleto
 * @author Plínio Cardoso <plynyo@gmail.com.br>
 * @copyright 2014 - Plínio Cardoso
 */
class PlinioCardoso_Fortunus_Model_Payment_Boleto extends PlinioCardoso_Fortunus_Model_Abstract
{
    protected $_code = 'fortunus_boleto';
    protected $_infoBlockType = 'pliniocardoso_fortunus/info_boleto';
    protected $_isGateway = true;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canRefund = false;
    protected $_canVoid = true;
    protected $_canUseInternal = true;
    protected $_canUseCheckout = true;
    protected $_canUseForMultishipping = true;
    protected $_canSaveCc = false;

    public function assignData($data)
    {
    }


    public function validate()
    {
        parent::validate();
        return $this;
    }

    public function order(Varien_Object $payment, $amount)
    {
        $order  = $payment->getOrder();
        $params = Mage::helper('pliniocardoso_fortunus')->getApiCallParams($order, $payment);

        $result = $this->callApi($params);

        $this->processResult($result,$payment);

        return $this;
    }
}