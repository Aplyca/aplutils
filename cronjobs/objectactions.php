<?php

$objectactionsIni = eZINI::instance('objectactions.ini');
$actions = ApleZTools::getCycles($script -> getOptions( "", "", array()), 'cycle');

$scriptMutex->unlock();

if (isset($actions[0]) AND $objectactionsIni->hasGroup( "Action-" .  $actions[0]))    
{     
    $action = $actions[0];
    $block = "Action-" . $action;
    
    $levels = ApleZTools::getCycles($script -> getOptions( "", "", array()), 'level');
    $level = $objectactionsIni->variable( $block, 'Level' );
    if(!$level)
    {
        $level =  (isset($levels[0]))?$levels[0]:'off';
    }
    $cli->output("Action " . $action . " is in level $level");    
    
    $userName = $objectactionsIni->variable( $block, 'User' );
    $loginUser = ApleZTools::cronjobLoginUser($userName, $cli);   
    
    if (in_array($level, array('count', 'show', 'execute')))
    {       
        $fetchFile = "objectactions/" . $action . ".ini";
        if( $objectactionsIni->hasVariable( $block, 'FetchFile' ) )
        {
            $fetchFile = $objectactionsIni->variable( $block, 'FetchFile' );
        }
    
    	$nodes = ApleZTools::fetchSubTree($fetchFile);  
    	$executions = $objectactionsIni->variable( $block, 'Execute' );
    	
    	$cli->output(count($nodes) . " Nodes will be " . $action . " by " . implode(', ', $executions));
    
    	if (in_array($level, array('show', 'execute')))
    	{
        	foreach ($nodes as $index => $node)
        	{
        		$nodeName =  $node -> attribute('name');
        		$nodeID =  $node -> attribute('node_id');
        		$object =  $node -> object();
        		$objectID =  $object -> ID;
        		$cli->output("[" . $index . "] Name: " . $nodeName . " Node ID: " . $nodeID . " Object ID: ". $objectID . " Class: " . $object -> ClassIdentifier);
        		
		    	if (in_array($level, array('execute')))
		    	{
            		foreach ($objectactionsIni->variable( $block, 'Execute' ) as $execute)
            		{
            		    $executeClassName = 'AplExecuteAction' . ucfirst(strtolower($execute));
                		if (class_exists($executeClassName))
                		{
                		    $executePlugin = new $executeClassName;
                		    $result = $executePlugin -> execute($node);        		    
                		}
                		else
                		{
                		    $result = "Invalid execution " . $execute;
                		}
                		
                		$cli->output($result);
            		}
        	    }	
        	}
        }
    }		
}

?>