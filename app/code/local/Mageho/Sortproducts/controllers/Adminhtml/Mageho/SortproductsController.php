<?php
/*
 * @category   Mageho
 * @package    Mageho_Sortproducts
 * @version    1.0.0
 * @copyright  Copyright (c) 2012  Mageho (http://www.mageho.com)
 * @license    http://www.mageho.com/license  Proprietary License
 */
 
class Mageho_Sortproducts_Adminhtml_Mageho_SortproductsController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function tabAction()
	{
		$this->getResponse()->setBody(
	        $this->getLayout()->createBlock('mageho_sortproducts/adminhtml_tab')->toHtml()
		);	
	}
	
	public function infoAction()
	{
		$productId  = $this->getRequest()->getParam('id', false);
        $product    = Mage::getModel('catalog/product')
            ->setStoreId($this->getRequest()->getParam('store', 0));
			
		if ($productId) {
			$product->load($productId);
		
			Mage::register('product', $product);
			Mage::register('current_product', $product);
				
			$this->getResponse()->setBody(
				$this->getLayout()->createBlock('mageho_sortproducts/adminhtml_info')->toHtml()
			);
        }
	}
	
	public function saveAction()
	{
	    $data = $this->getRequest()->getPost('data');
		$categoryId = $this->getRequest()->getParam('id', false);
		
		try {
			if (! $data) {
				throw new Exception('no data post parameters');	
			}

        	if ($categoryId) {
				$category = Mage::getModel('catalog/category')
					->setStoreId($this->getRequest()->getParam('store', 0))
					->load($categoryId);
				
				if ($category->getId()) {
					parse_str($data);
					for ($i = 0; $i < count($sortlist); $i++) {
						$position = $i;
						$productId = $sortlist[$i];
							
						Mage::getResourceModel('mageho_sortproducts/position')->save($position, $productId, $category->getId());
					}
					
					Mage::app()->cleanCache(array(Mage_Catalog_Model_Category::CACHE_TAG.'_'.$category->getId()));
					
					Mage::getSingleton('adminhtml/session')->addSuccess(
						Mage::helper('mageho_sortproducts')->__('The position of products has been saved with success.')
					);
				}	
			}
		} catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}
		
		$resetUrl = Mage::helper('adminhtml')->getUrl('adminhtml/catalog_category/edit', array('active_tab_id' => 'category_info_tabs_sortproducts', '_current' => true));
		$this->getResponse()->setBody("window.top.categoryReset('{$resetUrl}', true);");
	}
	
	/**
     * Check if admin has permissions to visit categories pages
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/categories');
    }
}