<?php


/**
 *  @author David Sánchez Escobar
 *  @desc Provides debug tools for php development
 *  
 */

class AplDebugger
{
	
	 /**
	 * Outputs data about the files, classes, methods and other info called thoughout a php execution 	 
	 * @param bool $brief shows only file, class and methods if true
	 * @param bool $noParamInfo verbose data related
	 * @return array $bt execution data
	 */
	static function traceExecution($brief=true, $noParamInfo=true)
	{
		$bt = debug_backtrace($noParamInfo);
		if($brief)
		{
			foreach($bt as $key => $bti)
			{
				unset($bt[$key]['object']);
				unset($bt[$key]['args']);
			}			
		}
		return $bt;
	}
}

?>