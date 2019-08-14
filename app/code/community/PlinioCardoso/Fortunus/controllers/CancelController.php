<?php
/**
 * Class PlinioCardoso_Fortunus_CancelController
 * @author Plínio Cardoso <plynyo@gmail.com.br>
 * @copyright 2014 - Plínio Cardoso
 */
class PlinioCardoso_Fortunus_CancelController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        if(!$this->getRequest()->isGet() || !$this->getRequest()->getParam('key_billing') || !$this->getRequest()->getParam('order_id')){
            $this->_redirectReferer(true);
            return;
        }

        $params['Chave']   = $this->getRequest()->getParam('key_billing');
        $params['Token'] = Mage::helper('pliniocardoso_fortunus')->getToken();

        $request = Mage::getModel('pliniocardoso_fortunus/payment_boleto')->callApi($params,'cancel','CancelarCobranca');

        if(isset($request->StatusCod) && $request->StatusCod == 2){
            $order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id'));

            if($order->canCancel()){
                $order->cancel()->save();
            }

            Mage::getSingleton('adminhtml/session')->addSuccess('Boleto cancelado com sucesso');
        } else {
            Mage::getSingleton('adminhtml/session   ')->addError('Erro ao cancelar boleto');
        }

        $this->_redirectReferer(true);
        return;
    }
}
