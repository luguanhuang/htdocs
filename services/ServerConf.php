<?php
//****************** [Class] 校时服务对象 ******************

//该类必须运行于 PHP5.0 及以上的平台
if ( intval(PHP_VERSION) < 5 ){ exit("<p>Run \"ServerConf\" invite promotion your PHP arrives at 5.0 the above editions</p>");}

if (!class_exists('AtherFrameWork')){ require(dirname(__FILE__)."/AtherFrameWork.php"); }

class ServerConf extends AtherFrameWork{
	
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
	获取校时服务器配置
	number: 1600
	*/
	public function getVerifyConfig(){
		if (!$this->user_popedom(array(__METHOD__,__CLASS__."::getVerifyConfig"))){return $this->Ary_Popedom;}
		$result = $this->get_globalConf(1600,$interr,$strerr);
		if ($result === false) { return $this->result_set($interr,$strerr); }
		$result = isset($result['ntpserver']) ? $result['ntpserver'] : "";
		return $this->result_struct(
			array(
				"stateId"	=> -160,
				"message"	=> "返回校时服务器设置完成",
				"result"	=> $result
			)
		);
	}

	/*
	保存校时服务器配置
	number: 1700
	*/
	public function setVerifyConfig($param){
		if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		$keys = array("ip" => array( "name"=>"校时服务器IP地址", "rule"=>parent::_REG_IPADD_));
		$param	= $this->validateArgs($param,$keys,1700,$interr,$strerr);
		if ( !$param ){ return $this->result_set($interr, $strerr); }
		$result = $this->socket_send(144, _GLO_FILE_SERVER_VERI_CONF_.'|'.$param['ip']);
		$message= !$result['state'] ? "保存校时服务器配置失败：".$result['errtxt'] : "保存校时服务器配置成功";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set(($result['state']?-170:1706),$message);
	}
	
	/*
	获取日志服务器配置
	number: 1800
	*/
	public function getLogSvrConfig(){
		if (!$this->user_popedom(array(__METHOD__,__CLASS__."::setLogSvrConfig"))){return $this->Ary_Popedom;}
		$content = $this->file_content(_GLO_FILE_SERVER_CONF_,"日志服务器配置文件",1800,$interr,$strerr);
		if ($content===false){ return $this->result_set($interr,$strerr); }
		//匹配出一部分
		//$result = array("ip" => "", "port"	=> "");	#只允许配置单个服务器
		$result = array();								#允许配置多个服务器
		if (preg_match("/(^|[\r\n])[\ \t]*\[log\][\ \t]*([\r\n][\s\S]+)/i", $content, $match)){
			$content = preg_replace("/(^|[\r\n])[\ \t]*\[[^\]+\][\ \t]*[\s\S]*/is","", $match[2]);
			/*
			//只允许配置单个服务器
			$rules	= array(
				"ip"	=> "/[\r\n]+[\t\ ]*ip[\t\ ]*\=[\t\ ]*([^\r\n]*)/i",
				"port"	=> "/[\r\n]+[\t\ ]*port[\t\ ]*\=[\t\ ]*(\d*)/i",
			);
			foreach($rules as $key=>$rule){
				if (!isset($result[$key])){continue;}
				$result[$key] = preg_match($rule,$content,$match) ? trim($match[1]) : "";
			}
			unset($rules);	$rules = NULL;
			*/
			/*
			//允许配置多个服务器
			*/
			$rule = "/[\r\n]+[\t\ ]*logserver[\t\ ]*\=[\t\ ]*([^\r\n]*)/i";
			$drow = array("ip" => "", "port"	=> "");
			if (preg_match($rule,$content,$match)){
				$list	= explode(",",$match[1]);
				$strpos = false;
				foreach($list as $k=>$value){
					$list[$k] = trim($list[$k]);
					if ($list[$k]==""){continue;}
					$strpos = strpos($list[$k],":");
					$result[$k] = $drow;
					if ($strpos===false){
						$result[$k]['ip'] = $list[$k];
					}
					else{
						$result[$k]['ip'] =  trim(substr($list[$k],0,$strpos));
						$result[$k]['port']= trim(substr($list[$k],$strpos+1));
					}
				}
				$result = array_values($result);
			}
			unset($match);	$match = NULL;
		}
		return $this->result_struct(
			array(
				"stateId"	=> -180,
				"message"	=> "返回日志服务器设置完成",
				"result"	=> $result
			)
		);
	}
	
	protected function str2Server($str){
		$v = trim(strval($str));
		if ($v==""){ return NULL; }
		$i = strpos($v,":");
		return $i===false ? array("ip"=>$v,"port"=>"") : array("ip"=>substr($v,0,$i),"port"=>substr($v,$i+1));
	}
	
	/*
	保存日志服务器配置
	number: 1900
	interface: 146
	*/
	public function setLogSvrConfig($param){
		if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		$keys = array( "servers"	=> array("name"=>"日志服务器配置"));
		$param	= $this->validateArgs($param,$keys,1900,$interr,$strerr);
		if ( !$param ){ return $this->result_set($interr, $strerr); }
		//验证服务器IP
		if (!is_array($param['servers'])){
			$svrval = trim(strval($param['servers']));
			$param['servers'] = array();
			if ($svrval!=""){
				$a = explode(", ",$svrval);
				foreach($a as $v){$param['servers'][] = $this->str2Server($v); }
				unset($a); $a = NULL;
			}
		}
		else{
			$svrary = $param['servers'];
			$param['servers'] = array();
			foreach($svrary as $v){
				if (is_array($v)){
					if (!$v){
						$param['servers'][]=NULL;
					}
					else{
						$ip = NULL; $port = NULL;
						if (array_key_exists('ip',$v)!==false){$ip = $v['ip']; unset($v['ip']);	}
						if (array_key_exists('port',$v)!==false){$ip = $v['port']; unset($v['port']); }
						if (!isset($ip)){
							$ip=next($v);
							if ($ip===false){$param['servers'][]=NULL; continue;}
						}
						if (!isset($port)){$port = strval(next($v));}
						$param['servers'][] = array('ip'=>$ip,'port'=>$port);
					}
				}
				else{
					$param['servers'][] = $this->str2Server($v);
				}
			}
			unset($svrary); $svrary = NULL;
		}
		$svrary = array();
		foreach($param['servers'] as $k=>$v){
			if (!$v){ continue; }
			if ($v['ip']==""){
				return $this->result_set(1710,"第 ".($k+1)." 个日志服务器IP地址为空");
			}
			if (!preg_match(self::_REG_IPADD_,$v['ip'])){
				return $this->result_set(1711,"第 ".($k+1)." 个日志服务器IP地址错误");
			}
			if ($v['port']==""){
				return $this->result_set(1712,"第 ".($k+1)." 个日志服务器IP端口号为空");
			}
			if (!is_numeric($v['port'])){
				return $this->result_set(1713,"第 ".($k+1)." 个日志服务器端口号错误");
			}
			$v['port'] = intval($v['port']);
			if ($v['port']<self::_INT_PORT_MIN_ || $v['port']>self::_INT_PORT_MAX_){
				return $this->result_set(1714,"第 ".($k+1)." 个日志服务器端口号错误");
			}
			$svrary[] = $v['ip'].":".$v['port'] ;
		}
		if (!$svrary){
			return $this->result_set(1715,"至少必须保留一条日志服务器的记录");
		}
		//var_dump($svrary);
		$param['servers'] = implode(",", $svrary);
		unset($svrary); $svrary = NULL;
		
		$result = $this->socket_send(211, _GLO_FILE_SERVER_CONF_.'|'.$param['servers']);
		$message= !$result['state'] ? "保存日志服务器配置失败：".$result['errtxt'] : "保存日志服务器配置成功";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set(($result['state']?-190:1906),$message);
	}
}
?>