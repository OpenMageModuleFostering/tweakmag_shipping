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
 * @subpackage Australia Post
 * @copyright  Copyright (c) 2008 Tweakmag (http://www.tweakmag.com)
 * @author	   Adam Martin (adam.martin@tweakmag.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

//thanks to Chris Norton from Fontis IT (www.fontis.com.au) for posting the initial code for the Australia Post Shippping
//


class Tweakmag_Shipping_Model_Carrier_Australiapost
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{

    protected $_code = 'australiapost';

    /**
     * Collects the shipping rates for Australia Post from the DRC API.
     *
     * @param Mage_Shipping_Model_Rate_Request $data
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
    	// Check if this method is active
		if (!$this->getConfigFlag('active'))
		{
			return false;
		}

		// Check if this method is even applicable (must ship from Australia)
		$origCountry = Mage::getStoreConfig('shipping/origin/country_id', $this->getStore());
		if ($origCountry != "AU"){
			return false;
		}

		//check if cart order value falls between the minimum and maximum order amounts requred
		$packagevalue = $request->getBaseCurrency()->convert($request->getPackageValue(), $request->getPackageCurrency());
		$minorderval = $this->getConfigData('min_order_value');
		$maxorderval = $this->getConfigData('max_order_value');
		if($packagevalue <= $minorderval || $packagevalue >= $maxorderval){
			return false;
		}


		$result = Mage::getModel('shipping/rate_result');

		$frompcode = Mage::getStoreConfig('shipping/origin/postcode', $this->getStore());
		$topcode = $request->getDestPostcode();

		if ($request->getDestCountryId())
		{
			$destCountry = $request->getDestCountryId();
		}
		else
		{
			$destCountry = "AU";
		}

		//get weight units (kgs,grams)
		$sweightunit = $this->getConfigData('weight_units');

		$sweight = $request->getPackageWeight()*$sweightunit;
		$sheight = $swidth = $slength = 100; //@todo - need to set up configurable attributes for these
		$shipping_num_boxes = 1; //@todo - put option into shipping method to define whether to ship individually or as 1 unit

		// Switch between domestic and international shipping methods based
		// on destination country.
		if($destCountry == "AU")
		{
			$shipping_methods = array("STANDARD", "EXPRESS", "ECONOMY");
		}
		else
		{
			$shipping_methods = array("SEA", "AIR", "ECONOMY");
		}

        foreach($shipping_methods as $shipping_method)
        {
	        $url = "http://drc.edeliver.com.au/ratecalc.asp?" .
	        	"Pickup_Postcode=" . $frompcode .
	        	"&Destination_Postcode=" . $topcode .
	        	"&Country=" . $destCountry .
	        	"&Weight=" . $sweight .
	        	"&Service_Type=" . $shipping_method .
	        	"&Height=" . $sheight .
	        	"&Width=" . $swidth .
	        	"&Length=" . $slength .
	        	"&Quantity=" . $shipping_num_boxes;

			$answer = file($url);
			foreach($answer as $vals)
			{

					$tokens = split("=", $vals);
					$$tokens[0] = $tokens[1];
			}

			$method = Mage::getModel('shipping/rate_result_method');
			if(trim($err_msg) == "OK"){

		        $shippingPrice = $request->getBaseCurrency()->convert($charge, $request->getPackageCurrency());

		        $shippingPrice += $this->getConfigData('handling_fee');

	            $method->setCarrier('australiapost');
	            $method->setCarrierTitle($this->getConfigData('title'));

	            $method->setMethod($shipping_method);
	            if($days == 1){
	            	$method->setMethodTitle($this->getConfigData('title') . " $shipping_method ($days day)");
	            } else{
	            	$method->setMethodTitle($this->getConfigData('title') . " $shipping_method ($days days)");
	            }


	            $method->setPrice($shippingPrice);
	            $method->setCost($shippingPrice);
	            $result->append($method);
			}
		}

        return $result;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return array('australiapost' => $this->getConfigData('name'));
    }

}
