<?php
require_once dirname(__FILE__).'/cfg/database_config.php';
require_once dirname(__FILE__).'/../common/database_module.php';

class testDB 
{
	 var $DB;
   function __construct()
   {
   		 $num  = func_num_args();  
   		 if (0 == $num)
   		 {
				TLOG_MSG("testDB: func begin0");
   		 		//TLOG_MSG("IMerchantsResult_DB::__construct: [num]=".$num." [MODE]=".$MODE); 
   		 		$this->DB = new DataBase();
   		 }
   		 else if (1 == $num)
   		 {
				TLOG_MSG("testDB: func begin1 TEST_DATABASE_NAME=".TEST_DATABASE_NAME);
   		 		//TLOG_MSG("IMerchantsResult_DB::__construct: param is more than zero [num]=".$num." [MODE]=".MODE); 
   		 		/*TLOG_MSG("IMerchantsResult_DB::__construct: param is more than zero [SHOW_DEAL_DATABASE_NAME]=".SHOW_DEAL_DATABASE_NAME. \
   		 		" [SHOW_DEAL_DATABASE_IP]=".SHOW_DEAL_DATABASE_IP." [SHOW_DEAL_DATABASE_USER]=".SHOW_DEAL_DATABASE_USER. \
   		 		" [SHOW_DEAL_DATABASE_PASSWORD]=".SHOW_DEAL_DATABASE_PASSWORD." [SHOW_DEAL_DATABASE_CHARSET]=".SHOW_DEAL_DATABASE_CHARSET);*/ 
   		 		$this->DB = new DataBase(TEST_DATABASE_NAME, TEST_DATABASE_IP, TEST_DATABASE_USER, TEST_DATABASE_PASSWORD, TEST_DATABASE_CHARSET);
   		 }
   }
    
	function getConfig()
	{
		$totalSql = "select id, hgkqnode, hostnode, regtime, updev_node, downdev_node from HA_REGSOFTHW limit 1";
   		TLOG_MSG("testDB::getConfig: [sql]=".$totalSql); 
		$ret = $this->DB->execute_v2($totalSql, 'SELECT');
    	 if ($ret["result"] != 0)
	     {
	     	TLOG_ERR("testDB::getConfig: Call DB->execute_v2 error [error msg]=".$ret["errorMsg"]);  
	         $returnArray["result"] = $ret["result"];
	         $returnArray["sErrMsg"] = $ret["errorMsg"];
	         return $returnArray;  
	     }
		 
		 $returnArray["data_list"] = $ret["data_list"];
	     TLOG_MSG("testDB::getConfig: func end");  
	     return $returnArray;
	}
	
    function close()
    {
    		$this->DB->close();
    }
}

?>
