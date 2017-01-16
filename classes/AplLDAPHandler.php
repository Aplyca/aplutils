<?php
/**
 * This file contains the definition for OmsLDAPHandler class
 *  
 */

/**
 * @author Mauricio Sánchez
 * @desc The class allow to connect to LDAP server, create modify, delete users into LDAP directory
 */
class AplLDAPHandler
{	
	const ENTRY_NOT_FOUND = 1;
	const ENTRY_ALREADY_EXISTS = 2;
	const ENTRY_ADDED = 3;
	const ENTRY_UPDATED = 4;
	
    function __construct( $settings_block = false, $parameters = false )
    {
    	if (!$settings_block)
    	{
			$settings_block = 'LDAPSettings';
		}
	
    	if (!is_array($parameters))
    	{
    		$parameters = array();
	    	$ini_ldap = eZIni::instance('ldap.ini');	
	    	
	    	if ($ini_ldap -> hasVariable($settings_block, 'LDAPServer'))
	    	{
	    		$parameters['Server'] = $ini_ldap -> variable($settings_block, 'LDAPServer');
	    	}
	    	
	    	if ($ini_ldap -> hasVariable($settings_block, 'LDAPSSL'))
	    	{
	    		$parameters['SSL'] = ($ini_ldap -> variable($settings_block, 'LDAPSSL')=='true')?true:false;
	    	}
	    	
	    	if ($ini_ldap -> hasVariable($settings_block, 'LDAPPort'))
	    	{
	    		$parameters['Port'] = $ini_ldap -> variable($settings_block, 'LDAPPort');       
	    	}
	    	
	    	if ($ini_ldap -> hasVariable($settings_block, 'LDAPVersion'))
	    	{
	    		$parameters['Version'] = $ini_ldap -> variable($settings_block, 'LDAPVersion');
	    	}
	    	
	    	if ($ini_ldap -> hasVariable($settings_block, 'LDAPBindUser'))
	    	{
	    		$parameters['BindUser'] = $ini_ldap -> variable($settings_block, 'LDAPBindUser');
	    	}
	    	
	    	if ($ini_ldap -> hasVariable($settings_block, 'LDAPBindPassword'))
	    	{
	    		$parameters['BindPassword'] = $ini_ldap -> variable($settings_block, 'LDAPBindPassword');
	    	}
	    	
	    	if ($ini_ldap -> hasVariable($settings_block, 'LDAPBaseDn'))
	    	{	    	
	    		$parameters['BaseDn'] = $ini_ldap -> variable($settings_block, 'LDAPBaseDn');
	    	}
	    	
	    	if ($ini_ldap -> hasVariable($settings_block, 'LDAPLoginAttribute'))
	    	{
	    		$parameters['LoginAttribute'] = $ini_ldap -> variable($settings_block, 'LDAPLoginAttribute');
	    	}
	    	if ($ini_ldap -> hasVariable($settings_block, 'LDAPTimeout'))
	    	{
	    		$parameters['Timeout'] = $ini_ldap -> variable($settings_block, 'LDAPTimeout');
	    	}
	    	if ($ini_ldap -> hasVariable($settings_block, 'LDAPBaseOU'))
	    	{	    	
	    		$parameters['BaseOU'] = $ini_ldap -> variable($settings_block, 'LDAPBaseOU');
	    	}	    	
    	}                
    	
 		$this->Parameters = $parameters;
    }
    
	/**
	 * Connect to LDAP server
	 * @param parameters
	 * @return true
	 */
    public function connect() 
    {    	
        // Connect to the AD/LDAP server as the username/password
        $parameters = $this->Parameters;
        $connection = false;

        putenv('LDAPTLS_REQCERT=never');

        $connection = ldap_connect($parameters['Server'], $parameters['Port']);

        
        // Set some ldap options for talking to AD
        ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, $parameters['Version']);
        ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($connection, LDAP_OPT_NETWORK_TIMEOUT, $parameters['Timeout']);

		
        if ($parameters['BindUser'] && $parameters['BindPassword'])
        {
            $this->Bind = ldap_bind($connection, $parameters['BindUser'], $parameters['BindPassword']);
            if (!$this->Bind)
            {
            	eZDebug::writeNotice( var_export( $parameters, true ), __METHOD__ );
            	eZDebug::writeError( ldap_error($connection), __METHOD__ );
            	return $this -> Connection = false;
            }
        }
        
        return $this -> Connection = $connection;

    }
    
	 /**
	 * Search attributes
	 * @param customerID
	 * @return true if the persistent object was successful remove, false if not
	 */
    public function search( $filter_string = '', $attributes = false)
    {
    
	    $parameters = $this->Parameters;
		$data = array();
		$baseOu = array();
		$ldapConnection = array();
		$ini_ldap = eZIni::instance('ldap.ini');	
		$filter = "(" . $filter_string . ")";
		$connection = $this -> Connection;
	    	
		if (!$attributes)
		{
			$attributes = array_keys($ini_ldap -> variable('SearchSettings', 'Attributes'));
		}

		if ($parameters['BaseOU'])
		{
	 		for($i=0;$i<count($parameters['BaseOU']);$i++){
	 			$baseOu[$i] = $parameters['BaseOU'][$i];
	 			$ldapConnection[$i] = $connection;	
	 		}
	 		$results = ldap_search($ldapConnection,$baseOu,$filter,$attributes);
	 		for($j=0;$j<count($results);$j++){
	 			$count = ldap_get_entries($connection,$results[$j]);
    			if($count['count'] != 0)
    			{
    				$data = array_merge($data,ldap_get_entries($connection,$results[$j]));
    			}
	    	}
		}
		else
		{
			$result = ldap_search($this -> Connection, $parameters['BaseDn'], $filter, $attributes);
			$data = ldap_get_entries( $connection, $result );
		}

	    	return $data; 

    }     
    
	 /**
	 * Build attributes
	 * @param $entry
	 * @return $entry formatted in order to be acceoted by LDAP.
	 */
    public static function build( $entry = array())
    {
		$ini_ldap = eZIni::instance('ldap.ini');
		
		$base_entry_attributes = $ini_ldap -> variable('SearchSettings', 'Attributes');
		
		if (isset($base_entry_attributes['objectClass']))
		{
			$base_entry_attributes['objectClass'] = $ini_ldap -> variable('SearchSettings', 'ObjectClass');
		}
				
		foreach ($entry as $index => $value)
		{
			if (isset($base_entry_attributes[$index]))
			{
				$base_entry_attributes[$index] = $value;
			}
		}			
		return $base_entry_attributes;
    }  

	 /**
	 * Add entry
	 * @param $entry, $dn_prefix, $base_dn
	 * @return true if the $entry array was successful added, false if not
	 */
    public function add( $entry = array(), $dn_prefix = false, $base_dn = false)
    {
		$ini_ldap = eZIni::instance('ldap.ini');
		$parameters = $this->Parameters;
		if (!$dn_prefix)
		{	
			$dn_prefix = $parameters['LoginAttribute'];
		}
		
		if (!$base_dn)
		{	
			$base_dn = $parameters['BaseDn'];
		}
    	
		$dn = $dn_prefix . '=' . $entry[$dn_prefix] . ',' . $base_dn;
		
		return ldap_add($this -> Connection, $dn, $entry);

    }  	
	
	 /**
	 * Search attributes
	 * @param customerID
	 * @return true if the persistent object was successful remove, false if not
	 */
    public function modify( $entry, $base_dn = false, $dn_prefix = false)
    {
		$parameters = $this->Parameters;
		
		$ini_ldap = eZIni::instance('ldap.ini');		
		$base_entry_attributes = $ini_ldap -> variable('SearchSettings', 'Attributes');	
		$base_modified_entry = array();
		foreach ($base_entry_attributes as $index => $value)
		{
			if ($value != '')
			{
				$base_modified_entry[$index] = $value;
			}
		}

    	$modified_entry = array();
		foreach ($entry as $index => $value)
		{
			if (isset($base_entry_attributes[$index]))
			{
				$modified_entry[$index] = $value;
			}
		}
				
		$result_modified_entry = array_merge($base_modified_entry, $modified_entry);
		
		if (!$dn_prefix)
		{	
			$dn_prefix = $parameters['LoginAttribute'];
		}		
		
		if (!$base_dn)
		{	
			$base_dn = $parameters['BaseDn'];
		}
    	
		$dn = $dn_prefix . '=' . $entry[$dn_prefix] . ',' . $base_dn;			
		return ldap_modify($this -> Connection, $dn, $result_modified_entry);
    } 
	
    public function close()
    {
    	ldap_close($this -> Connection);
    }
	
}

?>