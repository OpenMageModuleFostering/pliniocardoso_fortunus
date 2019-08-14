<?php
/**
 * Class PlinioCardoso_Fortunus_Helper_Data
 * @author Plínio Cardoso <plynyo@gmail.com.br>
 * @copyright 2014 - Plínio Cardoso
 */
class PlinioCardoso_Fortunus_Helper_Data extends Mage_Core_Helper_Abstract {

    const XML_PATH_PAYMENT_FORTUNUS_TOKEN = 'payment/fortunus_boleto/token';

    public function getApiCallParams($order){
        $config          = array();
        $config['token'] = $this->getToken();

        // Customer data
        $config['clientes']['cliente']['nomeRazaoSocial']      = $this->removeCaracter($order->getCustomerName());
        $config['clientes']['cliente']['cpfcnpj']              = $order->getCustomerTaxvat();
        $config['clientes']['cliente']['cel']                  = $order->getBillingAddress()->getTelephone();
        $config['clientes']['cliente']['opcionais']['retorno'] = $order->getIncrementId();

        // Get Items
        $config['itens'] = $this->getItems($order);

        // Get discount amount
        $config['opcionais']['descontoSobreTotal'] = $this->getDiscount($order);

        // Get shipping amount
        $config['opcionais']['frete'] = $this->getShippingAmount($order);

        return $config;
    }

    public function getDiscount($order){
        $value = 0;

        $discount = $order->getSubtotal() - ($order->getGrandTotal()-$order->getShippingAmount());

        if($discount > 0)
            $value = number_format($discount*1,2,'','');

        return $value;
    }

    public function getShippingAmount($order){
        $value = 0;

        if($order->getShippingAmount() > 0)
            $value = number_format($order->getShippingAmount()*1,2,'','');

        return $value;
    }

    public function getItems($order){
        $items         = '';
        $ordered_items = $order->getAllItems();

        foreach($ordered_items as $ordered_item){
            $item['descricao'] = $ordered_item->getName().'('.$ordered_item->getSku().')';
            $item['valor']     = number_format($ordered_item->getPrice(),2,'','');
            $item['qtde']      = $ordered_item->getQtyOrdered();

            // Gera o XML dos itens e insere no array e retira o header do XML
            $items .= str_replace('<?xml version="1.0" encoding="utf-8"?>','',$this->arrayToXml($item,'item'));
        }

        return $items;
    }

    public function getToken(){
        return Mage::getStoreConfig(self::XML_PATH_PAYMENT_FORTUNUS_TOKEN);
    }

    public function arrayToXml($data, $rootNodeName = 'root', $xml = null) {
        if ($xml == null) {
            $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
        }

        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                $key = "unknownNode_" . (string) $key;
            }

            $key = preg_replace('/[^a-z]/i', '', $key);

            if (is_array($value)) {
                $node = $xml->addChild($key);
                $this->arrayToXml($value, $rootNodeName, $node);
            } else {
                $value = htmlentities($value);
                $xml->addChild($key, $value);
            }
        }

        return $this->processingLtGtItem($xml->asXML());
    }

    public function processingLtGtItem($string){
        $string = str_replace('&lt;','<',$string);
        $string = str_replace('&gt;','>',$string);

        return $string;
    }

    public function removeCaracter($string) {
        $string = strtr(
            $string
            ,array (
                'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
                'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
                'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ð' => 'D', 'Ñ' => 'N',
                'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O',
                'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Ŕ' => 'R',
                'Þ' => 's', 'ß' => 'B', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a',
                'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
                'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
                'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
                'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y',
                'þ' => 'b', 'ÿ' => 'y', 'ŕ' => 'r'
            )
        );

        return $string;
    }
}