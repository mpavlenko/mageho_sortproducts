<?php
/*
 * @category   Mageho
 * @package    Mageho_Sortproduct
 * @version    1.0.0
 * @copyright  Copyright (c) 2012  Mageho (http://www.mageho.com)
 * @license    http://www.mageho.com/license  Proprietary License
 */
 
#class Mageho_Sortproducts_Model_Mysql4_Position extends Mage_Catalog_Model_Resource_Eav_Mysql4_Category
class Mageho_Sortproducts_Model_Resource_Position extends Mage_Catalog_Model_Resource_Category
{
    /**
     * Initialize resource
     */
    public function __construct()
    {
        parent::__construct();
        $resource = Mage::getSingleton('core/resource');
        $this->setType('catalog_category')
		    ->setConnection(
                $resource->getConnection('catalog_read'),
                $resource->getConnection('catalog_write')
            );
			
        $this->_productCategoryTable = $resource->getTableName('catalog/category_product');
        $this->_productCategoryIndexTable = $resource->getTableName('catalog/category_product_index');
    }

    public function save($position, $productId, $categoryId)
	{
		$condition = array(
			$this->_getWriteAdapter()->quoteInto("product_id=?", $productId),
			$this->_getWriteAdapter()->quoteInto("category_id=?", $categoryId)
		);
		
        $db = $this->_getWriteAdapter();
		$db->update($this->_productCategoryTable, array('position' => $position), $condition);
		$db->update($this->_productCategoryIndexTable, array('position' => $position), $condition);
			
		return $this;
    }
}