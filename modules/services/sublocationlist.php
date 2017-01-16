<?php


$module = $Params['Module'];
$tpl = eZTemplate::factory();

$type = $Params['Type'];
$value = $Params['Value'];


if($type == 'cities')
{
	$cities = CityPO::fetchCitiesByCountry($value);	
	$citiesCount = count($cities);
	if($citiesCount > 100)
		echo $citiesCount . " cities";
	else
		print_r($cities);
}
elseif($type == 'usastates')
{
	$states = UsaStatePO::fetchAll();
	$tpl->setVariable( "data", $states );
	$tpl->setVariable( "type", "usastates" );	
	echo $tpl->fetch( "design:location/sublocationlist.tpl" );

}

$Result['pagelayout'] = false;
eZDB::checkTransactionCounter();
eZExecution::cleanExit();

?>
