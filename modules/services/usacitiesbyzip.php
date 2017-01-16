<?php

$module = $Params['Module'];
$zip = $Params['Zip'];
$state = $Params['State'];
$tpl = eZTemplate::factory();


$zipRegistries = UsaZIPCodePO::fetchListByZIP($zip, $state);

$success = $zipRegistries?1:0;
$data = false;
if($success)
{
	$eqKey = -1;
	foreach($zipRegistries as  $key => $zipRegistry)
	{
		if($zipRegistry->City == $zipRegistry->CityAliasName)
		{
			$eqKey = $key;
		}	
	}
	
	if($eqKey != -1)
	{
		$switchVal = $zipRegistries[$eqKey];
		unset($zipRegistries[$eqKey]);
		array_unshift($zipRegistries, $switchVal);	
	}
	
	
	$tpl->setVariable("cities", $zipRegistries);
	$data =  $tpl->fetch( "design:location/usacitylist.tpl" );
}

$response = array("response" => $success, "htmldata" => $data);
echo json_encode($response);


$Result['pagelayout'] = false;
eZDB::checkTransactionCounter();
eZExecution::cleanExit();


?>