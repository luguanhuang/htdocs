<?php
//****************** [Class] 路由对象 ******************

//该类必须运行于 PHP5.0 及以上的平台
if ( intval(PHP_VERSION) < 5 ){ exit("<p>Run \"Station\" invite promotion your PHP arrives at 5.0 the above editions</p>");}

if (!class_exists('AtherFrameWork')){ require(dirname(__FILE__)."/AtherFrameWork.php"); }
require_once 'tlog.php';
//require_once '/var/www/html/htdocs/dao/IMerchantsResult.DB.php';
require_once dirname(dirname(__FILE__)).'/dao/IMerchantsResult.DB.php';
class Station extends AtherFrameWork{
	
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
		
		
	}
	
	
	public function getStationCompAllInfo($param){
		$var=explode(",",$param);
		TLOG_MSG("Station::getStationCompAllInfo: func begin page=".$param." var0=".$var[0]." var1=".$var[1]);
		$urlparam = "";
	    $pagequery = array();
	    $output = array("data"=>array(),
	        "pagequery"=>array(),
	        "totalNum"=>0,
	        "retCode"=>0,
	        "sErrMsg"=>"");
		
		/*if (intval($page) <= 0)
	    {
	        $page = 1;
	    }*/
		
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$ret =  $merchantsResult_DB->getCompanyDetail(1);
		if ($ret["result"] != 0)
	    {
	        $output["retCode"] = $ret["result"];
	        $output["sErrMsg"] = $ret["sErrMsg"];
	        $test = "SearchResultCB(".json_encode($output).")";
	        echo $test;
	        return -1;
	    }
		
		
		$id = "";
		if(is_array($ret["data_list"]))
		{
			foreach($ret["data_list"] as $item)
			{
				
				$retItem = array();
	            
	            $retItem["id"] = $item["id"];
	            $retItem["companyname"] = $item["companyname"];
	            $retItem["status"] = $item["status"];
				$retItem["created_date"] = $item["created_date"];
				$output["data"][] = $retItem;
			}
		}
		$output["totalNum"] = $ret["totalNum"];
		$return =  $merchantsResult_DB->getSpecialGroupName($var[0]);
		if ($return["result"] != 0)
	    {
	        $output["retCode"] = $return["result"];
	        $output["sErrMsg"] = $return["sErrMsg"];
	        $test = "SearchResultCB(".json_encode($output).")";
	        echo $test;
	        return -1;
	    }
		
		if(is_array($return["data_list"]))
		{
			foreach($return["data_list"] as $item)
			{
				
				$retItem = array();
	            
	            $retItem["id"] = $item["id"];
	            $retItem["name"] = $item["name"];
				$output["datainfo"][] = $retItem;
			}
		}
		
		$sqloption = "  where company_id='".$var[0]."' and group_id='".$var[1]."'";
		$resStation =  $merchantsResult_DB->getStationName($sqloption);
		if ($resStation["result"] != 0)
		{
			$output["retCode"] = $resStation["result"];
			$output["sErrMsg"] = $resStation["sErrMsg"];
			$test = "SearchResultCB(".json_encode($output).")";
			echo $test;
			return -1;
		}
		
		if(is_array($resStation["data_list"]))
		{
			foreach($resStation["data_list"] as $item)
			{
				
				$retItem = array();
	            
	            $retItem["id"] = $item["id"];
	            $retItem["name"] = $item["name"];
				$output["stainfo"][] = $retItem;
			}
		}
	    
		$sqloption = "";
		$resStation =  $merchantsResult_DB->getPtype($sqloption);
		if ($resStation["result"] != 0)
		{
			$output["retCode"] = $resStation["result"];
			$output["sErrMsg"] = $resStation["sErrMsg"];
			$test = "SearchResultCB(".json_encode($output).")";
			echo $test;
			return -1;
		}
		
		if(is_array($resStation["data_list"]))
		{
			foreach($resStation["data_list"] as $item)
			{
				
				$retItem = array();
	            
	            $retItem["id"] = $item["id"];
	            $retItem["name"] = $item["name"];
				$output["ptypeinfo"][] = $retItem;
			}
		}
		
	    $psize = 20;
	    $pshow = 10;
	    /*$result		= array();
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
	    $output["urlparam"] = $urlparam;*/
		
		//$paging	= FuncExt::pagination($loglist['total'], $limit, $page, 10);					//分页
		
		return $this->result_struct(
	        array(
	            'stateId'	=> -160,
	            'message'	=> 'get company info succeed',
	            'result'	=> $output
	        )
	    );
		
		
	}
	
	
	
	public function getConnList($param){
		TLOG_MSG("Station::getConnList: func begin");
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
		$ret =  $merchantsResult_DB->getConnList();
		if ($ret["result"] != 0)
	    {
	        $output["retCode"] = $ret["result"];
	        $output["sErrMsg"] = $ret["sErrMsg"];
	        $test = "SearchResultCB(".json_encode($output).")";
	        echo $test;
	        return -1;
	    }
		
		if(is_array($ret["data_list"]))
		{
			foreach($ret["data_list"] as $item)
			{
				$retItem = array();
	            $retItem["id"] = $item["id"];
				$retItem["connected"] = $item["connected"];
				
				$output["data"][] = $retItem;
			}
		}
		
		
		return $this->result_struct(
	        array(
	            'stateId'	=> -160,
	            'message'	=> 'get Station info succeed',
	            'result'	=> $output
	        )
	    );
		
	}
	
	public function getDeviceList($param){
		TLOG_MSG("Station::getDeviceList: func begin");
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
		$ret =  $merchantsResult_DB->getDeviceList($param);
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
				$retItem["macid"] = $item["macid"];
	            $retItem["devname"] = $item["devname"];
				$retItem["ptype"] = $item["ptype"];
	            $retItem["servip"] = $item["servip"];
				$retItem["servport"] = $item["servport"];
	            $retItem["retry"] = $item["retry"];
				$retItem["timeout"] = $item["timeout"];
	            $retItem["polltime"] = $item["polltime"];
				$retItem["company_id"] = $item["company_id"];
	            $retItem["group_id"] = $item["group_id"];
				$retItem["station_id"] = $item["station_id"];
				$retItem["devicedesc"] = $item["devicedesc"];
				$retItem["templatelocation"] = $item["templatelocation"];
	            $retItem["active"] = $item["active"];
				$retItem["socktype"] = $item["socktype"];
				$retItem["connected"] = $item["connected"];
				$retItem["pic1filelocation"] = $item["pic1filelocation"];
				$retItem["pic2filelocation"] = $item["pic2filelocation"];
				$retItem["pic3filelocation"] = $item["pic3filelocation"];
				$retItem["pic4filelocation"] = $item["pic4filelocation"];
				$retItem["mainpagediv"] = $item["mainpagediv"];
				TLOG_MSG("Station::getDeviceList: pic1filelocation=".$retItem["pic1filelocation"]);
				$output["data"][] = $retItem;
				$datacnt++;
			}
		}
		
		$sqloption = "";
		$idscompany="";
		$idsgroup="";
		$idsstation="";
		$ptype="";
		$cnt=0; 
		if(is_array($output["data"]))
		{
			foreach($output["data"] as $item)
			{
				if ($cnt == 0)
				{
					$idscompany="(";
					$idsgroup="(";
					$idsstation="(";
					$ptype="(";
				}
					
				$cnt++;
				
				if ($datacnt == $cnt)
				{
					$idscompany.="$item[company_id])";
					$idsgroup.="$item[group_id])";
					$idsstation.="$item[station_id])";
					$ptype.="$item[ptype])";
				}
				else
				{
					$idscompany.="$item[company_id],";
					$idsgroup.="$item[group_id],";
					$idsstation.="$item[station_id],";
					$ptype.="$item[ptype],";
				}
					
			}
		}
		
		if ($cnt > 0)
		{	
			$sqloption = "where id in$idscompany";
			$resCompany =  $merchantsResult_DB->getCompanyName($sqloption);
			if ($ret["result"] != 0)
			{
				$output["retCode"] = $ret["result"];
				$output["sErrMsg"] = $ret["sErrMsg"];
				$test = "SearchResultCB(".json_encode($output).")";
				echo $test;
				return -1;
			}
			
			$sqloption = "where id in$idsgroup";
			$resGroup =  $merchantsResult_DB->getGroupName($sqloption);
			if ($ret["result"] != 0)
			{
				$output["retCode"] = $ret["result"];
				$output["sErrMsg"] = $ret["sErrMsg"];
				$test = "SearchResultCB(".json_encode($output).")";
				echo $test;
				return -1;
			}
			
			$sqloption = "where id in$idsstation";
			$resStation =  $merchantsResult_DB->getStationName($sqloption);
			if ($ret["result"] != 0)
			{
				$output["retCode"] = $ret["result"];
				$output["sErrMsg"] = $ret["sErrMsg"];
				$test = "SearchResultCB(".json_encode($output).")";
				echo $test;
				return -1;
			}
			
			$sqloption = "where id in$ptype";
			$resPtype =  $merchantsResult_DB->getPtype($sqloption);
			if ($ret["result"] != 0)
			{
				$output["retCode"] = $ret["result"];
				$output["sErrMsg"] = $ret["sErrMsg"];
				$test = "SearchResultCB(".json_encode($output).")";
				echo $test;
				return -1;
			}
			
			if(is_array($resCompany["data_list"]))
			{
				if(is_array($output["data"]))
				{
					foreach($resCompany["data_list"] as $key => $item)
					{
						foreach($output["data"] as $k =>$data)
						{
							if ($data["company_id"] == $item["id"])
							{
								$output["data"][$k]["companyname"] = $item["companyname"];
							}
						}
					}
					
				}	
			}
			
			if(is_array($resGroup["data_list"]))
			{
				if(is_array($output["data"]))
				{
					foreach($resGroup["data_list"] as $key => $item)
					{
						foreach($output["data"] as $k =>$data)
						{
							if ($data["group_id"] == $item["id"])
							{
								$output["data"][$k]["groupname"] = $item["name"];
							}
						}
					}
					
				}	
			}
			
			if(is_array($resStation["data_list"]))
			{
				if(is_array($output["data"]))
				{
					foreach($resStation["data_list"] as $key => $item)
					{
						foreach($output["data"] as $k =>$data)
						{
							if ($data["station_id"] == $item["id"])
							{
								$output["data"][$k]["stationname"] = $item["name"];
							}
						}
					}
					
				}	
			}
			
			if(is_array($resPtype["data_list"]))
			{
				if(is_array($output["data"]))
				{
					foreach($resPtype["data_list"] as $key => $item)
					{
						foreach($output["data"] as $k =>$data)
						{
							if ($data["ptype"] == $item["id"])
							{
								$output["data"][$k]["ptypename"] = $item["name"];
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
		
		return $this->result_struct(
	        array(
	            'stateId'	=> -160,
	            'message'	=> 'get Station info succeed',
	            'result'	=> $output
	        )
	    );
		
	}
	
	public function getStationNameInfo($param)
	{
		
		$output = array("datainfo"=>array(),
	        "pagequery"=>array(),
	        "totalNum"=>0,
	        "retCode"=>0,
	        "sErrMsg"=>"");
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$return =  $merchantsResult_DB->getSpecialStationName($param);
		if ($return["result"] != 0)
	    {
	        $output["retCode"] = $return["result"];
	        $output["sErrMsg"] = $return["sErrMsg"];
	        $test = "SearchResultCB(".json_encode($output).")";
	        echo $test;
	        return -1;
	    }
		
		$str="";
		if(is_array($return["data_list"]))
		{
			foreach($return["data_list"] as $item)
			{
				
				$retItem = array();
	            
				$str.=strval($item["id"]);
				$str.=":";
				$str.=strval($item["name"]);
				$str.="|";
	            $retItem["id"] = $item["id"];
	            $retItem["name"] = $item["name"];
				$output["datainfo"][] = $retItem;
			}
		}
		
		//return $this->result_set(-108,"test");
		//$str = "北京奥运";
		$arrStr = explode("|", $str); 
		if ($arrStr[count($arrStr)-1] == "") {
			$str = substr($str, 0, -1);
			TLOG_MSG("Station::getStationNameInfo: got it");
		}

		TLOG_MSG("Station::getStationNameInfo: companyname=[$str]  ".$param['companyname']);
		return $this->result_struct(
			array(
				"stateId"	=> "",
				"message"	=> $str,
				//"message"	=> $output,
				"result"	=>$output
			)
		);
	}
	
	public function getDevNameInfo($param)
	{
		
		$output = array("datainfo"=>array(),
	        "pagequery"=>array(),
	        "totalNum"=>0,
	        "retCode"=>0,
	        "sErrMsg"=>"");
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$return =  $merchantsResult_DB->getSpecialDevName($param);
		if ($return["result"] != 0)
	    {
	        $output["retCode"] = $return["result"];
	        $output["sErrMsg"] = $return["sErrMsg"];
	        $test = "SearchResultCB(".json_encode($output).")";
	        echo $test;
	        return -1;
	    }
		
		$str="";
		if(is_array($return["data_list"]))
		{
			foreach($return["data_list"] as $item)
			{
				
				$retItem = array();
	            
				$str.=strval($item["id"]);
				$str.=":";
				$str.=strval($item["devname"]);
				$str.="|";
	            $retItem["id"] = $item["id"];
	            $retItem["name"] = $item["devname"];
				$output["datainfo"][] = $retItem;
			}
		}
		
		//return $this->result_set(-108,"test");
		//$str = "北京奥运";
		$arrStr = explode("|", $str); 
		if ($arrStr[count($arrStr)-1] == "") {
			$str = substr($str, 0, -1);
			
		}

		TLOG_MSG("Station::getStationNameInfo: companyname=[$str]  ".$param['companyname']);
		return $this->result_struct(
			array(
				"stateId"	=> "",
				"message"	=> $str,
				//"message"	=> $output,
				"result"	=>$output
			)
		);
	}
	
	public function getStationInfoList($param){
		TLOG_MSG("Company::getStationInfoList: func begin param=$param ");
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
		$ret =  $merchantsResult_DB->getStationList($param);
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
				$retItem["group_id"] = $item["group_id"];
	            $retItem["status"] = $item["status"];
				$output["data"][] = $retItem;
				$datacnt++;
			}
		}
		
		$sqloption = "";
		$idscompany="";
		$idsgroup="";
		$cnt=0; 
		if(is_array($output["data"]))
		{
			foreach($output["data"] as $item)
			{
				if ($cnt == 0)
				{
					$idscompany="(";
					$idsgroup="(";
				}
					
				$cnt++;
				
				if ($datacnt == $cnt)
				{
					$idscompany.="$item[company_id])";
					$idsgroup.="$item[group_id])";
				}
				else
				{
					$idscompany.="$item[company_id],";
					$idsgroup.="$item[group_id],";
				}
					
			}
		}
		
		if ($cnt > 0)
		{	
			$sqloption = "where id in$idscompany";
			$resCompany =  $merchantsResult_DB->getCompanyName($sqloption);
			if ($ret["result"] != 0)
			{
				$output["retCode"] = $ret["result"];
				$output["sErrMsg"] = $ret["sErrMsg"];
				$test = "SearchResultCB(".json_encode($output).")";
				echo $test;
				return -1;
			}
			
			$sqloption = "where id in$idsgroup";
			$resGroup =  $merchantsResult_DB->getGroupName($sqloption);
			if ($ret["result"] != 0)
			{
				$output["retCode"] = $ret["result"];
				$output["sErrMsg"] = $ret["sErrMsg"];
				$test = "SearchResultCB(".json_encode($output).")";
				echo $test;
				return -1;
			}
			
			if(is_array($resCompany["data_list"]))
			{
				if(is_array($output["data"]))
				{
					foreach($resCompany["data_list"] as $key => $item)
					{
						foreach($output["data"] as $k =>$data)
						{
							if ($data["company_id"] == $item["id"])
							{
								$output["data"][$k]["companyname"] = $item["companyname"];
							}
						}
					}
					
				}	
			}
			
			if(is_array($resGroup["data_list"]))
			{
				if(is_array($output["data"]))
				{
					foreach($resGroup["data_list"] as $key => $item)
					{
						foreach($output["data"] as $k =>$data)
						{
							if ($data["group_id"] == $item["id"])
							{
								$output["data"][$k]["groupname"] = $item["name"];
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
		
		return $this->result_struct(
	        array(
	            'stateId'	=> -160,
	            'message'	=> 'get Station info succeed',
	            'result'	=> $output
	        )
	    );
		
	}
	
	public function addStation($param)
	{
		TLOG_MSG("Station::addStation: station=".$param['station']." active=".$param['active']);
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$result = $merchantsResult_DB->AddStationInfo($param);
		$message= $result['result'] ? "add station failure: ".$result['errorMsg']:"add station succeed";
		//$this->log($this->Ary_Session['user'],$message);
		return $this->result_set((!$result['result'] ? -108 : 1807),$message);
	}
	
	public function delStation($param)
	{
		TLOG_MSG("Station::delStation: station=".$param['station']." active=".$param['active']);
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$result = $merchantsResult_DB->DelStationInfo($param);
		$message= $result['result'] ? "delete station failure: ".$result['errorMsg']:"delete station succeed";
		//$this->log($this->Ary_Session['user'],$message);
		return $this->result_set((!$result['result'] ? -108 : 1807),$message);
	}
	
	public function addDevice($param)
	{
		TLOG_MSG("Station::addDevice: station=".$param['station']." active=".$param['active']);
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$result = $merchantsResult_DB->AddDeviceInfo($param);
		$message= $result['result'] ? "add device failure: ".$result['errorMsg']:"add device succeed";
		//$this->log($this->Ary_Session['user'],$message);
		return $this->result_set((!$result['result'] ? -108 : 1807),$message);
	}
	
	public function editDevice($param)
	{
		//TLOG_MSG("Station::editDevice: station=".$param['station']." active=".$param['active']);
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$result = $merchantsResult_DB->editDeviceInfo($param);
		$message= $result['result'] ? "edit station failure: ".$result['errorMsg']:"edit station succeed";
		//$this->log($this->Ary_Session['user'],$message);
		return $this->result_set((!$result['result'] ? -108 : 1807),$message);
	}
	
	public function delDevice($param)
	{
		//TLOG_MSG("Station::editDevice: station=".$param['station']." active=".$param['active']);
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$result = $merchantsResult_DB->DelDev($param);
		$message= $result['result'] ? "delete device failure: ".$result['errorMsg']:"delete device succeed";
		//$this->log($this->Ary_Session['user'],$message);
		return $this->result_set((!$result['result'] ? -108 : 1807),$message);
	}
	
	public function setStation($param)
	{
		TLOG_MSG("Station::setStation: companyname=".$param['companyname']." active=".$param['active']." groupname=".$param['groupname']." stationname=".$param['stationname']." id=".$param['id']);
		
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$result = $merchantsResult_DB->EditStation($param);
		$message= $result['result'] ? "edit station failure: ".$result['errorMsg']:"edit station succeed";
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