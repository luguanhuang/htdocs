<?php

/*DB class contains some methods
**@author: terenceli,billwu,
**/
class DataBase  {

	private $db_host = DATABASE_IP;
	private $db_user = DATABASE_USER;
	private $db_passwd = DATABASE_PASSWORD;
	private $db_dbname = DATABASE_NAME;
	private $db_charset = DATABASE_CHARSET;

	private $db_conn = NULL;

	/*constructor of MySQL ,which extends the abstract class database
	**
	**@param: string
	**@param: string
	**@param: string
	**@param: string
	*/


	function __construct($dbname='', $db_ip='',$db_user='',$db_passwd='', $db_charset='' ) {
        if ($db_ip != "") {
            $this->db_host = $db_ip;
        }
        if ($db_user != "") {
            $this->db_user = $db_user;
        }
        if ($db_passwd != "") {
            $this->db_passwd = $db_passwd;
        }
        if ($db_charset != "") {
            $this->db_charset = $db_charset;
        }
        if ($dbname != "") {
            $this->db_dbname = $dbname;
        }

		$this->db_conn   = NULL;

		if ( ($this->db_conn = mysqli_connect( $this->db_host, $this->db_user, $this->db_passwd )) ) {
				//mysql_query( $this->charset );
				mysqli_query($this->db_conn, "SET NAMES ".$this->db_charset);
				mysqli_select_db ($this->db_conn, $this->db_dbname);
				

		} else {
				TLOG_MSG("DataBase: can't connect to db");
				die ( "cannot connect to the database,host:".$this->db_host.",user:".$this->db_user.",database:".$this->db_dbname );
		}
	}

	//--------------------------------------------------------------------------

	/*destructor of MySQL class,close the connection between client and database*/

	function __destruct( ) {
		$this->close();
	}

	function close() {
		if( $this->db_conn ) {
			if (is_resource( $this->db_conn))
			{
					mysqli_close($this->db_conn);		
			}
			
			$this->db_conn = NULL;
		}
	}

	//--------------------------------------------------------------------------

	/*execute function,finishing tasks except query such insert, update, delete
	**@param: string --SQL sentences
	**@param: string --table name
	*/

	function execute ( $sql, $operateType ) {
	    $operateType = strtoupper($operateType);
    
		switch ( $operateType ) {
			case 'UPDATE':
				if ( mysql_query ( $sql ,$this->db_conn) ) {
					if( mysql_affected_rows() <= 0) {
						return false;
					} else {
						return true;
					}
				} else {
					mysql_error ();
				}

				break;

			case 'INSERT':
				if ( mysql_query ( $sql, $this->db_conn ) ) {
					return mysql_insert_id();
				} else {
					mysql_error ();
				}

				break;

			case 'DELETE':
				if ( mysql_query ( $sql, $this->db_conn ) ) {
					return true;
				} else {
					mysql_error ();
				}

				break;
				
		    case 'SELECT':
                return $this->query ( $sql );
			    
			    
			default:
			    break;
		}

		return false;
	}

	function execute_v2 ( $sql, $operateType ) {
		
	    $result = array("result"=>-1,"data_list"=>array(),"insert_id"=>0,"errorNo"=>0,"errorMsg"=>"","errorSql"=>"");
	    
	    
	    $operateType = strtoupper($operateType);
    	
		switch ( $operateType ) {
			case 'UPDATE':
				if ( mysqli_query ($this->db_conn,  $sql) ) {
					if( mysqli_affected_rows($this->db_conn) <= 0) {
						/*$result['result'] = -2;
						$result['errorMsg'] = "mysql_affected_rows return 0";*/
						$result['result'] = 0;
					} else {
						$result['result'] = 0;
						
					}
				} else {
					$result['result'] = 1;
					$result['errorSql'] = $sql;
					$result['errorNo'] = mysqli_errno($this->db_conn);
					$result['errorMsg'] = mysqli_error($this->db_conn);
					
				}

				break;

			case 'INSERT':
				if ( mysqli_query ($this->db_conn,  $sql) ) {
					$result['result'] = 0;
					$result['insert_id'] = mysqli_insert_id($this->db_conn);
				} else {
					$result['result'] = 1;
					$result['errorSql'] = $sql;
					$result['errorNo'] = mysqli_errno($this->db_conn);
					$result['errorMsg'] = mysqli_error($this->db_conn);
				}

				break;

			case 'DELETE':
				if ( mysqli_query ($this->db_conn,  $sql) ) {
					if( mysqli_affected_rows($this->db_conn) <= 0) {
						$result['result'] = -2;
						$result['errorMsg'] = "mysql_affected_rows return 0";
					} else {
						$result['result'] = 0;
						
					}
				} else {
					$result['result'] = 1;
					$result['errorSql'] = $sql;
					$result['errorNo'] = mysqli_errno($this->db_conn);
					$result['errorMsg'] = mysqli_error($this->db_conn);
				}

				break;
				
		    case 'SELECT':
                $query_result = $this->query ( $sql );
                if (!is_array($query_result)) {
					$result['result'] = 1;
					$result['errorSql'] = $sql;
					$result['errorNo'] = mysqli_errno($this->db_conn);
					$result['errorMsg'] = mysqli_error($this->db_conn);
				    
				} else {
					$result['result'] = 0;
				    $result['data_list'] = $query_result;
				}
			    break;
			default:
			    break;
		}

		return $result;
	}

	//---------------------------------------------------------------------------------

	/*function insertData insert a full row of  or specific items data in one time
	**@param: array     $key to column name and $value to the value of the column
	**@param: string    table name
	**return: int 		insert_id
	*/

	function insertData ( $table, $data ) {
		$key = implode ( ',', array_keys ( $data ) );

		$i   = 0;
		$value = "";
		foreach ( $data as $values ) {
			if ( $i < ( count ( $data ) - 1 ) ) {
				$value .= "'".$values."',";
				$i++;
			} elseif ( $i == ( count ( $data ) - 1 ) ) {
				$value .= "'".$values."'";
				$i++;
			} else {
				break;
			}
		}

		$sql = "insert into $table (".$key.") values (".$value.")";
		//testing
		//echo $sql;

		return $this -> execute ( $sql, 'INSERT' );
	}

	//-----------------------------------------------------------------------------------

	/*function updateData update the specific data of a table
	**@param: array      $key to column name and $value to the value of the column
	**@param: string     table name
	**@param: int		 table id
	**return; bool       true/false
	*/

	function updateData ( $table, $data, $where ) {
		if ( empty ( $table ) || empty ( $data ) || empty ( $where) ) {
			die ( 'donot connect or parament empty' );
		}

		foreach ( $data as $key => $value ) {
			$format_ar[] = $key."='".$value."'";
		}

		$value_string = implode ( ",", $format_ar );
		$sql    = "update $table set ".$value_string." where 1=1 ".$this->addWhere_and_or( $where );
		//testing
		//die( $sql );

		return $this -> execute ( $sql, 'UPDATE' );
	}

	/*
	 * $table: database table that we are going to delete from
	 * $where: we can specific the condiction to the target the item we want to delete
	 */
	function deleteData ( $table, $where ) {
		if ( empty ( $table ) || empty ( $where ) ) {
			die ( 'donot connect or parament empty' );
		}

		$sql  = "delete from $table ";
		$sql .= $this->addWhere_and_or( $where );

		return $this -> execute ( $sql, 'DELETE' );
	}

	//------------------------------------------------------------------------------------

	/*addWhere function add a whwere condiction to the SQL query sentences
	**@param: 2-array			couples of cindiction
	**@param: string   			determine which method to add and/or
	**@return: string	    	the result sentence added where condiction
	*/

	function addWhere_and_or ( $sql_array, $which = '' ) {
		if ( count( $sql_array ) == 0 ) {
		    return "";
		}
		$sql = "  ";

		$which = ( $which == '' ) ? " and " : " or ";



        for($i = 0; $i < sizeof($sql_array); $i++) {
            $one_condition = $sql_array[$i];
            foreach($one_condition as $key=>$value) {
                $sql.= $which;
                $sql .= $key."='".$value."'";
            }
        }

        
    
		return $sql;
	}

	//----------------------------------------------------------------------------------------

	/*query function to return the information queryed from database
	**@param: string	SQL query sentence
	**@return: 2-array  a set of information query from database
	*/

	function query ( $sql ) {
		$result_data = array ();

		if( ($resource = mysqli_query ($this->db_conn, $sql)) === false ) {
			//die( 'really false:'.mysql_errno()."=>".mysql_error() );
			return false;
		}
		
		//die( "rows:".mysql_num_rows( $resource ) );

		if( ($num = mysqli_num_rows( $resource )) == 0 ) {
			return array();

		} else {
			while ( $row_result = mysqli_fetch_assoc ( $resource ) ) {
				$result_data[]  = $row_result;
			}

			//print_r( $result_data );die();

			return $result_data;
		}

		return false;
	}

	//-----------------------------------------------------------------------------------------

	/*
	**get_row function to get a row of data from database, you can specific a key or it will return a row of information
	**@param: string		you can give the column name which you want to $key so that it returns only the information you want. default: *
	**@param: string		table name
	**@param: string		where condiction narrow the informatin to query
	**@param: string		the method of where: and/or
	**return: array			the oinformation you want
	*/

	function get_row ( $table, $key = '*', $where = '', $which = '' ) {
		$result = array ();

		$where  = ( $where == '' ) ? '' : $this -> addWhere_and_or ( $where, $which );
		$sql    = "select ".$key." from {$table} ".$where;

		//test
		//die( $sql );

		$result = $this->query( $sql );

		if( $result === false ) {
			return false;
		} else if( $result === NULL ) {
			return NUll;
		}

		return $result[0];
	}

	//------------------------------------------------------------------------------------------

	/*
	**get_all funtion get the whole set of the data of the table you give
	**@param: string			table name
	**@param: string			where condiction narrow the informatin to query
	**@param: string			the method of where: and/or
	*/

	function get_all ( $table, $where='', $which='' ) {
		$where  = ( $where == '' ) ? '' : $this -> addWhere_and_or ( $where, $which );
		$sql    = "select * from {$table} ".$where;

		//die( $sql );

		return $this -> query( $sql );
	}

	//--------------------------------------------------------------------------------------------

	/*
	**get_total_item return the total item of the table you give
	**@param: string		table name
	**@return int			the total item of your table
	*/

	function get_total_item ( $table, $where="" ) {
		$result = array ();
		if( $where != "" && is_array( $where ) ) {
			$sql    = "select count(*) as total from {$table} ".$this->addWhere_and_or( $where );

		} else {
			$sql    = "select count(*) as total from $table";
		}

		//die( $sql );
		$result = $this -> query( $sql );

		return $result[0]['total'];
	}
	function transaction_action($action) {
		if ($action == "BEGIN" || $action == "COMMIT" || $action == "ROLLBACK" || $action == "END") {
			mysql_query($action, $this->db_conn);
		} else {
			die("only accept BEGIN or COMMIT or ROLLBACK or END parameter");
		}
	}
	/*
	 * 事务执行方法
	 */
	function mysql_commit($sql){
//		var_dump($sql);
		mysql_query("BEGIN", $this->db_conn);
		$res = mysql_query($sql, $this->db_conn);
		$result = false;
		//因为使用事务，只要有一条失败就不执行
		if($res){
//	      	  echo "<br>111<br>";
		      $result = true;
		      mysql_query("COMMIT", $this->db_conn);
		}
		else{
//	         echo "<br>222<br>";
		     mysql_query("ROLLBACK", $this->db_conn);
		}
		mysql_query("END", $this->db_conn); 
//		echo '<br>done<br>';
		return $result;
	}

}//-------------------------------------------------------------------------------------------------end of class

?>
