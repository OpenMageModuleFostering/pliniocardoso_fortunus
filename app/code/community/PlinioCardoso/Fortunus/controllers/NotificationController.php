<?php
/**
 * Class PlinioCardoso_Fortunus_NotificationController
 * @author Plínio Cardoso <plynyo@gmail.com.br>
 * @copyright 2014 - Plínio Cardoso
 */
class PlinioCardoso_Fortunus_NotificationController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        if($this->getRequest()->isPost() && $this->getRequest()->getParam('xml')){
            $objXml       = simplexml_load_string($this->getRequest()->getParam('xml'));
            $numeroPedido = $objXml->cobranca->retorno;
            $status       = $objXml->cobranca->status;

            $order = Mage::getModel('sales/order')->loadByIncrementId($numeroPedido);

            // Cria fatura caso seja status finalizado ou confirmado
            if(in_array($status,array('p','f'))){
                $this->generateInvoice($order);
            }

            // Cancela pedido caso o boleto seja cancelado
            if($status == 'c' && $order->canCancel()){
                $order->cancel()
                    ->addStatusHistoryComment('Boleto cancelado via Fortunus')
                    ->save();
            }
        }
        return;
    }

    protected function generateInvoice($order)
    {
        if ($order->canInvoice()) {
            $invoice = $order->prepareInvoice();
            $invoice->register()->pay();
            $invoice->addComment('Fatura criada via Fortunus');
            //$invoice->sendEmail(Mage::getStoreConfigFlag('payment/pagseguro/send_invoice_email'),'Pagamento recebido com sucesso.');

            $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, 'processing', 'Pagamento autorizado e capturado com sucesso', true);
            $order->sendOrderUpdateEmail(true, '');

            Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();

            $order->addStatusHistoryComment('Fatura criada via Fortunus');
            $order->save();
        }

        return;
    }
}
