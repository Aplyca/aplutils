<?php

class aplFileSystem
{
	function aplFileSystem() 
	{
		return true;
	}

 	function getFile($base_names, $dirs, $tmp_dir = false )
	{		
		$file_path = false;
		foreach ($dirs as $dir)
		{
			foreach($base_names as $base_name)
			{
				if ($base_name)
				{		
					$file = $dir . $base_name;					 		
					if (strpos($file, '://'))
					{
						$url_structure=parse_url($file);								
						$file = aplFileSystem::downloadFile($url_structure, $tmp_dir);
					}
					
					$file = preg_replace('/\/+/xms', '/', $file); 					
					if (is_file($file) and file_exists($file))
					{
						$file_path = $file;
						break;
					}
				}	
			}
			
			if ($file_path)
			{
				break;			
			}
				
		}
		
		return $file_path;
	}	
	
 	function downloadFile($url_structure, $download_path )
	{		
		if (!$download_path)
		{
			$download_path = 'var/storage';
		
		}			
		$tmp_file = trim($download_path) . '/' . $url_structure['host'] . $url_structure['path'] ;
		if (!self::validFile($tmp_file))
		{		
			$directory = dirname($tmp_file);
			$filename = basename($tmp_file);
		
			$file_url = $url_structure['scheme'] . '://' . $url_structure['host'] . $url_structure['path'];
			$ch = curl_init ($file_url);
			
			$useragent="Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 GTB5";			
			$options = array(	CURLOPT_URL => $file_url,  
								CURLOPT_HEADER => false,
								CURLOPT_RETURNTRANSFER => true,						
								CURLOPT_FOLLOWLOCATION => true,  
								CURLOPT_BINARYTRANSFER => true,
								CURLOPT_USERAGENT => $useragent,
								CURLOPT_TIMEOUT => 60);
			        
	        curl_setopt_array($ch, $options); 
	        
			$data=curl_exec ($ch);
			$request_info_http_code = curl_getinfo ($ch, CURLINFO_HTTP_CODE );
			$request_info_content_type = curl_getinfo ($ch, CURLINFO_CONTENT_TYPE );
			
			curl_close ($ch);
					
			if ($request_info_http_code == '200' AND $data)
			{
				eZLog::write('Success;' . $tmp_file . ";" . $file_url . ";" . $request_info_http_code . ";" .  $request_info_content_type, 'download_file.log', 'extension/aplinstall/var/log/');		
				eZFile::create( $filename, $directory , $data );
			}
			else
			{
				$tmp_file = false;
				eZLog::write('Fail;' . $tmp_file . ";" . $request_info_http_code . ";" .  $request_info_content_type, 'download_file.log', 'extension/aplinstall/var/log/');
			}
		}			
		return $tmp_file;
	}		
	
  	function validFile($filename) 
	{
		if (eZFileHandler::doExists($filename) AND eZFileHandler::doIsFile($filename) AND eZFileHandler::doIsReadable($filename) )
		{
			return 	true;
		}
		return false;
	}	
	
  	function validDirectory($filename) 
	{
		if (eZFileHandler::doExists($filename) AND eZFileHandler::doIsDirectory($filename) AND eZFileHandler::doIsExecutable($filename . '/.') )
		{
			return 	true;
		}
		return false;
	}
	
} 

?>
