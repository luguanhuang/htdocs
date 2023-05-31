<?php
//****************** [Class] 路由对象 ******************

//该类必须运行于 PHP5.0 及以上的平台
if ( intval(PHP_VERSION) < 5 ){ exit("<p>Run \"Nic\" invite promotion your PHP arrives at 5.0 the above editions</p>");}

if (!class_exists('AtherFrameWork')){ require(dirname(__FILE__)."/AtherFrameWork.php"); }

require_once 'tlog.php';

class Nic extends AtherFrameWork{
	
	//__________________  构造/析构函数  ________________
	
	/**/
	function __construct(){
		parent::__construct();
	}
	
	
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
	
	/*
	返回路由器设置列表
	number: 1600
	*/
	public function getNicList(){
	    TLOG_MSG("Nic::getNicList: func begin");
	    //if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
	    $result	= $this->socket_send(300,"",_GLO_SOCKET_WANSTATE_);
	    $message= !$result['state'] ? "获取物理接口失败：".$result['errtxt'] : "获取物理接口成功";
	    if (!$result['state']){return $this->result_set(2000,$message);}
	    $result["request"] = iconv("gb2312","utf-8//IGNORE",$result["request"]);
	    $responseData = array();
	    $redata = explode("|",$result["request"]);
	    $idx=0;
	    foreach ($redata as $nicinfo)
	    {
	        if (0 == $idx)
	        {
	            $idx = $idx + 1;
	            continue;
	        }
	        
	        $idx = $idx + 1;
	        $tmp = explode(",", $nicinfo);
	        TLOG_MSG("Nic::getNicList: nic name=".$tmp[0]." up=".$tmp[1]." mode=".$tmp[2]." use=".$tmp[3]." ip=".$tmp[4]." mask=".$tmp[5]);
	        $responseData[] = array(
	            "name"		=> $tmp[0],
	            "upflg"		=> $tmp[1],
	            "mode"		=> $tmp[2],
	            "forbitflg"	=> $tmp[3],
	            "ip"	=> $tmp[4],
	            "mask"	=> $tmp[5]
	        );
	        /*$idx = 0;
	        foreach ($tmp as $data)
	        {
	            if (0 == $idx)
	            {
	                TLOG_MSG("Nic::getNicList: func begin");
	                $aSid[] = $data;
	            }
	            else if (1 == $idx)
	            {
	                //$aSellerUin[] = $data;
	            }
	            else if (2 == $idx)
	            {
	                //$aTradeId[] = $data;
	            }
	            $idx = $idx + 1;
	        }*/
	    }
	    
	    unset($redata); $redata = NULL;
	    return $this->result_struct(
	        array(
	            "stateId"	=> -160,
	            "message"	=> "获取物理接口信息完成",
	            "result"	=> $responseData
	        )
	    );
	}
}
?>