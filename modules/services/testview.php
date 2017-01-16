<?php


$om = new ProductOrderManager(137);
$om->initializeOrderXML('order_data');

$superhideninfo = array("kinkilevel" => 10, "transformlevel" => 5);
//$superhideninfo = ":)";
$miInfo = array("nombre" => "david", "altura" => "1.88", "xtras" => $superhideninfo, "sexo" => "m");

$om->setOrderInformation("my_info", $miInfo);



//print_r($om);





 /*
  // test ezfind indexing 
  $searchEngine = new eZSolr();
 $object = eZContentObject::fetch(2780);
  $result = $searchEngine->addObject( $object, false );
   if (! $result)
                {
                   echo "fail";
                }*/

$Result['pagelayout'] = false;


?>