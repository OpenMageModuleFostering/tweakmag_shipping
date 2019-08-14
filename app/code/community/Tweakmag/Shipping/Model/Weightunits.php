<?php
/**
 * Magento Tweakmag Shipping Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Tweakmag
 * @package    Tweakmag_Shipping
 * @copyright  Copyright (c) 2008 Tweakmag (http://www.tweakmag.com)
 * @author	   Adam Martin (adam.martin@tweakmag.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Tweakmag_Shipping_Model_Weightunits
{

    public function toOptionArray()
    {
        return array(
            array('value'=>1000, 'label'=>Mage::helper('adminhtml')->__('Grams')),
            array('value'=>1, 'label'=>Mage::helper('adminhtml')->__('Kilograms')),
        );
    }

}