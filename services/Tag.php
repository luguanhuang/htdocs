<?php
//****************** [Class] 路由对象 ******************

//该类必须运行于 PHP5.0 及以上的平台
if ( intval(PHP_VERSION) < 5 ){ exit("<p>Run \"Station\" invite promotion your PHP arrives at 5.0 the above editions</p>");}

if (!class_exists('AtherFrameWork')){ require(dirname(__FILE__)."/AtherFrameWork.php"); }
require_once 'tlog.php';
//require_once '/var/www/html/htdocs/dao/IMerchantsResult.DB.php';
require_once dirname(dirname(__FILE__)).'/dao/IMerchantsResult.DB.php';
class Tag extends AtherFrameWork{
	
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
	
	public function getTagList($page){
		TLOG_MSG("Tag::getTagList: func begin");
		$urlparam = "";
	    $pagequery = array();
	    $output = array("data"=>array(),
	        "pagequery"=>array(),
	        "totalNum"=>0,
	        "retCode"=>0,
	        "sErrMsg"=>"");
		
		$outputtmp = array("data"=>array(),
	        "pagequery"=>array(),
	        "totalNum"=>0,
	        "retCode"=>0,
	        "sErrMsg"=>"");
		
		if (intval($page) <= 0)
	    {
	        $page = 1;
	    }
		
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$ret =  $merchantsResult_DB->getTagInfo($page);
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
	            $retItem["chid"] = $item["chid"];
				$retItem["tagid"] = $item["tagid"];
				$retItem["tagname"] = $item["tagname"];
				$retItem["webshow"] = $item["webshow"];
				$output["data"][] = $retItem;
				$datacnt++;
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
		
		return $this->result_struct(
			array(
				"stateId"	=> -160,
				"message"	=> "get tag succeed",
				"result"	=> $outputtmp
			)
		);
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