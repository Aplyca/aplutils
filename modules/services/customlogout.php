<?php


$module = $Params['Module'];


$ini = eZINI::instance('utils.ini');	
$sessionVariables =  $ini->variable('CustomLogout','SessionVariables');

if(is_array($sessionVariables))
{
	if(!empty($sessionVariables))
	{			
		$http = eZHTTPTool::instance();
		foreach($sessionVariables as $sessionVariable)
		{
			$http->removeSessionVariable($sessionVariable);	
		}
	}	
}

$module->redirect('user', 'logout');
$Result['pagelayout'] = false;




?>
