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
define('MAGIC_QUOTES_GPC',ini_set("magic_quotes_runtime",0)?True:False);
class InterFaces{
	
	//__________________  构造/析构函数  ________________
	
	/*
	function __construct(){}
	function __destruct(){}
	*/
	
	//__________________  私有变量  ________________
	
	const _REG_SLASH_	= "/[\/\\\\]+/";
	const _REG_DEFDIR_	= "services/";

	protected static $Int_Error		= 0;
	protected static $Str_Error		= "";
	
	protected static $Str_Class		= "";
	protected static $Str_Method	= "";
	protected static $Ary_Params	= array();
	protected static $Ary_SvrDir	= array();
	protected static $Ary_Allow		= array();	//"*"表示都可以
	protected static $Ary_UnAllow	= array();	//"*"表示都不可以
	
	//__________________  只读属性(用方法代替)  ________________
	
	public static function _version()	{return '1.0';}					//版本
	public static function _build()		{return '11.11.24';}			//版本
	public static function _create()	{return '11.11.24';}			//创建
	public static function _classname()	{return __CLASS__;}				//名称
	public static function _developer()	{return "OldFour";}				//开发者
	public static function _copyright()	{return "Aether.CORP";}			//公司
	
	public static function _error()		{return self::$Int_Error;}
	public static function _errtext()	{return self::$Str_Error;}
	
	//__________________  只写属性  ________________
	
	
	//__________________  可读写属性  ________________
	
	public static function setdir($value){	//服务目录
	  //  TLOG_MSG("InterFaces::setdir: func begin");
		static $str_phpapp;
		if (!isset($str_phpapp))
		{
		    $str_phpapp = preg_replace(self::_REG_SLASH_,"/",(dirname(__FILE__)."/"));
		    //TLOG_MSG("InterFaces::setdir: func begin 1 str_phpapp=".$str_phpapp);
		}
		if (is_null($value)){
		  //  TLOG_MSG("InterFaces::setdir: func begin 2");
			self::$Ary_SvrDir = array($str_phpapp.self::_REG_DEFDIR_);
			return true;
		}
		if (!is_array($value)){
		 //   TLOG_MSG("InterFaces::setdir: func begin 3");
			$value = strval($value);
			$value = $value=="" ? array() : array($value);
		}
		if (!$value)
		{
		    $value = $str_phpapp;
		   // TLOG_MSG("InterFaces::setdir: func begin 4");
		}
		if (!is_array(self::$Ary_SvrDir))
		{ 
		   // TLOG_MSG("InterFaces::setdir: func begin 5");
		    self::$Ary_SvrDir = array(); 
		}
		foreach($value as $v)
		{
			$v = @self::return_realpath($v);
			//TLOG_MSG("InterFaces::setdir: func begin 6 v=".$v);
			if (in_array($v,self::$Ary_SvrDir)){continue;}
			self::$Ary_SvrDir[] = $v;
		}
		return true;
	}
	
	public static function setallow($value,$type=true){	//允许/不允许的对象
		if (!is_array($value)){
			$value = strval($value);
			$value = $value=="" ? array() : array($value);
		}
		if (!$value){ return false; }
		$result = in_array("*",$value) ? "*" : $value;
		if ($type){ self::$Ary_Allow = $result; }
		else{ self::$Ary_UnAllow = $result; }
		return true;
	}
	
	//是否允许对象的方法被访问的判断顺序
	// self::$Ary_SvrUnAllow > self::$Ary_SvrAllow > class->_intreface()
	
	public static $MethodName	= "";	//方法的参数名
	public static $ParamsName	= "";	//参数的参数名
	
	//__________________  私有方法  ________________
	
	//将路径转换为物理路径(支持物理地址或相对地址)
	protected final function return_realpath($path,$isfile=NULL){
		static $str_phpapp;
		static $str_phpself;
		static $str_phproot;
		if (!isset($str_phproot)){$str_phproot = preg_replace(self::_REG_SLASH_,"/",($_SERVER['DOCUMENT_ROOT']."/"));}
		if (!isset($str_phpapp)){$str_phpapp = preg_replace(self::_REG_SLASH_,"/",(dirname(__FILE__)."/"));}
		if (!isset($str_phpself)){$str_phpself = substr($str_phproot,-1).$_SERVER['PHP_SELF']; }
		
		$isfile	= $isfile == NULL ? NULL : !!$isfile;
		$isreal	= preg_match("/^file\:[\/\\\\]{3}/i",$path);
		$path	= trim($path);
		//如果是物理路径
		if ($isreal){$path = substr($path,8);}
		//如果为空(如果指定是文件则等于程序路径，如果未指定或指定为目录则等于程序目录)
		if ($path==""){$path = $isfile ? $str_phpself : $str_phpapp;}
		//如果是相对路径
		elseif (!$isreal){$path=preg_match("/^[\/\\\\]/",$path) ? $str_phproot.substr($path,1):$str_phpapp.$path;}
		//如果是目录
		if (is_dir($path)){return $isfile ? false : preg_replace(self::_REG_SLASH_,"/",$path."/");}			//返回目录
		elseif (is_file($path)){return $isfile===false ? false : preg_replace(self::_REG_SLASH_,"/",$path);}	//返回文件
		return false;
	}
	
	//是否已加载文件：NULL=不存在;true=已加载; false=未加载(支持物理地址或相对地址)
	protected final function require_exists($file, &$real=NULL){
		$real = self::return_realpath(strval($file),true);
		if (!$real){ return NULL; }
		$require= explode("|",strtolower(str_replace("\\","/",implode("|",get_included_files()))));
		$result	= in_array(strtolower($real),$require);
		unset($require); $requiree = NULL;
		return $result ? true : false;
	}
	
	//读取变量($case:0=原型，1=小写,2=大写,3=数字,4=实数,5=数组)
	protected final static function getvalue($name=NULL,$type="",$case=0,$decode=false){
	   // TLOG_MSG("Interfaces::getvalue: func begin");
		$int_args	= func_num_args();
		//TLOG_MSG("Interfaces::getvalue: func begin args=".$int_args);
		//参数非1/2/3可忽略
		if ($int_args>0 && $int_args<4){
		    //TLOG_MSG("Interfaces::getvalue: func begin args=111".$int_args);
			$str_larg	= func_get_arg($int_args-1);
			$str_ltype	= gettype($str_larg);
			$ary_type = array("NULL","boolean","float","integer");
			if ($str_ltype=="boolean"){$decode=$str_larg;}									//确定$decode(不是boolean类型则就是默认值)
			elseif ($str_ltype=="float" || $str_ltype=="integer"){$case=$str_larg;$name=NULL;}	//预定$case(如果为数字类型则可能就是)
			if($int_args==1){		//一个参数的可退出
				if( array_search($str_ltype,$ary_type) && trim($str_larg)=="" ){$type=$str_larg;$name=NULL;}
			}
			else{					//最后一个参数可能还未被分配出
				$str_larg2	= func_get_arg($int_args-2);
				$str_ltype2	= gettype($str_larg2);
				if (array_search($str_ltype2,$ary_type)){$case=intval($str_larg2); $decode=$str_larg;}
				if($int_args==2){	//二个参数的可退出
					if(array_search($str_ltype2,$ary_type)){$name=NULL;$type="";}
					else{$name=$str_larg2; if (!array_search($str_ltype,$ary_type)){$type=strval($str_larg);}}
				}
				elseif (trim($name)==""){$type=$name;$name=NULL;}
			}
			unset($ary_type);
		}
		
		//TLOG_MSG("Interfaces::getvalue: func begin args=222".$int_args);
		$name = trim($name); if ($name==""){$name = _GLO_CONS_STR_STEP_;}
		$case = intval($case); if ($case<0){$case=0;}elseif ($case>3){$case=3;}
		$type = strtolower(trim($type)); if ($type==""){$type="request";}
		$value = "";
		TLOG_MSG("Interfaces::getvalue: func begin type=".$type);
		switch ($type){
			case "session"	: @session_start();	$value=	session_is_registered($name)? $_SESSION[$name] : "";	break;
			case "cookie"	: if (isset($_COOKIE[$name]))	{$value = $_COOKIE[$name];} 	break;
			case "post"		: if (isset($_POST[$name]))		{$value = $_POST[$name];}		break;
			case "get"		: if (isset($_GET[$name]))		{$value = $_GET[$name];}		break;
			case "file"	: ;
			case "files"	: if (isset($_FILES[$name]))	{$value = $_FILES[$name];}		break;
			case "global"	: ;
			case "globals"	: if (isset($GLOBALS[$name]))	{$value = $GLOBALS[$name];}		break;
			case "server"	: if (isset($_SERVER[$name]))	{$value = $_SERVER[$name];}		break;
			default			: if (isset($_REQUEST[$name]))	{$value = $_REQUEST[$name];}	break;
		}
		if (!is_array($value)){
		   // TLOG_MSG("Interfaces::getvalue: func begin type=111".$type);
			if ($decode){$value = rawurldecode($value);}
			switch($case){
				case 1 : $value = strtolower($value);	break;
				case 2 : $value = strtoupper($value);	break;
				case 3 : $value = intval($value);		break;
				case 4 : $value = is_numeric($value) ? $value * 1 : 0;	break;
				case 5 : $value = array($value) ;		break;
			}
		}
		
		//TLOG_MSG("Interfaces::getvalue: func end value=".$value);
		//echo("name:".$name."<br>type:".$type."<br>case:".$case."<br>decode:".$decode."<br>value:".$value."<br>");
		return $value;
	}
	
	protected static function get_interface(){
		//指定指定值的
		$interface = "";
		//TLOG_MSG("Interfaces::get_interface: func begin");
		if (func_num_args()){
			$interface = trim(strval(func_get_arg(0)));
			TLOG_MSG("Interfaces::get_interface: func begin 1interface=".$interface);
		}
		else{
			$intername = trim(strval(self::$MethodName));
			if ($intername==""){
				self::$Int_Error = 10;
				self::$Str_Error = "未指定接口的 Request 参数名称";
				return false;
			}
			$interface = strval(self::getvalue($intername,"request", 0, true));
			TLOG_MSG("Interfaces::get_interface: func begin intername=".$intername." interface=".$interface);
		}
		if ($interface==""){
		    //TLOG_MSG("Interfaces::get_interface: func begin 3");
			self::$Int_Error = 11;
			self::$Str_Error = "未指定接口名称";
			return false;
		}
		$strpos= strpos($interface,"::");
		if ($strpos===false){
		    //TLOG_MSG("Interfaces::get_interface: func begin 4");
			self::$Int_Error = 12;
			self::$Str_Error = "未指定接口的对象或方法名(参数格式：对象::方法) ";
			return false;
		}
		$class = trim(substr($interface,0,$strpos));
		if ($strpos===false){
		    //TLOG_MSG("Interfaces::get_interface: func begin 5");
			self::$Int_Error = 13;
			self::$Str_Error = "未指定接口的对象名称";
			return false;
		}
		$method = trim(substr($interface,$strpos+2));
		if ($strpos===false){
		    //TLOG_MSG("Interfaces::get_interface: func begin 6");
			self::$Int_Error = 14;
			self::$Str_Error = "未指定接口的方法名称";
			return false;
		}
		self::$Str_Class	= $class;
		self::$Str_Method	= $method;
		TLOG_MSG("Interfaces::get_interface: func begin class=".$class." method=".$method);
		return true;
	}
	
	public static function get_parameter(){
		//指定值的
	   // TLOG_MSG("Interfaces::get_parameter: func begin");
		if (func_num_args()){
		    //TLOG_MSG("Interfaces::get_parameter: func begin 1");
			$parameter = func_get_arg(0);
			self::$Ary_Params = is_array($parameter) ? $parameter : array($parameter);
			unset($parameter); $parameter = NULL;
		}
		else{
			$paramname = self::$ParamsName;
			//TLOG_MSG("Interfaces::get_parameter: func begin 2 paramname=".$paramname);
			if (!is_array($paramname) && !is_object($paramname)){
				$paramname	= trim(strval($paramname));
				$str_first	= substr($paramname,0,1);
				$str_last	= substr($paramname,-1);
				if ( ($str_first=="[" && $str_last=="]") || ($str_first=="{" && $str_last=="}") ){
					//if (intval(get_magic_quotes_gpc())){ $paramname = stripslashes($paramname); } by lgh
					$bln_class	= class_exists("json");
					$bln_func	= function_exists('json_decode');
					if (!$bln_class && !$bln_func){
						self::$Int_Error = 20;
						self::$Str_Error = "缺少JSON函数，请加载Json对象或升级PHP版本";
						return false;
					}
					
					//TLOG_MSG("Interfaces::get_parameter: func begin 3 param=".$paramname);
					$paramname = $bln_class ? @json::decode($paramname) : @json_decode($paramname);
					//TLOG_MSG("Interfaces::get_parameter: func begin 3.1 param=".$paramname." bln_class=".$bln_class);
				}
				elseif ($paramname!=""){
					$paramname = explode(",", $paramname);
				}
			}
			
			self::$Ary_Params = !$paramname ? array() : self::get_paramvalue($paramname);
			//TLOG_MSG("Interfaces::get_parameter: func begin 4 Ary_Params=".self::$Ary_Params['interface']);
		}
		return true;
	}
	
	protected static function get_paramvalue($name){
	    //TLOG_MSG("Interfaces::get_paramvalue: func begin 1");
		if (!is_array($name) && !is_object($name)){
		    //TLOG_MSG("Interfaces::get_paramvalue: func begin 2");
			$name = trim(strval($name));
			$name = $name=="" ? array() : array($name);
		}
		$parameter	= array();
		foreach($name as $k=>$p){
		    //TLOG_MSG("Interfaces::get_paramvalue: func begin 3");
			if (is_array($p) || is_object($p)){
			    //TLOG_MSG("Interfaces::get_paramvalue: func begin 4");
				$parameter[$k] = self::get_paramvalue($p);
			}
			else{
				$p = trim(strval($p));
				if ($p==""){ continue; }
				$parameter[$p] = call_user_func_array(array(__CLASS__,"getvalue"),array($p));
				//TLOG_MSG("Interfaces::get_paramvalue: func begin 5 p=".$p." parameter[p]=".$parameter[$p]);
			}
		}
		
		//TLOG_MSG("Interfaces::get_paramvalue: func begin 6");
		return $parameter;
	}
	
	//验证对象是否被允许
	protected static function service_allow(){
		$class = trim(strval(self::$Str_Class));
		if ($class==""){
			self::$Int_Error = 30;
			self::$Str_Error = "缺少访问的目标对象名称";
			return false;	
		}
		//验证
		$result = false;
		if (!is_array(self::$Ary_UnAllow)){
			if (strval(self::$Ary_UnAllow)!="*"){$result=true;}//不等于"*"时表示验证条件无效，不需要验证
		}
		else{
			if (!in_array($class, self::$Ary_UnAllow)){$result=true;}
		}
		if (!$result){
			self::$Int_Error = 31;
			self::$Str_Error = "指定的对象不允许访问";
			return false;
		}
		//验证
		$result = false;
		if (!is_array(self::$Ary_Allow)){
			if (strval(self::$Ary_Allow)=="*"){$result=true;}
			//不等于"*"时表示验证条件无效，全部拒绝
		}
		else{
			if (in_array($class, self::$Ary_Allow)){$result=true;}
		}
		if (!$result){
			self::$Int_Error = 32;
			self::$Str_Error = "指定的对象没有被允许访问";
			return false;
		}
		return true;
	}
	
	//验证文件/对象/方法是否存在
	protected static function validate_interface(){
	    //TLOG_MSG("validate_interface: func begin 1");
		list($class1,	$class2)	= array(self::$Str_Class,	strtolower(self::$Str_Class));
		list($file1,	$file2)		= array($class1.".php",	"cls_".$class2.".php");
		$file = NULL;
		$class= NULL;
		foreach(self::$Ary_SvrDir as $dir){
			if (is_file($dir.$file1)){ $file = $dir.$file1; break;}
			elseif (is_file($dir.$file2)){ $file=$dir.$file2; break;}
		}
		if (!$file){
			self::$Int_Error = 40;
			self::$Str_Error = "指定的对象文件不存在";
			return false;
		}
		
		//TLOG_MSG("validate_interface: func begin file=".$file);
		//加载文件
		$exists = @self::require_exists("file:///".$file,$real);
		if (is_null($exists)){
		   // TLOG_MSG("validate_interface: func begin 2");
			self::$Int_Error = 41;
			self::$Str_Error = "转换对象文件路径发生错误";
			return false;	
		}
	   if (!$exists)
		{
			//TLOG_MSG("validate_interface: func begin 3 real=".$real);
			require($real);
		}
		//判断对象
		if (class_exists($class1))
		{
		   // TLOG_MSG("validate_interface: func begin 4 class1=".$class1." method=".self::$Str_Method);
		    $class = $class1;
		}
		elseif (class_exists($class2))
		{
		    //TLOG_MSG("validate_interface: func begin 5");
		     $class = $class2;
		}
		else
		{
		  //  TLOG_MSG("validate_interface: func begin 6");
			self::$Int_Error = 41;
			self::$Str_Error = "指定访问的对象不存在";
			return false;
		}
		//判断方法
		if (!in_array(self::$Str_Method,get_class_methods($class)))
		{
		   // TLOG_MSG("validate_interface: func begin 7");
			self::$Int_Error = 42;
			self::$Str_Error = "指定访问的方法不存在";
			return false;
		}
		return new $class;
	}
	
	public static function transfer_interface(){
	   // TLOG_MSG("Interfaces::transfer_interface: func begin");
		if (!self::service_allow()){ return false; }
		//TLOG_MSG("Interfaces::transfer_interface: func begin 1");
		$object = self::validate_interface();
		if (!$object){ return false; }
		//TLOG_MSG("Interfaces::transfer_interface: func begin 2");
		self::$Int_Error = -1;
		self::$Str_Error = "";
		if (!is_array(self::$Ary_Params)){
		    //TLOG_MSG("Interfaces::transfer_interface: func begin 3");
			$result =  call_user_func(array($object,self::$Str_Method));
		}
		else{
		    //TLOG_MSG("Interfaces::transfer_interface: func begin 4 interface=".self::$Ary_Params['interface']);
			//$result =  call_user_func_array(array($object,self::$Str_Method),array(self::$Ary_Params));
			if (0 == strcmp("delBridgeInfo", self::$Str_Method) || 0 == strcmp("getNicSet", self::$Str_Method))
			{
			    $result =  call_user_func_array(array($object,self::$Str_Method),array(self::$Ary_Params));
			}
		    else
		    {
		        $result =  call_user_func_array(array($object,self::$Str_Method),self::$Ary_Params);
		    }
		}
		unset($object); $object = NULL;
		return $result;
	}
	
	//__________________  公有方法  ________________
	
	public static function parameter(){
		$result = !func_num_args() ? InterFaces::get_parameter() : InterFaces::get_parameter(func_get_arg(0));
		return !$result ? array() : self::$Ary_Params;
	}
	
	//由属性rMethod和rParams获取接口名称和参数(当指定一个参数时表示直接指定接口名称，接口参数由rParams属性获取)
	public static function call($method=""){
		if (func_num_args()==1){
			if (!self::get_interface($method)){ return false; }
		}
		else{
			if (!self::get_interface()){ return false; }
		}
		if (!self::get_parameter()){ return false; }
		return self::transfer_interface();
	}
	
	//由参数$method和$args获取接口名称和参数(当指定一个参数时表示直接指定接口名称，接口参数为NULL)
	public static function call_arg($method,$args=NULL){
	   // TLOG_MSG("Interfaces::call_arg: func begin func_num_args=".func_num_args());
		if (func_num_args()==2){
			if (!self::get_parameter($args)){ return false; }
		}
		else{
			self::$Ary_Params = NULL;
		}
		if (!self::get_interface($method))	{ return false; }
		return self::transfer_interface();
	}
	
	public static function str2js($str=""){
		$str = strval($str);
		if ($str==""){return "";}
		$js = addslashes($str);
		$js = str_replace("\n","\\n",$js);
		$js = str_replace("\r","\\r",$js);
		return $js;
	}
	
	public static function json($val){
		if (is_array($val) || is_object($val)){
			$result = array();
			foreach($val as $key=>$value){$result[] = "\"". self::str2js($key) ."\":".self::json($value);}
			$result = "{". implode(",",$result) ."}";
			return $result;
		}
		if (is_null($val)){ return 'null'; }
		if (is_float($val) || is_long($val)){ return $val; }
		return "\"". self::str2js($val) ."\"";
	}
	
	public static function recover(){
		self::$Ary_Params	= array();
		self::$Ary_SvrDir	= array();
		self::$Ary_Allow	= array();	//"*"表示都可以
		self::$Ary_UnAllow	= array();	//"*"表示都不可以
	}
	
}
?>