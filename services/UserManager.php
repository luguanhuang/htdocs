<?php
//****************** [Class] User对象 ******************

//该类必须运行于 PHP5.0 及以上的平台
if ( intval(PHP_VERSION) < 5 ){ exit("<p>Run \"UserManager\" invite promotion your PHP arrives at 5.0 the above editions</p>");}

if (!class_exists('AtherFrameWork')){ require(dirname(__FILE__)."/AtherFrameWork.php"); }
require_once 'tlog.php';
require_once dirname(dirname(__FILE__)).'/dao/cfg/database_config.php';
require_once dirname(dirname(__FILE__)).'/dao/database_module.php';
require_once dirname(dirname(__FILE__)).'/dao/IMerchantsResult.DB.php';

class UserManager extends AtherFrameWork{
	
	//__________________  构造/析构函数  ________________
	
	
	function __construct(){
		$shandanDBFlg = 1;
		parent::__construct();
	}
	
	public $shandanDBFlg;
	function __destruct(){
		parent::__destruct();
		unset($this->Obj_Fso);		$this->Obj_Fso 		= NULL;
		unset($this->Ary_UserDB);	$this->Ary_UserDB	= NULL;
		unset($this->Ary_Session);	$this->Ary_Session	= NULL;
		unset($this->Ary_UserAll);	$this->Ary_UserAll	= NULL;
		unset($this->Ary_GroupAll);	$this->Ary_GroupAll	= NULL;
		unset($this->Ary_CommAll);	$this->Ary_CommAll	= NULL;
	}

	//__________________  私有变量  ________________

	const _REG_USER_NAME_	= "/^[a-zA-Z][\w]*$/";
	
	protected $Obj_Fso		= NULL;
	protected $Ary_UserAll	= NULL;
	protected $Ary_GroupAll	= NULL;
	protected $Ary_CommAll	= NULL;
	protected $Ary_Session	= NULL;
	protected $Ary_UserDB	= NULL;
	protected $Ary_LockDB	= array("user"=>"","datetime"=>"0","error"=>0,"locktime"=>"0");
	
	
	//__________________  只读属性(用方法代替)  ________________
	
	public static function _version()	{return '2.0';}				//版本
	public static function _build()		{return '11.11.24';}		//版本
	public static function _create()	{return '11.11.14';}		//创建
	public static function _classname()	{return __CLASS__;}			//名称
	public static function _developer()	{return "SSOC";}			//开发者
	public static function _copyright()	{return "Aether.CORP";}		//公司
	
	public function _userdb($key=NULL){
		if (!is_array($this->Ary_Session) && !$this->validate_session()){return false; }
		//获取整个数组
		if (($key=strval($key))==""){return $this->Ary_Session;}
		//获取某一项
		return array_key_exists($key,$this->Ary_Session) === false ? NULL : $this->Ary_Session[$key];
	}
	
	//__________________  只写属性  ________________
	
	
	//__________________  可读写属性  ________________
	
	
	//__________________  私有方法  ________________

	//必须验证可读
	protected function require_group(){
		if (!$this->require_file("file:///"._GLO_SERVER_DIRMDB_._GLO_USER_PATH_GROP_,"r")){ return false; }
		if (!isset($GLOBALS['Glo_Ary_Group']) || !is_array($GLOBALS['Glo_Ary_Group'])){
			return $this->error_seting(40,"用户组配置文件无效");
		}
		if (!isset($GLOBALS['Glo_Ary_Common']) || !is_array($GLOBALS['Glo_Ary_Common'])){
			$GLOBALS['Glo_Ary_Common'] = array();
		}
		$this->Ary_GroupAll = &$GLOBALS['Glo_Ary_Group'];
		$this->Ary_CommAll	= &$GLOBALS['Glo_Ary_Common'];
		return true;
	}
	
	//必须验证可读写
	protected  function require_user(){
		if (!$this->require_file("file:///"._GLO_SERVER_DIRMDB_._GLO_USER_PATH_MDB_,"rw")){ return false; }
		if (!isset($GLOBALS['Glo_Ary_User']) || !is_array($GLOBALS['Glo_Ary_User'])){
			return $this->error_seting(41,"Invalid user data file");
		}
		$this->Ary_UserAll = &$GLOBALS['Glo_Ary_User'];
		return true;
	}
	
	//必须验证可读写
	protected function validate_loginlog(){
		$file = _GLO_SERVER_DIRMDB_._GLO_USER_PATH_LOG_;
		$real = $this->return_realpath("file:///".$file,true);
		if (!$real){ return $this->error_seting(42,"文件 ". basename($file) ." 不存在");  }
		if (!is_readable($real)){return $this->error_seting(43,"文件 ".basename($file)." 没有读取权限");}
		if (!is_writeable($real)){return $this->error_seting(44,"文件 ".basename($file)." 没有写入权限");}
		return $real;
	}
	
	//必须验证可读写
	protected function validate_locklog(){
		static $logfile;
		if (isset($logfile)){ return $logfile; }
		$file = _GLO_SERVER_DIRMDB_._GLO_USER_PATH_LOCK_;
		$real = $this->return_realpath("file:///".$file,true);
		if (!$real){ $logfile = $this->error_seting(50,"文件 ". basename($file) ." 不存在");  }
		elseif (!is_readable($real)){$logfile = $this->error_seting(51,"文件 ".basename($file)." 没有读取权限");}
		elseif (!is_writeable($real)){$logfile = $this->error_seting(52,"文件 ".basename($file)." 没有写入权限");}
		else{ $logfile = $real; }
		return $logfile ? $logfile : false;
	}
	
	//读取登录错误记录文件
	protected function lock_read($user,&$lockdb=NULL){
		$user = trim(strval($user));
		if ($user==""){ return $this->error_seting(60,"没有指定要验证的用户名"); }
		$real = $this->validate_locklog();
		if (!$real){ return false; }
		$logtxt = $this->Obj_Fso->read("file:///".$real,$user);
		if ($logtxt===false){ return $this->error_seting(61,"读取登录错误记录文件发生错误"); }
		//默认结果
		$ary_def = array("user"=>$user,"datetime"=>date('Y-m-d H:i:s'),"error"=>0,"locktime"=>"");
			//如果该用户没有错误登记
		if ($logtxt==""){ $lockdb = $ary_def; return true;}
			//如果存在用户错误登记记录
		$logtxt = preg_replace("/(^\s+)|(\s+(\/\/)?$)/","",$logtxt);
		$arylog = explode("|",$logtxt);
		$lockdb = $this->Ary_LockDB;
		$i = 0;
		foreach($lockdb as $key=>$value){
			if (!isset($arylog[$i])){break;}
			$lockdb[$key] = $arylog[$i];
			$i++;
		}
			//错误登记已过期了
		if (!$lockdb["datetime"] || strtotime($lockdb["datetime"])<strtotime(date('Y-m-d'))){ $lockdb=$ary_def; return true; }
			//被锁定了
		if ($lockdb["locktime"] && strtotime($lockdb["locktime"])>=time()){ return $this->error_seting(62,"该用户被锁定，暂时不可登录"); }
		return true;
	}
	
	protected function lock_write($lockdb){
		//验证数据格式
		if (!is_array($lockdb)){return $this->error_seting(63,"写入到登录错误记录文件的数据格式不匹配");}
		foreach($this->Ary_LockDB as $key=>$value){
			if (!isset($lockdb[$key])){ return $this->error_seting(64,"写入到登录错误记录文件的数据缺少 \"". $key ."\""); }
		}
		$user = $lockdb['user'];
		if ($user==""){ return $this->error_seting(65,"指定的用户名为空"); }
		//验证错误记录文件
		$real = $this->validate_locklog();
		if (!$real){ return false; }
		//读取错误记录文件
		$logfile= "file:///".$real;
		$logtxt = $this->Obj_Fso->read($logfile);
		if ($logtxt===false){ return $this->error_seting(66,"读取登录错误记录文件发生错误"); }
		//记录行的内容
		$logline="\r\n\t".$lockdb['user']."|".$lockdb['datetime']."|".intval($lockdb['error'])."|".$lockdb['locktime']."\r\n";
		//验证记录中是否存在该用户
		if (preg_match("/\<\!\-\-start\:\s*". $user ."\-\-\>/",$logtxt)){
			$this->Obj_Fso->write($logfile,array($user=>$logline));
		}
		else{
			$logtxt = trim($logtxt);
			$logtxt .= "\r\n<!--start:". $user ."-->".$logline."<!--end:". $user ."-->";
			$this->Obj_Fso->write($logfile,ltrim($logtxt));
		}
		return true;
	}
	
	function session_is_registered($key)
	{
	    return isset($_SESSION[$key]);
	}
	
	protected  function validate_session(){
		static $session;
		if (!is_array($session) || !$session){
			error_reporting(0);
			session_start();
			error_reporting($this->Int_Report);
			//$session = !session_is_registered(_GLO_SESSION_NAME_) || !$_SESSION[_GLO_SESSION_NAME_] ? false : $_SESSION[_GLO_SESSION_NAME_];
			$session = !isset($_SESSION[_GLO_SESSION_NAME_]) || !$_SESSION[_GLO_SESSION_NAME_] ? false : $_SESSION[_GLO_SESSION_NAME_];
			
		}
		if (!is_array($session) || !isset($session['user']) || !isset($session['group'])){
			return $this->error_seting(9999,"您还没有登录或登录超时");
		}
		$this->error_clear();
		$this->Ary_Session = array_merge($session,array('sessionid'=>session_id()));
		return $this->Ary_Session;
	}
	
	//验证权限
	protected function validate_popedom($method=""){
		//TLOG_MSG("validate_popedom: func begin");
		//获取session
		if (!$this->validate_session()){ return false; }
		//获取分组
		if (!$this->require_group()){return false;  }
		//获取日志
		$logfile = $this->validate_loginlog();
		if ($logfile===false){ return false; }
		$logtext = file_get_contents($logfile)."\r\n";
		$logpos1 = strpos($logtext,"|".$this->Ary_Session['user']."|");//不一定存在
		if ($logpos1===false){$this->error_seting(9998,"Your login may be timed out, please log in again"); return false;}
		$logpos1 = $logpos1+strlen("|".$this->Ary_Session['user']."|");
		$logpos2 = strpos($logtext,"\r\n",$logpos1); //不一定存在
		$seessid = $logpos2===false ? "" : substr($logtext,$logpos1,$logpos2-$logpos1);
		if ($seessid!==session_id()){$this->error_seting(9997,"The current user has logged in on another terminal, and you cannot use this account temporarily."); return false;}
		//验证缺陷
		//TLOG_MSG("validate_popedom: func begin 1");
		$int_group = $this->Ary_Session['group'];
		$ary_power = NULL;
		$ary_common= NULL;
		$ary_method= NULL;
		$bln_result= false;
		if (!isset($this->Ary_GroupAll[$int_group])){
			//TLOG_MSG("validate_popedom: func begin 2");
			$this->error_seting(9996,"您的帐号不属于任何用户组");
		}
		else{
			//TLOG_MSG("validate_popedom: func begin 3");
			//去掉注释则任何用户拥有任何权限
			//return true;
			if (is_array($method))
			{
			    $ary_method = $method;
			    //TLOG_MSG("validate_popedom: func begin 3。1");
			}
			else
			{
			   // TLOG_MSG("validate_popedom: func begin 3.2 method111=".trim(strval($method)));
			    $ary_method = $method=trim(strval($method))=="" ? array() : explode("|", $method);
			}
			//只验证是否登录
			if (!$ary_method){
				//TLOG_MSG("validate_popedom: func begin 4");
				$bln_result = true;
			}
			else{
				//TLOG_MSG("validate_popedom: func begin 5");
				$ary_common	= array_change_key_case($this->Ary_CommAll,CASE_LOWER);
				$ary_power	= array_change_key_case($this->Ary_GroupAll[$int_group]['power'],CASE_LOWER);
				foreach($ary_method as $thd){
					$thd = strtolower(strval($thd));
					if (isset($ary_common["$thd"]) || isset($ary_power["$thd"])){
						$bln_result = $this->error_clear(); break;
					}
				}
			}	
			if (!$bln_result){
				//TLOG_MSG("validate_popedom: func begin 6");
				$this->log($this->Ary_Session['user'],implode(", ", $ary_method));
				return $this->error_seting(9995,"您的帐号没有该操作权限");
			}
		}
		unset($ary_method);	$ary_method= NULL;
		unset($ary_power);	$ary_power = NULL;
		unset($ary_common);	$ary_common= NULL;
		return $bln_result;
	}
	
	//验证用户名
	protected function validate_username(&$user){
		$user = strval($user);
		if ($user==""){ return $this->error_seting(70,"Username missing"); }
		if (!preg_match(self::_REG_USER_NAME_,$user)){ return $this->error_seting(71,"Username Entered Incorrectly"); }
		$leng = strlen($user);
		if ($leng<4){ return $this->error_seting(72,"Username cannot be less than 4 characters in length"); }
		if ($leng>20){return $this->error_seting(73,"User name length cannot be greater than 20 characters"); }
		return true;
	}
	
	//验证密码
	protected function validate_password(&$password,$passname=""){
		$password = strval($password);
		$passname = trim(strval($passname));
		if ($passname==""){ $passname = "user password"; }
		if ($password==""){ return $this->error_seting(74,"缺少".$passname); }
		$leng = FuncExt::str_length($password);
		if ($leng<6){ return $this->error_seting(75,$passname."The length cannot be less than 6 digits"); }
		if ($leng>50){ return $this->error_seting(76,$passname."The length cannot be more than 50 digits"); }
		return true;
	}
	
	//验证用户是否存在
	protected function validate_exists(&$user){
		if (!$this->validate_username($user)){ return false; }
		if (!$this->require_user()){return false;}
		return isset($this->Ary_UserAll[$user]) ? 1 : 0;
	}
	
	//__________________  公有方法  ________________
	
	public function popedom($method){
		if ($this->validate_popedom($method)){ $this->error_clear(); }
		return $this->result_set();
	}
	
	public function getsession($key,$restru=false){
		if (func_num_args()==1 && is_bool($key)){ $restru=$key; $key = ""; }
		$key	= trim(strval($key));
		$restru	= !!$restru;
		$result = $this->validate_session();
		//已登录
		if ($result){
			//如果返回结构的
			if ($restru){
				if ($key==""){
					$this->error_clear();
					return $this->result_struct(array('result'=>$this->Ary_Session));
				}
				elseif (array_key_exists($key,$this->Ary_Session)!==false){
					$this->error_clear();
					return $this->result_struct(array('result'=>$this->Ary_Session[$key]));
				}
			}
			else{
				if ($key==""){ return $this->Ary_Session; }
				if (array_key_exists($key,$this->Ary_Session)!==false){ return $this->Ary_Session[$key]; }
			}
		}
		//未登录或不存在元素的
			//如果不返回结构的(不管是“未登录”或“元素不存在”都返回NULL);
		if (!$restru){ return NULL;}
			//$result==true表示“已登录”，那么到这就表示是元素不存在的
		if ($result){ $this->error_seting(100,"Doesnt exist with name $key Element value"); }
			//如果返回结构
		return $this->result_set();
	}
	
	/**
	 * 用户登录
	 * 参数：$user，表示用户名称，字符型，必选
	 * 　　　$password，表示用户密码，字符型，必选(必须经过md5加密的密文)
	 * 返回：默认结构(对象/数组)
	 * 　　　'state'　　　: 处理结果状态(true：处理成功；false：处理失败)
	 * 　　　'stateId'　　: 处理结果状态编码(处理成功时改编码<=0；否则>0)
	 * 　　　'message'　　: 处理结果状态描述
	 * 　　　'result'　　 : 返回到界面的数据，包含了用户的资料，对象类型，含有以下属性
	 * 　　　　　　　　　　　'id'　　　　: 用户ID,
　　　　　　　　　　　　　　　'user'　　　: 帐号名称,
　　　　　　　　　　　　　　　'name'　　　: 用户实名,
　　　　　　　　　　　　　　　'group'　　 : 所属用户组编号,
　　　　　　　　　　　　　　　'date'　　  : 用户创建时间
　　　　　　　　　　　　　　　'sessionid' : 当前登录SessionID
	*/
	public function login($user,$password,$vcode=""){
		//加载fso对象
		TLOG_MSG("UserManager::login: func begin");
		$merchantsResult_DB = new IMerchantsResult_DB($this->shandanDBFlg);
		$ret =  $merchantsResult_DB->getUserInfo();
		
		$retrole =  $merchantsResult_DB->getRoleInfo("");
		if ($retrole["result"] != 0)
	    {
	        $output["retCode"] = $retrole["result"];
	        $output["sErrMsg"] = $retrole["sErrMsg"];
	        $test = "SearchResultCB(".json_encode($output).")";
	        echo $test;
	        return -1;
	    }
		
		$loginstatus = -1;
		$errorinfo = "";
		$rolename = "";
		$roleid = 0;
		$company_id = 0;
		$group_id = 0;
		$station_id = 0;
		if(is_array($ret["data_list"]))
		{
			foreach($ret["data_list"] as $k=>$item)
			{
				//TLOG_MSG("UserManager::login: k=$k");
				if ($ret["data_list"][$k]["username"] == $user)
				{
					$errorinfo = "";
					
					if ($item["password"] == md5($password))
					{
						
						$loginstatus = 0;
						$errorinfo = "";
						$company_id = $item["company_id"];
						$group_id = $item["group_id"];
						$station_id = $item["station_id"];
						if(is_array($retrole["data_list"]))
						{
							foreach($retrole["data_list"] as $roleitem)
							{
								if ($roleitem["id"] == $item["roll_id"])
								{
									$roleid = $roleitem["id"];
									break;
								}
							}
						}
						break;
					}
					else
					{
						$errorinfo = "User password is incorrect";
						//break;
					}
					
					break;
				}
				else
				{
					$errorinfo = "account not exist";
					
					//break;
				}
			}
		}
		
		if ($loginstatus)
			return $this->result_struct(
				array("stateId" => 116, "message"	=> $errorinfo, "result" => ""));
		$this->Obj_Fso = $this->new_fso(true);
		//启用session
		error_reporting(0);
		session_start();
		error_reporting($this->Int_Report);
		$data = "$roleid,$company_id,$group_id,$station_id";
		$_SESSION[_GLO_SESSION_USERINFO_] = array();
		$_SESSION[_GLO_SESSION_USERINFO_]['userinfo'] = $data;
		$_SESSION[_GLO_SESSION_USERINFO_]['username'] = $user;
		//$_SESSION["user"][] = "user";
		
		TLOG_MSG("UserManager::login: data=$data errorinfo=$errorinfo loginstatus=$loginstatus");
		//验证其他的..........................
		$result = true;
		$cvcode	= 0;
		//验证提交的用户名格式
		if (!$this->validate_username($user)){$result=false;}
		//验证提交密码格式
		if (!$this->validate_password($password)){$result=false;}
		//验证登录
			//加载用户数据文件(php数组形式，因此不可直接用fso对象读取和分析)
		elseif (!$this->require_user()){$result=false;}
			//验证用户是否存在(需要更换验证码)
			//验证数据文件格式是否有效
		/*elseif ( !isset($this->Ary_UserAll[$user]['password']) || !isset($this->Ary_UserAll[$user]['group'])){
			$result	= $this->error_seting(115,"Incorrect user data file");
		}
			//验证该用户是否被锁定
		elseif (!$this->lock_read($user,$lockdb)){
			$result	= false;
		}
			//验证用户密码是否正确(需要更换验证码)
		elseif (md5($password)!=$this->Ary_UserAll[$user]['password']){
				//错误记录递增
			$lockdb["error"] = intval($lockdb["error"])+1;
				//如果达到规定次数
			if ($lockdb["error"] >= _GLO_USER_LOCK_COUNT_){
				$lockdb["locktime"] = date("Y-m-d H:i:s",time()+(_GLO_USER_LOCK_TIME_*60));
			}
				//写入错误到记录文件
			$this->lock_write($lockdb);
			list($result,$cvcode) = array($this->error_seting(116,"User password is incorrect"),1);
			
		}*/
		else{
				//登录成功，重置错误记录数据
			$lockdb = array_merge($this->Ary_LockDB,array('user' => $user));
			$result = $this->lock_write($lockdb);
		}
		unset($lockdb); $lockdb = NULL;
		//失败的
		if (!$result){
			return $this->result_struct(
				array("stateId" => $this->Int_Error, "message"	=> $this->Str_Error, "result" => $cvcode)
			);
		}
		
		//打开session
		$sessionid = session_id();
		//写入登录记录
		$loginfile	= $this->validate_loginlog();
		if ($loginfile===false){ return $this->result_set(); }
		$loginfile  = "file:///".$loginfile;
		$loginlog	= $this->Obj_Fso->read($loginfile)."\r\n";
		$loginpos1	= strpos($loginlog,"|$user|");			//不一定存在
		$loginpos2	= strpos($loginlog,"\r\n",$loginpos1);	//一定存在
		$loginlog1	= "";
		$loginlog2	= "";
		if ($loginpos1!==false){
			$loginlog1	= substr($loginlog,0, $loginpos1);
			$loginlog2	= substr($loginlog, $loginpos2);
			$loginlog	= $loginlog1."|$user|$sessionid".$loginlog2;
		}
		else{
			$loginlog	.= "|$user|$sessionid";
		}
		if (!$this->Obj_Fso->write($loginfile,trim($loginlog))){
			return $this->return_result(115,"保存登录日志失败，请检查日志文件属性。");
		}
		//生成session
		//var_dump($this->Ary_UserAll[$user]);
		$result = array_merge($this->Ary_UserAll["lity3"],array("sessionid"=>$sessionid));
		$_SESSION[_GLO_SESSION_USERINFO_][] = $sessionid;
		unset($result['password']);
		//TLOG_MSG("UserManager::login: result=$result");
		$_SESSION[_GLO_SESSION_NAME_] = $result;
		//写入系统日志
		$this->log($user,"登录成功");
		//返回结果
		return $this->result_struct(
			array(
				"stateId"	=> -1,
				"message"	=> "登录成功",
				//"result"	=> $result
				"result"	=> $_SESSION[_GLO_SESSION_USERINFO_]
			)
		);
	}
	
	public function logout(){
		error_reporting(0);
		session_start();
		error_reporting($this->Int_Report);
		if (!isset($_SESSION[_GLO_SESSION_NAME_])){return $this->result_set(0,"");}
		$user = $_SESSION[_GLO_SESSION_NAME_]['user'];
		unset($_SESSION[_GLO_SESSION_NAME_]);
		//session_unregister(_GLO_SESSION_NAME_);	//注销变量
		unset($_SESSION[_GLO_SESSION_NAME_]);
		
		if (!isset($_SESSION[_GLO_SESSION_USERINFO_])){return $this->result_set(0,"");}
		
		unset($_SESSION[_GLO_SESSION_USERINFO_]);
		//session_unregister(_GLO_SESSION_NAME_);	//注销变量
		//unset($_SESSION[_GLO_SESSION_NAME_]);
		
		session_destroy();						//清理文件
		//写入系统日志
		$this->log($user,"Exit system");
		//返回结果
		return $this->result_struct(
			array(
				"stateId"	=> -2,
				"message"	=> "Logout successful",
				"result"	=> $user
			)
		);
	}
	
	//获取所有用户组及权限
	public function getgroups(){
		if (!$this->validate_session()){ return $this->result_set(); }
		if (!$this->require_group()){return $this->result_set();  }
		return $this->result_struct(
			array(
				"stateId"	=> -3,
				"message"	=> "获取用户组及权限成功",
				"result"	=> array(
					"common"	=> array_values($this->Ary_CommAll),
					"groups"	=> $this->Ary_GroupAll
				)
			)
		);
	}
	
	//获取所有用户
	public function getusers($page){
		if (!$this->validate_popedom(__METHOD__)){ return $this->result_set(); }
		if (!$this->require_user()){return $this->result_set();  }
		$ary_user = array_values($this->Ary_UserAll);
		rsort($ary_user);
		if (func_num_args()){
			$psize	= 20;
			$pshow	= 10;
			$result		= FuncExt::pagination(sizeof($ary_user), $psize, $page, $pshow);		//分页
			$ary_user	= array_chunk($ary_user, $psize);										//分隔
			$result['userlist']	= $ary_user ? $ary_user[$result['absolutepage']-1] : array();	//合并
		}
		else{
			$result	=  $ary_user;
		}
		unset($ary_user); $ary_user = NULL;
		return $this->result_struct(
			array(
				"stateId"	=> -4,
				"message"	=> "获取用户组成员成功",
				"result"	=> $result
			)
		);
	}
	
	/**
	 * 添加用户
	 * 参数：$param，表示用户参数，对象，必选，含有四个属性
	 *  　　　       'user'，表示用户名称，字符型，必选
	 *  　　　       'pass'，表示用户密码，字符型，必选
	 *  　　　       'group'，表示所属用户组，字符型，必选
	 *  　　　       'name'，表示用户真实姓名，字符型，必选
	 * 返回：默认结构(对象/数组)
	 * 　　　'state'　　　: 处理结果状态(true：处理成功；false：处理失败)
	 * 　　　'stateId'　　: 处理结果状态编码(处理成功时改编码<=0；否则>0)
	 * 　　　'message'　　: 处理结果状态描述
	 * 　　　'result'　　 : 返回到界面的数据，该接口返回空
	 * 测试：{'user':'testuser','pass':'123456','group':1,'name':'测试用户'}
	*/
	public function adduser($param){
		if (!$this->validate_popedom(__METHOD__)){ return $this->result_set(); }
		$param = is_array($param) ? array_change_key_case($param,CASE_LOWER) : array("user"=>$param);
		$str_user	= strval(FuncExt::str_value($param,"user"));
		$str_pass	= strval(FuncExt::str_value($param,"pass"));
		$int_group	= strval(FuncExt::str_value($param,"group"));
		$str_name	= strval(FuncExt::str_value($param,"name"));
		//验证用户帐号
		$int_exist	= $this->validate_exists($str_user);
		if ($int_exist!==0){
			if ($int_exist===1){ $this->error_seting(120,"该名称的用户已经存在，请使用其他名称");}
			return $this->result_set();
		}
		//验证用户密码
		if (!$this->validate_password($str_pass)){ return $this->result_set(); }
		//验证用户组
		if (!$this->require_group()){ return $this->result_set(); }
		if ($int_group==1){return $this->result_set(121,"不能添加该用户组的用户");}
		if (!isset($this->Ary_GroupAll[$int_group])){return $this->result_set(122,"选择的用户组不存在");}
		//验证真实姓名
		if (!$this->error_analy(FuncExt::validate_basic("用户真实名称", $str_name, 1,20,"!html-",123))){
			return $this->result_set();
		}
		//生成用户id
		$ary_uid = array();
		foreach($this->Ary_GroupAll as $key=>$value){
			if (is_array($value) && isset($value['id'])){ $ary_uid[strval($value['id'])] = true; }
		}
		$bln_has = true;
		while($bln_has){
			$int_uid = strval(FuncExt::number_rand(9));
			if (!isset($ary_uid[$int_uid])){$bln_has=false;break;}
		}
		unset($ary_uid); $ary_uid = NULL;
		//加载fso对象
		$this->Obj_Fso = $this->new_fso(true);
		//获取代码
		$str_file = "file:///"._GLO_SERVER_DIRMDB_._GLO_USER_PATH_MDB_;
		$str_code = $this->Obj_Fso->read($str_file,"UserAccount");
		if ($str_code===false){ return $this->result_set(130,"读取用户数据文件发生错误"); }
		//合并代码
		$str_code = preg_replace("/\s*\/\/$/","",$str_code);
		$str_code .= "\r\n\t\t//<!--start:$str_user-->\r\n\t'$str_user'\t=> array('id'=>$int_uid,\t'user'=>'$str_user',\t'name'=>'$str_name',\t'password'=>'". md5($str_pass) ."',\t'group'=>'$int_group',\t'date'=>'". date('Y-m-d') ."'),\r\n\t\t//<!--end:$str_user-->\r\n\t//";
		//exit($str_code);
		$bln_save = $this->Obj_Fso->write($str_file,array("UserAccount"=>$str_code));
		if (!$bln_save){ return $this->result_set(131,"保存用户数据时发生错误"); }
		return $this->result_set(-5,"添加新用户成功");
	}
	
	//获取某一用户的资料
	public function getuser($user){
		if (!$this->validate_popedom(__METHOD__)){ return $this->result_set(); }
		if (!$this->validate_exists($user)){return $this->result_set(140,"指定的用户不存在");}
		$this->Ary_UserDB = $this->Ary_UserAll[$user];
		$this->error_clear();
		return $this->result_struct(
			array(
				"stateId"	=> -6,
				"message"	=> "获取用户资料完成",
				"result"	=> $this->Ary_UserDB
			)
		);
	}
	
	//设置用户
	public function setuser($param){
		if (!$this->validate_popedom(__METHOD__)){ return $this->result_set(); }
		$param = is_array($param) ? array_change_key_case($param,CASE_LOWER) : array("user"=>$param);
		$str_user	= strval(FuncExt::str_value($param,"user"));
		$int_group	= strval(FuncExt::str_value($param,"group"));
		$str_name	= strval(FuncExt::str_value($param,"name"));
		//验证用户是否存在
		if (!$this->validate_exists($str_user)){return $this->result_set(150,"指定的用户不存在");}
		$this->Ary_UserDB = $this->Ary_UserAll[$str_user];
		//验证用户组
		if (!$this->require_group()){ return $this->result_set(); }
		if (intval($this->Ary_UserDB['group'])==1 && $int_group!=1){
			return $this->result_set(151,"默认管理员所属用户组不可更改");
		}
		if (intval($this->Ary_UserDB['group'])!=1 && $int_group==1){
			return $this->result_set(152,"不可修改为默认管理员");
		}
		if (!isset($this->Ary_GroupAll[$int_group])){ 
			return $this->result_set(153,"选择的用户组不存在");
		}
		//验证真实姓名
		if (!$this->error_analy(FuncExt::validate_basic("用户真实名称", $str_name, 1,20,"!html-",154))){
			return $this->result_set();
		}
		//加载fso对象
		$this->Obj_Fso = $this->new_fso(true);
		$str_code= "\r\n\t'$str_user'\t=> array('id'=>". $this->Ary_UserDB['id'] .",\t'user'=>'$str_user',\t'name'=>'$str_name',\t'password'=>'". $this->Ary_UserDB['password'] ."',\t'group'=>'$int_group',\t'date'=>'". $this->Ary_UserDB['date'] ."'),\r\n\t\t//";
		//exit($str_code);
		$str_file = "file:///"._GLO_SERVER_DIRMDB_._GLO_USER_PATH_MDB_;
		$bln_save = $this->Obj_Fso->write($str_file,array($str_user=>$str_code));
		if (!$bln_save){ return $this->result_set(159,"保存用户数据时发生错误"); }
		return $this->result_set(-7,"修改用户资料成功");
	}
	
	//删除用户
	public function deluser($user){
		if (!$this->validate_popedom(__METHOD__)){ return $this->result_set(); }
		//验证用户是否存在
		if (!$this->validate_exists($user)){return $this->result_set(160,"指定的用户不存在");}
		$this->Ary_UserDB = $this->Ary_UserAll[$user];
		if (intval($this->Ary_UserDB['group'])==1){return $this->result_set(161,"默认管理员不可删除");}
		//加载fso对象
		$this->Obj_Fso = $this->new_fso(true);
		//exit($str_code);
		$str_file = "file:///"._GLO_SERVER_DIRMDB_._GLO_USER_PATH_MDB_;
		$str_code = $this->Obj_Fso->read($str_file,"UserAccount");
		$str_code = preg_replace("/\s*\/\/\<\!\-\-start\:". $user ."\-\-\>.*?\/\/\<\!\-\-end\:". $user ."\-\-\>/is","",$str_code);
		$bln_save = $this->Obj_Fso->write($str_file,array("UserAccount"=>$str_code));
		if (!$bln_save){return $this->result_set(162,"保存用户数据时发生错误"); }
		return $this->result_set(-8,"删除用户成功");
	}
	
	//重置用户密码
	public function resetpass($user){
		if (!$this->validate_popedom(__METHOD__)){ return $this->result_set(); }
		//验证用户是否存在
		if (!$this->validate_exists($user)){return $this->result_set(170,"指定的用户不存在");}
		$this->Ary_UserDB = $this->Ary_UserAll[$user];
		//生成随机密码
		$str_pass= FuncExt::str_randcode(6,array(2,0,6));
		//加载fso对象
		$this->Obj_Fso = $this->new_fso(true);
		$str_file	= "file:///"._GLO_SERVER_DIRMDB_._GLO_USER_PATH_MDB_;
		$str_code	= "\r\n\t'". $this->Ary_UserDB['user'] ."'\t=> array('id'=>". $this->Ary_UserDB['id'] .",\t'user'=>'". $this->Ary_UserDB['user'] ."',\t'name'=>'". $this->Ary_UserDB['name'] ."',\t'password'=>'". md5($str_pass) ."',\t'group'=>'". $this->Ary_UserDB['group'] ."',\t'date'=>'". $this->Ary_UserDB['date'] ."'),\r\n\t\t//";
		$bln_save = $this->Obj_Fso->write($str_file,array($this->Ary_UserDB['user']=>$str_code));
		if (!$bln_save){ return $this->result_set(171,"保存用户数据时发生错误"); }
		return $this->result_struct(
			array(
				"stateId"	=> -9,
				"message"	=> "重置用户密码成功",
				"result"	=> $str_pass
			)
		);
	}
	
	//查看本身资料
	public function getSelfData(){
		//获取session
		if (!$this->validate_session()){ return $this->result_set(); }
		//验证用户是否存在
		$user = $this->Ary_Session['user'];
		if (!$this->validate_exists($user)){return $this->result_set(180,"指定的用户不存在");}
		$this->Ary_UserDB = $this->Ary_UserAll[$user];
		$this->error_clear();
		return $this->result_struct(
			array(
				"stateId"	=> -10,
				"message"	=> "获取资料完成",
				"result"	=> $this->Ary_UserDB
			)
		);
	}
	
	//修改本身资料
	public function setSelfData($param){
		//获取session
		if (!$this->validate_session()){ return $this->result_set(); }
		//验证用户是否存在
		$user = $this->Ary_Session['user'];
		if (!$this->validate_exists($user)){return $this->result_set(190,"指定的用户不存在");}
		$this->Ary_UserDB = $this->Ary_UserAll[$user];
		$param		= is_array($param) ? array_change_key_case($param,CASE_LOWER) : array("name"=>$param);
		$str_name	= strval(FuncExt::str_value($param,"name"));
		//验证真实姓名
		if (!$this->error_analy(FuncExt::validate_basic("用户真实名称", $str_name, 1,20,"!html-",191))){
			return $this->result_set();
		}
		//加载fso对象
		$this->Obj_Fso = $this->new_fso(true);
		$str_file	= "file:///"._GLO_SERVER_DIRMDB_._GLO_USER_PATH_MDB_;
		$str_code	= "\r\n\t'". $this->Ary_UserDB['user'] ."'\t=> array('id'=>". $this->Ary_UserDB['id'] .",\t'user'=>'". $this->Ary_UserDB['user'] ."',\t'name'=>'". $str_name ."',\t'password'=>'". $this->Ary_UserDB['password'] ."',\t'group'=>'". $this->Ary_UserDB['group'] ."',\t'date'=>'". $this->Ary_UserDB['date'] ."'),\r\n\t\t//";
		$bln_save = $this->Obj_Fso->write($str_file,array($this->Ary_UserDB['user']=>$str_code));
		if (!$bln_save){return $this->result_set(197,"保存用户数据时发生错误"); }
		return $this->result_set(-11,"修改资料成功");
	}
	
	//修改本身密码
	public function setPassword($param){
		//获取session
		if (!$this->validate_session()){ return $this->result_set(); }
		$param = is_array($param) ? array_change_key_case($param,CASE_LOWER) : array("oldpass"=>$param);
		$str_oldpass	= strval(FuncExt::str_value($param,"oldpass"));
		$str_newpass	= strval(FuncExt::str_value($param,"newpass"));
		//验证用户是否存在
		$user = $this->Ary_Session['user'];
		if (!$this->validate_exists($user)){return $this->result_set(200,"指定的用户不存在");}
		$this->Ary_UserDB = $this->Ary_UserAll[$user];
		//验证用户密码
		if (!$this->validate_password($str_oldpass,"原密码")){ return $this->result_set(); }
		if (!$this->validate_password($str_newpass,"新密码")){ return $this->result_set(); }
		if ( $str_oldpass==$str_newpass ){ return $this->result_set(201,"新密码不能与原密码相同"); }
		if ( md5($str_oldpass)!=$this->Ary_UserDB['password']){return $this->result_set(202,"原密码不正确");}
		//加载fso对象
		$this->Obj_Fso = $this->new_fso(true);
		$str_file	= "file:///"._GLO_SERVER_DIRMDB_._GLO_USER_PATH_MDB_;
		$str_code	= "\r\n\t'". $this->Ary_UserDB['user'] ."'\t=> array('id'=>". $this->Ary_UserDB['id'] .",\t'user'=>'". $this->Ary_UserDB['user'] ."',\t'name'=>'". $this->Ary_UserDB['name'] ."',\t'password'=>'". md5($str_newpass) ."',\t'group'=>'". $this->Ary_UserDB['group'] ."',\t'date'=>'". $this->Ary_UserDB['date'] ."'),\r\n\t\t//";
		$bln_save = $this->Obj_Fso->write($str_file,array($this->Ary_UserDB['user']=>$str_code));
		if (!$bln_save){ return $this->result_set(203,"保存用户数据时发生错误"); }
		return $this->result_set(-12,"修改密码成功");
	}
}
?>