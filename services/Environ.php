<?php
//****************** [Class] DHCP对象 ******************

//该类必须运行于 PHP5.0 及以上的平台
if ( intval(PHP_VERSION) < 5 ){ exit("<p>Run \"Environ\" invite promotion your PHP arrives at 5.0 the above editions</p>");}

if (!class_exists('AtherFrameWork')){ require(dirname(__FILE__)."/AtherFrameWork.php"); }

class Environ extends AtherFrameWork{
	
	//__________________  构造/析构函数  ________________
	
	/**/
	function __construct(){
		parent::__construct();

		self::$Ary_EnvItem = array(
			//扩展
			"extend"	=> array(
				"sockets"	=> array('name'=>'sockets','error'=>5,	'msg'=>'无法完成配置操作')
			),
			//函数
			"function"	=> array(
				"popen"		=> array('name'=>'popen','error'=>3, 'msg'=>'无法获取/导出审计类日志')
			),
			//文件/目录权限
			"filesystem"=> array(
				"UserLock.log"	=> array('file'=>_GLO_SERVER_DIRROOT_.'mdb/UserLock.log', 	'valid'=>"r|w", 	'error'=>5, 'msg'=>'用户无法登录'),
				"UserLogin.log"	=> array('file'=>_GLO_SERVER_DIRROOT_.'mdb/UserLogin.log', 	'valid'=>"r|w", 	'error'=>5, 'msg'=>'用户无法登录'),
				"UserLogin.php"	=> array('file'=>_GLO_SERVER_DIRROOT_.'mdb/UserAccount.php','valid'=>"r|w", 	'error'=>5, 'msg'=>array(
					'r'	=> '用户无法登录',
					'w'	=> '无法创建新用户/删除用户/修改用户'
				)),
				"cert"			=> array('dir'=>_GLO_PATH_CERT_, 'valid'=>"r|w|e", "error"=>4, 'msg'=>array(
					'r'	=> '无法获取已存在的普通证书',
					'w'	=> '无法保存上传的普通证书',
					'e'	=> '无法访问普通证目录',
				)),
				"vpncert"		=> array('dir'=>_GLO_PATH_VPN_CERT_, 'valid'=>"r|w|e", "error"=>4, 'msg'=>array(
					'r'	=> '无法获取已存在的VPN证书',
					'w'	=> '无法保存上传的VPN证书',
					'e'	=> '无法访问VPN证书目录',
				)),
				"log_webadmin"	=> array('file'=>_GLO_FILE_WEB_OPERATOR_, 	'valid'=>"r", 	'error'=>3, 'msg'=>'无法读取或导出WEB操作日志'),
				"log_login"		=> array('file'=>_GLO_FILE_CLIENT_LOGIN_, 	'valid'=>"r", 	'error'=>3, 'msg'=>'无法读取或导出客户端登录日志'),
				"log_operator"	=> array('file'=>_GLO_PATH_CLIENT_OPERATOR_,'valid'=>"r", 	'error'=>3, 'msg'=>'无法读取或导出客户端操作/错误日志'),
				"log_offline"	=> array('file'=>_GLO_FILE_CLIENT_OFFLINE_, 'valid'=>"r", 	'error'=>3, 'msg'=>'无法读取或导出用户时长日志'),
				"log_online"	=> array('file'=>_GLO_FILE_CLIENT_ONLINE_, 	'valid'=>"r", 	'error'=>3, 'msg'=>'无法读取或导出用户在线时长日志'),
				"log_templet"	=> array('file'=>_GLO_SERVER_DIRROOT_.'logtemplet.pt', 	'valid'=>"r", 	'error'=>5, 'msg'=>'无法导出审计类日志'),
				"vpnconf"		=> array('file'=>_GLO_FILE_VPN_, 		'valid'=>"r", 	'error'=>3, 'msg'=>'无法获取VPN证书/VPN客户端配置'),
				"dhcpconf"		=> array('file'=>_GLO_FILE_DHCP_, 		'valid'=>"r", 	'error'=>3, 'msg'=>'无法获取DHCP服务配置'),
				"fwconf"		=> array('file'=>_GLO_FILE_FIREWALL_, 	'valid'=>"r", 	'error'=>3, 'msg'=>'无法获取防火墙规则配置'),
				"proxyconf"		=> array('file'=>_GLO_FILE_PROXY_, 		'valid'=>"r", 	'error'=>3, 'msg'=>'无法代理服务器配置'),
				"wlanip"		=> array('file'=>_GLO_FILE_IP_, 		'valid'=>"r", 	'error'=>3, 'msg'=>'无法获取WAN口或LAN口配置'),
				"route"			=> array('file'=>_GLO_FILE_ROUTE_, 		'valid'=>"r", 	'error'=>3, 'msg'=>'无法获取路由功能配置'),
				"pppoedns"		=> array('file'=>_GLO_FILE_PPPOE_DNS_, 	'valid'=>"r", 	'error'=>3, 'msg'=>'无法获取PPPOE DNS配置'),
				"pppoeuser"		=> array('file'=>_GLO_FILE_PPPOE_ACCOUNT_,	'valid'=>"r",'error'=>3, 'msg'=>'无法获取PPPOE帐号'),
				
				"svrconf"		=> array('file'=>_GLO_FILE_SERVER_CONF_, 'valid'=>"r",	'error'=>3, 'msg'=>'无法获取认证服务器/日志服务器配置'),
				"sysinfo"		=> array('file'=>_GLO_FILE_SYSTEM_INFO_, 'valid'=>"r",	'error'=>1, 'msg'=>'无法获取系统信息'),
				"pingdir"		=> array('dir'=>_GLO_PATH_TOOLS_RESULT_, 'valid'=>"r",	'error'=>1, 'msg'=>'无法进行Ping测试操作'),
			)
		);
		if ( defined('_GLO_USER_LOGIN_VCODE_') && _GLO_USER_LOGIN_VCODE_){
			self::$Ary_EnvItem["extend"]["gd"] = array('name'=>'gd2', 'error'=>5,	'msg'=>'用户无法登录');
		}
	}
	
	
	function __destruct(){
		parent::__destruct();
		unset($this->Ary_UserAll);	$this->Ary_UserAll = NULL;
	}
	
	//__________________  私有变量  ________________
	
	protected static $Ary_EnvItem	= array();
	
	protected $Ary_UserAll	= array();
	
	//__________________  只读属性(用方法代替)  ________________
	
	public static function _version()	{return '1.0';}					//版本
	public static function _build()		{return '11.12.12';}			//版本
	public static function _create()	{return '11.12.12';}			//创建
	public static function _classname()	{return __CLASS__;}				//名称
	public static function _developer()	{return "OldFour";}				//开发者
	public static function _copyright()	{return "Aether.CORP";}			//公司
	
	//__________________  只写属性  ________________
	
	
	//__________________  可读写属性  ________________
	
	protected function http_status401($msg=""){
		$msg = trim(strval($msg));
		header('HTTP/1.1 401 Unauthorized');
		header("Content-Language: UTF-8");
		header('WWW-Authenticate: Basic realm=""');
		exit();
	}
	
	
	//__________________  私有方法  ________________

	protected function validate_user(){
		$str_user= isset($_SERVER['PHP_AUTH_USER']) ? strval($_SERVER['PHP_AUTH_USER']) : '';
		$str_pwd = isset($_SERVER['PHP_AUTH_PW']) ? strval($_SERVER['PHP_AUTH_PW']) : '';
		$validate	= false;
		$message	= "";
		if ($str_user=="" || $str_pwd==""){
			if ($this->user_getlogin()){
				$group = isset($this->Ary_Session['group']) ? intval($this->Ary_Session['group']) : 0;
				if ($group==1){ $validate = true; }
			}
		}
		else{
			if (!$this->require_file("file:///"._GLO_SERVER_DIRMDB_._GLO_USER_PATH_MDB_,"r")){
				$this->load_error($this->Int_Error,$this->Str_Error);
				exit();
			}
			if (!isset($GLOBALS['Glo_Ary_User']) || !is_array($GLOBALS['Glo_Ary_User'])){
				$this->error_seting(1010,"用户数据文件无效");
				$this->load_error($this->Int_Error,$this->Str_Error);
				exit();
			}
			$this->Ary_UserAll = &$GLOBALS['Glo_Ary_User'];
			if (!isset($this->Ary_UserAll[$str_user])){
				$this->error_seting(1011,"输入的管理员用户名错误");
			}
			elseif (!is_array($this->Ary_UserAll[$str_user]) || !isset($this->Ary_UserAll[$str_user]['password']) || $this->Ary_UserAll[$str_user]['password']!=md5($str_pwd)){
				$this->error_seting(1011,"输入的登录密码不正确");
			}
			elseif (!isset($this->Ary_UserAll[$str_user]['group']) || $this->Ary_UserAll[$str_user]['group']!="1"){
				$this->error_seting(1012,"该管理帐号不是系统默认管理员，请使用系统默认管理员帐号");
			}
			else{
				$validate = true;
			}
		}
		if (!$validate){$this->http_status401();}
	}
	
	//__________________  公有方法  ________________
	
	public function getEnv(){
		$this->validate_user();
		$result = array();
		foreach(self::$Ary_EnvItem as $k1=>$v1){
			$result[$k1] = array();
			if ($k1=="extend"){
				foreach($v1 as $k2=>$v2){
					$result[$k1][$k2] = array();
					$result[$k1][$k2]['title']	= $v2['name'];
					$result[$k1][$k2]['result'] = extension_loaded($k2);					
					if ($result[$k1][$k2]['result']){
						$result[$k1][$k2]['error']	= "";
						$result[$k1][$k2]['msg']	= "";
					}
					else{
						$result[$k1][$k2]['error']	= $v2['error'];
						$result[$k1][$k2]['msg']	= $v2['msg'];
					}
				}
			}
			elseif ($k1=="function"){
				foreach($v1 as $k2=>$v2){
					$result[$k1][$k2] = array();
					$result[$k1][$k2]['title']	= $v2['name'];
					$result[$k1][$k2]['result'] = function_exists($k2);
					if ($result[$k1][$k2]['result']){
						$result[$k1][$k2]['error']	= "";
						$result[$k1][$k2]['msg']	= "";
					}
					else{
						$result[$k1][$k2]['error']	= $v2['error'];
						$result[$k1][$k2]['msg']	= $v2['msg'];
					}
				}
			}
			else{
				foreach($v1 as $k2=>$v2){
					$result[$k1][$k2] = array();
					$valres	= array('a'=>false);
					$valerr	= array('a'=>'');
					$path	= "";
					$haserr	= false;
					if (isset($v2['dir'])){
						$path = $v2['dir'];
						$valres['a'] = is_dir($path);
					}
					else{
						$path = $v2['file'];
						$valres['a'] = is_file($path);
					}
					if (!$valres['a']){
						$haserr = true;
						if (!is_array($v2['msg'])){
							$valerr['a'] = array($v2['msg']);
						}
						else{
							$valerr['a'] = array();
							foreach($v2['msg'] as $k3=>$v3){
								if (substr(trim($k3),0,1)=="-"){continue;}
								$valerr['a'][] = $v3;
							}
						}
					}
					else{
						$valerr= array();
						$valid = explode("|",$v2['valid']);
						foreach($valid as $k3=>$v3){
							if ($v3=='r'){
								$valres[$v3] = is_readable($path);
								if ($valres[$v3]){
									$valerr[$v3]= "";
								}
								else{
									$valerr[$v3]= is_array($v2['msg']) ? (isset($v2['msg'][$v3]) ? $v2['msg'][$v3] : "") : $v2['msg'];
									$haserr = true;
								}
							}
							if ($v3=='w'){
								$valres[$v3] = is_writeable($path);
								if ($valres[$v3]){
									$valerr[$v3]= "";
								}
								else{
									$valerr[$v3]= is_array($v2['msg']) ? (isset($v2['msg'][$v3]) ? $v2['msg'][$v3] : "") : $v2['msg'];
									$haserr = true;
								}
							}
							if ($v3=='-e' || $v3=='e'){
								$v3b = 'e';
								$valres[$v3b] = is_executable($path);
								if ($v3=="-e"){
									$valerr[$v3b]=  !$valres[$v3b] ? "" : ( is_array($v2['msg'])? (isset($v2['msg'][$v3])? $v2['msg'][$v3]:"") : $v2['msg']);
								}
								else{
									$valerr[$v3b]= $valres[$v3b] ? "" : ( is_array($v2['msg'])? (isset($v2['msg'][$v3])? $v2['msg'][$v3]:"") : $v2['msg']);
									$haserr = true;
								}
							}
						}
					}
					$result[$k1][$k2]['title']	= $path;
					$result[$k1][$k2]['result']	= $valres;
					$result[$k1][$k2]['error']	= !$haserr ? "" : $v2['error'];
					$result[$k1][$k2]['msg']	= $valerr;
				}
			}
		}
		return $result;
	}
}
?>