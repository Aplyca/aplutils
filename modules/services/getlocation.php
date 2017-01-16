<?php

$tpl = eZTemplate::factory();
$module = $Params['Module'];

$country = $Params['Country'];
$value = $Params['Value'];
$state = "";

if($country == 'US')
{
	$state = $Params['State'];	
}

$cities = array( "content" => CityPO::fetchCitiesByCountryLike($value, $state, $country) );

echo json_encode($cities);

$Result['pagelayout'] = false;
eZDebug::updateSettings(array("debug-enabled" => false, "debug-by-ip" => false));

?>
