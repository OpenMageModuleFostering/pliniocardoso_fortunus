<?php
/**
 * Class PlinioCardoso_Fortunus_Block_Callback
 * @author Plínio Cardoso <plynyo@gmail.com.br>
 * @copyright 2014 - Plínio Cardoso
 */
class PlinioCardoso_Fortunus_Block_Callback extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $url_callback = Mage::getBaseUrl().'fortunus/notification/';
        return $url_callback;
    }
}