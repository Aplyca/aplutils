<?php 

class AplExecuteActionFillattributes extends AplExecuteAction
{
    function __construct()
    {
        ;
    }
    
    public function execute( $node)
    {
        if ($node -> canEdit())
        {
            $object = $node->object();
	
        	if ($object instanceof eZContentObject)
        	{	
        		$class_identifier = $object -> ClassIdentifier;
        		$ini_utils = eZINI::instance('utils.ini');
        		if ($ini_utils -> hasVariable("FillAttributes", "Class-" . $class_identifier))
        		{
        			$attribute_match = $ini_utils -> variable("FillAttributes", "Class-" . $class_identifier);  
        			
        			foreach ($attribute_match as $target_identifier => $source_identifier)
        			{
        				$attributes = $object -> dataMap();                     
        				if ($attributes[$source_identifier] instanceof eZContentObjectAttribute)
        				{
        					$attribute_content = trim($attributes[$source_identifier] -> toString());					
        					$content_string = false;
        					switch ($attributes[$source_identifier] ->DataTypeString) 
        					{
        						case 'ezdate':
        							if ($attribute_content > 0)
        							{
        								$content_string = $attribute_content;
        							}
        						break;
        						default:
        							if ($attribute_content != "")
        							{
        								$content_string = $attribute_content;
        							}
        						break;
        					}
        					
        					if ($content_string)
        					{
        						if ($attributes[$target_identifier] instanceof eZContentObjectAttribute)
        						{
        							$db = eZDB::instance();
        							$db->begin();
        							$attributes[$target_identifier]->fromString($content_string);
        							$attributes[$target_identifier]->store();
        							$db->commit();
        						}
        						else
        						{
        							 $object->setAttribute( $target_identifier, $content_string ); 
        							 $db = eZDB::instance(); 
        							 $db->begin(); 
        							 $object->store(); 
        							 $db->commit(); 
        						}
        					}  
        				}
        			}
        		}
        	}
        	
            return 'Success Fill Atrributes';
        }
            
        return "Can not edit, permissions restriction";
    }
}

?>