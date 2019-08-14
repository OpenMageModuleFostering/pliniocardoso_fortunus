<?php
/**
 * Class PlinioCardoso_Fortunus_TestesController
 * @author Plínio Cardoso <plynyo@gmail.com.br>
 * @copyright 2014 - Plínio Cardoso
 */
class PlinioCardoso_Fortunus_TestesController extends Mage_Core_Controller_Front_Action
{
    private function toXml($data, $rootNodeName = 'root', $xml = null) {

        if ($xml == null) {
            $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
        }

        // faz o loop no array
        foreach ($data as $key => $value) {
            // se for indice numerico ele renomeia o indice
            if (is_numeric($key)) {
                $key = "unknownNode_" . (string) $key;
            }

            // substituir qualquer coisa não alfa numérico
            $key = preg_replace('/[^a-z]/i', '', $key);


            if (is_array($value)) {
                $node = $xml->addChild($key);
                $this->toXml($value, $rootNodeName, $node);
            } else {
                $value = htmlentities($value);
                $xml->addChild($key, $value);
            }
        }
        return $xml->asXML();
    }

    public function indexAction(){
        $config = array();

        $config['token'] = '52c79eb233a27a532223e898da32b638';
        $config['clientes']['cliente']['nomeRazaoSocial'] = 'Plinio Tasdeste 03';
        $config['clientes']['cliente']['cpfcnpj'] = '94324615640';
        $config['clientes']['cliente']['cel'] = '1155554885';

        $config['itens']['item']['descricao'] = 'Produto qweqede Teste 04';
        $config['itens']['item']['valor'] = '15301';
        $config['itens']['item']['qtde'] = '1';

        $xml = $this->toXml($config,'boleto');

        $xml = '<?xml version="1.0" encoding="utf-8"?>
<boleto><token>52c79eb233a27a532223e898da32b638</token><clientes><cliente><nomeRazaoSocial>plinio Cardoso</nomeRazaoSocial><cpfcnpj>45559673480</cpfcnpj><cel>64654654654</cel><opcionais><retorno>100000061</retorno></opcionais></cliente></clientes><itens>
<item><descricao>Teste Simples 02(12312312)</descricao><valor>11100</valor><qtde>1</qtde></item>

<item><descricao>Teste Simples(12123)</descricao><valor>16656</valor><qtde>1</qtde></item>
</itens><opcionais><descontoSobreTotal>10</descontoSobreTotal><frete>10</frete></opcionais></boleto>';

        $client = new Zend_Http_Client('https://integracao.gerencianet.com.br/xml/boleto/emite/xml');

        $client->setMethod(Zend_Http_Client::POST);
        $client->setConfig(array('timeout'=>45));

        $client->setParameterPost(array('entrada' => $xml));

        $response = $client->request();

        libxml_use_internal_errors(true);

        $xml = simplexml_load_string($response->getBody());

        Zend_Debug::dump($xml);

        die();
    }
}
