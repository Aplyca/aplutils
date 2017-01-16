<?php

/** 
 * @author Björn Dieding, bjoern@xrow.de, xrow GbmH Hannover Germany
 * 
 * The class eZPersistentObjectRemote is able to connect to remote databases using the active record pattern/persistent object pattern.
 * 
 * To create this class off eZ PersistentObject grap all functions from eZ PersistentObject
 * and replace 
 * 1.) eZPersistentObject:: with eZPersistentObjectRemote::
 * 2.) eZDB::instance() with self::getDB()
 * 
 * Our test case:
 * 
 * Locally we have a eZ Publish database that we access over the Golbal Instance
 * Remote we also have a eZ Publish Database that we access over a new Instance and write to it.
 * 
 * The remote database doesn`t need to be an eZ Publish database. It could be also an unknown database 
 * or a known database with defined classes of eZPersistentObjectRemote 
 * Example script:
 * 
 * $obj = eZPersistentObject::fetchObject( eZContentObject::definition(), null, array( 'id' => 14 ) );
 * echo "<pre>Local DB: ".$obj->Name." modified ". strftime('%Y-%m-%d %H:%M:%S', $obj->Modified)."</pre>";
 * $databaseParameters = array( 'server' => 'SBS', 'port' => 3306, 'user' => 'root', 'password' => false, 'database' => 'kip' );
 * $db = eZDB::instance( 'mysql', $databaseParameters, true );
 * eZPersistentObjectRemote::setDB( $db );
 * $obj = eZPersistentObjectRemote::fetchObject( eZContentObjectRemoteTest::definition(), null, array( 'id' => 14 ) );
 * echo "<pre>Remote DB: ".$obj->Name." modified ". strftime('%Y-%m-%d %H:%M:%S', $obj->Modified)."</pre>";
 * $obj->setAttribute( 'modified', time() );
 * $obj->store();
 * unset($obj);
 * $obj = eZPersistentObjectRemote::fetchObject( eZContentObjectRemoteTest::definition(), null, array( 'id' => 14 ) );
 * echo "<pre>Remote DB after update: ".$obj->Name." modified ". strftime('%Y-%m-%d %H:%M:%S', $obj->Modified)."</pre>";
 * $obj = eZPersistentObject::fetchObject( eZContentObject::definition(), null, array( 'id' => 14 ) );
 * echo "<pre>Local DB: ".$obj->Name." modified ". strftime('%Y-%m-%d %H:%M:%S', $obj->Modified)."</pre>";
 */
class eZPersistentObjectRemote extends eZPersistentObject
{
	/**
	 * @var eZDBInterface
	 */
	public static $db = null;
	/**
	 * 
	 * @param eZDBInterface $db
	 * @return void
	 */
	static function setDB()
	{
		$ini = eZIni::instance();
		$databaseParameters = array(  'server' => ($ini -> hasVariable('RemoteDatabaseSettings', 'Server'))?$ini -> variable('RemoteDatabaseSettings', 'Server'):false, 
								      'port' => ($ini -> hasVariable('RemoteDatabaseSettings', 'Port'))?$ini -> variable('RemoteDatabaseSettings', 'Port'):false, 
									  'user' => ($ini -> hasVariable('RemoteDatabaseSettings', 'User'))?$ini -> variable('RemoteDatabaseSettings', 'User'):false, 
									  'password' => ($ini -> hasVariable('RemoteDatabaseSettings', 'Password'))?$ini -> variable('RemoteDatabaseSettings', 'Password'):false, 
									  'database' => ($ini -> hasVariable('RemoteDatabaseSettings', 'Database'))?$ini -> variable('RemoteDatabaseSettings', 'Database'):false,
									);
									
		$db = eZDB::instance( ($ini -> hasVariable('RemoteDatabaseSettings', 'DatabaseImplementation'))?$ini -> variable('RemoteDatabaseSettings', 'DatabaseImplementation'):false, $databaseParameters, true );
	
		if ( !$db->isConnected() )
		{
			throw new Exception( "Remote Database Instance not connected." );
		}
		return self::$db = $db;
	}
	/**
	 * @return eZDBInterface
	 */
	static function getDB()
	{	
		if ( self::$db instanceof eZDBInterface )
		{
			return self::$db;
		}

		return self:: setDB();
	}
	
    function __construct( $row )
    {
        $this->PersistentDataDirty = false;
        if ( is_numeric( $row ) )
            $row = $this->fetch( $row, false );
        $this->fill( $row );
    }

    /*!
     Fetches the number of rows by using the object definition.
     Uses fetchObjectList for the actual SQL handling.

     See fetchObjectList() for a full description of the input parameters.
    */
    static function count( $def, $conds = null, $field = null )
    {
        if ( !isset( $field ) )
        {
            $field = '*';
        }
        $customFields = array( array( 'operation' => 'COUNT( ' . $field . ' )', 'name' => 'row_count' ) );
        $rows = eZPersistentObjectRemote::fetchObjectList( $def, array(), $conds, array(), null, false, false, $customFields );
        return $rows[0]['row_count'];
    }

    /*!
     Creates an SQL query out of the different parameters and returns an object with the result.
     If \a $asObject is true the returned item is an object otherwise a db row.
     Uses fetchObjectList for the actual SQL handling and just returns the first row item.

     See fetchObjectList() for a full description of the input parameters.
    */
    static function fetchObject( /*! The definition structure */
                               $def,
                               /*! If defined determines the fields which are extracted, if not all fields are fetched */
                               $field_filters,
                               /*! An array of conditions which determines which rows are fetched*/
                               $conds,
                               $asObject = true,
                               /*! An array of elements to group by */
                               $grouping = null,
                               /*! An array of extra fields to fetch, each field may be a SQL operation */
                               $custom_fields = null )
    {
        $rows = eZPersistentObjectRemote::fetchObjectList( $def, $field_filters, $conds,
                                                      array(), null, $asObject,
                                                      $grouping, $custom_fields );
        if ( $rows )
            return $rows[0];
        return null;
    }

    /*!
     Removes the object from the database, it will use the keys in the object
     definition to figure out which table row should be removed unless \a $conditions
     is defined as an array with fieldnames.
     It uses removeObject to do the real job and passes the object defintion,
     conditions and extra conditions \a $extraConditions to this function.
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
    */
    function remove( $conditions = null, $extraConditions = null )
    {
        $def = $this->definition();
        $keys = $def["keys"];
        if ( !is_array( $conditions ) )
        {
            $conditions = array();
            foreach ( $keys as $key )
            {
                $value = $this->attribute( $key );
                $conditions[$key] = $value;
            }
        }
        eZPersistentObjectRemote::removeObject( $def, $conditions, $extraConditions );
    }

    /*!
     Deletes the object from the table defined in \a $def with conditions \a $conditions
     and extra conditions \a $extraConditions. The extra conditions will either be
     appended to the existing conditions or overwrite existing fields.
     Uses conditionText() to create the condition SQL.
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
    */
    static function removeObject( $def, $conditions = null, $extraConditions = null )
    {
        $db = self::getDB();

        $table = $def["name"];
        if ( is_array( $extraConditions ) )
        {
            foreach ( $extraConditions as $key => $cond )
            {
                $conditions[$key] = $cond;
            }
        }

        /* substitute fields mentioned the conditions whith their
           short names (if any)
         */
        $fields = $def['fields'];
        eZPersistentObjectRemote::replaceFieldsWithShortNames( $db, $fields, $conditions );

        $cond_text = eZPersistentObjectRemote::conditionText( $conditions );

        $db->query( "DELETE FROM $table $cond_text" );
    }

    /*!
     Stores the object in the database, uses storeObject() to do the actual
     job and passes \a $fieldFilters to it.
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
    */
    function store( $fieldFilters = null )
    {
        return eZPersistentObjectRemote::storeObject( $this, $fieldFilters );
    }

    /*!
     \private
     Stores the data in \a $obj to database.
     \param fieldFilters If specified only certain fields will be stored.
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
    */
 	static function storeObject( $obj, $fieldFilters = null )
    {
		$persistentobject_id = 0;
		$db = self::getDB();
        $useFieldFilters = ( isset( $fieldFilters ) && is_array( $fieldFilters ) && $fieldFilters );

        $def = $obj->definition();
        $fields = $def["fields"];
        $keys = $def["keys"];
        $table = $def["name"];
        $relations = isset( $def["relations"] ) ? $def["relations"] : null;
        $insert_object = false;
        $exclude_fields = array();
        foreach ( $keys as $key )
        {
            $value = $obj->attribute( $key );
            if ( $value === null )
            {
                $insert_object = true;
                $exclude_fields[] = $key;
            }
        }

        if ( $useFieldFilters )
            $insert_object = false;

        $use_fields = array_diff( array_keys( $fields ), $exclude_fields );
        // If we filter out some of the fields we need to intersect it with $use_fields
        if ( is_array( $fieldFilters ) )
            $use_fields = array_intersect( $use_fields, $fieldFilters );
        $doNotEscapeFields = array();
        $changedValueFields = array();
        $numericDataTypes = array( 'integer', 'float', 'double' );

        foreach ( $use_fields as $field_name  )
        {
            $field_def = $fields[$field_name];
            $value = $obj->attribute( $field_name );

            if ( $value === null )
            {
                if ( ! is_array( $field_def ) )
                {
                    $exclude_fields[] = $field_name;
                }
                else
                {
                    if ( array_key_exists( 'default', $field_def ) &&
                         ( $field_def['default'] !== null ||
                          ( $field_name == 'data_int' &&
                            array_key_exists( 'required', $field_def ) &&
                            $field_def[ 'required' ] == false ) ) )
                    {
                        $obj->setAttribute( $field_name, $field_def[ 'default' ] );
                    }
                    else
                    {
                        //if ( in_array( $field_def['datatype'], $numericDataTypes )
                        $exclude_fields[] = $field_name;
                    }
                }
            }

            if ( strlen( $value ) == 0 &&
                 is_array( $field_def ) &&
                 in_array( $field_def['datatype'], $numericDataTypes  ) &&
                 array_key_exists( 'default', $field_def ) &&
                 ( $field_def[ 'default' ] === null || is_numeric( $field_def[ 'default' ] ) ) )
            {
                $obj->setAttribute( $field_name, $field_def[ 'default' ] );
            }

            if ( $value !== null                                &&
                 $field_def['datatype'] === 'string'            &&
                 array_key_exists( 'max_length', $field_def )   &&
                 $field_def['max_length'] > 0                   &&
                 strlen( $value ) > $field_def['max_length'] )
            {
                $obj->setAttribute( $field_name, substr( $value, 0, $field_def['max_length'] ) );
                eZDebug::writeDebug( $value, "truncation of $field_name to max_length=". $field_def['max_length'] );
            }
            $bindDataTypes = array( 'text' );
            if ( $db->bindingType() != eZDBInterface::BINDING_NO &&
                 strlen( $value ) > 2000 &&
                 is_array( $field_def ) &&
                 in_array( $field_def['datatype'], $bindDataTypes  )
                 )
            {
                $boundValue = $db->bindVariable( $value, $field_def );
//                $obj->setAttribute( $field_name, $value );
                $doNotEscapeFields[] = $field_name;
                $changedValueFields[$field_name] = $boundValue;
            }

        }
        $key_conds = array();
        foreach ( $keys as $key )
        {
            $value = $obj->attribute( $key );
            $key_conds[$key] = $value;
        }
        unset( $value );

        $important_keys = $keys;
        if ( is_array( $relations ) )
        {
//            $important_keys = array();
            foreach( $relations as $relation => $relation_data )
            {
                if ( !in_array( $relation, $keys ) )
                    $important_keys[] = $relation;
            }
        }
        if ( count( $important_keys ) == 0 && !$useFieldFilters )
        {
            $insert_object = true;
        }
        else if ( !$insert_object )
        {
            $rows = self::fetchObjectList( $def, $keys, $key_conds,
                                                          array(), null, false,
                                                          null, null );
            if ( count( $rows ) == 0 )
            {
                /* If we only want to update some fields in a record
                 * and that records does not exist, then we should do nothing, only return.
                 */
                if ( $useFieldFilters )
                    return;

                $insert_object = true;
            }
        }

        if ( $insert_object )
        {
            // Note: When inserting we cannot hone the $fieldFilters parameters

            $use_fields = array_diff( array_keys( $fields ), $exclude_fields );
            $use_field_names = $use_fields;
            if ( $db->useShortNames() )
            {
                $use_short_field_names = $use_field_names;
				self::replaceFieldsWithShortNames( $db, $fields, $use_short_field_names );
                $field_text = implode( ', ', $use_short_field_names );
                unset( $use_short_field_names );
            }
            else
                $field_text = implode( ', ', $use_field_names );

            $use_values_hash = array();
            $escapeFields = array_diff( $use_fields, $doNotEscapeFields );

            foreach ( $escapeFields as $key )
            {
                $value = $obj->attribute( $key );
                $field_def = $fields[$key];

                if ( $field_def['datatype'] == 'float' || $field_def['datatype'] == 'double' )
                {
                    if ( $value === null )
                    {
                        $use_values_hash[$key] = 'NULL';
                    }
                    else
                    {
                        $use_values_hash[$key] = sprintf( '%F', $value );
                    }
                }
                else if ( $field_def['datatype'] == 'int' || $field_def['datatype'] == 'integer' )
                {
                    if ( $value === null )
                    {
                        $use_values_hash[$key] = 'NULL';
                    }
                    else
                    {
                        $use_values_hash[$key] = sprintf( '%d', $value );
                    }
                }
                else
                {
                    // Note: for more colherence, we might use NULL for sql strings if the php value is NULL and not an empty sring
                    //       but to keep compatibility with ez db, where most string columns are "not null default ''",
                    //       and code feeding us a php null value without meaning it, we do not.
                    $use_values_hash[$key] = "'" . $db->escapeString( $value ) . "'";
                }
            }
            foreach ( $doNotEscapeFields as $key )
            {
                $use_values_hash[$key] = $changedValueFields[$key];
            }
            $use_values = array();
            foreach ( $use_field_names as $field )
                $use_values[] = $use_values_hash[$field];
            unset( $use_values_hash );
            $value_text = implode( ", ", $use_values );

            $sql = "INSERT INTO $table ($field_text) VALUES($value_text)";
            $db->query( $sql );

            if ( isset( $def["increment_key"] ) &&
                 is_string( $def["increment_key"] ) &&
                 !( $obj->attribute( $def["increment_key"] ) > 0 ) )
            {
                $inc = $def["increment_key"];
                $id = $db->lastSerialID( $table, $inc );
                if ( $id !== false )
				{
                    $obj->setAttribute( $inc, $id );				
					$persistentobject_id = $id;
				}
            }
        }
        else
        {
            $use_fields = array_diff( array_keys( $fields ), array_merge( $keys, $exclude_fields ) );
            if ( count( $use_fields ) > 0 )
            {
                // If we filter out some of the fields we need to intersect it with $use_fields
                if ( is_array( $fieldFilters ) )
                    $use_fields = array_intersect( $use_fields, $fieldFilters );
                $use_field_names = array();
                foreach ( $use_fields as $key )
                {
                    if ( $db->useShortNames() && is_array( $fields[$key] ) && array_key_exists( 'short_name', $fields[$key] ) && strlen( $fields[$key]['short_name'] ) > 0 )
                        $use_field_names[$key] = $fields[$key]['short_name'];
                    else
                        $use_field_names[$key] = $key;
                }

                $field_text = "";
                $field_text_len = 0;
                $i = 0;


                foreach ( $use_fields as $key )
                {
                    $value = $obj->attribute( $key );

                    if ( $fields[$key]['datatype'] == 'float' || $fields[$key]['datatype'] == 'double' )
                    {
                        if ( $value === null )
                            $field_text_entry = $use_field_names[$key] . '=NULL';
                        else
                            $field_text_entry = $use_field_names[$key] . "=" . sprintf( '%F', $value );
                    }
                    else if ($fields[$key]['datatype'] == 'int' || $fields[$key]['datatype'] == 'integer' )
                    {
                        if ( $value === null )
                            $field_text_entry = $use_field_names[$key] . '=NULL';
                        else
                            $field_text_entry = $use_field_names[$key] . "=" . sprintf( '%d', $value );
                    }
                    else if ( in_array( $use_field_names[$key], $doNotEscapeFields ) )
                    {
                        $field_text_entry = $use_field_names[$key] . "=" .  $changedValueFields[$key];
                    }
                    else
                    {
                        $field_text_entry = $use_field_names[$key] . "='" . $db->escapeString( $value ) . "'";
                    }

                    $field_text_len += strlen( $field_text_entry );
                    $needNewline = false;
                    if ( $field_text_len > 60 )
                    {
                        $needNewline = true;
                        $field_text_len = 0;
                    }
                    if ( $i > 0 )
                        $field_text .= "," . ($needNewline ? "\n    " : ' ');
                    $field_text .= $field_text_entry;
                    ++$i;
                }
                $cond_text = self::conditionText( $key_conds );
                $sql = "UPDATE $table SET $field_text$cond_text";
                $db->query( $sql );
            }
        }
        $obj->setHasDirtyData( false );
		return $persistentobject_id;
    }

    /*!
     Calls conditionTextByRow with an empty row and \a $conditions.
    */
    static function conditionText( $conditions )
    {
        return eZPersistentObjectRemote::conditionTextByRow( $conditions, null );
    }

    /*!
     Generates an SQL sentence from the conditions \a $conditions and row data \a $row.
     If \a $row is empty (null) it uses the condition data instead of row data.
    */
    static function conditionTextByRow( $conditions, $row )
    {
        $db = self::getDB();

        $where_text = "";
        if ( is_array( $conditions ) and
             count( $conditions ) > 0 )
        {
            $where_text = " WHERE  ";
            $i = 0;
            foreach ( $conditions as $id => $cond )
            {
                if ( $i > 0 )
                    $where_text .= " AND ";
                if ( is_array( $row ) )
                {
                    $where_text .= $cond . "='" . $db->escapeString( $row[$cond] ) . "'";
                }
                else
                {
                    if ( is_array( $cond ) )
                    {
                        if ( is_array( $cond[0] ) )
                        {
                            $where_text .= $id . ' IN ( ';
                            $j = 0;
                            foreach ( $cond[0] as $value )
                            {
                                if ( $j > 0 )
                                    $where_text .= ", ";
                                $where_text .= "'" . $db->escapeString( $value ) . "'";
                                ++$j;
                            }
                            $where_text .= ' ) ';
                        }
                        else if ( is_array( $cond[1] ) )
                        {
                            $range = $cond[1];
                            $where_text .= "$id BETWEEN '" . $db->escapeString( $range[0] ) . "' AND '" . $db->escapeString( $range[1] ) . "'";
                        }
                        else
                        {
                          switch ( $cond[0] )
                          {
                              case '>=':
                              case '<=':
                              case '<':
                              case '>':
                              case '=':
                              case '<>':
                              case '!=':
                              case 'like':
                                  {
                                      $where_text .= $db->escapeString( $id ) . " " . $cond[0] . " '" . $db->escapeString( $cond[1] ) . "'";
                                  } break;
                              default:
                                  {
                                      eZDebug::writeError( "Conditional operator '$cond[0]' is not supported.",'eZPersistentObjectRemote::conditionTextByRow()' );
                                  } break;
                          }

                        }
                    }
                    else
                        $where_text .= $db->escapeString( $id ) . "='" . $db->escapeString( $cond ) . "'";
                }
                ++$i;
            }
        }
        return $where_text;
    }


    /*!
     Creates an SQL query out of the different parameters and returns an array with the result.
     If \a $asObject is true the array contains objects otherwise a db row.


     \param $def A definition array of all fields, table name and sorting
     \param $field_filters If defined determines the fields which are extracted (array of field names), if not all fields are fetched
     \param $conds \c null for no special condition or an associative array of fields to filter on.
                   Syntax is \c FIELD => \c CONDITION, \c CONDITION can be one of:
                   - Scalar value - Creates a condition where \c FIELD must match the value, e.g
                                    \code array( 'id' => 5 ) \endcode
                                    generates SQL
                                    \code id = 5 \endcode
                   - Array with two scalar values - Element \c 0 is the match operator and element \c 1 is the scalar value
                                    \code array( 'priority' => array( '>', 5 ) ) \endcode
                                    generates SQL
                                    \code priority > 5 \endcode
                   - Array with range - Element \c 1 is an array with start and stop of range in array
                                    \code array( 'type' => array( false, array( 1, 5 ) ) ) \endcode
                                    generates SQL
                                    \code type BETWEEN 1 AND 5 \endcode
                   - Array with multiple elements - Element \c 0 is an array with scalar values
                                    \code array( 'id' => array( array( 1, 5, 7 ) ) ) \endcode
                                    generates SQL
                                    \code id IN ( 1, 5, 7 ) \endcode
     \param $sorts An associative array of sorting conditions, if set to \c false ignores settings in \a $def,
                   if set to \c null uses settingss in \a $def.
                   Syntax is \c FIELD => \c DIRECTION. \c DIRECTION must either be string \c 'asc'
                   for ascending or \c 'desc' for descending.
     \param $limit An associative array with limitiations, can contain
                   - offset - Numerical value defining the start offset for the fetch
                   - length - Numerical value defining the max number of items to return
     \param $asObject If \c true then it will return an array with objects, objects are created from class defined in \a $def.
                      If \c false it will just return the rows fetch from database.
     \param $grouping An array of fields to group by or \c null to use grouping in defintion \a $def.
     \param $custom_fields Array of \c FIELD elements to add to SQL, can be used to perform custom fetches, e.g counts.
                           \c FIELD is an associative array containing:
                           - operation - A text field which is included in the field list
                           - name - If present it adds 'AS name' to the operation.
     \param $custom_tables Array of additional tables.
     \param $custom_conds String with sql conditions for 'WHERE' clause.

     A full example:
     \code
     $filter = array( 'id', 'name' );
     $conds = array( 'type' => 5,
                     'size' => array( false, array( 200, 500 ) ) );
     $sorts = array( 'name' => 'asc' );
     $limit = array( 'offset' => 50, 'length' => 10 );
     eZPersistentObjectRemote::fetchObjectList( $def, $filter, $conds, $sorts, $limit, true, false, null )
     \endcode

     Counting number of elements.
     \code
     $custom = array( array( 'operation' => 'count( id )',
                             'name' => 'count' ) );
     // Here $field_filters is set to an empty array, that way only count is used in fields
     $rows = eZPersistentObjectRemote::fetchObjectList( $def, array(), null, null, null, false, false, $custom );
     return $rows[0]['count'];
     \endcode

     Counting elements per type using grouping
     \code
     $custom = array( array( 'operation' => 'count( id )',
                             'name' => 'count' ) );
     $group = array( 'type' );
     $rows = eZPersistentObjectRemote::fetchObjectList( $def, array(), null, null, null, false, $group, $custom );
     return $rows[0]['count'];
     \endcode

     Example to fetch a result with custom conditions. The following example will fetch the attributes to
     the contentobject with id 1 and add the contentobject.name in each attribute row with the array key
     contentobject_name.
     \code
     $objectDef = eZContentObject::definition();
     $objectAttributeDef = eZContentObjectAttribute::definition();

     $fields = array();
     $conds = array( $objectDef['name'] . '.id' => 1 );
     $sorts = array( $objectAttributeDef['name'] . '.sort_key_string' => 'asc' );

     $limit = null;
     $asObject = false;
     $group = false;

     $customFields = array( $objectAttributeDef['name'] . '.*',
                             array( 'operation' => $objectDef['name'] . '.name',
                                    'name' => 'contentobject_name' ) );

     $customTables = array( $objectDef['name'] );

     $languageCode = 'eng-GB';
     $customConds = ' AND ' . $objectDef['name'] . '.current_version=' . $objectAttributeDef['name'] . '.version' .
                     ' AND ' . $objectDef['name'] . '.id=' . $objectAttributeDef['name'] . '.contentobject_id' .
                     ' AND ' . $objectAttributeDef['name'] . '.language_code=\'' . $languageCode . '\'';

     $rows = eZPersistentObjectRemote::fetchObjectList( $objectAttributeDef, $fields, $conds, $sorts, $limit, $asObject,
                                                  $group, $customFields, $customTables, $customConds );
     \endcode
    */
    static function fetchObjectList( $def,
                              $field_filters = null,
                              $conds = null,
                              $sorts = null,
                              $limit = null,
                              $asObject = true,
                              $grouping = false,
                              $custom_fields = null,
                              $custom_tables = null,
                              $custom_conds = null )
    {
        $db = self::getDB();
        $fields = $def["fields"];
        $tables = $def["name"];
        $class_name = $def["class_name"];
        if ( is_array( $custom_tables ) )
        {
            foreach( $custom_tables as $custom_table )
                $tables .= ', ' . $db->escapeString( $custom_table );
        }
        eZPersistentObjectRemote::replaceFieldsWithShortNames( $db, $fields, $conds );
        if ( is_array( $field_filters ) )
            $field_array = array_unique( array_intersect(
                                             $field_filters, array_keys( $fields ) ) );
        else
            $field_array = array_keys( $fields );
        if ( $custom_fields !== null and is_array( $custom_fields ) )
        {
            foreach( $custom_fields as $custom_field )
            {
                if ( is_array( $custom_field ) )
                {
                    $custom_text = $custom_field["operation"];
                    if ( isset( $custom_field["name"] ) )
                    {
                        $field_name = $custom_field["name"];
                        $custom_text .= " AS $field_name";
                    }
                }
                else
                {
                    $custom_text = $custom_field;
                }
                $field_array[] = $custom_text;
            }
        }
        eZPersistentObjectRemote::replaceFieldsWithShortNames( $db, $fields, $field_array );
        $field_text = '';
        $i = 0;
        foreach ( $field_array as $field_item )
        {
            if ( ( $i % 7 ) == 0 and
                 $i > 0 )
                $field_text .= ",       ";
            else if ( $i > 0 )
                $field_text .= ', ';
            $field_text .= $field_item;
            ++$i;
        }

        $where_text = eZPersistentObjectRemote::conditionText( $conds );
        if ( $custom_conds )
            $where_text .= $custom_conds;

        $sort_text = "";
        if ( $sorts !== false and ( isset( $def["sort"] ) or is_array( $sorts ) ) )
        {
            $sort_list = array();
            if ( is_array( $sorts ) )
            {
                $sort_list = $sorts;
            }
            else if ( isset( $def['sort'] ) )
            {
                $sort_list = $def["sort"];
            }
            if ( count( $sort_list ) > 0 )
            {
                $sort_text = " ORDER BY ";
                $i = 0;
                foreach ( $sort_list as $sort_id => $sort_type )
                {
                    if ( $i > 0 )
                        $sort_text .= ", ";
                    if ( $sort_type == "desc" )
                        $sort_text .= "$sort_id DESC";
                    else
                        $sort_text .= "$sort_id ASC";
                    ++$i;
                }
            }
        }

        $grouping_text = "";
        if ( isset( $def["grouping"] ) or ( is_array( $grouping ) and count( $grouping ) > 0 ) )
        {
            $grouping_list = $def["grouping"];
            if ( is_array( $grouping ) )
                $grouping_list = $grouping;
            if ( count( $grouping_list ) > 0 )
            {
                $grouping_text = " GROUP BY ";
                $i = 0;
                foreach ( $grouping_list as $grouping_id )
                {
                    if ( $i > 0 )
                        $grouping_text .= ", ";
                    $grouping_text .= "$grouping_id";
                    ++$i;
                }
            }
        }

        $db_params = array();
        if ( is_array( $limit ) )
        {
            if ( isset( $limit["offset"] ) )
            {
                $db_params["offset"] = $limit["offset"];
            }
            if ( isset( $limit['limit'] ) )
            {
                $db_params["limit"] = $limit["limit"];
            }
            else
            {
                $db_params["limit"] = $limit["length"];
            }
        }

        $sqlText = "SELECT $field_text
                    FROM   $tables" .
                    $where_text .
                    $grouping_text .
                    $sort_text;
        $rows = $db->arrayQuery( $sqlText,
                                 $db_params );

        // Indicate that a DB error occured.
        if ( $rows === false )
            return null;

        $objectList = eZPersistentObjectRemote::handleRows( $rows, $class_name, $asObject );
        return $objectList;
    }

    /*!
     Sets row id \a $id2 to have the placement of row id \a $id1.
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
    */
    static function swapRow( $table, $keys, $order_id, $rows, $id1, $id2 )
    {
        $db = self::getDB();
        $text = $order_id . "='" . $db->escapeString( $rows[$id1][$order_id] ) . "' WHERE ";
        $i = 0;
        foreach ( $keys as $key )
        {
            if ( $i > 0 )
                $text .= " AND ";
            $text .= $key . "='" . $db->escapeString( $rows[$id2][$key] ) . "'";
            ++$i;
        }
        return "UPDATE $table SET $text";
    }

    /*!
     Returns an order value which can be used for new items in table, for instance placement.
     Uses \a $def, \a $orderField and \a $conditions to figure out the currently maximum order value
     and returns one that is larger.
    */
    static function newObjectOrder( $def, $orderField, $conditions )
    {
        $db = self::getDB();
        $table = $def["name"];
        $keys = $def["keys"];
        $cond_text = eZPersistentObjectRemote::conditionText( $conditions );
        $rows = $db->arrayQuery( "SELECT MAX($orderField) AS $orderField FROM $table $cond_text" );
        if ( count( $rows ) > 0 and isset( $rows[0][$orderField] ) )
            return $rows[0][$orderField] + 1;
        else
            return 1;
    }

    /*!
     Moves a row in a database table. \a $def is the object definition.
     Uses \a $orderField to determine the order of objects in a table, usually this
     is a placement of some kind. It uses this order field to figure out how move
     the row, the row is either swapped with another row which is either above or
     below according to whether \a $down is true or false, or it is swapped
     with the first item or the last item depending on whether this row is first or last.
     Uses \a $conditions to figure out unique rows.
     \sa swapRow
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
    */
    static function reorderObject( $def,
                            /*! Associative array with one element, the key is the order id and values is order value. */
                            $orderField,
                            $conditions,
                            $down = true )
    {
        $db = self::getDB();
        $table = $def["name"];
        $keys = $def["keys"];

        reset( $orderField );
        $order_id = key( $orderField );
        $order_val = $orderField[$order_id];
        if ( $down )
        {
            $order_operator = ">=";
            $order_type = "asc";
            $order_add = -1;
        }
        else
        {
            $order_operator = "<=";
            $order_type = "desc";
            $order_add = 1;
        }
        $fields = array_merge( $keys, array( $order_id ) );
        $rows = eZPersistentObjectRemote::fetchObjectList( $def,
                                                      $fields,
                                                      array_merge( $conditions,
                                                                   array( $order_id => array( $order_operator,
                                                                                              $order_val ) ) ),
                                                      array( $order_id => $order_type ),
                                                      array( "length" => 2 ),
                                                      false );
        if ( count( $rows ) == 2 )
        {
            $swapSQL1 = eZPersistentObjectRemote::swapRow( $table, $keys, $order_id, $rows, 1, 0 );
            $swapSQL2 = eZPersistentObjectRemote::swapRow( $table, $keys, $order_id, $rows, 0, 1 );
            $db->begin();
            $db->query( $swapSQL1 );
            $db->query( $swapSQL2 );
            $db->commit();
        }
        else
        {
            $tmp = eZPersistentObjectRemote::fetchObjectList( $def,
                                                         $fields,
                                                         $conditions,
                                                         array( $order_id => $order_type ),
                                                         array( "length" => 1 ),
                                                         false );
            $where_text = eZPersistentObjectRemote::conditionTextByRow( $keys, $rows[0] );
            $db->query( "UPDATE $table SET $order_id='" . ( $tmp[0][$order_id] + $order_add ) .
                        "'$where_text"  );
        }
    }

    static function escapeArray( $array )
    {
        $db = self::getDB();
        $out = array();
        foreach( $array as $key => $value )
        {
            if ( is_array( $value ) )
            {
                $tmp = array();
                foreach( $value as $valueItem )
                {
                    $tmp[] = $db->escapeString( $valueItem );
                }
                $out[$key] = $tmp;
                unset( $tmp );
            }
            else
                $out[$key] = $db->escapeString( $value );
        }
        return $out;
    }

    /*!
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
     */
    static function updateObjectList( $parameters )
    {
        $db = self::getDB();
        $def = $parameters['definition'];
        $table = $def['name'];
        $fields = $def['fields'];
        $keys = $def['keys'];

        $updateFields = $parameters['update_fields'];
        $conditions = $parameters['conditions'];

        $query = "UPDATE $table SET ";
        $i = 0;
        $valueBound = false;

        foreach( $updateFields as $field => $value )
        {
            $fieldDef = $fields[ $field ];
            $numericDataTypes = array( 'integer', 'float', 'double' );
            if ( strlen( $value ) == 0 &&
                 is_array( $fieldDef ) &&
                 in_array( $fieldDef['datatype'], $numericDataTypes  ) &&
                 array_key_exists( 'default', $fieldDef ) &&
                 $fieldDef[ 'default' ] !== null )
            {
                $value = $fieldDef[ 'default' ];
            }

            $bindDataTypes = array( 'text' );
            if ( $db->bindingType() != eZDBInterface::BINDING_NO &&
                 strlen( $value ) > 2000 &&
                 is_array( $fieldDef ) &&
                 in_array( $fieldDef['datatype'], $bindDataTypes  )
                 )
            {
                $value = $db->bindVariable( $value, $fieldDef );
                $valueBound = true;
            }
            else
                $valueBound = false;

            if ( $i > 0 )
                $query .= ', ';
            if ( $valueBound )
                $query .= $field . "=" . $value;
            else
                $query .= $field . "='" . $db->escapeString( $value ) . "'";
            ++$i;
        }
        $query .= ' WHERE ';
        $i = 0;
        foreach( $conditions as $conditionKey => $condition )
        {
            if ( $i > 0 )
                $query .= ' AND ';
            if ( is_array( $condition ) )
            {
                $query .= $conditionKey . ' IN (';
                $j = 0;
                foreach( $condition as $conditionValue )
                {
                    if ( $j > 0 )
                        $query .= ', ';
                    $query .= "'" . $db->escapeString( $conditionValue ) . "'";
                    ++$j;
                }
                $query .= ')';
            }
            else
                $query .= $conditionKey . "='" . $db->escapeString( $condition ) . "'";
            ++$i;
        }
        $db->query( $query );
    }

    /// \privatesection
    /// Whether the data is dirty, ie needs to be stored, or not.
    public $PersistentDataDirty;
}

?>
