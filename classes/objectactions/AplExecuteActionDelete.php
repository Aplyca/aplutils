<?php 

class AplExecuteActionDelete extends AplExecuteAction
{
    function __construct()
    {
        ;
    }
    
    public function execute( $node)
    {
        if ($node -> canRemove())
        {
            $nodeId = $node ->NodeID;
    
            $deleteIDArray = array($nodeId);
            $moveToTrash = false;
            if ( eZOperationHandler::operationIsAvailable( 'content_delete' ) )
            {
                $operationResult = eZOperationHandler::execute( 'content', 
                												'delete', 
                												array( 	'node_id_list' => $deleteIDArray,
    																	'move_to_trash' => $moveToTrash ),
                                                                null, 
                                                                true );
            }
            else
            {
                eZContentOperationCollection::deleteObject( $deleteIDArray, $moveToTrash );
            }
            return 'Success Deleted';
        }
            
        return "Can not delete, permissions restriction";
    }
}

?>