<?php
//****************** [Class] 路由对象 ******************

//该类必须运行于 PHP5.0 及以上的平台
if ( intval(PHP_VERSION) < 5 ){ exit("<p>Run \"Station\" invite promotion your PHP arrives at 5.0 the above editions</p>");}

if (!class_exists('AtherFrameWork')){ require(dirname(__FILE__)."/AtherFrameWork.php"); }
require_once 'tlog.php';
//require_once '/var/www/html/htdocs/dao/IMerchantsResult.DB.php';
require_once dirname(dirname(__FILE__)).'/dao/IMerchantsResult.DB.php';

class Channel extends AtherFrameWork{
	
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
	
	public function getAllChannelData()
	{
		TLOG_MSG("Channel::getAllChannelData: func begin");
		
	    $output = array("data"=>array(),
	        "pagequery"=>array(),
	        "totalNum"=>0,
	        "retCode"=>0,
	        "sErrMsg"=>"");
			
		TLOG_MSG("Channel::getAllChannelDatnnn: func begin");
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		TLOG_MSG("Channel::getAllChannelDatabbb: func begin");
		$ret =  $merchantsResult_DB->getActiveDeviceList();
		TLOG_MSG("Channel::getAllChannelDataccc: func begin");
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
	            $retItem["devname"] = $item["devname"];
				$output["data"][] = $retItem;
			}
		}
		
		$alldata = array();
		foreach($output["data"] as $key => $item)
		{
			TLOG_MSG("Channel::getAllChannelData: devname=".
			$item['devname']);
			
			$alldata[] = $this->getChannelInfo($item['devname']);
			
		}
		
		return $this->result_struct(
	        array(
	            'stateId'	=> -160,
	            'message'	=> 'get Station info succeed',
	            'result'	=> $alldata
	        )
	    );
	}
	
	public function DelChannel($param)
	{
		TLOG_MSG("Channel::DelChannel: func begin id=".$param['id']." devname=".$param['devname']);
		
	    $output = array("data"=>array(),
	        "pagequery"=>array(),
	        "totalNum"=>0,
	        "retCode"=>0,
	        "sErrMsg"=>"");
		
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$result =  $merchantsResult_DB->DelTab($param);
		$result =  $merchantsResult_DB->DelAlconfig($param);
		$result =  $merchantsResult_DB->DelConnel($param);
		
		
		$message= $result['result'] ? "del channel failure: ".$result['errorMsg']:"del channel succeed";
		return $this->result_set((!$result['result'] ? -108 : 1807),$message);
	}
	
	
	public function getAlarmData($param)
	{
		TLOG_MSG("Channel::getAlarmData: func begin");
		
	    $output = array("data"=>array(),
	        "retCode"=>0,
	        "sErrMsg"=>"");
			
		
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		
		$ret =  $merchantsResult_DB->getAlarmConf($param);
		
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
	            $retItem["chid"] = $item["chid"];
	            $retItem["tagid"] = $item["tagid"];
				$retItem["alarmontext"] = $item["alarmontext"];
				$retItem["tagname"] = $item["tagname"];
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
	
	public function getMaindata($param)
	{
		TLOG_MSG("Channel::getMaindata: func begin");
		
	    $output = array("data"=>array(),
			"tab"=>array(),
	        "retCode"=>0,
	        "sErrMsg"=>"");
		$arrDev = explode(",", $param);
		
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$ret =  $merchantsResult_DB->getDeviceInfo($arrDev[0]);
		if ($ret["result"] != 0)
	    {
	        $output["retCode"] = $ret["result"];
	        $output["sErrMsg"] = $ret["sErrMsg"];
	        $test = "SearchResultCB(".json_encode($outputtmp).")";
	        echo $test;
	        return -1;
	    }
		
		if(is_array($ret["data_list"]))
		{
			foreach($ret["data_list"] as $tmpitem)
			{
				$retchandata =  $merchantsResult_DB->getChannelData($arrDev[0]);
				if ($retchandata["result"] != 0)
				{
					$output["retCode"] = $retchandata["result"];
					$output["sErrMsg"] = $retchandata["sErrMsg"];
					$test = "SearchResultCB(".json_encode($outputtmp).")";
					echo $test;
					return -1;
				}
				
				
				if(is_array($retchandata["data_list"]))
				{
					foreach($retchandata["data_list"] as $item)
					{
						$retItem = array();
						
						$retItem["ch"] = $item["ch"];
						$retItem["dtype"] = $item["dtype"];
						$retItem["slaveid"] = $item["slaveid"];
						$retItem["funcode"] = $item["funcode"];
						
						$retItem["startreg"] = $item["startreg"];
						$retItem["countreg"] = $item["countreg"];
						
						
						$retItem["pic1filelocation"] = $tmpitem["pic1filelocation"];
						$retItem["pic2filelocation"] = $tmpitem["pic2filelocation"];
						$retItem["pic3filelocation"] = $tmpitem["pic3filelocation"];
						$retItem["pic4filelocation"] = $tmpitem["pic4filelocation"];
						$retItem["mainpagediv"] = $tmpitem["mainpagediv"];
						
						
						$output["data"][] = $retItem;
						
					}
				}
		
			}
		}
		
		
		
		$retTab =  $merchantsResult_DB->getChannelTabData($arrDev[0], "where mainpageenable='1'");
		if ($retTab["result"] != 0)
	    {
	        $output["retCode"] = $retTab["result"];
	        $output["sErrMsg"] = $retTab["sErrMsg"];
	        $test = "SearchResultCB(".json_encode($output).")";
	        echo $test;
	        return -1;
	    }
		
		if(is_array($retTab["data_list"]))
		{
			foreach($retTab["data_list"] as $item)
			{
				$retItem = array();
	            
				$retItem["id"] = $item["id"];
	            $retItem["chid"] = $item["chid"];
				$retItem["tagid"] = $item["tagid"];
				
				$retItem["tagname"] = $item["tagname"];
				$retItem["tagdata"] = "";
				$retItem["tagdesc"] = $item["tagdesc"];
	            $retItem["writeenable"] = $item["writeenable"];
				$retItem["coiltype"] = $item["coiltype"];
				$retItem["mainpagelocation"] = $item["mainpagelocation"];
				
				$output["tab"][] = $retItem;
			}
		}
		
		foreach($output["tab"] as $k =>$data)
		{
			
			$resdata =  $merchantsResult_DB->getChannelLimitData($arrDev[0],$data['chid'],$data['tagid']);
			if(is_array($resdata["data_list"]))
			{
				foreach($resdata["data_list"] as $item)
				{
					$field = "data_".$data['tagid'];
					if (empty($item[$field]))
					{
						$output["tab"][$k]["tagdata"] = 0;
					}
					else
					{
						$output["tab"][$k]["tagdata"] = $item[$field];
					}
									
				}
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
	
	public function getAllTagInfo($param)
	{
		TLOG_MSG("Channel::getAllTagInfo: func begin111");
		
	    $output = array("data"=>array(),
			"tab"=>array(),
	        "retCode"=>0,
	        "sErrMsg"=>"");
		//$arrDev = explode(",", $param);
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$retTab =  $merchantsResult_DB->getChannelTabData($param, "");
		if ($retTab["result"] != 0)
	    {
	        $output["retCode"] = $retTab["result"];
	        $output["sErrMsg"] = $retTab["sErrMsg"];
	        $test = "SearchResultCB(".json_encode($output).")";
	        echo $test;
	        return -1;
	    }
		
		if(is_array($retTab["data_list"]))
		{
			foreach($retTab["data_list"] as $item)
			{
				$retItem = array();
	            
				$retItem["id"] = $item["id"];
	            $retItem["chid"] = $item["chid"];
				$retItem["tagid"] = $item["tagid"];
				
				$retItem["tagname"] = $item["tagname"];
				$retItem["tagdata"] = "";
				$retItem["tagdesc"] = $item["tagdesc"];
	            $retItem["writeenable"] = $item["writeenable"];
				$retItem["coiltype"] = $item["coiltype"];
				$retItem["mainpagelocation"] = $item["mainpagelocation"];
				
				$output["tab"][] = $retItem;
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
	
	public function getHistoryData($param)
	{
		TLOG_MSG("Channel::getHistoryData: func begin param=".$param);
		
	    $output = array("data"=>array(),
			"tab"=>array(),
	        "retCode"=>0,
	        "sErrMsg"=>"");
		$allinfo = explode("&", $param);
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$arrdata = explode("|", $allinfo[0]);
		$time = explode("|", $allinfo[1]);
		$num = count($arrdata);
		for($i=0;$i<$num;++$i)
		{
			$from = "$time[0]:00";
			$to = "$time[1]:00";
			$arrDev = explode(",", $arrdata[$i]);
			$resdata =  $merchantsResult_DB->getChannelTimeData($arrDev[0],$arrDev[1],$arrDev[2],$from,$to);
			if(is_array($resdata["data_list"]))
			{
				foreach($resdata["data_list"] as $item)
				{
					$retItem = array();
					$retItem["devname"] = $arrDev[0];
					$retItem["chid"] = $arrDev[1];
					$retItem["tagid"] = $arrDev[2];
					$retItem["time"] = $item['time'];
					$field = "data_".$arrDev[2];
					if (empty($item[$field]))
					{
						$retItem["tagdata"] = 0;
					}
					else
					{
						$retItem["tagdata"] = $item[$field];
					}
					
					
					$output["tab"][] = $retItem;
				}
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
	
	public function getTagDataDetail($param)
	{
		TLOG_MSG("Channel::getTagDataDetail: func begin");
		
	    $output = array("data"=>array(),
			"tab"=>array(),
	        "retCode"=>0,
	        "sErrMsg"=>"");
		
		
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$arrdata = explode("|", $param);
		$num = count($arrdata);
		for($i=0;$i<$num;++$i)
		{
			$arrDev = explode(",", $arrdata[$i]);
			$resdata =  $merchantsResult_DB->getChannelLimitData($arrDev[0],$arrDev[1],$arrDev[2]);
			if(is_array($resdata["data_list"]))
			{
				foreach($resdata["data_list"] as $item)
				{
					$retItem = array();
					$retItem["devname"] = $arrDev[0];
					$retItem["chid"] = $arrDev[1];
					$retItem["tagid"] = $arrDev[2];
					
					$field = "data_".$arrDev[2];
					if (empty($item[$field]))
					{
						$retItem["tagdata"] = 0;
					}
					else
					{
						$retItem["tagdata"] = $item[$field];
					}
					
					
					$output["tab"][] = $retItem;
				}
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
	
	public function getChdata($param)
	{
		TLOG_MSG("Channel::getChata: func begin");
		
	    $output = array("data"=>array(),
			"tab"=>array(),
	        "retCode"=>0,
	        "sErrMsg"=>"");
		$arrDev = explode(",", $param);
		
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$ret =  $merchantsResult_DB->getChannelData($arrDev[0]);
		if ($ret["result"] != 0)
	    {
	        $output["retCode"] = $ret["result"];
	        $output["sErrMsg"] = $ret["sErrMsg"];
	        $test = "SearchResultCB(".json_encode($outputtmp).")";
	        echo $test;
	        return -1;
	    }
		
		
		if(is_array($ret["data_list"]))
		{
			foreach($ret["data_list"] as $item)
			{
				$retItem = array();
	            
				$retItem["ch"] = $item["ch"];
	            $retItem["dtype"] = $item["dtype"];
				$retItem["slaveid"] = $item["slaveid"];
				$retItem["funcode"] = $item["funcode"];
				
				$retItem["startreg"] = $item["startreg"];
	            $retItem["countreg"] = $item["countreg"];
				
				
				$output["data"][] = $retItem;
				
			}
		}
		
		$retTab =  $merchantsResult_DB->getChannelTabData($arrDev[0], "where webshow='1' and chid=$arrDev[1]");
		if ($retTab["result"] != 0)
	    {
	        $output["retCode"] = $retTab["result"];
	        $output["sErrMsg"] = $retTab["sErrMsg"];
	        $test = "SearchResultCB(".json_encode($output).")";
	        echo $test;
	        return -1;
	    }
		
		if(is_array($retTab["data_list"]))
		{
			foreach($retTab["data_list"] as $item)
			{
				$retItem = array();
	            
				$retItem["id"] = $item["id"];
	            $retItem["chid"] = $item["chid"];
				$retItem["tagid"] = $item["tagid"];
				
				$retItem["tagname"] = $item["tagname"];
				$retItem["tagdata"] = "";
				$retItem["tagdesc"] = $item["tagdesc"];
	            $retItem["writeenable"] = $item["writeenable"];
				$retItem["coiltype"] = $item["coiltype"];
				
				$output["tab"][] = $retItem;
			}
		}
		
		foreach($output["tab"] as $k =>$data)
		{
			
			$resdata =  $merchantsResult_DB->getChannelLimitData($arrDev[0],$data['chid'],$data['tagid']);
			if(is_array($resdata["data_list"]))
			{
				foreach($resdata["data_list"] as $item)
				{
					$field = "data_".$data['tagid'];
					if (empty($item[$field]))
					{
						$output["tab"][$k]["tagdata"] = 0;
					}
					else
					{
						$output["tab"][$k]["tagdata"] = $item[$field];
					}
									
				}
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
	
	public function getChannelList($param){
		TLOG_MSG("Channel::getChannelList: func begin");
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
	        $outputtmp["retCode"] = $ret["result"];
	        $outputtmp["sErrMsg"] = $ret["sErrMsg"];
	        $test = "SearchResultCB(".json_encode($outputtmp).")";
	        echo $test;
	        return -1;
	    }
		
		$datacnt=0;
		if(is_array($ret["data_list"]))
		{
			foreach($ret["data_list"] as $item)
			{
				$retItem = array();
	            
				$retItem["devname"] = $item["devname"];
	            $retItem["company_id"] = $item["company_id"];
				$retItem["group_id"] = $item["group_id"];
				$retItem["station_id"] = $item["station_id"];
				
				$outputtmp["data"][] = $retItem;
				$datacnt++;
			}
		}
		
		$sqloption = "";
		$idscompany="";
		$idsgroup="";
		$idsstation="";
		
		$cnt=0; 
		if(is_array($outputtmp["data"]))
		{
			foreach($outputtmp["data"] as $item)
			{
				if ($cnt == 0)
				{
					$idscompany="(";
					$idsgroup="(";
					$idsstation="(";
					$idsrole="(";
				}
					
				$cnt++;
				
				if ($datacnt == $cnt)
				{
					$idscompany.="$item[company_id])";
					$idsgroup.="$item[group_id])";
					$idsstation.="$item[station_id])";
					
				}
				else
				{
					$idscompany.="$item[company_id],";
					$idsgroup.="$item[group_id],";
					$idsstation.="$item[station_id],";
					
				}
					
			}
		}
		
		if ($cnt > 0)
		{	
			$sqloption = "where id in$idscompany";
			$resCompany =  $merchantsResult_DB->getCompanyName($sqloption);
			if ($ret["result"] != 0)
			{
				$outputtmp["retCode"] = $ret["result"];
				$outputtmp["sErrMsg"] = $ret["sErrMsg"];
				$test = "SearchResultCB(".json_encode($outputtmp).")";
				echo $test;
				return -1;
			}
			
			$sqloption = "where id in$idsgroup";
			$resGroup =  $merchantsResult_DB->getGroupName($sqloption);
			if ($ret["result"] != 0)
			{
				$outputtmp["retCode"] = $ret["result"];
				$outputtmp["sErrMsg"] = $ret["sErrMsg"];
				$test = "SearchResultCB(".json_encode($outputtmp).")";
				echo $test;
				return -1;
			}
			
			$sqloption = "where id in$idsstation";
			$resStation =  $merchantsResult_DB->getStationName($sqloption);
			if ($ret["result"] != 0)
			{
				$outputtmp["retCode"] = $ret["result"];
				$outputtmp["sErrMsg"] = $ret["sErrMsg"];
				$test = "SearchResultCB(".json_encode($outputtmp).")";
				echo $test;
				return -1;
			}
		
			
			if(is_array($resCompany["data_list"]))
			{
				if(is_array($outputtmp["data"]))
				{
					foreach($resCompany["data_list"] as $key => $item)
					{
						foreach($outputtmp["data"] as $k =>$data)
						{
							if ($data["company_id"] == $item["id"])
							{
								$outputtmp["data"][$k]["companyname"] = $item["companyname"];
							}
						}
					}
					
				}	
			}
			
			if(is_array($resGroup["data_list"]))
			{
				if(is_array($outputtmp["data"]))
				{
					foreach($resGroup["data_list"] as $key => $item)
					{
						foreach($outputtmp["data"] as $k =>$data)
						{
							if ($data["group_id"] == $item["id"])
							{
								$outputtmp["data"][$k]["groupname"] = $item["name"];
							}
						}
					}
					
				}	
			}
			
			if(is_array($resStation["data_list"]))
			{
				if(is_array($outputtmp["data"]))
				{
					foreach($resStation["data_list"] as $key => $item)
					{
						foreach($outputtmp["data"] as $k =>$data)
						{
							if ($data["station_id"] == $item["id"])
							{
								$outputtmp["data"][$k]["stationname"] = $item["name"];
							}
						}
					}
					
				}	
			}
			$datacnt=0;
			if(is_array($outputtmp["data"]))
			{
				foreach($outputtmp["data"] as $k =>$data)
				{
					$resdata =  $merchantsResult_DB->getChannelInfo(1,$data["devname"]);
					foreach($resdata["data_list"] as $key => $item)
					{
						$retItem = array();
						$retItem["id"] = $item["ch"];
						$retItem["devname"] = $data["devname"];
						$retItem["companyname"] = $data["companyname"];
						$retItem["groupname"] = $data["groupname"];
						$retItem["stationname"] = $data["stationname"];
						
						$retItem["dtype"] = $item["dtype"];
						$sql = " where id=".$item["dtype"];
						$resDtype =  $merchantsResult_DB->getDtype($sql);
						if ($resDtype["result"] != 0)
						{
							$output["retCode"] = $resDtype["result"];
							$output["sErrMsg"] = $resDtype["sErrMsg"];
							$test = "SearchResultCB(".json_encode($output).")";
							echo $test;
							return -1;
						}
						if(is_array($resDtype["data_list"]))
						{
							foreach($resDtype["data_list"] as $key => $tmp)
							{
								$retItem["dname"] = $tmp["name"];
							}
						}
						
						$retItem["ch"] = $item["ch"];
						$retItem["history"] = $item["history"];
						$retItem["chdesc"] = $item["chdesc"];
						$retItem["slaveid"] = $item["slaveid"];
						$retItem["funcode"] = $item["funcode"];
						$retItem["startreg"] = $item["startreg"];
						$retItem["reporttype"] = $item["reporttype"];
						$retItem["countreg"] = $item["countreg"];
						$retItem["active"] = $item["active"];
						$retItem["Alarmchannel"] = $item["Alarmchannel"];
						
						$retItem["company_id"] = $data["company_id"];
						$retItem["group_id"] = $data["group_id"];
						$retItem["station_id"] = $data["station_id"];
						
						
						$output["data"][] = $retItem;
						$datacnt++;
					}
					
				}
			}	
		}
		
		$output["totalNum"] = $datacnt;
	    
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
	
	public function getChannelInfo($devname){
		TLOG_MSG("Channel::getChannelInfo: func begin");
		$urlparam = "";
	    $pagequery = array();
	    $output = array("data"=>array(),
			"tab"=>array(),
	        "pagequery"=>array(),
	        "totalNum"=>0,
	        "retCode"=>0,
	        "sErrMsg"=>"");
		
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$ret =  $merchantsResult_DB->getChannelData($devname);
		if ($ret["result"] != 0)
	    {
	        $outputtmp["retCode"] = $ret["result"];
	        $outputtmp["sErrMsg"] = $ret["sErrMsg"];
	        $test = "SearchResultCB(".json_encode($outputtmp).")";
	        echo $test;
	        return -1;
	    }
		
		$chid = array();
		if(is_array($ret["data_list"]))
		{
			foreach($ret["data_list"] as $item)
			{
				$retItem = array();
	            
				$retItem["ch"] = $item["ch"];
	            $retItem["dtype"] = $item["dtype"];
				$retItem["slaveid"] = $item["slaveid"];
				$retItem["funcode"] = $item["funcode"];
				
				$retItem["startreg"] = $item["startreg"];
	            $retItem["countreg"] = $item["countreg"];
				$retItem["chdesc"] = $item["chdesc"];
				$retItem["devname"] = $devname;
				$chid[] = $item["ch"];
				$output["data"][] = $retItem;
				
			}
		}
		
		$retTab =  $merchantsResult_DB->getChannelTabData($devname,"where webshow='1'");
		if ($retTab["result"] != 0)
	    {
	        $outputtmp["retCode"] = $retTab["result"];
	        $outputtmp["sErrMsg"] = $retTab["sErrMsg"];
	        $test = "SearchResultCB(".json_encode($outputtmp).")";
	        echo $test;
	        return -1;
	    }
		
		$str="";
		if(is_array($retTab["data_list"]))
		{
			foreach($retTab["data_list"] as $item)
			{
				$retItem = array();
	            
				$retItem["id"] = $item["id"];
	            $retItem["chid"] = $item["chid"];
				$retItem["tagid"] = $item["tagid"];
				
				
				$str.=strval($item["tagid"]);
				$str.=":";
				$str.=strval($item["tagname"]);
				$str.=":";
				$str.=strval($item["tagdesc"]);
				$str.="|";
				
				$retItem["tagname"] = $item["tagname"];
				$retItem["tagdata"] = "";
				$retItem["tagdesc"] = $item["tagdesc"];
	            $retItem["writeenable"] = $item["writeenable"];
				$retItem["coiltype"] = $item["coiltype"];
				$retItem["devname"] = $devname;
				$output["tab"][] = $retItem;
			}
		}
		
		/*foreach($output["tab"] as $k =>$data)
		{
			$find = 0;
			$arrlength=count($chid);
			for($x=0;$x<$arrlength;$x++) 
			{
				if ($chid[$x] == $data['chid'])
				{
					$find = 1;
					break;
					
				}
					
			}
			
			if (0 == $find)
				continue;
			
			$resdata =  $merchantsResult_DB->getChannelLimitData($devname,$data['chid'],$data['tagid']);
			if(is_array($resdata["data_list"]))
			{
				foreach($resdata["data_list"] as $item)
				{
					$field = "data_".$data['tagid'];
					if (empty($item[$field]))
					{
							$output["tab"][$k]["tagdata"] = 0;
					}
					else
					{
						$output["tab"][$k]["tagdata"] = $item[$field];					
					}
					
				}
			}
		}*/
		
		return $this->result_struct(
	        array(
	            'stateId'	=> -160,
	            'message'	=> 'get Station info succeed',
	            'result'	=> $output
	        )
	    );
		
	}
	
	public function AddWriteData($param)
	{
		$var=explode(",",$param);
		TLOG_MSG("Channel::AddWriteData: func begin");
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$result = $merchantsResult_DB->AddWriteData($var[0],$var[1],$var[2],$var[3],$var[4],$var[5],$var[6]);
		$result = $merchantsResult_DB->UpdateChInfo($var[0],$var[1],$var[7]);
		
		$message= $result['result'] ? "add data failure: ".$result['errorMsg']:"add data succeed";
		//$this->log($this->Ary_Session['user'],$message);
		return $this->result_set((!$result['result'] ? -108 : 1807),$message);
	}
	
	public function DownloadHours($devname)
	{
		$devname = $_REQUEST['devname'];
		$frdt = new DateTime($_REQUEST['date_from']);
		$frdate = $frdt->format('Y-m-d');
		
		
		$frtime = $frdt->format('H');
	
		$todt = new DateTime($_REQUEST['date_to']);
		
		$tmp = " where time in(";
		
		
		$todate = $todt->format('Y-m-d');
		$totime = $todt->format('H');
			$fromtime = $frdate." $frtime:00:00";
		//$totime = $todate." $totime:00:00";
		TLOG_MSG("Channel::DownloadHours: func begin frtime=".(int)$frtime." to=".(int)$totime." fromtime=".$fromtime." totime=".$totime);	
		
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		//$ret =  $merchantsResult_DB->getChannelid($devname);
		$ret =  $merchantsResult_DB->getChannelidhourly($devname);
		if ($ret["result"] != 0)
	    {
	        $output["retCode"] = $ret["result"];
	        $output["sErrMsg"] = $ret["sErrMsg"];
	        $test = "SearchResultCB(".json_encode($output).")";
	        echo $test;
	        return -1;
	    }
		
		$chid = 0;
		if(is_array($ret["data_list"]))
		{
			foreach($ret["data_list"] as $item)
			{
				
	            $chid = $item["ch"];
				
			}
		}

		$ret =  $merchantsResult_DB->getTagId($devname,$chid);
		if ($ret["result"] != 0)
	    {
	        $output["retCode"] = $ret["result"];
	        $output["sErrMsg"] = $ret["sErrMsg"];
	        $test = "SearchResultCB(".json_encode($output).")";
	        echo $test;
	        return -1;
	    }
		
		$locationItem = array();
		$resItem = array();
		if(is_array($ret["data_list"]))
		{
			foreach($ret["data_list"] as $item)
			{
				
				$resItem[] = "data_".$item["tagid"];
				$locationItem[] = $item["excellocation"];
			}
		}
		
		$sqloption = "select ";
		
		foreach($resItem as $k=>$item)
		{
			$sqloption .= $resItem[$k];
			$sqloption .=",";
			//TLOG_MSG("id=".$resItem[$k]);	
		}
		
		$sqloption = substr($sqloption, 0, -1);
		
		$frtime = (int)$frtime;
		$totime = (int)$totime;
		$name = $devname."_$chid";
		$total = "";
		$totallocations = "";
		$frtime = (int)$frdt->format('d');
		$totime = (int)$todt->format('d');
		
		$begin = $frdt->format('H');
		$begin = (int)$begin;
		$tmpdata = "";
		$tohour = 0;
		for ($i=$frtime;$i<=$totime; $i++)
		{
			
			$tohour = 23;
			if ($i == $totime)
				$tohour = $todt->format('H');;
			
			while ($begin <=$tohour)
			{
				TLOG_MSG("Channel::DownloadHours: func totime=".$totime);
				if ($i < 10)
				$tmpdata = $frdt->format('Y-m-')."0$i";
				else
					$tmpdata = $frdt->format('Y-m-')."$i";
				
				$tmpfrom = $tmpdata;
				if ($begin < 10)
				$tmpfrom .= " 0$begin:02:00";
				else
					$tmpfrom .= " $begin:02:00";
				$tmpto = $tmpdata;
				if ($begin < 10)
				$tmpto .= " 0$begin:59:00";
				else
					$tmpto .= " $begin:59:00";
			
				
				$totalsql = $sqloption;
				$totalsql .= " from $name where time > '$tmpfrom' and time < '$tmpto'";
			
				$retTab =  $merchantsResult_DB->getTagAllData($devname,$chid,$totalsql);
				if ($retTab["result"] != 0)
				{
					$output["retCode"] = $retTab["result"];
					$output["sErrMsg"] = $retTab["sErrMsg"];
					$test = "SearchResultCB(".json_encode($output).")";
					//echo $test;
					return -1;
				}
			
				$resdata = "";
				$locations = "";
				if(is_array($retTab["data_list"]))
				{
					foreach($retTab["data_list"] as $item)
					{
						$retItem = array();
						
						foreach($resItem as $k=>$itemdata)
						{
							if (!empty($item[$resItem[$k]]) && $item[$resItem[$k]] != "")
							{
								$locations .= $locationItem[$k];
								$locations .=",";
								//TLOG_MSG("Channel::Download: data=".$item[$resItem[$k]]);
								$resdata .= $item[$resItem[$k]];
								$resdata .=",";
							}
						}
						
						
					}
					
					
					if ($resdata != "")
					{
						//TLOG_MSG("Channel::Download: $resdata=".$resdata);
						$resdata = substr($resdata, 0, -1);
						$tmp1 = $tmpfrom;
						$tmp1 .= ",";
						
						
						$resdata = $tmp1.$resdata;
						$total .= $resdata;
						$total .= "|";
						
						$locations = substr($locations, 0, -1);
						$locations = $tmp1.$locations;
						$totallocations .= $locations;
						$totallocations .= "|";
						
						//$output["data"][] = $resdata;
					}
						
					
				}
				
				$begin++;
			}
			
			$begin = 0;
			
		}
		
		
		$total = substr($total, 0, -1);
		$totallocations = substr($totallocations, 0, -1);
		//TLOG_MSG("Channel::Download: total=".$total." totallocations=".$totallocations);
		return $this->result_struct(
	        array(
	            'stateId'	=> -160,
	            'message'	=> $totallocations,
	            'result'	=> $total
	        )
	    );
	}
	
	public function Download($devname)
	{
		//TLOG_MSG("Channel::Download: func begin");
		 $output = array("data"=>array(),
			"tab"=>"",
	        "retCode"=>0,
	        "sErrMsg"=>"");
			
		if (2 == $_REQUEST['report_type'])
		{
			return $this->DownloadHours($devname);
		}
		
		$devname = $_REQUEST['devname'];
		$frdt = new DateTime($_REQUEST['date_from']);
		$frdate = $frdt->format('Y-m-d');
		
		$frtime = $frdt->format('H');
		$todt = new DateTime($_REQUEST['date_to']);
		$todate = $todt->format('Y-m');
		$totime = $todt->format('H');
		$fromtime = $frdate." $frtime:00:00";
		//$totime = $todate." $totime:00:00";
		//TLOG_MSG("Channel::Download: func begin frtime=".(int)$frtime." to=".(int)$totime." fromtime=".$fromtime." totime=".$totime);	
		TLOG_MSG("Channel::Download: func begin 1=".$frdt->format('Y-m')." 2=".$frdt->format('d'));	
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$ret =  $merchantsResult_DB->getChannelid($devname);
		if ($ret["result"] != 0)
	    {
	        $output["retCode"] = $ret["result"];
	        $output["sErrMsg"] = $ret["sErrMsg"];
	        $test = "SearchResultCB(".json_encode($output).")";
	        echo $test;
	        return -1;
	    }
		
		$chid = 0;
		if(is_array($ret["data_list"]))
		{
			foreach($ret["data_list"] as $item)
			{
				
	            $chid = $item["ch"];
				
			}
		}

		$ret =  $merchantsResult_DB->getTagId($devname,$chid);
		if ($ret["result"] != 0)
	    {
	        $output["retCode"] = $ret["result"];
	        $output["sErrMsg"] = $ret["sErrMsg"];
	        $test = "SearchResultCB(".json_encode($output).")";
	        echo $test;
	        return -1;
	    }
		
		$locationItem = array();
		$resItem = array();
		if(is_array($ret["data_list"]))
		{
			foreach($ret["data_list"] as $item)
			{
				
				$resItem[] = "data_".$item["tagid"];
				$locationItem[] = $item["excellocation"];
			}
		}
		
		$sqloption = "select ";
		
		foreach($resItem as $k=>$item)
		{
			$sqloption .= $resItem[$k];
			$sqloption .=",";
			//TLOG_MSG("id=".$resItem[$k]);	
		}
		
		$sqloption = substr($sqloption, 0, -1);
		$frtime = (int)$frdt->format('d');
		$totime = (int)$todt->format('d');
		
		$name = $devname."_$chid";
		$total = "";
		$totallocations = "";
		for ($i=$frtime;$i<$totime; $i++)
		{
			if ($i < 10)
			$tmpfrom = $frdt->format('Y-m-')."0$i";
			else
				$tmpfrom = $frdt->format('Y-m-')."$i";
			
			$tmpfrom .= " 06:02:00";
			
			
			$info = $i;
			$info++;
			
			if ($i < 10)
				$tmpto = $frdt->format('Y-m-')."0$info";
			else
				$tmpto = $frdt->format('Y-m-')."$info";
			
			//$tmpto = $frdt->format('Y-m-')." $info";
			$tmpto .= " 05:59:00";
			
			$totalsql = $sqloption;
			$totalsql .= " from $name where time > '$tmpfrom' and time < '$tmpto'";
			
				$retTab =  $merchantsResult_DB->getTagAllData($devname,$chid,$totalsql);
				if ($retTab["result"] != 0)
				{
					$output["retCode"] = $retTab["result"];
					$output["sErrMsg"] = $retTab["sErrMsg"];
					$test = "SearchResultCB(".json_encode($output).")";
					//echo $test;
					return -1;
				}
			
				$resdata = "";
				$locations = "";
				if(is_array($retTab["data_list"]))
				{
					foreach($retTab["data_list"] as $item)
					{
						$retItem = array();
						
						foreach($resItem as $k=>$itemdata)
						{
							if (!empty($item[$resItem[$k]]) && $item[$resItem[$k]] != "")
							{
								$locations .= $locationItem[$k];
								$locations .=",";
								//TLOG_MSG("Channel::Download: data=".$item[$resItem[$k]]);
								$resdata .= $item[$resItem[$k]];
								$resdata .=",";
							}
						}
						
						
					}
					
					
					if ($resdata != "")
					{
						//TLOG_MSG("Channel::Download: $resdata=".$resdata);
						$resdata = substr($resdata, 0, -1);
						
						$tmp1 = $tmpfrom;
						$tmp1 .= ",";
						$resdata = $tmp1.$resdata;
						
						$total .= $resdata;
						$total .= "|";
						
						$locations = substr($locations, 0, -1);
						
						$locations = $tmp1.$locations;
						$totallocations .= $locations;
						
						$totallocations .= "|";
						
						//$output["data"][] = $resdata;
					}
						
					
				}
			}
		
		
		/*$retTab =  $merchantsResult_DB->getTagAllData($devname,$chid,$sqloption);

		if ($retTab["result"] != 0)
	    {
	        $output["retCode"] = $retTab["result"];
	        $output["sErrMsg"] = $retTab["sErrMsg"];
	        $test = "SearchResultCB(".json_encode($output).")";
	       // echo $test;
	        return -1;
	    }
		
		$resdata = "";
		if(is_array($retTab["data_list"]))
		{
			foreach($retTab["data_list"] as $item)
			{
				$retItem = array();
	            
				foreach($resItem as $k=>$itemdata)
				{
					$resdata .= $item[$resItem[$k]];
					$resdata .=",";
					
				}
				
				
			}
		}
		
		$output["tab"] = $resdata;*/
		$total = substr($total, 0, -1);
		$totallocations = substr($totallocations, 0, -1);
		//TLOG_MSG("Channel::Download: total=".$total." totallocations=".$totallocations);
		return $this->result_struct(
	        array(
	            'stateId'	=> -160,
	            'message'	=> $totallocations,
	            'result'	=> $total
	        )
	    );
	}
	/*public function Download($devname)
	{
		 $output = array("data"=>array(),
			"tab"=>"",
	        "retCode"=>0,
	        "sErrMsg"=>"");
		$devname = $_REQUEST['devname'];
		$frdt = new DateTime($_REQUEST['date_from']);
		$frdate = $frdt->format('Y-m-d');
		
		
		$frtime = $frdt->format('H');
	
		$todt = new DateTime($_REQUEST['date_to']);
		
		$tmp = " where time in(";
		
		
		$todate = $todt->format('Y-m-d');
		$totime = $todt->format('H');
		
		
			$fromtime = $frdate." $frtime:00:00";
		//$totime = $todate." $totime:00:00";
		TLOG_MSG("Channel::Download: func begin frtime=".(int)$frtime." to=".(int)$totime." fromtime=".$fromtime." totime=".$totime);
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$ret =  $merchantsResult_DB->getChannelid($devname);
		if ($ret["result"] != 0)
	    {
	        $output["retCode"] = $ret["result"];
	        $output["sErrMsg"] = $ret["sErrMsg"];
	        $test = "SearchResultCB(".json_encode($output).")";
	        echo $test;
	        return -1;
	    }
		
		$chid = 0;
		if(is_array($ret["data_list"]))
		{
			foreach($ret["data_list"] as $item)
			{
				
	            $chid = $item["ch"];
				
			}
		}
		
		$ret =  $merchantsResult_DB->getTagId($devname,$chid);
		if ($ret["result"] != 0)
	    {
	        $output["retCode"] = $ret["result"];
	        $output["sErrMsg"] = $ret["sErrMsg"];
	        $test = "SearchResultCB(".json_encode($output).")";
	        echo $test;
	        return -1;
	    }
		
		
		$resItem = array();
		if(is_array($ret["data_list"]))
		{
			foreach($ret["data_list"] as $item)
			{
				
				$resItem[] = "data_".$item["tagid"];
				
			}
		}
		
		$sqloption = "select ";
		
		foreach($resItem as $k=>$item)
		{
			$sqloption .= $resItem[$k];
			$sqloption .=",";
			//TLOG_MSG("id=".$resItem[$k]);	
		}
		
		$sqloption = substr($sqloption, 0, -1);
		$frtime = (int)$frtime;
		$totime = (int)$totime;
		$name = $devname."_$chid";
		for ($i=$frtime;$i<$totime; $i++)
		{
			$tmpfrom = $frdate;
			if ($i < 10)
			$tmpfrom .= " 0$i:00:00";
			else
				$tmpfrom .= " $i:00:00";
			$tmpto = $frdate;
			if ($i < 10)
			$tmpto .= " 0$i:59:59";
			else
				$tmpto .= " $i:59:59";
			
			
			$totalsql = $sqloption;
			$totalsql .= " from $name where time > '$tmpfrom' and time < '$tmpto'";
			
				$retTab =  $merchantsResult_DB->getTagAllData($devname,$chid,$totalsql);
				if ($retTab["result"] != 0)
				{
					$output["retCode"] = $retTab["result"];
					$output["sErrMsg"] = $retTab["sErrMsg"];
					$test = "SearchResultCB(".json_encode($output).")";
					echo $test;
					return -1;
				}
			
				$resdata = "";
				if(is_array($retTab["data_list"]))
				{
					foreach($retTab["data_list"] as $item)
					{
						$retItem = array();
						
						foreach($resItem as $k=>$itemdata)
						{
							$resdata .= $item[$resItem[$k]];
							$resdata .=",";
							
						}
						
						
					}
					
					
					if ($resdata != "")
					{
						TLOG_MSG("Channel::addStation: $resdata=".$resdata);
						$output["data"][] = $resdata;
					}
						
					
				}
			}
		
		
		
		
		$output["tab"] = $resdata;
		TLOG_MSG("Channel::addStation: resdata=".$resdata);
		return $this->result_struct(
	        array(
	            'stateId'	=> -160,
	            'message'	=> 'get Station info succeed',
	            'result'	=> $output
	        )
	    );
	}*/
	
	public function addStation($param)
	{
		TLOG_MSG("Channel::addStation: macid=".$param['macid']." user=".$param['user']." ptype=".$param['ptype']." servip=".$param['servip']." servport=".$param['servport']." retry=".$param['retry']." timeout=".$param['timeout']." polltime=".$param['polltime']." connected=".$param['connected']." active=".$param['active']);
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$result = $merchantsResult_DB->AddStationInfo($param);
		$message= $result['result'] ? "add Station failure: ".$result['errorMsg']:"add Station succeed";
		//$this->log($this->Ary_Session['user'],$message);
		return $this->result_set((!$result['result'] ? -108 : 1807),$message);
	}
	
	public function addChannel($param)
	{
		TLOG_MSG("Channel::addChannel: station=".$param['station']." active=".$param['active']);
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$result = $merchantsResult_DB->AddChannel($param);
		$message= $result['result'] ? "add Channel failure: ".$result['errorMsg']:"add Channel succeed";
		//$this->log($this->Ary_Session['user'],$message);
		return $this->result_set((!$result['result'] ? -108 : 1807),$message);
	}
	
	public function editChannel($param)
	{
		TLOG_MSG("Channel::editChannel: station=".$param['station']." active=".$param['active']);
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$result = $merchantsResult_DB->EditChannel($param);
		$message= $result['result'] ? "edit Channel failure: ".$result['errorMsg']:"edit Channel succeed";
		//$this->log($this->Ary_Session['user'],$message);
		return $this->result_set((!$result['result'] ? -108 : 1807),$message);
	}
	
	
	
	public function AddTag($param)
	{
		TLOG_MSG("Channel::AddTag: id=$param[id] devicename=$param[devicename] tagid=$param[tagid] tagname=$param[tagname] tagdesc=$param[tagdesc] webshow=$param[webshow]");
		//TLOG_MSG("Channel::AddTag: id=$param[id] devicename=$param[devicename]");
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$result = $merchantsResult_DB->AddTag($param);
		$message= $result['result'] ? "add tag failure: ".$result['errorMsg']:"add tag succeed";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set((!$result['result'] ? -108 : 1807),$message);
	}
	
	public function editTag($param)
	{
		TLOG_MSG("Channel::editTag: id11=$param[id] devicename11=$param[devicename_1] tagid=$param[tagid] tagname_1=$param[tagname_1] tagdesc=$param[tagdesc] countreg=$param[countreg]");
		$i=0;
		for ($i=1; $i<=$param[countreg];$i++)
		{
			$channelid = "channelid_$i";
			$tagid = "tagid_$i";
			$tagname = "tagname_$i";
			$tagdesc = "tagdesc_$i";
			$webshow = "webshow_$i";
			$coiltype = "coiltype_$i";
			$writeenable = "writeenable_$i";
			 
			
			
			TLOG_MSG("tagid=$tagid channelid=$param[$channelid] tagid=$param[$tagid] tagname=$param[$tagname] tagdesc=$param[$tagdesc] webshow=$param[$webshow] coiltype=$param[$coiltype] writeenable=$param[$writeenable] devname=$param[devname]");
		}
		//TLOG_MSG("Channel::editTag: id=$param[id] devicename=$param[devicename]");
		
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$result = $merchantsResult_DB->EditTag($param);
		$message= $result['result'] ? "edit tag failure: ".$result['errorMsg']:"edit tag succeed";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set((!$result['result'] ? -108 : 1807),$message);
	}
	
	public function editAlarm($param)
	{
		TLOG_MSG("Channel::editalarm: func begin");
		
		//TLOG_MSG("Channel::editLarm: id=$param[id] devicename=$param[devicename]");
		
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$result = $merchantsResult_DB->editAlarm($param);
		$message= $result['result'] ? "edit alarm failure: ".$result['errorMsg']:"edit alarm succeed";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set((!$result['result'] ? -108 : 1807),$message);
	}
	
	public function getTagInfo($param)
	{
		$var=explode(",",$param);
		TLOG_MSG("Channel::getTagInfo: func begin page=".$param." var0=".$var[0]." var1=".$var[1]);
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
		$ret =  $merchantsResult_DB->getTagInfo($var[0],$var[1]);
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
	            
	            $retItem["chid"] = $item["chid"];
	            $retItem["tagid"] = $item["tagid"];
	            $retItem["tagname"] = $item["tagname"];
				$retItem["tagdesc"] = $item["tagdesc"];
				$retItem["webshow"] = $item["webshow"];
				$retItem["coiltype"] = $item["coiltype"];
				$retItem["writeenable"] = $item["writeenable"];
				$retItem["reportenable"] = $item["reportenable"];
				$retItem["excellocation"] = $item["excellocation"];
				$retItem["mainpageenable"] = $item["mainpageenable"];
				$retItem["mainpagelocation"] = $item["mainpagelocation"];
				$output["data"][] = $retItem;
			}
		}
		
		return $this->result_struct(
	        array(
	            'stateId'	=> -160,
	            'message'	=> 'get company info succeed',
	            'result'	=> $output
	        )
	    );
		
		
		//TLOG_MSG("Channel::editTag: id=$param[id] devicename=$param[devicename]");
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$result = $merchantsResult_DB->getTagInfo($param);
		$message= $result['result'] ? "get tag failure: ".$result['errorMsg']:"get tag succeed";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set((!$result['result'] ? -108 : 1807),$message);
	}
	
	public function getAlarmInfo($param)
	{
		$var=explode(",",$param);
		TLOG_MSG("Channel::getAlarmInfo: func begin page=".$param." var0=".$var[0]." var1=".$var[1]);
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
		$ret =  $merchantsResult_DB->getAlarmInfo($var[0],$var[1]);
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
	            
	            $retItem["chid"] = $item["chid"];
	            $retItem["tagid"] = $item["tagid"];
	            $retItem["tagname"] = $item["tagname"];
				
				$retItem["alarmenable"] = $item["alarmenable"];
				$retItem["alarmontext"] = $item["alarmontext"];
				$retItem["alarmofftext"] = $item["alarmofftext"];
				$retItem["minvalue"] = $item["minvalue"];
				$retItem["maxval"] = $item["maxval"];
				
				$output["data"][] = $retItem;
			}
		}
		
		return $this->result_struct(
	        array(
	            'stateId'	=> -160,
	            'message'	=> 'get company info succeed',
	            'result'	=> $output
	        )
	    );
		
		
		//TLOG_MSG("Channel::editTag: id=$param[id] devicename=$param[devicename]");
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$result = $merchantsResult_DB->getTagInfo($param);
		$message= $result['result'] ? "get tag failure: ".$result['errorMsg']:"get tag succeed";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set((!$result['result'] ? -108 : 1807),$message);
	}
	
	public function writeDevData($param)
	{
		TLOG_MSG("writeDevData");
	}
	
	public function getChannelData($page){
		TLOG_MSG("Channel::getChannelData: func begin");
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
		
		$resDev =  $merchantsResult_DB->getAllDevName();
		if ($resDev["result"] != 0)
		{
			$output["retCode"] = $resDev["result"];
			$output["sErrMsg"] = $resDev["sErrMsg"];
			$test = "SearchResultCB(".json_encode($output).")";
			echo $test;
			return -1;
		}
		
		if(is_array($resDev["data_list"]))
		{
			foreach($resDev["data_list"] as $item)
			{
				
				$retItem = array();
	            
	            $retItem["id"] = $item["id"];
	            $retItem["name"] = $item["devname"];
				
				 $retItem["company_id"] = $item["company_id"];
	            $retItem["group_id"] = $item["group_id"];
				 $retItem["station_id"] = $item["station_id"];
				
				$output["devinfo"][] = $retItem;
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
	
	
	public function getDevCompAllInfo($param){
		$var=explode(",",$param);
		TLOG_MSG("Channel::getDevCompAllInfo: func begin page=".$param." var0=".$var[0]." var1=".$var[1]);
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
	    
		
		$resStation =  $merchantsResult_DB->getDevName($var);
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
	            $retItem["name"] = $item["devname"];
				$output["devinfo"][] = $retItem;
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
	
}
?>