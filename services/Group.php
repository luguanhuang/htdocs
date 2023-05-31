<?php
//****************** [Class] 路由对象 ******************

//该类必须运行于 PHP5.0 及以上的平台
if ( intval(PHP_VERSION) < 5 ){ exit("<p>Run \"Station\" invite promotion your PHP arrives at 5.0 the above editions</p>");}

if (!class_exists('AtherFrameWork')){ require(dirname(__FILE__)."/AtherFrameWork.php"); }
require_once 'tlog.php';
//require_once '/var/www/html/htdocs/dao/IMerchantsResult.DB.php';
require_once dirname(dirname(__FILE__)).'/dao/IMerchantsResult.DB.php';
class Group extends AtherFrameWork{
	
	//__________________  构造/析构函数  ________________
	
	/**/
	function __construct(){
		$shandanDBFlg = 1;
		parent::__construct();
	}
	
	public $shandanDBFlg;
	function __destruct(){
		parent::__destruct();
	}
	
	//__________________  私有变量  ________________
	
	
	//__________________  只读属性(用方法代替)  ________________
	
	public static function _version()	{return '1.0';}					//版本
	public static function _build()		{return '11.11.25';}			//版本
	public static function _create()	{return '11.11.25';}			//创建
	public static function _classname()	{return __CLASS__;}				//名称
	public static function _developer()	{return "OldFour";}				//开发者
	public static function _copyright()	{return "Aether.CORP";}			//公司
	
	//__________________  只写属性  ________________
	
	
	//__________________  可读写属性  ________________
	
	
	//__________________  私有方法  ________________
	
	

	
	//__________________  公有方法  ________________
	
	
	public function addGroup($param)
	{
		if (empty($param['active']))
		{
			TLOG_MSG("Group::addGroup: empty");
		}
		else
		{
			TLOG_MSG("Group::addGroup: not empty");
		}
		TLOG_MSG("Group::addGroup: companyname=".$param['companyname']." active=".$param['active']." groupname=".$param['groupname']);
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$result = $merchantsResult_DB->AddGroup($param);
		$message= $result['result'] ? "add group failure: ".$result['errorMsg']:"add group succeed";
		//$this->log($this->Ary_Session['user'],$message);
		return $this->result_set((!$result['result'] ? -108 : 1807),$message);
	}
	
	public function editGroup($param)
	{
		if (empty($param['active']))
		{
			TLOG_MSG("Group::editGroup: empty");
		}
		else
		{
			TLOG_MSG("Group::editGroup: not empty");
		}
		TLOG_MSG("Group::editGroup:id=".$param['id']."  companyname=".$param['companyname']." active=".$param['active']." groupname=".$param['groupname']);
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$result = $merchantsResult_DB->EditGroup($param);
		$message= $result['result'] ? "edit group failure: ".$result['errorMsg']:"edit group succeed";
		//$this->log($this->Ary_Session['user'],$message);
		return $this->result_set((!$result['result'] ? -108 : 1807),$message);
	}
	
	public function delGroup($param)
	{
		
		TLOG_MSG("Group::delGroup:id=".$param['id']);
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$result = $merchantsResult_DB->delGroup($param);
		$message= $result['result'] ? "delete group failure: ".$result['errorMsg']:"delete group succeed";
		//$this->log($this->Ary_Session['user'],$message);
		return $this->result_set((!$result['result'] ? -108 : 1807),$message);
	}
	
	public function getGroupList($param){
		TLOG_MSG("Company::getGroupList: func begin");
		$urlparam = "";
	    $pagequery = array();
	    $output = array("data"=>array(),
	        "pagequery"=>array(),
	        "totalNum"=>0,
	        "retCode"=>0,
	        "sErrMsg"=>"");
		
		$data = explode(",", $param);
		$page = $data[0];
		if (intval($page) <= 0)
	    {
	        $page = 1;
	    }
		
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$ret =  $merchantsResult_DB->getGroupDetail1($param);
		if ($ret["result"] != 0)
	    {
	        $output["retCode"] = $ret["result"];
	        $output["sErrMsg"] = $ret["sErrMsg"];
	        $test = "SearchResultCB(".json_encode($output).")";
	        echo $test;
	        return -1;
	    }
		
		$datacnt=0;
		if(is_array($ret["data_list"]))
		{
			foreach($ret["data_list"] as $item)
			{
				$retItem = array();
	            $retItem["id"] = $item["id"];
				$retItem["name"] = $item["name"];
	            $retItem["company_id"] = $item["company_id"];
	            $retItem["status"] = $item["status"];
				$output["data"][] = $retItem;
				$datacnt++;
			}
		}
		
		$sqloption = "";
		$ids="";
		$cnt=0; 
		if(is_array($output["data"]))
		{
			foreach($output["data"] as $item)
			{
				if ($cnt == 0)
					$ids="(";
				//TLOG_MSG("Company::getGroupList: ids=$ids $item[company_id]");
				
				$cnt++;
				
				if ($datacnt == $cnt)
					$ids.="$item[company_id])";
				else
					$ids.="$item[company_id],";
			}
		}
		//TLOG_MSG("Company::getGroupList: ids=$ids");
		//substr($ids,0,strlen($ids)-1);
		//TLOG_MSG("Company::getGroupList:1 ids=$ids");
		if ($cnt > 0)
		{	
			$sqloption = "where id in$ids";
			//$sqloption = "where id in(1,2)";
			//$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
			$res =  $merchantsResult_DB->getCompanyName($sqloption);
			if ($ret["result"] != 0)
			{
				$output["retCode"] = $ret["result"];
				$output["sErrMsg"] = $ret["sErrMsg"];
				$test = "SearchResultCB(".json_encode($output).")";
				echo $test;
				return -1;
			}
			
			if(is_array($res["data_list"]))
			{
				if(is_array($output["data"]))
				{
					foreach($res["data_list"] as $key => $item1)
					{
						
						foreach($output["data"] as $k =>$data)
						{
							//TLOG_MSG("Company::getGroupList: id=".$item1["id"]." dataid=".$data["id"]);
							if ($data["company_id"] == $item1["id"])
							{
								$output["data"][$k]["companyname"] = $item1["companyname"];
								//$data["companyname"]=$item1["companyname"];
								//TLOG_MSG("equal Company::getGroupList: id=".$item1["id"]." dataid=".$data["company_id"]." name=".$data["companyname"]);
								//break;
							}
						}
					}
					
				}	
			}
			
		}
		
		
		$output["totalNum"] = $ret["totalNum"];
	    
	    $psize = 20;
	    $pshow = 10;
	    $result		= array();
	    $result		= FuncExt::pagination($ret["totalNum"], $psize, $page, $pshow);
		$output["recordcount"] = $result["recordcount"];
	    $output["pagecount"] = $result["pagecount"];
	    $output["pagesize"] = $result["pagesize"];
	    $output["absolutepage"] = $result["absolutepage"];
	    $output["previouspage"] = $result["previouspage"];
	    $output["nextpage"] = $result["nextpage"];
	    $output["startpage"] = $result["startpage"];
	    $output["endpage"] = $result["endpage"];
	    $output["pagequery"] = $pagequery;
	    $output["urlparam"] = $urlparam;
		
		//$paging	= FuncExt::pagination($loglist['total'], $limit, $page, 10);					//分页
		
		return $this->result_struct(
	        array(
	            'stateId'	=> -160,
	            'message'	=> 'get company info succeed',
	            'result'	=> $output
	        )
	    );
		
		/*if (!$this->user_popedom(
			array(__METHOD__, __CLASS__."::setRoute",__CLASS__."::deleteRoute")
		)){return $this->Ary_Popedom;}
		$lines= $this->file_array(_GLO_FILE_ROUTE_,"路由配置文件",1600,$interr,$strerr);
		$result = array();
		if ($lines===false)
		{
		    //return $this->result_set($interr,$strerr);
		    return $this->result_struct(
		        array(
		            "stateId"	=> -160,
		            "message"	=> "获取路由器设置完成",
		            "result"	=> $result
		        )
		    );
		}
		
		foreach ($lines as $line){
			$line = trim($line);
			if( $line=="" || substr($line,0,1)=="#" ) {continue;}
			$fields = explode("|", $line);
			for ($l=sizeof($fields); $l<5; $l++){ $fields[$l] = NULL; }
			$result[] = array(
				"type"		=> $fields[0],
				"ip"		=> $fields[1],
				"mask"		=> $fields[2],
				"netgate"	=> $fields[3],
				"ifname"	=> $fields[4]
			);
		}
		return $this->result_struct(
			array(
				"stateId"	=> -160,
				"message"	=> "获取路由器设置完成",
				"result"	=> $result
			)
		);*/
	}
	
	public function getStationList($page){
		TLOG_MSG("Station::getStationList: func begin1111");
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$stationList =  $merchantsResult_DB->getStationDetail();
		if(is_array($stationList["data_list"]))
		{
			foreach($stationList["data_list"] as $stationItem)
			{
				$result[] = array(
				"id"		=> $stationItem["id"],
				"macid"		=> $stationItem["macid"],
				"user"		=> $stationItem["user"],
				"ptype"		=> $stationItem["ptype"],
				"servip"	=> $stationItem["servip"],
				"servport"		=> $stationItem["servport"],
				"retry"		=> $stationItem["retry"],
				"timeout"		=> $stationItem["timeout"],
				"polltime"		=> $stationItem["polltime"],
				"connected"		=> $stationItem["connected"],
				"active"		=> $stationItem["active"]
				);
			}
		}
		$psize	= 20;
		$pshow	= 10;
		$result1		= FuncExt::pagination(27, $psize, $page, $pshow);		//分页
		$result1['userlist'] = $result;
		//$paging	= FuncExt::pagination($loglist['total'], $limit, $page, 10);					//分页
		
		return $this->result_struct(
			array(
				"stateId"	=> -160,
				"message"	=> "获取路由器设置完成",
				"result"	=> $result1
			)
		);
		
		/*if (!$this->user_popedom(
			array(__METHOD__, __CLASS__."::setRoute",__CLASS__."::deleteRoute")
		)){return $this->Ary_Popedom;}
		$lines= $this->file_array(_GLO_FILE_ROUTE_,"路由配置文件",1600,$interr,$strerr);
		$result = array();
		if ($lines===false)
		{
		    //return $this->result_set($interr,$strerr);
		    return $this->result_struct(
		        array(
		            "stateId"	=> -160,
		            "message"	=> "获取路由器设置完成",
		            "result"	=> $result
		        )
		    );
		}
		
		foreach ($lines as $line){
			$line = trim($line);
			if( $line=="" || substr($line,0,1)=="#" ) {continue;}
			$fields = explode("|", $line);
			for ($l=sizeof($fields); $l<5; $l++){ $fields[$l] = NULL; }
			$result[] = array(
				"type"		=> $fields[0],
				"ip"		=> $fields[1],
				"mask"		=> $fields[2],
				"netgate"	=> $fields[3],
				"ifname"	=> $fields[4]
			);
		}
		return $this->result_struct(
			array(
				"stateId"	=> -160,
				"message"	=> "获取路由器设置完成",
				"result"	=> $result
			)
		);*/
	}
	
	/*
	保存路由器设置
	number: 1700
	*/
	public function setRoute( $param,$action="add" ){
		$action = strtolower(strval($action));
		if ($action =="del" ){
			$method = __CLASS__."::delRoute";
			$metnum = "022";
			$title	= "删除路由配置";
		}
		/*
		elseif ($action =="mod" ){
			$method = __METHOD__;
			$metnum = "021";
			$title	= "修改路由配置";
		}
		*/
		else{
			$method = __METHOD__;
			$metnum = "020";
			$title	= "添加路由配置";
		}
		if (!$this->user_popedom($method)){return $this->Ary_Popedom;}
		
		if ($param['type'] == "net")
		{
		    $keys	= array(
		        'type'		=>	array('name'=>'路由类别', explode(",",parent::_STR_ROUTER_TYPE_)),
		        'ip'		=>	array('name'=>'网段', 	'rule'=>parent::_REG_IPADD_),
		        'mask'		=>	array('name'=>'子网掩码','rule'=>parent::_REG_IPMASK_, 'default'=>parent::_STR_MASK_DEF_),
		        'netgate'	=>	array('name'=>'网关地址','rule'=>parent::_REG_IPADD_),
		        'ifname'	=>	array('name'=>'接口名称', explode(",",parent::_STR_ROUTER_INAME_)),
		    );
		    
		    $param	= $this->validateArgs($param,$keys,1080,$interr,$strerr);
		}
		else 
		{
		    $keys	= array(
		        'type'		=>	array('name'=>'路由类别', explode(",",parent::_STR_ROUTER_TYPE_)),
		        'ip'		=>	array('name'=>'网段', 	'rule'=>parent::_REG_IPADD_),
		        'ifname'	=>	array('name'=>'接口名称', explode(",",parent::_STR_ROUTER_INAME_)),
		    );
		    
		    $param	= $this->validateArgs($param,$keys,1080,$interr,$strerr);
		}
		
		if ( !$param ){ return $this->result_set($interr, $strerr); }
		$sendarg = _GLO_FILE_ROUTE_."|".$param['type']."|".$param['ip']."|".$param['mask']."|".$param['netgate']."|".$param['ifname'];
		//发送socket
		$result	= $this->socket_send($metnum,$sendarg);
		$message= !$result['state'] ? $title."失败：".$result['errtxt']:$title."成功";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set(($result['state'] ? -108 : 1807),$message);
	}
	
	public function addStation($param)
	{
		TLOG_MSG("Station::addStation: macid=".$param['macid']." user=".$param['user']." ptype=".$param['ptype']." servip=".$param['servip']." servport=".$param['servport']." retry=".$param['retry']." timeout=".$param['timeout']." polltime=".$param['polltime']." connected=".$param['connected']." active=".$param['active']);
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$result = $merchantsResult_DB->AddStationInfo($param);
		$message= $result['result'] ? "add device failure: ".$result['errorMsg']:"add device succeed";
		//$this->log($this->Ary_Session['user'],$message);
		return $this->result_set((!$result['result'] ? -108 : 1807),$message);
	}
	
	/*
	删除路由器设置
	number: 1900
	*/
	public function delRoute($param){
		return $this->setRoute($param,"del");
	}
}
?>