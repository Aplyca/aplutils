<?php

class AplInstantcontentModuleFunctionCollection
{

	function __construct()
	{
		
	}
		
	public function root( $rootNodes )
	{	
		if (!$rootNodes)
		{
			$searchIni = eZINI::instance('search.ini');
			$rootNodes = $searchIni -> variable('RootNode', 'Default');
		}
		
		return array('result' => (array)$rootNodes);
	}	

	public function getSearchFilter( $filters )
	{	
		return array('result' => AplSearch::searchFilter((array)$filters));
	}	 
	
	public function getSearchQuery( $query )
	{
		return array('result' => AplSearch::searchQuery($query));
	}	
	
	public function getSortBy( $sort )
	{		
		$sortby = array();
		$searchIni = eZINI::instance('search.ini');
		foreach ($searchIni -> variable('SearchSettings', 'SortParams') as $sortParam)
		{
			if ( array_key_exists($sortParam, $sort) AND trim($sort[$sortParam]) !='')
			{
				$value = $sort[$sortParam];
			}
			else
			{
				$value = $searchIni -> variable('SearchSettings', $sortParam . 'Default');
			}		

			$sortby[] = $value;
		}
		return array('result' => array($sortby[0] => $sortby[1]));
	}

	public function getInputFilters($type, $data )
    {
        $filters = array();
        switch ($type)
        {
            case 'view':
                $fields = $params['data'];
            break;
            case 'module':
                $fields = array();
            break;            
            case 'post':
                $fields = $_POST;
            break;
            case 'get':            
            default:
                $fields = $_GET;
            break;
        }
        
        $searchIni = eZINI::instance('search.ini'); 
        $attributes_filter = $searchIni -> variable('SearchSettings', 'FieldsFilter');

        $searchClasses = $searchIni -> variable('SearchSettings', 'ClassIdentifierFilter');
        $class = eZContentClass::fetchByIdentifier($searchClasses[0]);

        foreach ($attributes_filter as $filterName)
        {
            $attribute = AplSearch::getSearchableAttribute($class, $filterName);

            if ($attribute)
            {    
                $filter = array('name' => $filterName, 'content' => $attribute);
                if ( array_key_exists($filterName, $fields))
                {
                    $input_element = AplSearch::cleanInput($fields[$filterName]);
                    if ($input_element)
                    {
                        $filter = $input_element;
                    }
                    elseif($fields[$filterName]) 
                    {
                        if ($searchIni -> hasVariable('FieldsFilter-' . $filterName, 'DefaultValue'))
                        {
                            $default_value = AplSearch::cleanInput($searchIni -> variable('FieldsFilter-' . $filterName, 'DefaultValue'));
                            if ($default_value)
                            {
                                $filter = $default_value;
                            }
                        } 
                    }
                }
                else
                {
                    if ($searchIni -> hasVariable('FieldsFilter-' . $filterName, 'DefaultValue'))
                    {
                        $default_value = AplSearch::cleanInput($searchIni -> variable('FieldsFilter-' . $filterName, 'DefaultValue'));
                        if ($default_value)
                        {
                            $filter["default"] = $default_value;
                        }
                    }         
                }  

            $filters[]=$filter;

            }  



        }
        

        return array('result' => $filters);
    }		
	
    public function getRootNodes($type, $data )
    {
        $filters = array();
        switch ($type)
        {
            case 'view':
                $fields = $data;
            break;
            case 'module':
                $fields = array();
            break;            
            case 'post':
                $fields = $_POST;
            break;
            case 'get':            
            default:
                $fields = $_GET;
            break;
        }
        

        $rootNodeField = false;
        $searchIni = eZINI::instance('search.ini');

        if ($searchIni -> hasVariable('RootNode', 'Attribute') AND $searchIni -> variable('RootNode', 'Attribute'))
        {
            $rootNodeField = $searchIni -> variable('SearchSettings', 'RootNode');
        }
        
        $defaultRootNodes = $searchIni -> variable('RootNode', 'Default');


        if ( array_key_exists($rootNodeField, $fields))
        {
            $input_element = AplSearch::cleanInput($fields[$rootNodeField]);
            if ($input_element)
            {
                $rootNodes = $input_element;
            }
            elseif($fields[$filterName]) 
            {
                $rootNodes = $defaultRootNodes; 
            }
        }
        else
        {
            $rootNodes = $defaultRootNodes;       
        }   

        return array('result' => (array)$rootNodes);
    }   

}

?>