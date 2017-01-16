<?php

$FunctionList = array();

$FunctionList['root'] = array(	'name' => 'root',
                                'operation_types' => 'read',
                                'call_method' => array( 'class' => 'AplInstantcontentModuleFunctionCollection',
                                                        'include_file' => 'extension/aplutils/classes/instantcontent/AplInstantcontentModuleFunctionCollection.php',
                                                        'method' => 'root' ),
                                'parameter_type' => 'standard',
                                'parameters' => array( array( 'name' => 'nodes',
                                                              'type' => 'array',
                                                              'required' => true,
                                                              'default' => '' )),		
);

$FunctionList['getsearchfilter'] = array(	'name' => 'getsearchfilter',
											'operation_types' => 'read',
											'call_method' => array( 'class' => 'AplInstantcontentModuleFunctionCollection',
																	'include_file' => 'extension/aplutils/classes/instantcontent/AplInstantcontentModuleFunctionCollection.php',
																	'method' => 'getSearchFilter' ),
											'parameter_type' => 'standard',
											'parameters' => array( array( 	'name' => 'filters',
																			'type' => 'array',
																			'required' => true,
																			'default' => '' ))
);

$FunctionList['getsearchquery'] = array('name' => 'getsearchquery',
										'operation_types' => 'read',
										'call_method' => array( 'class' => 'AplInstantcontentModuleFunctionCollection',
																'include_file' => 'extension/aplutils/classes/instantcontent/AplInstantcontentModuleFunctionCollection.php',
																'method' => 'getSearchQuery' ),
										'parameter_type' => 'standard',
										'parameters' => array( array( 	'name' => 'query',
																		'type' => 'string',
																		'required' => true,
																		'default' => '' ))
);

$FunctionList['getsortby'] = array(	'name' => 'getsortby',
										'operation_types' => 'read',
										'call_method' => array( 'class' => 'AplInstantcontentModuleFunctionCollection',
																'include_file' => 'extension/aplutils/classes/instantcontent/AplInstantcontentModuleFunctionCollection.php',
																'method' => 'getSortBy' ),
										'parameter_type' => 'standard',
										'parameters' => array( array( 	'name' => 'sortby',
																		'type' => 'array',
																		'required' => true,
																		'default' => array() ))
);

$FunctionList['getinputfilters'] = array(	'name' => 'getinputfilters',
											'operation_types' => 'read',
											'call_method' => array( 'class' => 'AplInstantcontentModuleFunctionCollection',
																	'include_file' => 'extension/aplutils/classes/instantcontent/AplInstantcontentModuleFunctionCollection.php',
																	'method' => 'getInputFilters' ),
											'parameter_type' => 'standard',
											'parameters' => array( 	array( 	'name' => 'data',
																			'type' => 'array',
																			'required' => true,
																			'default' => '' ),
																	array( 	'name' => 'type',
																			'type' => 'string',
																			'required' => false,
																			'default' => '' ))
);

$FunctionList['getrootnodes'] = array(	'name' => 'getrootnodes',
										'operation_types' => 'read',
										'call_method' => array( 'class' => 'AplInstantcontentModuleFunctionCollection',
																'include_file' => 'extension/aplutils/classes/instantcontent/AplInstantcontentModuleFunctionCollection.php',
																'method' => 'getRootNodes' ),
										'parameter_type' => 'standard',
										'parameters' => array( 	array( 	'name' => 'type',
																		'type' => 'string',
																		'required' => true,
																		'default' => '' ),
																array( 	'name' => 'data',
																		'type' => 'array',
																		'required' => true,
																		'default' => '' ))
);

?>
