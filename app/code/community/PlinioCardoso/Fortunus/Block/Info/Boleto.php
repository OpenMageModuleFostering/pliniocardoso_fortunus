<?php
/**
 * Class PlinioCardoso_Fortunus_Block_Info_Boleto
 * @author Plínio Cardoso <plynyo@gmail.com.br>
 * @copyright 2014 - Plínio Cardoso
 */
class PlinioCardoso_Fortunus_Block_Info_Boleto extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('pliniocardoso_fortunus/info/boleto.phtml');
    }
}