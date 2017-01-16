<?php

$tpl = eZTemplate::factory();
$module = $Params['Module'];
$http= eZHTTPTool::instance();
$objectID = $_POST['objectID'];

if($objectID == "")
	 return false;

$object = eZContentObject::fetch($objectID);

if(! $object instanceof eZContentObject)
	return false;

$atttributeIdentifiers = $_POST['atttributeIdentifiers'];

echo $object->attribute('can_read')?1:0;
echo $object->attribute('can_edit')?1:0;

if( !$object->attribute('can_read') || !$object->attribute( 'can_edit' ) )
	return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );

$attributes = $object->fetchAttributesByIdentifier($atttributeIdentifiers);
$validation = ApleZTools::validateAttributesInput($attributes);
if( $validation['result'] )
{
	ApleZTools::setAttributesFromImput($attributes);

}

$Result['pagelayout'] = false;
eZDebug::updateSettings(array("debug-enabled" => false, "debug-by-ip" => false));


?>