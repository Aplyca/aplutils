<?php


/**
 * This file contains the definition for AplContentHandler class
 *  
 */

/**
 *  @author David Sánchez Escobar
 *  @desc Operation toolbox for location tasks  
 *   
 */

class AplLocationOperations
{
	const MISSED_COUNTRY = 1;
	const MISSED_REGION = 2;
	const MISSED_CITY = 3;
	const MISSED_ZIP = 4;
	const NOT_CITY_ON_COUNTRY = 5;
	const NOT_CITY_ON_STATE = 6;
	const NOT_ZIP_ON_CITY = 7;
	const OK = 0;
	

	static function validateLocation($locationData, $strict=false)
	{						
		$locationCityParts = explode("-", $locationData['city']); 
		if(count($locationCityParts) == 2)
		{
			$locationData['city'] =  $locationCityParts[0];
			$locationData['cityaliasname'] =  $locationCityParts[1];
		}
		
		$resultHier = self::setLocationHier($locationData, $strict);		
		if($resultHier['result'] != self::OK)
			return $resultHier['result'];
		$locationHier = $resultHier['data'];						
		if($strict)
		{	
			if($locationData['country'] == 'US')
			{				
				if(! UsaZIPCodePO::isCityOnState($locationData['region'], $locationData['city']))
				{
					return self::NOT_CITY_ON_STATE;
				}				
				if(!UsaZIPCodePO::isZipOnCity($locationData['city'], $locationData['zip_code']))
				{
					return self::NOT_ZIP_ON_CITY;
				}				
			}	
			else
			{
				$cityCheck = CityPO::cityExistsOnCountry($locationData['country'], $locationData['city'], $locationData['cityaliasname']);						
				if(!$cityCheck)
					return self::NOT_CITY_ON_COUNTRY;
			}
			return self::OK;		
		}	
	}
	
	static function setLocationHier($locationData, $strict)
	{
		$locationHier = array();
		$hierCount = 0;
		$result = self::OK;
		if($locationData['country'])
		{
			$locationHier[$hierCount] = 'country';
			$hierCount++;
		}
		else
		{
			if($strict) $result =  self::MISSED_COUNTRY;
		}
		if($locationData['region'])
		{
			$locationHier[$hierCount] = 'region';
			$hierCount++;
			
		}
		else
		{
			if($locationData['country'] == 'US')
			{
				if($strict) $result =  self::MISSED_REGION;
			}
		}
		if($locationData['city'])
		{
			$locationHier[$hierCount] = 'city';
			$hierCount++;
			
		}
		else
		{
			if($strict) $result =  self::MISSED_CITY;
		}
		if($locationData['zip_code'])
		{
			$locationHier[$hierCount] = 'zip_code';
			$hierCount++;
			
		}	
		else
		{
			if($locationData['country'] == 'US')
			{
				if($strict) $result =  self::MISSED_ZIP;	
			}			
		}
		return array ('result' => $result, 'data' => $locationHier);
	}
	
	
	static function getResponseMessage($responseCode)
    {
    	if($responseCode == self::MISSED_COUNTRY)
    	{
    		return "Country is not set";
    	}
    	elseif($responseCode == self::MISSED_REGION)
    	{
    		return "Region/State/Province is not set";
    	}
    	elseif($responseCode == self::MISSED_CITY)
    	{
    		return "City is not set";
    	}
    	elseif($responseCode == self::MISSED_ZIP)
    	{
    		return "Zip code is not set";
    	}
    	elseif($responseCode == self::NOT_CITY_ON_COUNTRY)
    	{
    		return "The city doesn't exist in the country";
    	}
    	elseif($responseCode == self::NOT_CITY_ON_STATE)
    	{
    		return "The city doesn't exist in the state";
    	}
    	elseif($responseCode == self::NOT_ZIP_ON_CITY)
    	{
    		return "The zip code is not related with the city";
    	}
    	elseif($responseCode == self::OK)
    	{
    		return "Ok";
    	}    	
    	else
    	{
    		return "Unhandled location error";
    	}
    }
    
    static function getZipCodesInRange ( $zipCode, $range )
    {    	
    	$usaZIPCodePO = UsaZIPCodePO::fetch( $zipCode );
    	
    	if ( $usaZIPCodePO instanceof UsaZIPCodePO )
    	{    	
	    	$zipCodesByRange = array();
    		$latRange = $range/69.172;
	    	$lonRange = abs($range/(cos($usaZIPCodePO->Latitude) * 69.172));
	    	
	    	$minLat = number_format($usaZIPCodePO->Latitude - $latRange, "4", ".", "");
	    	$maxLat = number_format($usaZIPCodePO->Latitude + $latRange, "4", ".", "");
	    	$minLon = number_format($usaZIPCodePO->Longitude - $lonRange, "4", ".", "");
	    	$maxLon = number_format($usaZIPCodePO->Longitude + $lonRange, "4", ".", "");
	    	
	    	$resultZipCodesByRange = UsaZIPCodePO::getZipCodesByRange( $minLat, $maxLat, $minLon, $maxLon );
	    		    		    	
	    	foreach ( $resultZipCodesByRange as $resultZipCodeByRange )
	    	{	
	    		foreach ( $resultZipCodeByRange as $key => $item )
	    		{	
	    			if ( $key == 'zipcode' )
	    			{
	    				$zipCodesByRange[] = $item;
	    			}
	    		}
	    	}
	    	
	    	return $zipCodesByRange;
    	}
    	else
    	{
    		return false;
    	}
    	
    }
    
	static function getZipCodeByCityName ( $cityName, $stateCode = '' )
    {
    	$attributes = array();
    	       	
    	if ($stateCode)
    	{
    		$attributes['State'] = $stateCode;
    	}
    	
    	if ($cityName)
    	{
    		$attributes['City'] = $cityName;	
    	} 

    	if( !$attributes )
    		return false;
    	    	
    	$usaZIPCodePO = UsaZIPCodePO::fetchByAttributes( $attributes );
    	
    	if ( $usaZIPCodePO instanceof UsaZIPCodePO )
    	{
    		return $usaZIPCodePO;
    	}
    	else
    		return false;
    }

    static function getZipCodesByCityName ( $cityName, $stateCode = '' )
    {
        $attributes = array();
         
        if ($stateCode)
        {
            $attributes['State'] = $stateCode;
        }
         
        if ($cityName)
        {
            $attributes['City'] = $cityName;
        }
    
        if( $attributes )
        {
            return  UsaZIPCodePO::fetchListByAttributes( $attributes );
        }
        
        return false;
    }    
    
    static function getLocationFromZip($zip)
    {
    	$zip = self::addZeros($zip, 5);
    	return UsaZIPCodePO::fetchListByZIP($zip);    	
    }
    
	/**
	 * Adds anteposed zeros to a string
	 * @param string string the string to be modified with anteposed zeros
	 * @param int requiredSize the required size of the string after filling with anteposed zeros
	 * @return the modified string
	 */	
	static function addZeros($string, $requiredSize)
	{
		$forcedString = $string .'';
		$zerosToAdd = $requiredSize - strlen($forcedString); 
		$zeroString = '';
		for($k=0; $k < $zerosToAdd; $k++)
		{
			$zeroString .= '0';
		}
		return  $zeroString . $forcedString;
	}
}

?>