<?php 

class AplSearch 
{
    static function searchFilter($inputs = array()) 
    {
        $filter = array();
        $searchIni = eZINI::instance('search.ini');   
        $attribute_filter=array();     
        $search_classes = $searchIni -> variable('SearchSettings', 'ClassIdentifierFilter');
        $search_class = $search_classes[0];  
        $class = eZContentClass::fetchByIdentifier($search_class);

        if ($class instanceof eZContentClass)
        { 
            foreach($inputs as $filter_name => $filter_value)
            {                        
                if ($filter_value != "")
                {                            
                    $contentAttribute = self::getSearchableAttribute($class, $filter_name);
                    if ($contentAttribute)
                    {                    
                        $filter_class_name = 'AplSearchInput' . $filter_name;          
                        if (class_exists($filter_class_name))
                        {
                            $filter_class = new $filter_class_name;
                            $filter_input = $filter_class -> execute($filter_value, $filter_name, $inputs);
                            $filter_value = $filter_input['value'];
                        }     

                        $filter_content = self::cleanInput($filter_value);
                        if ($filter_content  != "")
                        {
                            if (!is_array($filter_content))
                            {
                                $filter_content = array($filter_content);
                            }
                            if ( ($searchIni -> hasVariable('FieldsFilter-' . $filter_name, 'ExactMatch')) AND ($searchIni -> variable('FieldsFilter-' . $filter_name, 'ExactMatch') == 'true'))
                            {
                                $exact_values = array();
                                foreach ($filter_content as $value)
                                {
                                    $exact_values[]='"' . $value . '"';
                                }
                                $filter_content = $exact_values;
                            }   
                            
                            $attribute_filter_content =  $search_class . '/' . $contentAttribute -> Identifier . ':';     
                            if (count($filter_content) == 1)
                            {
                                $attribute_filter_content .=  $filter_content[0];    
                            }
                            else
                            {   
                                $search_type = 'or';
                                if ($searchIni -> hasVariable('FieldsFilter-' . $filter_name, 'Type'))
                                {
                                    $search_type = $searchIni -> variable('FieldsFilter-' . $filter_name, 'Type');
                                }                            
                                switch($search_type)
                                {
                                    case 'and';
                                        $attribute_filter_content .=  '(' . implode(' AND ', $filter_content) . ')';
                                    break;
                                    case 'range';
                                        $attribute_filter_content .=  '[' . implode(' TO ', $filter_content) . ']';
                                    break;
                                    case 'or';
                                    default;
                                        $attribute_filter_content .=  '(' . implode(' OR ', $filter_content) . ')';   
                                    break;
                                }
                            }   
                            $attribute_filter[] = $attribute_filter_content;                
                        }   
                    }
                }
            }
        }
        
        if ($attribute_filter)
        {
            $filter = array($attribute_filter);
        }
        
        return $filter;
    }
    
    static function getSearchableAttribute($class, $filterName)
    {
        $attribute = false;
        $searchIni = eZINI::instance('search.ini');   
        $attributeIdentifier = $searchIni -> variable('FieldsFilter-' . $filterName, 'Attribute');         
        if ($class instanceof eZContentClass )
        {                       
            $contentAttribute = $class -> fetchAttributeByIdentifier($attributeIdentifier);  

            if (($contentAttribute instanceof eZContentClassAttribute) and ($contentAttribute -> IsSearchable == 1))
            {
                $attribute = $contentAttribute;
            }
        }
        return $attribute;
    }

    static function getDefaultInput( $input)
    {
        if ($searchIni -> hasVariable('FieldsFilter-' . $input, 'DefaultValue'))
        {
            $default_value = self::cleanInput($searchIni -> variable('FieldsFilter-' . $input, 'DefaultValue'));
            if ($default_value)
            {
                return $default_value;
            }
        } 
        return false;
    }    
    
    static function cleanInput( $input)
    {

        $result = "";
        if (is_array($input) AND (trim($input[0]) != ''))
        {
            $result = $input;
        }
        elseif (!is_array($input) AND (trim($input) != ''))
        {
           $result = $input;
        }
        return $result;
    }
    
    public function execute( $value, $name, $inputs)
    {
        return $value;
    }
    static function searchQuery($query)
    {
        $query = trim($query);
        $searchIni = eZINI::instance('search.ini');
        
        if(!$query)
        {
        	$query = "*:*";
        }	
        
        if ($searchIni -> variable('SearchSettings', 'QueryExactMatch') == 'enabled')
        {     
            $query = str_replace('"', '', $query);
            if ($query)
            {
                $query = '"' . str_replace('"', '', $query) . '"';
            }    
        }
        else
        {	   	
        	if (strpbrk($query, '"+*' . "'") === false) 
        	{
        		$query = '*' . $query . '*';
        	}	
        }	
        return $query;
    }    
}

?>