<?php
//****************** [Class] 校时服务器对象 ******************

//该类必须运行于 PHP5.0 及以上的平台
if ( intval(PHP_VERSION) < 5 ){ exit("<p>Run \"BackupRecover\" invite promotion your PHP arrives at 5.0 the above editions</p>");}

if (!class_exists('AtherFrameWork')){ require(dirname(__FILE__)."/AtherFrameWork.php"); }

class BackupRecover extends AtherFrameWork{
	
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
	恢复出厂设置
	number: 1700
	*/
	public function defaultConf(){
		if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		$result = $this->socket_send(155,"");
		$message= !$result['state'] ? "恢复出厂设置失败：".$result['errtxt'] : "恢复出厂设置成功";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set(($result['state']?-170:1700),$message);
	}

	/*
	备份配置
	number: 1800
	*/
	public function backupConf(){
		if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		$result = $this->socket_send(167,"");
		$message= !$result['state'] ? "备份配置失败：".$result['errtxt'] : "备份配置成功";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set( ($result['state']?-180:1800),$message );
	}
	
	/*
	获取所有备份
	number: 1900
	*/
	
	/*
	删除已有备份
	number: 2000
	*/

	/*
	恢复配置指定的备份
	number: 2100
	*/
	public function resumeConf(){
		if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		$result = $this->socket_send(166,"");
		$message= !$result['state'] ? "恢复配置失败：".$result['errtxt'] : "恢复配置成功";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set( ($result['state']?-210:2100), $message);
	}
}
?>