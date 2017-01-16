<?php
class AplTypeValidator
{
	function __construct() 
    {
    	
    }
            
	static function validateInteger($var)
    {
    	$validator = new eZIntegerValidator();
    	$result = $validator->validate( $var );
    	if($result === 1)
    	{
    		return 1;
    	}
    	else
    	{
    		return 0;
    	}        	
    	//return $validator->validate( $var )?true:false; 
    }
    static function validateFloat($var)
    {    	
    	return is_numeric($var)?1:0;     	
    }    
    static function validateEmail($var)
    {
    	return eZMail::validate( $var )?true:false;
    }
    static function validateText($var)
    {
    	if( strlen($var) < 300)
    	{
    		return 1;
    	}
    	else
    	{
    		return 0;
    	}    
    }
    
    static function validateTextField($var)
    {    
    	$processString = $var;
    	
    	if( strlen($var) < 300)
    	{
    		$validChars = array('.', '-', '_', '#', ' ', ',','&', '/', '@', '(', ')', '!');
    		foreach($validChars as $validChar)
    		{
    			$processString = str_replace($validChar, '', $processString);
    		}
    		if(ctype_alnum($processString))
    			return 1;
    		else
    			return 0;
    	}
    	else
    	{
    		return 0;
    	}  
    }
    

    static function validatePassword($var)
    {
    	if( strlen($var) < 300)
    	{
    		return 1;
    	}
    	else
    	{
    		return 0;
    	}    
    }    
    
    static function validateCreditCard($cc_num, $type = "")
    {
	    
        $numberPatterns = array();
        $numberPatterns[] = "/^([34|37]{2})([0-9]{13})$/";      // american express
        $numberPatterns[] = "/^([30|36|38]{2})([0-9]{12})$/";   // diner's club
        $numberPatterns[] = "/^([6011]{4})([0-9]{12})$/";       // discover
        $numberPatterns[] = "/^([51|52|53|54|55]{2})([0-9]{14})$/";     // master card
        $numberPatterns[] = "/^([4]{1})([0-9]{12,15})$/";       // visa
        
	    
        $verified = false;
        
        switch ($type)
        {
            case "American":
                if (preg_match($numberPatterns[0],$cc_num)) {
	               $verified = true;
	            } else {
	               $verified = false;
	            }	            
            break;
            
            case "Diners":
                if (preg_match($numberPatterns[1],$cc_num)) {
	               $verified = true;
	            } else {
	               $verified = false;
	            }
            break;
            
            
            case "Discover":
                if (preg_match($numberPatterns[2],$cc_num)) {
	               $verified = true;
	            } else {
	               $verified = false;
	            }
            break;
            
            
            case "Master":
                if (preg_match($numberPatterns[3],$cc_num)) {
	               $verified = true;
	            } else {
	               $verified = false;
	            }
            break;
            
            case "Visa":
                if (preg_match($numberPatterns[4],$cc_num)) {
	               $verified = true;
	            } else {
	               $verified = false;
	            }
            break;
            
            
            default:
                
                foreach($numberPatterns as $pattern) {
                    if (preg_match($pattern,$cc_num)) {
	                   $verified = true;
	                   break; // no need to check others. 
	                } else {
	                   $verified = false;
	                }
                }
            break;
        }
       
        
        
	    if($verified == false) {        
	        return false;    
	    } else { //if it will pass...do something
	
	        // checking the card number with luhns algorithm
	
	        $stack = 0;
	        $number = str_split(strrev($cc_num), 1);
	        if(array_sum($number) == 0){
	            return false;
	        }    
	        foreach ($number as $key => $value)
	        {
	            if ($key % 2 != 0)
	            {            
	                $value = array_sum(str_split($value * 2, 1));
	            }
	            $stack += $value;
	        }
	
	        if($stack%10 == 0)
	        {
	            return true;
	        }
	
	        return false;
	    }
	    return false;   	
    }

}
 
?>
 