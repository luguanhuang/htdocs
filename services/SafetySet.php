<?php
//****************** [Class] 安全设置对象 ******************

//该类必须运行于 PHP5.0 及以上的平台
if ( intval(PHP_VERSION) < 5 ){ exit("<p>Run \"SafetySet\" invite promotion your PHP arrives at 5.0 the above editions</p>");}

if (!class_exists('AtherFrameWork')){ require(dirname(__FILE__)."/AtherFrameWork.php"); }

class SafetySet extends AtherFrameWork{
	
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
	获取认证服务器配置
	number: 1600
	*/
	public function getAuthServer(){
		if (!$this->user_popedom(array(__METHOD__,__CLASS__."::setAuthServer"))){return $this->Ary_Popedom;}
		$content = $this->file_content(_GLO_FILE_SERVER_CONF_,"认证服务器配置文件",1600,$interr,$strerr);
		if ($content===false){ return $this->result_set($interr,$strerr); }
		$result = array();
		$match	= array();
		$rules	= array(
			"servers"	=> "/[\r\n]+authserver[\t\ ]*\=[\t\ ]*([^\r\n]*)/i",
			"maxtime"	=> "/[\r\n]+connect_times[\t\ ]*\=[\t\ ]*(\d*)/i",
			"timeout"	=> "/[\r\n]+connect_timeout[\t\ ]*\=[\t\ ]*(\d*)/i",
		);
		/*
		server = 192.168.5.63:11,192.168.5.63:12
		connect_timeout = 5
		connect_times = 5
		*/
		foreach($rules as $key=>$rule){
			$result[$key] = preg_match_all($rule,$content,$match,2) ? trim($match[0][1]) : "";
		}
		$svrval = $result['servers'];
		$result['servers'] = array();
		if ($svrval!=""){
			$a = explode(",",$svrval);
			foreach($a as $v){if ($v=$this->str2Server($v)){ $result['servers'][] = $v; }}
			unset($a); $a = NULL;
		}
		
		unset($match); $match = NULL;
		unset($rules); $rules = NULL;
		return $this->result_struct(
			array(
				"stateId"	=> -160,
				"message"	=> "获取认证服务器配置完成",
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
	保存认证服务器配置
	number: 1700
	interface: 200
	*/
	public function setAuthServer($param){
		if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		$keys	= array(
			"servers"	=> array("name"=>"认证服务器地址"),
			"timeout"	=> array("name"=>"连接超时时间",		"rule"=>parent::_REG_POSINT_,	"default"=>5),
			"maxtime"	=> array("name"=>"最大连接次数",		"rule"=>parent::_REG_POSINT_,	"default"=>5)
		);
		$param	= $this->validateArgs($param,$keys,1700,$interr,$strerr);
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
				return $this->result_set(1710,"第 ".($k+1)." 个认证服务器IP地址为空");
			}
			if (!preg_match(self::_REG_IPADD_,$v['ip'])){
				return $this->result_set(1711,"第 ".($k+1)." 个认证服务器IP地址错误");
			}
			if ($v['port']==""){
				return $this->result_set(1712,"第 ".($k+1)." 个认证服务器IP端口号为空");
			}
			if (!is_numeric($v['port'])){
				return $this->result_set(1713,"第 ".($k+1)." 个认证服务器端口号错误");
			}
			$v['port'] = intval($v['port']);
			if ($v['port']<self::_INT_PORT_MIN_ || $v['port']>self::_INT_PORT_MAX_){
				return $this->result_set(1714,"第 ".($k+1)." 个认证服务器端口号错误");
			}
			$svrary[] = $v['ip'].":".$v['port'] ;
		}
		if (!$svrary){
			return $this->result_set(1715,"至少必须保留一条认证服务器的记录");
		}
		//var_dump($svrary);
		$param['servers'] = implode(",", $svrary);
		unset($svrary); $svrary = NULL;
		
		$sendaeg= _GLO_FILE_SERVER_CONF_.'|'.$param['timeout'].'|'.$param['maxtime']."|".$param['servers'];
		$result = $this->socket_send(200, $sendaeg);
		$message= !$result['state'] ? "配置认证服务器失败：".$result['errtxt'] : "配置认证服务器成功";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set(($result['state']?-170:1715),$message);
	}
	
	/**
	读取安全接入客户端配置
	number: 1800
	*/
	public function getSafeClient(){
		if (!$this->user_popedom(array(__METHOD__,__CLASS__."::setSafeClient"))){return $this->Ary_Popedom;}
		$lines = $this->file_array(_GLO_FILE_VPN_,"VPN客户端配置文件",1800,$interr,$strerr);
		if ($lines===false){return $this->result_set($interr,$strerr);}
		//$lines = file('test-cfg/client.conf');//调试文件
		//去掉注释
		$lines	= preg_replace('/(^\s*[\#\;].*?\n)|([\n\r])/', '',$lines);
		$line	= array();
		//读取必要字段，单条内容
		$fields = array("proto","cipher");
		$field	= array();
		$result = array("remotes"=>array());
		foreach ($lines as $line){
			$line = trim(strval($line));
			if($line==""){continue;}
			$field	= explode(" ", $line);
			$key	= $field[0];
			//如果是远程的
			if ($key=="remote"){
				$result["remotes"][] = array(
					"ip"	=> isset($field[1])? $field[1] : "",
					"port"	=> isset($field[2])? $field[2] : ""
				);
			}
			//其他的: proto/cipher
			elseif( in_array($key, $fields) ){
				$result[$key] = isset($field[1])? $field[1] : "";
			}
		}
		unset($field);	$field	= NULL;
		unset($fields);	$fields = NULL;
		unset($line);	$line	= NULL;
		unset($lines);	$lines	= NULL;
		return $this->result_struct(
			array(
				"stateId"	=> -180,
				"message"	=> "获取安全客户端设置完成",
				"result"	=> $result
			)
		);
	}
	/**
	保存安全接入客户端配置
	number: 1900
	interface: 90
	*/
	public function setSafeClient($param){
		if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		//参数
		$keys	= array(
			'remotes'	=> 	array('name'=>'服务器配置'),
			'proto'		=>	array('name'=>'协议类型', 	"rule"=>array("UDP","TCP")),
			'cipher'	=>	array('name'=>'加密算法',	 	"rule"=>explode("|",_GLO_SAFECLIENT_CIPHER_)),
		);
		$param	= $this->validateArgs($param,$keys,1900,$interr,$strerr);
		if ( !$param ){ return $this->result_set($interr, $strerr); }
		
		//验证服务器IP
		if (!is_array($param['remotes'])){
			$svrval = trim(strval($param['remotes']));
			$param['remotes'] = array();
			if ($svrval!=""){
				$a = explode(", ",$svrval);
				foreach($a as $v){$param['remotes'][] = $this->str2Server($v); }
				unset($a); $a = NULL;
			}
		}
		else{
			$svrary = $param['remotes'];
			$param['remotes'] = array();
			foreach($svrary as $v){
				if (is_array($v)){
					if (!$v){
						$param['remotes'][]=NULL;
					}
					else{
						$ip = NULL; $port = NULL;
						if (array_key_exists('ip',$v)!==false){$ip = $v['ip']; unset($v['ip']);	}
						if (array_key_exists('port',$v)!==false){$ip = $v['port']; unset($v['port']); }
						if (!isset($ip)){
							$ip=next($v);
							if ($ip===false){$param['remotes'][]=NULL; continue;}
						}
						if (!isset($port)){$port = strval(next($v));}
						$param['remotes'][] = array('ip'=>$ip,'port'=>$port);
					}
				}
				else{
					$param['remotes'][] = $this->str2Server($v);
				}
			}
			unset($svrary); $svrary = NULL;
		}
		$remote = array();
		foreach($param['remotes'] as $k=>$v){
			if (!$v){ continue; }
			if ($v['ip']==""){
				return $this->result_set(1910,"第 ".($k+1)." 个服务器IP地址为空");
			}
			if (!preg_match(self::_REG_IPADD_,$v['ip'])){
				return $this->result_set(1911,"第 ".($k+1)." 个服务器IP地址错误");
			}
			if ($v['port']==""){
				return $this->result_set(1912,"第 ".($k+1)." 个服务器IP端口号为空");
			}
			if (!is_numeric($v['port'])){
				return $this->result_set(1913,"第 ".($k+1)." 个服务器端口号错误");
			}
			$v['port'] = intval($v['port']);
			if ($v['port']<self::_INT_PORT_MIN_ || $v['port']>self::_INT_PORT_MAX_){
				return $this->result_set(1914,"第 ".($k+1)." 个服务器端口号错误");
			}
			$remote[] = $v['ip'].":".$v['port'] ;
		}
		if (!$remote){
			return $this->result_set(1915,"至少必须保留一条服务器的记录");
		}
		//var_dump($remote);
		$param['remotes'] = implode(",", $remote);
		unset($remote); $remote = NULL;
		#数据包长度|数据包类型|文件名|协议类型|加密算法|ip地址：端口|ip地址：端口。如：
		#0093|090|/usr/local/openvpn/etc/client.conf|udp|BF-CBC|192.168.5.207:8280|192.168.7.122:58754。
		$sendarg = _GLO_FILE_VPN_."|".strtolower($param['proto'])."|". strtoupper($param['cipher']) ."|". $param['remotes'];
		//print($sendarg);
		//发送socket
		$result	= $this->socket_send(90,$sendarg);
		$message= !$result['state'] ? "保存安全客户端设置失败：".$result['errtxt']:"保存安全客户端设置成功";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set(($result['state'] ? -190 : 1912),$message);
	}

	/**
	重启安全接入服务
	number: 2000
	interface: 145
	*/
	public function restartSafe(){
		if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		$result	= $this->socket_send(145,"",_GLO_SOCKET_REVPNSVR_);
		$message= !$result['state']?"重启可管控安全通讯平台失败：".$result['errtxt']:"重启可管控安全通讯平台成功";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set(($result['state'] ? -200 : 2000),$message);
	}
	
	/*
	安全接入状态
	number: 2100
	interface: 91
	*/
	public function getSafeStatus(){
		if (!$this->user_popedom()){return $this->Ary_Popedom;}
		$result	= $this->socket_send(91,"", _GLO_SOCKET_SAFESTATE_);
		$message= !$result['state'] ? "获取安全接入状态失败：".$result['errtxt'] : "获取安全状态成功";
		//$this->log($this->Ary_Session['user'],$message);
		if (!$result['state']){
			return $this->result_struct(
				array(
					"stateId"	=> 2100,
					"message"	=> $message,
					"result"	=> array(
						'socket_state'	=> $result['state'],
						'socket_error'	=> $result['error'],
						'socket_errtxt'	=> $result['errtxt'],
					)
				)
			);
		}
		$result["request"] = iconv("gb2312","utf-8//IGNORE",$result["request"]);
		$redata = explode("|",$result["request"]);
		$state	= array(
			'socket_state'	=> $result['state'],
			'socket_error'	=> $result['error'],
			'socket_errtxt'	=> $result['errtxt'],
			"type"		=> isset($redata[1]) ? $redata[1] : "",
			"ip"		=> isset($redata[2]) ? $redata[2] : "",
			"mask"		=> isset($redata[3]) ? $redata[3] : "",
			"broadcast"	=> isset($redata[4]) ? $redata[4] : "",
			"dns1"		=> isset($redata[5]) ? $redata[5] : "",
			"dns2"		=> isset($redata[6]) ? $redata[6] : "",
		);
		unset($redata); $redata = NULL;
		unset($result); $result = NULL;
		return $this->result_struct(
			array(
				"stateId"	=> -210,
				"message"	=> "读取安全接入状态完成",
				"result"	=> $state
			)
		);
	}

}
?>