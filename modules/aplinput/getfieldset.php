<?php

$module = $Params['Module'];
$iniName = $_GET['iniName'];
$blockName = $_GET['blockName'];
$groupName = $_GET['groupName'];
$count =  $_GET['count'];
$inputObject = new AplInputObject($iniName);
$attributes = $inputObject->attributeMetadataArray(); 



$fieldset = $attributes[$groupName][$blockName]['data'];
$tpl = eZTemplate::factory();

$tpl->setVariable( 'fieldset', $fieldset );
$tpl->setVariable( 'fieldset_identifier', $blockName );
$tpl->setVariable( 'count', $count );
$result =  $tpl->fetch( "design:attributeinput/types/fieldset.tpl" );

echo $result;

$Result['pagelayout'] = false;
eZDB::checkTransactionCounter();
eZExecution::cleanExit();

?>

