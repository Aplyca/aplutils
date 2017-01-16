<?php

/**
 *  @author David Sánchez Escobar
 *  @desc Simulates the eZHTTPTool class methods, useful when data doesn't come from POST variable
 *  and you need to pass an eZHTTPTool parameter.
 */

class AplArrayGetter
{
	
	var $data;
	
	function __construct($data)
    {
   		$this->data = $data;
    }
	
    public function hasVariable($variableName)
    {
    	$found = 0;
    	foreach($this->data as $key => $val)
    	{
    		if($key == $variableName)
    		{
    			$found = 1;
    		}    		
    	}
    	return $found;    	  
    }
    
  	public function variable($variableName)
   	{
		return $this->data[$variableName];
   	}

   	static function XMLToArray($xmlString, $rootNodeName)
   	{
   		$dom = new DOMDocument( '1.0', 'utf-8' );
      	$billingData = array();   
        $success = $dom->loadXML( $xmlString );
        $dataNodes = $dom->getElementsByTagName($rootNodeName);
        if($dataNodes->length == 0)
        {
        	return false;
        }
		$root = $dataNodes->item(0);
        $result = AplArrayGetter::DOMToArray($root);
        return self::unsetEmptyArrays($result);        
   	}
   	
 
   	
	static function DOMToArray($node) 
	{
		// Thanks to ad   http://gaarf.info/2009/08/13/xml-string-to-php-array/
		$output = array();
		switch ($node->nodeType) 
		{
			case XML_CDATA_SECTION_NODE:
			case XML_TEXT_NODE:
				$output = trim($node->textContent);
				break;
			case XML_ELEMENT_NODE:
		    	for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) 
		    	{
		     		$child = $node->childNodes->item($i);
		     		$v = self::DOMToArray($child);
		     		if(isset($child->tagName))
		     		{
			       		$t = $child->tagName;
			       		if(!isset($output[$t])) 
			       		{
		        			$output[$t] = array();
		       			}
				       $output[$t][] = $v;
					}
		     		elseif($v) {
		      			$output = (string) $v;
		     		}
		    	}
		    	if(is_array($output))
		    	{
		     		if($node->attributes->length) 
		     		{
		      			$a = array();
		      			foreach($node->attributes as $attrName => $attrNode) 
		      			{
		       				$a[$attrName] = (string) $attrNode->value;
		      			}
		      			$output['@attributes'] = $a;
		     		}
		     		foreach ($output as $t => $v) {
			      		if(is_array($v) && count($v)==1 && $t!='@attributes') 
			      		{
			       			$output[$t] = $v[0];
			      		}
		     		}
		    	}
			break;
		}
		return $output;
	}   	
	
  	static function unsetEmptyArrays($result)
   	{
   		foreach($result as $key => $item)
   		{
   			if(is_array($item))
   			{
   				if(empty($item))
   				{
   					unset($result[$key]);
   				}
   				else
   				{
   					$result[$key] = self::unsetEmptyArrays($item);
   				}
   			}
   		}
   		return $result;
   	}	
}





?>