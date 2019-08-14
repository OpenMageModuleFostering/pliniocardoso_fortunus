<?php
/**
 * Class PlinioCardoso_Fortunus_Model_Abstract
 * @author Plínio Cardoso <plynyo@gmail.com.br>
 * @copyright 2014 - Plínio Cardoso
 */
class PlinioCardoso_Fortunus_Model_Abstract extends Mage_Payment_Model_Method_Abstract
{
    protected $url = array(
        'generate' => array('url' => 'https://integracao.gerencianet.com.br/xml/boleto/emite/xml','param' => 'entrada'),
        'cancel'   => array('url' => 'https://fortunus.gerencianet.com.br/webservice/cancelarCobranca','param' => 'xml')
    );

    public function callApi($params, $type = 'generate', $tag_main = 'boleto')
    {
        $xml = $this->_getHelperFortunus()->arrayToXml($params,$tag_main);
        try {
            $client = new Zend_Http_Client($this->url[$type]['url']);

            $client->setMethod(Zend_Http_Client::POST);
            $client->setConfig(array('timeout'=>45));

            $client->setParameterPost(array($this->url[$type]['param'] => $xml));

            $response = $client->request();

            libxml_use_internal_errors(true);
            $response = simplexml_load_string($response->getBody());
        } catch (Mage_Core_Exception $e) {
            Mage::log('ERRO CHAMADA API FORTUNUS: '.$xml,null,'PlinioCardoso_Fortunus.log',true);
            Mage::log($e->getMessage(),null,'Pliniocardoso_Fortunus.log',true);
            $response = false;
        } catch (Exception $e) {
            Mage::log('ERRO CHAMADA API FORTUNUS',null,'PlinioCardoso_Fortunus.log',true);
            $response = false;
        }

        return $response;
    }

    public function processResult($result,$payment){
        $additional = array();

        if($result){
            $additional['statusCod'] = (string)$result->statusCod;
            $additional['status']    = (string)$result->status;
            $additional['process']   = (string)$result->processo;

            if($result->statusCod == 2){
                $charge = $result->resposta->cobrancasGeradas->cliente->cobranca;

                $additional['link'] = (string)$charge->link;
                $additional['expiration'] = (string)$charge->vencimento;
                $additional['key'] = (string)$charge->chave;
            }
        } else {
            $additional['status'] = 'Erro ao se comunicar com a API da Fortunus';
        }

        if($existing = $payment->getAdditionalInformation())
        {
            if(is_array($existing))
            {
                $additional = array_merge($additional,$existing);
            }
        }

        $payment->setAdditionalInformation($additional);

        return $this;
    }

    private function _getHelperFortunus(){
        return Mage::helper('pliniocardoso_fortunus');
    }
}