<?php
/*
 * @category  Mageho
 * @package   Mageho_All
 * @copyright Copyright (c) 2012 - 2013  Mageho (http://www.mageho.com)
 * @license     http://www.mageho.com/license  Proprietary License
 */

class Mageho_All_Block_System_Config_Form_Fieldset_Extensions extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
	protected $_dummyElement;
	protected $_fieldRenderer;
	protected $_values;

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
		$html = $this->_getHeaderHtml($element);
		$modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
		sort($modules);

        foreach ($modules as $moduleName) {
        	if (strstr($moduleName,'Mageho_') === false) {
        		continue;
        	}
			if ($moduleName == 'Mageho_All') {
				continue;
			}
			
        	$html.= $this->_getFieldHtml($element, $moduleName);
        }

        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    protected function _getDummyElement()
    {
    	if (empty($this->_dummyElement)) {
    		$this->_dummyElement = new Varien_Object(array('show_in_default' => 1, 'show_in_website' => 1));
    	}
    	return $this->_dummyElement;
    }

    protected function _getFieldRenderer()
    {
    	if (empty($this->_fieldRenderer)) {
    		$this->_fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
    	}
    	return $this->_fieldRenderer;
    }

	protected function _getFieldHtml($fieldset, $moduleName)
    {
		$name = strtolower($moduleName);
		$version = Mage::getConfig()->getModuleConfig($moduleName)->version;
		
		$field = $fieldset->addField($name, 'label',
            array(
                'name'          => $name,
                'label'           => $moduleName,
				'value'			=> $version
            ))->setRenderer($this->_getFieldRenderer());

		return $field->toHtml();
    }
    
    protected function _convertVersion($v)
	{
		$digits = @explode('.', $v);
		$version = 0;
		
		if (is_array($digits)) {
			foreach($digits as $k=>$v) {
				$version += ($v * pow(10, max(0, (3-$k))));
			}
		}
		return $version;
	}
}