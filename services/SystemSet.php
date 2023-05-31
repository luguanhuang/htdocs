<?php
//****************** [Class] 系统管理对象 ******************
require_once 'tlog.php';
//该类必须运行于 PHP5.0 及以上的平台
if ( intval(PHP_VERSION) < 5 ){ exit("<p>Run \"SystemSet\" invite promotion your PHP arrives at 5.0 the above editions</p>");}

if (!class_exists('AtherFrameWork')){ require(dirname(__FILE__)."/AtherFrameWork.php"); }

class SystemSet extends AtherFrameWork{
	
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
	获取系统信息
	number: 1600
	*/
	public function getSysInfo(){
	    TLOG_MSG("SystemSet::getSysInfo func begin _GLO_FILE_SYSTEM_INFO_="._GLO_FILE_SYSTEM_INFO_);
		if (!$this->user_popedom()){return $this->Ary_Popedom;}
		TLOG_MSG("SystemSet::getSysInfo func begin 1");
		$str_log = $this->file_content(_GLO_FILE_SYSTEM_INFO_,"系统信息日志文件",1600,$interr,$strerr);
		TLOG_MSG("SystemSet::getSysInfo func begin 1.1");
		if ($str_log===false)
		{
		    TLOG_MSG("SystemSet::getSysInfo func begin 1.2");
		    return $this->result_set($interr,$strerr);
		}
		$ary_data 	= explode("|",$str_log);
		TLOG_MSG("SystemSet::getSysInfo func begin 2");
		$ary_dict 	= array();
		$int_post 	= -1;
		$str_key	= "";
		$str_value	= "";
		foreach($ary_data as $key=>$value){
			$int_post = strpos($value,"=");
			if (!$int_post){ continue; }
			$str_key	= trim(substr($value,0,$int_post));
			$str_value	= trim(substr($value,$int_post+1));
			TLOG_MSG("SystemSet::getSysInfo func begin str_key=".$str_key." str_value=".$str_value);
			$ary_dict[$str_key] = $str_value;
		}
		unset($ary_data); $ary_data = NULL;
		TLOG_MSG("SystemSet::getSysInfo func begin 4");
		return $this->result_struct(
			array(
				"stateId"	=> -160,
				"message"	=> "读取系统信息完成",
				"result"	=> $ary_dict
			)
		);
	}
	
	////************************ 全局开关 **********************////

	/*
	返回全局开关配置
	number: 1700
	*/
	public function getGlobalSwitch(){
		if (!$this->user_popedom(array(__METHOD__,__CLASS__."::saveGlobalSwitch"))){return $this->Ary_Popedom;}
		$result = $this->get_globalConf(1700,$interr,$strerr);
		if ($result===false){ return $this->result_set($interr,$strerr); }
		return $this->result_struct(
			array(
				"stateId"	=> -170,
				"message"	=> "读取全局设置完成",
				"result"	=> $result
			)
		);
	}
	/*
	保存全局开关配置
	number: 1800
	interface: 50
	*/
	public function setGlobalSwitch($param){
		if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		$keys	= array(
			"firewall"	=>	array("name"=>"防火墙服务",	"rule"=>array(-1,0,1), "empty"=>true),
			"dhcpsvr"	=>	array("name"=>"DHCP服务",	"rule"=>array(-1,0,1), "empty"=>true),
		);
		$param	= $this->validateArgs($param,$keys,1800,$interr,$strerr);
		if ( !$param ){ return $this->result_set($interr, $strerr); }
		//参数
		$param["firewall"] = !isset($param["firewall"]) ? -1 : intval($param["firewall"]);
		$param["dhcpsvr"] = !isset($param["dhcpsvr"]) ? -1 : intval($param["dhcpsvr"]);
		if ($param["firewall"]==-1 && $param["dhcpsvr"] ==-1 ){return $this->result_set(-180,"保存全局配置成功");}
		//发送字节
		$senddata = _GLO_FILE_GLOBAL_SWITCH_."|".$param['firewall']."|".$param['dhcpsvr'];
		//发送socket
		$result	= $this->socket_send(50,$senddata);
		$message= !$result['state'] ? "保存全局配置失败：".$result['errtxt'] : "保存全局配置成功";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set(($result['state'] ? -180 : 1806),$message);
	}
	
	/*
	设置系统时钟
	number: 1900
	interface: 168
	*/
	public function setDateTime($param){
		if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		$keys	= array("datetime"=>array("name"=>"日期时间"));
		$param	= $this->validateArgs($param,$keys,1900,$interr,$strerr);
		if ( !$param ){ return $this->result_set($interr, $strerr); }
		if (!FuncExt::is_datetime($param["datetime"], "1", $this->Str_Error)){
			return $this->result_set(1901, "日期时间不正确，".$this->Str_Error);
		}
		//发送socket
		$result	= $this->socket_send(168,$param["datetime"]);
		$message= !$result['state'] ? "设置系统时钟失败：".$result['errtxt'] : "设置系统时钟成功";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set(($result['state'] ? -190 : 1902),$message);
	}
}
?>