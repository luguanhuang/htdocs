<?php
//****************** [Class] 路由对象 ******************

//该类必须运行于 PHP5.0 及以上的平台
if ( intval(PHP_VERSION) < 5 ){ exit("<p>Run \"Station\" invite promotion your PHP arrives at 5.0 the above editions</p>");}

if (!class_exists('AtherFrameWork')){ require(dirname(__FILE__)."/AtherFrameWork.php"); }
require_once 'tlog.php';
//require_once '/var/www/webdata/htdocs/dao/IMerchantsResult.DB.php';
require_once dirname(dirname(__FILE__)).'/dao/IMerchantsResult.DB.php';

class Company extends AtherFrameWork{
	
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
	
	
	public function getCompanyList($page){
		TLOG_MSG("Company::getCompanyList: func begin");
		$urlparam = "";
	    $pagequery = array();
	    $output = array("data"=>array(),
	        "pagequery"=>array(),
	        "totalNum"=>0,
	        "retCode"=>0,
	        "sErrMsg"=>"");
		
		
		if (intval($page) <= 0)
	    {
	        $page = 1;
	    }
		
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$ret =  $merchantsResult_DB->getCompanyDetail($page);
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
	            $retItem["companyname"] = $item["companyname"];
	            $retItem["status"] = $item["status"];
				$retItem["created_date"] = $item["created_date"];
				$output["data"][] = $retItem;
			}
		}
		
		$ret =  $merchantsResult_DB->getGroupDetail($page);
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
	            $retItem["name"] = $item["name"];
				$retItem["company_id"] = $item["company_id"];
				
				$output["groupinfo"][] = $retItem;
			}
		}
		
		$ret =  $merchantsResult_DB->getStationName("");
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
	            $retItem["name"] = $item["name"];
				 $retItem["company_id"] = $item["company_id"];
	            $retItem["group_id"] = $item["group_id"];
				$output["stationinfo"][] = $retItem;
			}
		}
		
		$ret =  $merchantsResult_DB->getPtype("");
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
	            $retItem["name"] = $item["name"];
				$output["ptypeinfo"][] = $retItem;
			}
		}
		
		$ret =  $merchantsResult_DB->getDtype("");
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
	            $retItem["name"] = $item["name"];
				$output["dtypeinfo"][] = $retItem;
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
		
		
	}
	
	public function getCompanyList1($param){
		TLOG_MSG("Company::getCompanyList1: func begin");
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
		$ret =  $merchantsResult_DB->getCompanyDetail($page);
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
	            $retItem["companyname"] = $item["companyname"];
	            $retItem["status"] = $item["status"];
				$retItem["created_date"] = $item["created_date"];
				$output["data"][] = $retItem;
			}
		}
		
		$ret =  $merchantsResult_DB->getGroupDetail1($param);
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
	            $retItem["name"] = $item["name"];
				$retItem["company_id"] = $item["company_id"];
				
				$output["groupinfo"][] = $retItem;
			}
		}
		
		$ret =  $merchantsResult_DB->getStationName("");
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
	            $retItem["name"] = $item["name"];
				 $retItem["company_id"] = $item["company_id"];
	            $retItem["group_id"] = $item["group_id"];
				$output["stationinfo"][] = $retItem;
			}
		}
		
		$ret =  $merchantsResult_DB->getPtype("");
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
	            $retItem["name"] = $item["name"];
				$output["ptypeinfo"][] = $retItem;
			}
		}
		
		$ret =  $merchantsResult_DB->getDtype("");
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
	            $retItem["name"] = $item["name"];
				$output["dtypeinfo"][] = $retItem;
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
		
		
	}
	
	public function getGroupInfo($param)
	{
		TLOG_MSG("Station::getGroupInfo: companyname=".$param['companyname']);
		$output = array("datainfo"=>array(),
	        "pagequery"=>array(),
	        "totalNum"=>0,
	        "retCode"=>0,
	        "sErrMsg"=>"");
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$return =  $merchantsResult_DB->getSpecialGroupName($param['companyname']);
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
		
		return $this->result_struct(
			array(
				"stateId"	=> "",
				"message"	=> $str,
				//"message"	=> $output,
				"result"	=>$output
			)
		);
	}
	
	public function getCompanyAllInfo($page){
		TLOG_MSG("Company::getCompanyAllInfo: func begin page=".$page);
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
		
		$return =  $merchantsResult_DB->getSpecialGroupName($page);
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
		
		
	}
	
	public function addCompany($param)
	{
		if (empty($param['active']))
		{
			TLOG_MSG("Station::addCompany: empty");
		}
		else
		{
			TLOG_MSG("Station::addCompany: not empty");
		}
		TLOG_MSG("Station::addStation: companyname=".$param['companyname']." active=".$param['active']);
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$result = $merchantsResult_DB->AddCompany($param);
		$message= $result['result'] ? "add company failure: ".$result['errorMsg']:"add company succeed";
		//$this->log($this->Ary_Session['user'],$message);
		return $this->result_set((!$result['result'] ? -108 : 1807),$message);
	}
	
	public function editCompany($param)
	{
		TLOG_MSG("editCompany::editCompany: companyname=".$param['companyname']." active=".$param['active']." id=".$param['id']);
		
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$result = $merchantsResult_DB->SetCompany($param);
		$message= $result['result'] ? "edit company failure: ".$result['errorMsg']:"edit company succeed";
		//$this->log($this->Ary_Session['user'],$message);
		return $this->result_set((!$result['result'] ? -108 : 1807),$message);
	}
	
	public function delCompany($param)
	{
		TLOG_MSG("Company::delCompany: id=".$param['id']);
		
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$result = $merchantsResult_DB->DelCompany($param);
		$message= $result['result'] ? "del company failure: ".$result['errorMsg']:"del company succeed";
		//$this->log($this->Ary_Session['user'],$message);
		return $this->result_set((!$result['result'] ? -108 : 1807),$message);
		//return $this->setRoute($param,"del");
	}
	
	/*
	删除路由器设置
	number: 1900
	*/
	public function delRoute($param){
		TLOG_MSG("Station::addCompany: empty");
		//return $this->setRoute($param,"del");
	}
}
?>