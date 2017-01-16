<?php
class AplCSVExport
{
	
	// TODO Candidate for deletion
	
	private $buffer;
	private $filename;
	private $separator;
	private $columnNames;
	
	function __construct($settingsFile, $settingBlock) 
    {      	
    	$creditsIni = eZINI::instance($settingsFile);
    	$settingBlock = $creditsIni -> group($settingBlock);
    	print_r($settingBlock); die();
    	
    	/*$this->filename = $filename;
    	$this->separator = $separator;
    	$this->buffer = "";
    	$this->columnNames = array();*/    	
    }   
    
    private function setColumnNames($names)
    {
    	    	
    }
    
    
}

?>