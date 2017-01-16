<?php


function simpleXor($string, $Key) 
	{
	  $KeyList = array();
	  $output = "";
	  
	  // Convert $Key into array of ASCII values
	  for($i = 0; $i < strlen($Key); $i++){
	    $KeyList[$i] = ord(substr($Key, $i, 1));
	  }
	  // Step through string a character at a time
	  for($i = 0; $i < strlen($string); $i++) {
	    // Get ASCII code from string, get ASCII code from key (loop through with MOD), XOR the two, get the character from the result
	    // % is MOD (modulus), ^ is XOR
	    $output.= chr(ord(substr($string, $i, 1)) ^ ($KeyList[$i % strlen($Key)]));
	  }
	  return $output;
	}

	 function base64Encode($string) 
	{
		$output = "";
		$output = base64_encode($string);
		return $output;
	}

	 function base64Decode($scrambled) {
	  $output = "";
	  
	  // Fix plus to space conversion issue
	  $scrambled = str_replace(" ","+",$scrambled);
	  
	  // Do encoding
	  $output = base64_decode($scrambled);
	  
	  // Return the result
	  return $output;
	}		
	
	function encryptOrderId($order_id)
	{
		$key = "|||5gg6r7jhj78tyjtyj5hftgty6yy";
		
		
		$extended = $order_id . $key;
		$xored = self::simpleXor($extended, $key);
		$encrypted = self::base64Encode($xored);
		$urlencoded = urlencode ( $encrypted );
		return $urlencoded; 
	}
	
	 function decryptOrderId($urlencoded)
	{		
		$key = "|||5gg6r7jhj78tyjtyj5hftgty6yy";
		$crypted  = urldecode ($urlencoded);
		$xored = self::base64Decode($crypted);		
		$extended = self::simpleXor($xored, $key);
		$temp = explode("|||", $extended);
		$id = $temp[0];
		return $id;	
	}	       



?>