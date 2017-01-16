<?php


/**
 * This file contains the class definition for AplQueryBuilder
 * 
 */

/**
 * This class manages filter arraays and converts it into SQL Queries
 *  
 */


class AplQueryBuilder
{
	/**
	 * 
	 * @param array $conditionList plain array of strings
	 * @param string $conector AND|OR
	 * @return string querypart
	 */
	static function buildConectorConditions($conditionList, $field, $connector)
	{
			$quotedConditionList = array();
			foreach($conditionList as $cond)
			{
				$quotedConditionList[] = $field . " = " . "'" . $cond . "'";
			}
			
			$conditionList = $quotedConditionList;
		
			$conditionListCount = count($conditionList);
			if($conditionListCount <= 0)
				return "";
			$connectorPiece = " " . $connector . " ";	
			$querypart = implode($connectorPiece, $conditionList);
			return $querypart;
				
		/*	 = "";
			foreach ($conditionList as $key => $condition)
			{
				if($key == $conditionListCount -1 )
				{
					$querypart.= $condition . " " . $connector;
				}
				else
				{
					$querypart.= $condition . " " . $connector;
				}
			}				
				
			else return false;	*/
		
	}
}

?>