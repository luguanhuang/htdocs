<?php
//****************** [Class] DHCP对象 ******************

//该类必须运行于 PHP5.0 及以上的平台
if ( intval(PHP_VERSION) < 5 ){ exit("<p>Run \"DHCPService\" invite promotion your PHP arrives at 5.0 the above editions</p>");}

if (!class_exists('AtherFrameWork')){ require(dirname(__FILE__)."/AtherFrameWork.php"); }

class DHCPService extends AtherFrameWork{
	
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
	
	protected function dhcpConf2Array($conf){
		//配置内容
		$conf	= strval($conf);
		//预置返回结果
		$result = array();
		//IP过滤
		preg_match('/(^[^\#]\s*\[LISTEN\_ON\]\n.*?\n+)([^#]\d+\.\d+\.\d+\.\d+)?$/ms', $conf, $match);
		$result["listen"] = $match && count($match)>2 ? $match[2] : "";
		//范围过滤
		preg_match('/^\s*[^#\']\s*\[RANGE\_SET\]\n(.*?)\n\s*\[/ms', $conf, $ms);
		if($ms && count($ms)>0){
			$ranges= preg_replace('/(^(\#|\').*?\n)|(^(\#|\').*?$)/m', "", $ms[1]);
			$lines = explode("\n", $ranges);
			foreach ($lines as $line){
				$linary = $this->dhcpLine2Array($line);
				if($linary){ $result=array_merge($result,$linary); }
			}
			unset($ms); 	$ms		= NULL;
			unset($lines);	$lines	= NULL;
		}
		return $result;
	}
	
	/** 将DHCP中的RANGE_SET段内行数据转换成"数组" **/
	
	protected function dhcpLine2Array($line){
		$line	= strval($line);
		$strpos = strpos($line,"=");
		if ($strpos<1){ return false; }
		$key	= trim(substr($line,0,$strpos));
		$value	= trim(substr($line,$strpos+1));
		//返回的结果
		$result = array();
		switch ($key){
			case 'DHCP_Range':
				$ips = explode("-", $value);
				$result["minip"] = isset($ips[0]) ? trim($ips[0]) : "";
				$result["maxip"] = isset($ips[1]) ? trim($ips[1]) : "";
				unset($ips); $ips = NULL;
				break;
			case 'Subnet_Mask':
				$result["mask"] = trim($value);
				break;
			case 'DNS_Server':
				$dns = explode(",",$value);
				$result['dns1'] = isset($dns[0]) ? trim($dns[0]) : "";
				$result['dns2'] = isset($dns[1]) ? trim($dns[1]) : "";
				unset($dns); $dns = NULL;
				break;
			case 'Router':
				$result["router"] = trim($value);
				break;
		}
		return $result;
	}

	
	//__________________  公有方法  ________________
	
	/*
	返回DHCP服务器设置列表
	number: 1600
	*/
	public function getDHCP(){
		if (!$this->user_popedom(array(__METHOD__, __CLASS__."::setDHCP"))){return $this->Ary_Popedom;}
		//获取配置
		$content= $this->file_content(_GLO_FILE_DHCP_,"DHCP 配置文件",1600,$interr,$strerr);
		TLOG_MSG("getDHCP: content=".$content);
		if ($content===false){return $this->result_set($interr,$strerr);}
		//获取当前全局配置
		$gloconf = $this->get_globalConf(1610,$this->Int_Error,$this->Str_Error);
		if ($gloconf===false){return $this->result_set();}
		$dhcpsvr = strval($gloconf['dhcpsvr']);
		unset($gloconf); $gloconf = NULL;
		//配置
		$result = array_merge($this->dhcpConf2Array($content),array('dhcpsvr'=>$dhcpsvr));
		return $this->result_struct(
			array(
				"stateId"	=> -160,
				"message"	=> "返回DHCP服务器设置完成",
				"result"	=> $result
			)
		);
	}
	/*
	保存DHCP服务器设置
	number: 1700
	*/
	public function setDHCP($param,$dhcpsvr=1){
		if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		//开关值
		$dhcpsvr= intval(!!$dhcpsvr);
		//获取当前全局配置
		$gloconf = $this->get_globalConf(1700,$this->Int_Error,$this->Str_Error);
		if ($gloconf===false){return $this->result_set();}
		$ischang = $dhcpsvr!=intval(!!$gloconf['dhcpsvr']);
		unset($gloconf); $gloconf = NULL;
		//是否启用
		if ($dhcpsvr){
			$keys	= array(
				"listen"	=>	array("name"=>"监听IP", 			"rule"=>parent::_REG_IPADD_),
				"minip"		=>	array("name"=>"最小IP", 			"rule"=>parent::_REG_IPADD_),
				"maxip"		=>	array("name"=>"最大IP", 			"rule"=>parent::_REG_IPADD_),
				"router"	=>	array("name"=>"路由IP", 			"rule"=>parent::_REG_IPADD_),
				"dns1"		=>	array("name"=>"首选DNS服务器", 	"rule"=>parent::_REG_IPADD_),
				"dns2"		=>	array("name"=>"备用DNS服务器", 	"rule"=>parent::_REG_IPADD_),
			);
			$param	= $this->validateArgs($param,$keys,1710,$interr,$strerr);
			if ( !$param ){ return $this->result_set($interr, $strerr); }
		}
		else{
			//获取配置
			$content= $this->file_content(_GLO_FILE_DHCP_,"DHCP 配置文件",1720,$interr,$strerr);
			if ($content===false){return $this->result_set($interr,$strerr);}
			$config= $this->dhcpConf2Array($content);
			$param	= array("listen"=>"","minip"=>"","maxip"=>"","router"=>"","dns1"=>"","dns2"=>"");
			foreach($param as $key=>$val){ if (isset($config[$key])){$param[$key]=$config[$key];} }
			unset($config); $config = NULL;
		}
		//发送的字节
		$senddata = _GLO_FILE_DHCP_."|".$param['listen']."|".$param['minip']."|".$param['maxip']."|".$param['router']."|".$param['dns1']."|".$param['dns2']."|".($ischang?$dhcpsvr:-1);
		//发送socket
		$result	= $this->socket_send(80,$senddata);
		$message= !$result['state'] ? "保存DHCP失败：".$result['errtxt']:"保存DHCP成功";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set(($result['state'] ? -170 : 1706),$message);
	}
}
?>