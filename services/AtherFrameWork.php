<?php
//该类必须运行于 PHP5.0 及以上的平台
if ( intval(PHP_VERSION) < 5 ){ exit("<p>Run \"UserManager\" invite promotion your PHP arrives at 5.0 the above editions</p>");}

/*
 * 错误编码设定规则：
 * 框架类: init=1-9; pro=10-59; pub=60-99
 * 一层类: init=100-109; pro=110-159; pub=160-999
 * 二层类: init=1000-1009; pro=1010-1059; pub=1060-1999
*/
require_once 'tlog.php';
class AtherFrameWork{
	
	//__________________  构造/析构函数  ________________
	
	function __construct(){
		$this->Reg_Slash	= "/[\/\\\\]+/";
		$this->Int_Report	= ini_get('error_reporting');
		$this->Str_PHPRoot	= preg_replace($this->Reg_Slash,"/",($_SERVER['DOCUMENT_ROOT']."/"));
		$this->Str_PHPApp	= preg_replace($this->Reg_Slash,"/",(dirname(__FILE__)."/"));
		$this->Str_PHPPro	= preg_replace($this->Reg_Slash,"/",(dirname($this->Str_PHPApp)."/"));
		$this->Str_PHPSelf	= substr($this->Str_PHPRoot,-1).$_SERVER['PHP_SELF'];
		//加载配置文件
		if (!defined('_GLO_SERVER_CONFIG_')){
			require($this->Str_PHPApp.'Config.php');
		}
		//加载共用函数
		if (!class_exists('FuncExt')){
			require($this->Str_PHPApp.'FuncExt.php');
		}
		//加载日志对象
		if (!class_exists('Logger')){
			require(_GLO_FRAME_LOGGER_.'Logger.php');
			Logger::configure(_GLO_FRAME_LOGGER_.'log4php.properties');
		}
		if (!isset(self::$Obj_Logger)){ self::$Obj_Logger	= Logger::getRootLogger(); }
		if (!isset(self::$Obj_Audit)){	self::$Obj_Audit	= Logger::getLogger('Audit');}
		//if (!class_exists('PDOConn')){ require($this->Str_PHPApp.'PDOConn.php');}		//加载数据库对象
		$this->Bln_Enabled = true;
		return true;
	}
	function __destruct(){
		if (empty($this->Obj_Conn))
			return;
		if (get_class($this->Obj_Conn)=='PDOConn'){
			$this->Obj_Conn->free();	$this->Obj_Conn->close();
			unset($this->Obj_Conn); 	$this->Obj_Conn = NULL;
		}
		unset($this->Ary_Popedom);	$this->Ary_Popedom	= NULL;
		unset($this->Ary_Session);	$this->Ary_Session	= NULL;
		unset($this->Obj_User);		$this->Obj_User 	= NULL;
		unset($this->Obj_Socket);	$this->Obj_Socket	= NULL;
	}
	
	//__________________  私有变量  ________________
	
	const _STR_CALL_INTER_	= "php_interface";
	const _STR_CALL_PARAM_	= "php_parameter";
	const _STR_CALL_REMODE_	= "php_returnmode";
	
	const _INT_PORT_MIN_	= 1;
	const _INT_PORT_MAX_	= 65535;
	const _STR_MASK_DEF_	= "255.255.255.0";
	const _STR_PROTOCOL_ALL_= "ALL,UDP,TCP,ICMP";
	const _STR_RULELINK_ALL_= "INPUT,FORWARD";
	const _STR_ACTION_ALL_	= "ACCEPT,DROP";
	const _STR_ROUTER_TYPE_	= "net,host";
	const _STR_ROUTER_INAME_= "sw,eth0";
	
	const _REG_NUMWORD_		= "/^[\w\-\+]+$/";
	const _REG_POSINT_		= "/^[1-9]\d*$/";
	const _REG_NONNEG_		= "/^(0|[1-9]\d*)$/";
	const _REG_EXCTACT_		= "/^[^\<\>\'\\\"\|\`\#\&]+$/";
	const _REG_PASSWORD_	= "/^[^\<\>\'\\\"\`]+$/";
	const _REG_CERT_		= "/^[\w\-][\w\-\.]*(\.[\w\-]+)?$/";
	const _REG_IPADD_		= "/^((25[0-5]|2[0-4][0-9]|[0-1]?[0-9]{1,2})\.){3}(25[0-5]|2[0-4][0-9]|[0-1]?[0-9]{1,2})$/";
	const _REG_MACADD_		= "/^([\da-fA-F]{2}\:){5}([\da-fA-F]{2})$/";
	const _REG_IPMASK_		= "/^(((25[0-5]|2[0-4][0-9]|[0-1]?[0-9]{1,2})\.){3}(25[0-5]|2[0-4][0-9]|[0-1]?[0-9]{1,2})|\d|[1-2]\d|3[0-2])$/";
	const _REG_IPPORT_		= "/^((25[0-5]|2[0-4][0-9]|[0-1]?[0-9]{1,2})\.){3}(25[0-5]|2[0-4][0-9]|[0-1]?[0-9]{1,2})\:[1-9]\d*$/";
	
	/*
	const _LOG_TYPE_AUDIT_	= 60000;
	const _LOG_TYPE_FATAL_	= 50000;
	const _LOG_TYPE_ERROR_	= 40000;
	const _LOG_TYPE_WARN_	= 30000;
	const _LOG_TYPE_INFO_	= 20000;
	const _LOG_TYPE_DEBUG_	= 10000;
	*/
	
	static $Obj_Logger	= NULL;
	static $Obj_Audit	= NULL;

	protected	$Bln_Enabled	= false;
	protected	$Int_Error		= 0;
	protected	$Str_Error		= "";
	
	protected	$Int_Report		= 0;
	protected 	$Str_PHPRoot	= "";
	protected 	$Str_PHPApp		= "";
	protected	$Str_PHPPro		= "";
	protected 	$Str_PHPSelf	= "";
	
	protected	$Obj_Conn		= NULL;
	protected	$Obj_Socket		= NULL;
	protected	$Obj_User		= NULL;
	protected	$Ary_Popedom	= array();
	protected	$Ary_Session	= array();
	
	//__________________  只读属性(用方法代替)  ________________
	
	public static function _version()	{return '1.2';}				//版本
	public static function _build()		{return '11.11.21';}		//版本
	public static function _create()	{return '11.11.14';}		//创建
	public static function _classname()	{return __CLASS__;}			//名称
	public static function _developer()	{return "SSOC";}			//开发者
	public static function _copyright()	{return "Aether.CORP";}		//公司
	
	public final function _enabled()	{return $this->Bln_Enabled;}
	public final function _error()		{return $this->Int_Error;}
	public final function _errtext()	{return $this->Str_Error;}
	public final function _phproot()	{return $this->Str_PHPRoot;}
	public final function _phpapp()		{return $this->Str_PHPApp;}
	public final function _phppro()		{return $this->Str_PHPPro;}
	public final function _phpself()	{return $this->Str_PHPSelf;}
	
	//__________________  私有方法  ________________
	
	#++++++++++++++++++++ 基本扩展函数 ++++++++++++++++++++
	
	//验证
	protected final function validate($name,$str,$min,$max,$rule=false,$interr=1){
		if (!class_exists('FuncExt')){
			$this->Int_Error	= 	intval($interr)*10+9;
			$this->Str_Error	=	"缺少共用函数对象";
			return false;
		}
		$args	= func_get_args();
		$valid	= call_user_func_array(array("FuncExt",'validate_basic'),$args);
		$result	= !$valid[0];
		if (!$result){
			$this->Int_Error	= 	$result[0];
			$this->Str_Error	=	$result[1];
		}
		unset($args); $args = NULL;
		unset($valid);$valid= NULL;
		return $result;
	}
	
	/*
	验证参数项目
	
	重要说明：不存在或存在但为空时， 如果指定了"empty"且为true，则通过;如果指定了"default"时，则等于默认值；"empty"优先度高于"default"
	*/
	protected function validateArgs($param,$keys,$starterr=1,&$reerr=0,&$reerrtxt=""){
		$starterr = max(intval($starterr),1);
		if (is_object($param)) {$param = FuncExt::object2array($param); }
		if (!is_array($param)) {$reerr=$starterr; $reerrtxt="指定的配置参数无效"; return false; }
		if (is_array($keys) && $keys){
			foreach($keys as $k=>$v){
				$name = isset($v['name'])? $v['name']:$k;
				$exist= array_key_exists($k,$param)!==false;
				#不存在 或 存在但为空
				if (!$exist || strval($param[$k])==""){
					if (isset($v['empty']) && $v['empty']){ $param[$k] = ""; continue; }	#允许为空
					if (isset($v['default'])){$param[$k] = $v['default']; continue;}		#存在默认值
					if (!$exist){$reerr=$starterr+1;$reerrtxt="缺少配置参数：[".$name."]";return false;}#缺少参数
				}
				#如果指定了最大值或最小值时
				if (isset($v['min']) || isset($v['max'])){
					#默认验证是否为数字
					if (!preg_match(FuncExt::preg_rule("real"),strval($param[$k]))){
						$reerr=$starterr+2; $reerrtxt="配置参数[".$name."]必须是数字"; return false;
					}
					$intv = (float)$param[$k];
					if (isset($v['min'])){
						$v['min']=intval($v['min']);
						if ($intv<$v['min']){$reerr=$starterr+3; $reerrtxt="配置参数[".$name."]不可小于 ".$v['min'];return false;}
					}
					if (isset($v['max'])){
						$v['max']=intval($v['max']);
						if ($intv>$v['max']){$reerr=$starterr+4; $reerrtxt="配置参数[".$name."]不可大于 ".$v['max'];return false;}
					}
					return $param;
				}
				$v['lenmax'] = isset($v['lenmax']) ? max(intval($v['lenmax']),0) : 0;
				$v['lenmin'] = isset($v['lenmin']) ? max(intval($v['lenmin']),0) : 0;
				if ($v['lenmax'] || $v['lenmin']){
					$length = FuncExt::str_length($param[$k]);
					if ($v['lenmax'] && $length>$v['lenmax']){
						$reerr=$starterr+3; $reerrtxt="配置参数[".$name."]不得多于 ".$v['lenmax']." 个字符";return false;
					}
					if ($v['lenmin'] && $length<$v['lenmin']){
						$reerr=$starterr+4; $reerrtxt="配置参数[".$name."]不得少于 ".$v['lenmin']." 个字符";return false;
					}
				}
				if (isset($v['rule'])){
					if (is_array($v['rule'])){
						list($inary,$vcase) = array(false,strtolower($param[$k]));
						foreach($v['rule'] as $r){if (strtolower($r)==$vcase){ $inary=true; break; }}
						if (!$inary){ $reerr=$starterr+5; $reerrtxt="配置参数[".$name."]不被允许"; return false;}
					}
					elseif ($v['rule']!="" && !preg_match($v['rule'],$param[$k])){
						$reerr=$starterr+5; $reerrtxt="配置参数[".$name."]格式错误";return false;
					}
				}
			}
		}
		return $param;
	}
	
	#++++++++++++++++++++ 进阶扩展函数 ++++++++++++++++++++
	
	//将路径转换为物理路径(支持物理地址或相对地址)
	protected final function return_realpath($path,$isfile=NULL){
		$isfile	= $isfile == NULL ? NULL : !!$isfile;
		$isreal	= preg_match("/^file\:[\/\\\\]{3}/i",$path);
		$path	= trim($path);
		//如果是物理路径
		if ($isreal){$path = substr($path,8);}
		//如果为空(如果指定是文件则等于程序路径，如果未指定或指定为目录则等于程序目录)
		if ($path==""){$path = $isfile ? $this->Str_PHPSelf : $this->Str_PHPApp;}
		//如果是相对路径
		elseif (!$isreal){$path=preg_match("/^[\/\\\\]/",$path) ? $this->Str_PHPRoot.substr($path,1):$this->Str_PHPApp.$path;}
		//如果是目录
		if (is_dir($path)){return $isfile ? false : preg_replace($this->Reg_Slash,"/",$path."/");}			//返回目录
		elseif (is_file($path)){return $isfile===false ? false : preg_replace($this->Reg_Slash,"/",$path);}	//返回文件
		return false;
	}
	
	//是否已加载文件：NULL=不存在;true=已加载; false=未加载(支持物理地址或相对地址)
	protected final function require_exists($file, &$real=NULL){
		$real = $this->return_realpath(strval($file),true);
		if (!$real){ return NULL; }
		$require= explode("|",strtolower(str_replace("\\","/",implode("|",get_included_files()))));
		$result	= in_array(strtolower($real),$require);
		unset($require); $requiree = NULL;
		return $result ? true : false;
	}
	
	//加载文件(支持物理地址或相对地址)
	protected final function require_file($file,$pope=""){
		$exists	= $this->require_exists($file,$real);
		$pope	= strtolower(strval($pope));
		if ($exists===NULL){ return $this->error_seting(30,"文件 ". basename($file) ." 不存在");  }
		if ($pope!=""){
			if (strpos($pope,"r")!==false&&!is_readable($real)){ return $this->error_seting(31,"文件 ".basename($file)." 没有读取权限");}
			if (strpos($pope,"w")!==false&&!is_writeable($real)){return $this->error_seting(32,"文件 ".basename($file)." 没有写入权限");}
		}
		if (!$exists) { require($real); }
		return true;
	}
	
	#++++++++++++++++++++ 基本业务函数 ++++++++++++++++++++
	
	//分析错误结果
	protected final function error_analy($array){
		if (!is_array($array) && sizeof($array)!=2 || !is_numeric($array[0])){return false;}
		$array[0] = intval($array[0]);
		$array[1] = strval($array[1]);
		if ($array[0]!=0){$this->Int_Error = $array[0];$this->Str_Error = $array[1];return false;}
		return true;
	}
	//设置错误
	protected final function error_seting($int_error=0,$str_error=''){
		if ( func_num_args()==1 ){
			$arg1=func_get_arg(0);
			if ( is_array($arg1) ){
				for($i=sizeof($arg1);$i<3;$i++){$arg1[$i]='';}
				$this->Int_Error = intval($arg1[0]);
				$this->Str_Error = strval($arg1[1]);
				return $this->Int_Error<=0;
			}
			if(!is_numeric($arg1)){$str_error=$int_error;$int_error=0;}
		}
		$this->Int_Error = intval($int_error);
		$this->Str_Error = strval($str_error);
		return $this->Int_Error<=0;
	}
	//重置错误结果
	protected final function error_clear(){
		$this->Int_Error = 0;
		$this->Str_Error = "";
        return true;
	}
	//使用字典填补的填充返回一个默认结构
	protected final function result_struct($data=NULL){
		$result = array(
			'state'		=> true,
			'stateId'	=> 0,
			'message'	=> '',
			'sql'		=> '',
			'rowCount'	=> 0,
			'newId'		=> 0,
			'result'	=> array()
		);
		if (!is_array($data) || !$data){ return $result; }
		if (isset($data['stateId'])){$this->Int_Error = intval($data['stateId']);}
		if (isset($data['message'])){$this->Str_Error = strval($data['message']);}
		#如果指定 stateId 时那么 state 根据 stateId 决定；否则 state 无效
		if (isset($data['stateId'])){  $data['state'] = $data['stateId'] <= 0; }
		elseif (isset($data['state'])){ unset($data['state']); }
		$result2 = array_merge($result,$data);
		return array_intersect_key($result2,$result);
	}
	//以传入error和errtext的形式返回默认结构的结果
	protected final function result_set($interr=NULL,$strerr=NULL){
		if (func_num_args()==1 && !is_null($interr) && !is_numeric($interr)){
			$strerr = $interr;
			$interr = NULL;
		}
		$result = $this->result_struct();
		if (isset($interr)){ $this->Int_Error = intval($interr); }
		if (isset($strerr)){ $this->Str_Error = strval($strerr); }
		$result['stateId']	= $this->Int_Error;
		$result['message']	= $this->Str_Error;
		$result['state']	= $result['stateId'] <= 0;
		return $result;
	}
	//以传入error和errtext的形式返回带数据库查询的结果
	protected final function result_dbset($interr=NULL,$strerr=NULL){
		if (func_num_args()==1 && !is_null($interr) && !is_numeric($interr)){
			$strerr = $interr;
			$interr = NULL;
		}
		$result = $this->result_set($interr,$strerr);
		//加入db属性
		if (get_class($this->Obj_Conn)=='PDOConn'){
			$result['sql'] = $this->Obj_Conn.sql();
            if ($result['state']){
                $result['result']    = $this->Obj_Conn.rs();
                $result['newId']     = $this->Obj_Conn.newid();
                $result['rowCount']  = $this->Obj_Conn.sum();
			}
		}
		return $result;
	}
	
	/*验证文件存在且是否有读权限(直接使用物理地址)*/
	protected function validateFileRead($file,$name,$starterr=1,&$reerr=0,&$reerrtxt=""){
		$file = strval($file);
		$name = strval($name);
		$starterr = max(intval($starterr),1);
		if ($file==""){ $reerr = $starterr; $reerrtxt = "没有指定文件路径"; return false; }
		if (!is_file($file)){ $reerr = $starterr+1; $reerrtxt = $name."不存在"; return false; }
		if (!is_readable($file)){$reerr = $starterr+2; $reerrtxt = $name."没有读取权限"; return false; }
		return $file;
	}
	
		/*验证目录存在且是否有读权限(直接使用物理地址)*/
	protected function validateFolderRead($folder,$name,$starterr=1,&$reerr=0,&$reerrtxt=""){
		$folder = strval($folder);
		$name	= strval($name);
		$starterr = max(intval($starterr),1);
		if ($folder==""){ $reerr = $starterr; $reerrtxt = "没有指定目录路径"; return false; }
		if (!is_dir($folder)){ $reerr = $starterr+1; $reerrtxt = $name."不存在"; return false; }
		if (!is_readable($folder)){$reerr = $starterr+2; $reerrtxt = $name."没有读取权限"; return false; }
		return $folder;
	}
	
	public function file_content($file,$name,$starterr=1,&$reerr=0,&$reerrtxt=""){
		$file = $this->validateFileRead($file,$name,$starterr,$reerr,$reerrtxt);
		if (!$file){ return false; }
		FuncExt::error_report(0);
		$contents = file_get_contents($file);
		FuncExt::error_report(true);
		if ($contents===false){ $reerr = $reerr+3; $reerrtxt = "读取". strval($name) ."发生错误"; return false; }
		return $contents;
	}
	
	protected function file_array($file,$name,$starterr=1,&$reerr=0,&$reerrtxt=""){
		$file = $this->validateFileRead($file,$name,$starterr,$reerr,$reerrtxt);
		if (!$file){ return false; }
		FuncExt::error_report(0);
		$lines = file($file);
		FuncExt::error_report(true);
		if ($lines===false){ $reerr = $reerr+3; $reerrtxt = "读取". strval($name) ."发生错误"; return false; }
		return $lines;
	}
	
	//转换为标准虚拟路径(转换后格式为 /****/*** )
	protected function dirLinkPath($path="./"){
		$path = trim(strtolower($path));													//转换为小写字符并去掉前后空格
		if ($path==""){return preg_replace($this->Reg_Slash,"/",$this->Str_PHPSelf);}		//如果为空返回当前目录
		$path = preg_replace($this->Reg_Slash,"/",$path);									//多个/或\转换为单个/
		if ($path==""){return "/";}															//如果为空一定是目录，返回根目录
		if (substr($path,0,1)=="/" && strpos($path,"./")===false){return $path;}			//如果非空起始为/且不含有./，返回本身
		//echo($path."<br>");
		//-----------------------------------------------------------------------------------------------------
		$str_root = strtolower($this->Str_PHPRoot);									//站点根目录物理地址,统一为以\结束
		$str_dirs = $str_root;														//起始路径为根目录物理地址
		//如果参数不是以"/"开头(则非根目录的形式)时，起始路径全部加上当前路径
		if (substr($path,0,1)!="/"){$str_dirs .= str_replace("\\","/",substr($this->Str_PHPSelf,1));}
		//如果起始路径最后一个字符为"/"，则删除(防止explode以后最后一个元素为空)
		if (substr($str_dirs,-1)=="/"){$str_dirs = substr($str_dirs,0,-1);}
		//echo($str_dirs."<br>");
		//-----------------------------------------------------------------------------------------------------	
		$ary_dirs	= explode("/",$str_dirs);
		$ary_path	= explode("/",$path);
		$int_path	= sizeof($ary_path);
		for ($i=0;$i<$int_path;++$i){
			if ($ary_path[$i]=="" || $ary_path[$i]=="."){continue;}						//为空或.就是本目录，继续下一个
			if (substr($ary_path[$i],0,1)!="."){array_push($ary_dirs,$ary_path[$i]);}	//以.开头的(实际就是多个.的)，删除最后一个元素
			elseif (sizeof($ary_dirs)>1){array_pop($ary_dirs);}							//否则就在最后添加一个元素
		}
		$str_dirs = implode("/",$ary_dirs)."/";											//合并数组，最后一个字符是"/"
		unset($ary_dirs);	$ary_dirs = NULL;
		unset($ary_path);	$ary_path = NULL;
		if (strpos($str_dirs,$str_root)!==0){return "";}						//如果超过站点根目录
		$str_dirs = substr($str_dirs,strlen($str_root)-1);						//去掉站点根目录物理地址部分
		return $str_dirs;
	}
		
	//加载fso对象
	protected function new_fso($new=true){
		if (!class_exists('FileSysObj')){ require_once($this->Str_PHPApp."FileSysObj.php"); }
		return $new ? new FileSysObj : true;
	}
	//加载Pn对象
	protected function new_paging($new=true){
		if (!class_exists('Paging')){ require_once($this->Str_PHPRoot."Paging.php"); }
		return $new ? new Paging : true;
	}
	//加载Socket对象
	protected function new_socket($new=true){
		if (!class_exists('Socket')){ require_once($this->Str_PHPRoot."Socket.php"); }
		return $new ? new Socket : true;
	}
	//加载json对象
	protected function new_json($new=true){
		if (!class_exists('Json')){ require_once($this->Str_PHPRoot."Json.php"); }
		return $new ? new json : true;
	}
	//加载InterFace对象
	protected function new_interface(){
		//TLOG_MSG("AtherFrameWork::new_interface: func begin");
		static $setconf;
		if (!class_exists('InterFaces')){
			//TLOG_MSG("AtherFrameWork::new_interface: func begin 1");
			require($this->Str_PHPApp."InterFaces.php");
			//TLOG_MSG("AtherFrameWork::new_interface: Str_PHPApp=".$this->Str_PHPApp."InterFaces.php");
		}
		
		//TLOG_MSG("AtherFrameWork::new_interface: func begin 2");
		if (!isset($setconf)){
			InterFaces::setdir("file:///".$this->Str_PHPApp);
			InterFaces::setallow(explode("|",preg_replace("/(\|[\s]*)+\|/","|",_GLO_INTERFACE_ALLOW_)));
			InterFaces::$MethodName = self::_STR_CALL_INTER_;
			InterFaces::$ParamsName = FuncExt::getvalue(self::_STR_CALL_PARAM_,0,true);
			TLOG_MSG("AtherFrameWork::new_interface: func begin method=".InterFaces::$MethodName." ParamsName=".InterFaces::$ParamsName." param=".self::_STR_CALL_PARAM_);
			$setconf = true;
		}
		return true;
	}
	
	#++++++++++++++++++++ 进阶业务函数 ++++++++++++++++++++
	
	//将用户自定义配置转换为数组
	protected function config2array($config){
		$config		= trim(strval($config));
		$ary_list	= array();
		$ary_type	= $config=="" ? array() : explode("|",$config);
		$str_key	= "";
		$str_val	= "";
		foreach($ary_type as $k=>$v){
			$v = trim($v);
			if ($v!=""){
				$i = strpos($v,"=");
				if ($i===false){
					$str_key = $str_val = $v;
				}
				elseif (!$i){
					$str_key = $str_val = substr($v,1);
				}
				else{
					$str_key = substr($v,0,$i);
					$str_val = substr($v,$i+1);
					if ($str_val==""){ $str_val = $str_key; }
				}
			}
			$ary_list[$str_key] = $str_val;
		}
		unset($ary_type); $ary_type = NULL;
		return $ary_list;
	}
	
	//获取全局配置
	protected function get_globalConf($starterr,&$interr,&$strerr){
		$fields = NULL;
		$lines	= $this->file_array(_GLO_FILE_GLOBAL_SWITCH_,"全局配置文件",$starterr,$interr,$strerr);
		if ($lines===false){return $this->error_seting($interr,$strerr);}
		$result	= array();
		foreach ($lines as $line){
			$line = trim(preg_replace('/[\s\n\r]/', "", $line));
			if (substr($line,0,1)=="#" || $line==""){ continue; }	//过滤空行和注释行	
			$fields = explode("=", $line, 2);
			$result[trim($fields[0])] = isset($fields[1]) ? trim($fields[1]) : "";
			TLOG_MSG("get_globalConf: fields[0]=".$fields[0]." fields[1]=".$fields[1]);
		}
		unset($lines); $lines = NULL;
		unset($fields);$fields= NULL;
		return $result;
	}
	
	//写入日志
	protected function log($user,$message,$type="audit"){
		$user 	= strval($user);
		$message= strval($message);
		$type	= strtolower(trim(strval($type)));
		LoggerMDC::put("clientUser",$user);
		LoggerMDC::put("clientIp",@FuncExt::clientip());
		switch($type){
			case "fatal":	self::$Obj_Logger->fatal($message); break;
			case "error":	self::$Obj_Logger->error($message);	break;
			case "warn":	self::$Obj_Logger->warn($message);	break;
			case "info":	self::$Obj_Logger->info($message);	break;
			case "debug":	self::$Obj_Logger->debug($message);	break;
			default:		self::$Obj_Audit->info($message);	//审计日志
		}
	}

	//__________________  公有方法  ________________
	
	#++++++++++++++++++++ 基本业务函数 ++++++++++++++++++++
	
	//加载错误页面并终止程序
	public function load_error($interr,$strerr,$message=array(),$page="errpage"){
		$interr	= intval($interr);
		$strerr	= strval($strerr);
		//处理参数
		if ( func_num_args()==3 ){
			$arg1 = func_get_arg(2);
			if (!is_array($arg1) ){ $message=array();$page=$arg1;}
		}
		if (trim(strval($page))==""){ $page = "error.pt"; }
		if (!is_array($message)){ $message = NULL; }
		//读取session
		error_reporting(0);
		session_start();
		error_reporting($this->Int_Report);
		//转换路径
		$read	= 0;
		$fhas	= "";
		$html	= "";
		$dir	= $this->dirLinkPath($page);
		$file	= $this->Str_PHPRoot.substr($dir,1).basename($page);
		if (is_file($file) || is_readable($file)){
			$fhas = md5($file);
			if (
				!session_is_registered(_GLO_SESSION_ERROR_) ||
				!isset($_SESSION[_GLO_SESSION_ERROR_])		||
				!is_array($_SESSION[_GLO_SESSION_USER_])
			){
				$_SESSION[_GLO_SESSION_USER_] = array();
			}
			if (isset($_SESSION[_GLO_SESSION_USER_][$fhas])){
				$html = $_SESSION[_GLO_SESSION_USER_][$fhas];
				$read = 1;
			}
			else{
				$html = file_get_contents($file);
				$read = $html!==false ? -1 : 0;
			}
		}
		//获取错误页面失败，系统内核错误
		if (!$read){
			exit('<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>Message '. _GLO_PROJECT_LTD_ .'</title>
<style type="text/css">
<!--
body.ErrPage{margin:38px 0px 0px 0px;background-color:#eeeeee;font-family:\'Times New Roman\', Times, serif;text-align:center;}
body.ErrPage .main{width:620px;margin:auto;text-align:left;padding:30px 35px;border:1px solid #808080;background-color:#FFF;}
body.ErrPage h1{margin:0px 0px 10px 0px;padding:0px;font-size:24px;}
body.ErrPage a, body.ErrPage a:hover{text-decoration:none;color:#000000;}
body.ErrPage .desc{border-top:1px solid #D4D0C8;border-bottom:1px solid #D4D0C8;padding:12px 0px;}
body.ErrPage ul{line-height:20px;font-size:13px;margin:14px 0px 0px 18px;padding:0px;}
body.ErrPage ul li{margin-bottom:4px;}
body.ErrPage .help{margin-top:14px;margin-left:18px;font-size:13px;}
-->
</style>
</head>
<body class="ErrPage">
<div class="main">
	<h1>StateID： '. $interr .'</h1>
	<div class="desc">Description：'. $strerr .'</div>
	<ul>
		<li><a href="#" onclick="window.history.back()">返回上一页</a></li>
		<li><a href="#" onclick="window.location.href=\'enter.php\'">登录系统</a></li>
		<li><a href="#" onclick="window.close()">关闭窗口</a></li>
	</ul>
	<div class="help">如果您有疑问或需要提供帮助，请联系系统管理员或设备供应商。</div>
</div>
</body></html>');
		}
		if ($read==-1){
			//转换falsh/视频/音频/连接部分
			$html	= preg_replace("/\<param([^\>]+)value\=([\'\"])?(\.{1,2}\/(\.+\/)*)?images\//i","<param\\1value=\\2".$dir."\\3images/",$html);
			//转换img地址部分
			$html	= preg_replace("/src\=([\'\"])?(\.{1,2}\/(\.+\/)*)?images\//i","src=\\1".$dir."\\2images/",$html);
			//转换背景地址部分
			$html	= preg_replace("/background((\-image\:\ *url\()|(\:.*?url\()|(\=[\"\']?))(\.{1,2}\/(\.+\/)*)?images\//i","background\\1". $dir ."\\5images/",$html);
			//转换CSS的href/Script的src
			preg_match_all("/\<(link|script|iframe|frame)([^\>]+)(href|src)=((\"[^\"]+\")|(\'[^\']+\')|([^\ \'\"]+))/i",$html,$match,2);
			if ($match){
				foreach($match as $key=>$value){
					$quot = substr($value[4],0,1);
					$quot = $quot=="'" || $quot=="\"" ? $quot : "" ;
					$href = $quot ? substr($value[4],1) : $value[4];
					$link = strtolower($href);
					if (strpos($link,"/")===0 || strpos($link,"http://")===0 || strpos($link,"about:")===0){continue;}
					$href = strpos($link,"root://")===0 ? $quot.$this->Str_PHPRoot.substr($href,7):$quot.$dir.$href;
					$html = str_replace($value[0],"<".$value[1].$value[2].$value[3]."=".$href,$html);
				}
				unset($match); $match = NULL;
			}
			$_SESSION[_GLO_SESSION_USER_][$fhas] = $html;
		}
		$html = str_replace("{var:_ERROR_}",	$interr,	$html);
		$html = str_replace("{var:_ERRTXT_}",	$strerr,	$html);
		if ($message){foreach($message as $key=>$val){$html = str_replace("{var:$key}", $val, $html);}}
		exit($html);
	}
	
	//加载结果信息并终止程序
	public function load_message($interr,$strerr,$message=array(),$loadtype="normal"){
		$loadtype	= strtolower(strval($loadtype));
		$interr		= intval($interr);
		$strerr		= strval($strerr);
		//处理参数
		if ( func_num_args()==3 ){
			$arg1 = func_get_arg(2);
			if (!is_array($arg1) ){ $message=array(); $loadtype=$arg1;}
		}
		elseif (!is_array($message)){
			$message = array();
		}
		if ($loadtype=="json"){
			$message['error']	= $interr;
			$message['errtext']	= $strerr;
			echo(FuncExt::json($message));	
		}
		elseif ($loadtype=="page"){
			$this->load_error($interr,$strerr,$message);
		}
		else{
			array_unshift($message,$strerr);
			array_unshift($message,$interr);
			echo(implode(",",array_values($message)));
		}
		unset($message); $message = NULL;
	}
	
	public function load_interargs(){
		if (!class_exists('json')){require($this->Str_PHPApp."Json.php");}
		$this->new_interface();
		return InterFaces::parameter();
	}
	
	#++++++++++++++++++++ 进阶业务函数 ++++++++++++++++++++
	
	//【多态函数】验证权限
	public function user_popedom($method="",$loaderr=false){
		if (func_num_args()==1 && (is_bool($method) || is_integer($method) || is_float($method))){
			 $loaderr = !!$method; $method="";
		}
		if (get_class($this)=="UserManager"){
			$this->Ary_Popedom = $this->popedom($method);
		}
		else{
			if (empty($this->Obj_User))
				return;
			if (get_class($this->Obj_User)!="UserManager"){
				if (!class_exists('UserManager')){require_once($this->Str_PHPApp."UserManager.php");}
				$this->Obj_User	= new UserManager();
			}
			$this->Ary_Popedom = $this->Obj_User->popedom($method);
			$this->Ary_Session = $this->Obj_User->_userdb();
		}
		//如果错误(则将错误传递到 $this->Int_Error, $this->Str_Error)
		if (!$this->Ary_Popedom['state']){
			$this->error_seting($this->Ary_Popedom['stateId'],$this->Ary_Popedom['message']);
			if ($loaderr){ $this->load_error($this->Int_Error,$this->Str_Error); }
		}
		//返回结果
		return $this->Ary_Popedom['state'];
	}
	
		//验证是否登录并获取用户资料
	public function is_user_login(){
		error_reporting(0);
		session_start();
		error_reporting($this->Int_Report);
			//$session = !session_is_registered(_GLO_SESSION_NAME_) || !$_SESSION[_GLO_SESSION_NAME_] ? false : $_SESSION[_GLO_SESSION_NAME_];
			
		
		//如果错误(则将错误传递到 $this->Int_Error, $this->Str_Error)
		if (!isset($_SESSION[_GLO_SESSION_USERINFO_]['userinfo']) || !isset($_SESSION[_GLO_SESSION_USERINFO_]['username'])){
			//$this->error_seting(9999,"您还没有登录或登录超时");
			//$this->load_error($this->Int_Error,$this->Str_Error); 
			return false;
		}
		
		return true;
	}
	
	//验证是否登录并获取用户资料
	public function user_getlogin($loaderr=false){
		static $session;
		if (!is_array($session) || !$session){
			error_reporting(0);
			session_start();
			error_reporting($this->Int_Report);
			//$session = !session_is_registered(_GLO_SESSION_NAME_) || !$_SESSION[_GLO_SESSION_NAME_] ? false : $_SESSION[_GLO_SESSION_NAME_];
			$session = !isset($_SESSION[_GLO_SESSION_NAME_]) || !$_SESSION[_GLO_SESSION_NAME_] ? false : $_SESSION[_GLO_SESSION_NAME_];
		}
		//如果错误(则将错误传递到 $this->Int_Error, $this->Str_Error)
		if (!is_array($session) || !isset($session['user']) || !isset($session['group'])){
			$this->error_seting(9999,"您还没有登录或登录超时");
			if ($loaderr){ $this->load_error($this->Int_Error,$this->Str_Error); }
			else{ return false; }
		}
		$this->error_clear();
		$this->Ary_Session = array_merge($session,array('sessionid'=>session_id()));
		return $this->Ary_Session;
	}

	//socket发送数据
	protected function socket_send($cmdnum,$cmdarg,$timeout=NULL){
	    TLOG_MSG("AtherFrameWork::socket_send: func begin Str_PHPApp=".$this->Str_PHPApp.'Socket.php');
		if (!class_exists('Socket')){ require($this->Str_PHPApp.'Socket.php'); }
		if (!isset($this->Obj_Socket) || strtolower(get_class($this->Obj_Socket))!="socket"){
			$this->Obj_Socket = new Socket();
			$this->Obj_Socket->type		= "stream";
			$this->Obj_Socket->protocol	= "tnet";
			$this->Obj_Socket->ptlcomm	= "tcp";
			$this->Obj_Socket->host		= _GLO_SOCKET_HOST_;
			$this->Obj_Socket->port		= _GLO_SOCKET_PORT_;
			TLOG_MSG("AtherFrameWork::socket_send: func begin 1 _GLO_SOCKET_HOST_="._GLO_SOCKET_HOST_." _GLO_SOCKET_PORT_="._GLO_SOCKET_PORT_);
		}
		$this->Obj_Socket->stimeout	= $timeout && is_numeric($timeout) ? max($timeout,1) : 2;
		$this->Obj_Socket->callback	= array($this,'socket_callback');
		$this->Obj_Socket->callargs = array($cmdnum,$cmdarg);
		$socket = $this->Obj_Socket->newclient();
		$state	= intval(current(explode("|",$socket)))>0;
		$error	= $this->Obj_Socket->_error();
		$errtxt	= $this->Obj_Socket->_errtext();
		//对象没有发生错误
		if (!$this->Obj_Socket->_error()){
			//返回为空
			if ($socket==""){
				$error	= 0;
				$errtxt	= "可能命令执行超时";
			}
			//返回失败
			elseif (!$state){
				$error	= 0;
				$errtxt	= "命令执行失败";
			}
		}
		$result = array(
			"state"		=> $state,
			"request"	=> strval($socket),
			"senddata"	=> $this->Obj_Socket->_lastsend(),
			"error"		=> $error,
			"errtxt"	=> $errtxt,
		);
		return $result;
	}

	//socket回调函数
	public function socket_callback($cmdnum,$cmdarg){
	    
		$cmdnum = substr("000".intval($cmdnum),-3);
		$cmdarg = strval($cmdarg);
		$cmdlen = strlen($cmdarg);
		$cmdtxt = substr("0000".($cmdlen+9),-4)."|". $cmdnum ."|".$cmdarg;
		TLOG_MSG("socket_callback: func begin cmdnum=".$cmdnum." cmdarg=".$cmdarg." cmdtxt=".$cmdtxt);
		$this->Obj_Socket->send($cmdtxt);
		return $this->Obj_Socket->_error() ? false : $this->Obj_Socket->get();
	}
	
	//加载页面(第三个参数为“响应模式”参数，表示当调用接口失败时如何响应；当只有二个参数时，第二个参数表示“响应模式”参数)
		//该方法直接调用InterFace的call_arg方法
	public function load_page($method,$param=NULL,$quiesce=false){
		//TLOG_MSG("AtherFrameWork::load_page: func begin");
		$this->new_interface();
		//TLOG_MSG("AtherFrameWork::load_page: func begin 1");
		$argnum	= func_num_args();
		//TLOG_MSG("AtherFrameWork::load_page: func begin 2 argnum=".$argnum);
		$quiesce= $argnum==2 ? $param : $quiesce;
		$result = $argnum==3 ? InterFaces::call_arg($method, $param) : InterFaces::call_arg($method);
		//TLOG_MSG("AtherFrameWork::load_page: func begin 3");
		if (InterFaces::_error()>0){
		    //TLOG_MSG("AtherFrameWork::load_page: func begin 4");
			$this->error_seting(InterFaces::_error(), "访问接口失败：".InterFaces::_errtext());
			if ($quiesce) { return $this->result_set(); }
			$this->load_error($this->Int_Error,$this->Str_Error);
		}
		//TLOG_MSG("AtherFrameWork::load_page: func begin 5");
		//if (!$result['state'] && !$quiesce){$this->load_error($result['stateId'],$result['message']); }
		return $result;
	}
	
	//调用接口(第二个参数为“返回模式”参数，表示返回的数据模式；当只有一个参数时，参数则代表“返回模式”参数)
		//该方法直接调用InterFace的call方法
	public function call($method="",$remode=NULL){
		if (!class_exists('json')){require($this->Str_PHPApp."Json.php");}
		$this->new_interface();
		$argnum	= func_num_args();
		TLOG_MSG("call: method=".$method." remode=".$remode." argnum=".$argnum);
		$remode = $argnum==1 ? func_get_arg(0) : ($argnum==2 ? func_get_arg(1) : NULL);
		$result = $argnum==2 ? InterFaces::call($method) :  InterFaces::call();
		if (InterFaces::_error()>0){
			$result = array(
				"state"		=> false,
				"stateId"	=> InterFaces::_error(),
				"message"	=> "访问接口失败：".InterFaces::_errtext(),
				'sql'		=> '',
				'rowCount'	=> 0,
				'newId'		=> 0,
				'result'	=> array()
			);
		}
		$remode = isset($remode) ? strtolower(trim(strval($remode))) : FuncExt::getvalue(self::_STR_CALL_REMODE_,1);
		switch($remode){
			case "json":	return InterFaces::json($result);
			//case "html":	return $result;
			case "html":	return InterFaces::json($result);
			case "normal":
			default:		return $result["stateId"].",".$result["message"];
		}
	}

}

/* ++ DEMO ++
$SSOC = new SSOCFrameWork();
var_dump($SSOC->get_sysparameter("simp_cm,cm_nodeid"));

$SSOC = new SSOCFrameWork();
var_dump($SSOC->NewFrameWork("ossim","../ossim/ossim.php"));
*/
?>