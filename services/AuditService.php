<?php
//****************** [Class] 审计服务对象 ******************

//该类必须运行于 PHP5.0 及以上的平台
if ( intval(PHP_VERSION) < 5 ){ exit("<p>Run \"AuditService\" invite promotion your PHP arrives at 5.0 the above editions</p>");}

if (!class_exists('AtherFrameWork')){ require(dirname(__FILE__)."/AtherFrameWork.php"); }

class AuditService extends AtherFrameWork{
	
	//__________________  构造/析构函数  ________________
	
	/**/
	function __construct(){
		parent::__construct();
		if (!class_exists('Analyser')){require($this->Str_PHPApp.'Analyser.php');}
		$this->Obj_Anly = new Analyser();
		$this->Obj_Anly->setSpliter(self::_STR_LOG_SPLITER_);
	}
	
	
	function __destruct(){
		parent::__destruct();
		unset($this->Obj_Anly); $this->Obj_Anly = NULL;
	}
	
	//__________________  私有变量  ________________
	
	static $Ary_URL_Param	= array(
		'query'	=> parent::_STR_CALL_PARAM_,
		'page'	=> 'page',
		'limit'	=> 'limit',
		'log'	=> 'log'
	);
	
	static $Ary_Type_Conf	= array(
		"0"			=> array("name"=>"WEB管理日志",		"file"=>"_GLO_FILE_WEB_OPERATOR_",	"inter"=>"getWebAdminLogListByShell"),
		"operator"	=> array("name"=>"客户端操作日志",	"file"=>"_GLO_PATH_CLIENT_OPERATOR_",	"inter"=>"getClientOperatorLogsByShell"),
		"error" 	=> array("name"=>"客户端错误日志",	"file"=>"_GLO_PATH_CLIENT_OPERATOR_",	"inter"=>"getClientErrorLogsByShell"),
		"login"		=> array("name"=>"用户登录日志",		"file"=>"_GLO_FILE_CLIENT_LOGIN_",	"inter"=>"getClientLoginInfoLogsByShell"),
		"online"	=> array("name"=>"用户在线时长日志",	"file"=>"_GLO_FILE_CLIENT_ONLINE_",	"inter"=>"getClientOnlineLogsByShell"),
		"offline"	=> array("name"=>"用户时长日志",		"file"=>"_GLO_FILE_CLIENT_OFFLINE_",	"inter"=>"getClientOfflineLogsByShell")
	);
	
	const _STR_LOG_SPLITER_	= "|";
	const _STR_LOG_PAGESIZE_= 50;
	const _STR_LOG_TEMPLET_	= "logtemplet.pt";
	const _REG_LOG_CMDCODE_	= "/[\~\`\^\\$\;\&\'\\\"\<\>\[\]]/";
	
	protected $Obj_Anly = NULL;
	
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
	
	//	Operator	=> 客户端操作日志
	//	Error		=> 客户端错误日志
	//	Login		=> 用户登录日志
	//	Online		=> 用户在线时长日志
	//	Offline		=> 用户时长日志
	//	WebAdmin	=> WEB管理日志
	
	protected function get_logname(&$type){
		$type = strtolower(trim(strval($type)));
		if (isset(self::$Ary_Type_Conf[$type])){ return self::$Ary_Type_Conf[$type]['name']; }
		else{ $type="webadmin"; return self::$Ary_Type_Conf["0"]['name'];}
	}
	
	protected function get_logfile($file){
		$file = strtolower(trim(strval($file)));
		return isset(self::$Ary_Type_Conf[$file]) ? constant(self::$Ary_Type_Conf[$file]['file']) : constant(self::$Ary_Type_Conf["0"]['file']);
	}
	
	protected function get_loginter($type){
		$type = strtolower(trim(strval($type)));
		return isset(self::$Ary_Type_Conf[$type]) ? self::$Ary_Type_Conf[$type]['inter'] :  self::$Ary_Type_Conf["0"]['inter'];
	}
	
	protected function validate_keyword($name,$val,$serr=1, $isfull=false){
		if (func_num_args()==3 && gettype($serr)=="boolean"){ $isfull=!!$serr; $sterr=1; }
		$name	= trim(strval($name));
		$val	= trim(strval($val));
		$serr	= max(intval($serr),1);
		$isfull	= !!$isfull;
		if ($name==""){ $name = "unknown"; }
		if ($val==""){
			if (!$isfull){ return true; }
			$this->Int_Error = $serr+1;
			$this->Str_Error = "缺少参数[". $name ."]";
			return false;
		}
		if (preg_match(self::_REG_LOG_CMDCODE_,$val)){
			$this->Int_Error = $serr+2;
			$this->Str_Error = "参数[". $name ."]不可含有 ~ ` ^ $ ; &amp; ' &quot; < > [ ] 等特殊字符";
			return false;
		}
		return true;
	}
	
	protected function validate_datetime($name,$val,$serr=1, $isfull=false){
		if (func_num_args()==3 && gettype($serr)=="boolean"){ $isfull=!!$serr; $serr=1; }
		$name	= trim(strval($name));
		$val	= trim(strval($val));
		$serr	= max(intval($serr),1);
		$isfull	= !!$isfull;
		if ($name==""){ $name = "unknown"; }
		if ($val==""){
			if (!$isfull){ return true; }
			$this->Int_Error = $serr+1;
			$this->Str_Error = "缺少参数[". $name ."]";
			return false;
		}
		if (!FuncExt::is_datetime($val,"1|2|3|y-m-d h:i|y-m|h:i",$this->Str_Error)){
			$this->Int_Error = $serr+2;
			$this->Str_Error = "参数[". $name ."]不正确，".$this->Str_Error;
			return false;
		}
		return true;
	}
	
	protected function result_merge($loglist, $limit, $page=1, $logfile="", $query=array()){
		if (func_num_args()==3 && strpos("/integer/float/", "/".strtolower(gettype($page))."/")!==false){
			$logfile = strval($page); $page = 1; 
		}
		
		if (!is_array($loglist)){ $loglist = array('rows'=>array($loglist)); }
		elseif (!isset($loglist['rows'])){ $loglist['rows'] = array(); }
		elseif (!is_array($loglist['rows'])){ $loglist['rows']= array($loglist['rows']); }
		
		$parname= FuncExt::getvalue(self::$Ary_URL_Param['query'], "request", 0,true);
		//var_dump(self::$Ary_URL_Param['query']."|".$parname);
		
		$paging	= FuncExt::pagination($loglist['total'], $limit, $page, 10);					//分页
		$params	= FuncExt::pagequery(explode(",", $parname.",".self::$Ary_URL_Param['query']));	//参数
		$result	= array_merge(																	//合并结果
			$paging,
			array(
				"logfile"	=> strval($logfile),
				"loglist"	=> $loglist['rows'],
				"shellcmd"	=> isset($loglist['cmd']) ? $loglist['cmd'] : "",
				"urlparam"	=> ($params!="" ? $params . "&" : ""),
				"pagequery"	=> is_array($query) ? $query : array(),
			)
		);
		unset($paging); $paging = NULL;
		unset($params);	$params = NULL;
		return $result;
	}
	
	//__________________  公有方法  ________________
	
	public static function secToTimeLong($sec){
		$sec = intval($sec);
		if ($sec<=0){ return "0秒"; }
		$hours	= substr('00'.intval($sec/3600),-2);
		$minute	= substr('00'.intval($sec%3600/60),-2);
		$sec	= substr('00'.intval($sec%60),-2);
		return $hours.":".$minute.":".$sec;
	}
	
	//统一接口
	public function getLog($log='', $page=1, $limit=NULL){
		$args	= func_num_args();
		$type	= $args>0 ? strtolower(gettype(func_get_arg(0))) : "";
		$trues	= $args==3 ? array(true,true,true) : array(false,false,false);
		if ($args==1 || $args==2){
			if ($args==1){
				if ($type=="null"){
					$limit		= func_get_arg(0);
					$trues[2]	= true;
				}
				elseif(strpos("/integer/float/", "/$type/")!==false){
					$page		= func_get_arg(0);
					$trues[1]	= true;
				}
				else{
					$trues[0]	= true;
				}
			}
			else{
				if(strpos("/integer/float/null/", "/$type/")!==false){
					$limit		= func_get_arg(1);
					$page		= func_get_arg(0);
					$trues[2]	= true;
					$trues[1]	= true;
				}
				elseif (is_null(func_get_arg(1))){
					$limit		= func_get_arg(1);
					$log		= func_get_arg(0);
					$trues[2]	= true;
					$trues[0]	= true;
				}
				else{
					$trues[1]	= true;
					$trues[0]	= true;
				}
			}
		}
		if (!$trues[0]){ $log	= FuncExt::getvalue(self::$Ary_URL_Param['log'],"request",0,true); }
		if (!$trues[1]){ $page	= FuncExt::getnumber(self::$Ary_URL_Param['page'],"request"); }
		if (!$trues[2]){ $limit = isset($_REQUEST[self::$Ary_URL_Param['limit']]) ? max(intval($_REQUEST[self::$Ary_URL_Param['limit']]),1) : NULL; }
		//查询参数
		$params = $this->load_interargs();
		//获取接口并调用
		$interface = $this->get_loginter($log);
		TLOG_MSG("AuditService::getLog: func begin"." args=".$args." type=".$type." interface=".$interface." log=".$log." page=".$page." limit=".$limit);
		return call_user_func(array($this,$interface), $params, $page, $limit);
	}
	
	/*
	返回WEB操作日志列表
	number: 1600
	*/
	public function getWebAdminLogListByShell($param=array(),$page=1,$limit=NULL){
		//验证权限
		if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		//验证文件
		if (!$this->validateFileRead(_GLO_FILE_WEB_OPERATOR_,"WEB操作日志文件",1600,$interr,$strerr)){
			return $this->result_set($interr,$strerr);
		}
		//转换参数
		$keys = array(
			"curtime"	=>	array("name"=>"操作时间","empty"=>true),
			"user_ip"	=>	array("name"=>"用户IP",	"empty"=>true,	"rule"=>parent::_REG_IPADD_),
			"user_name"	=>	array("name"=>"用户名",	"empty"=>true),
			"msg"		=>	array("name"=>"详细信息","empty"=>true),
		);
		$param	= $this->validateArgs($param,$keys,1610,$interr,$strerr);
		if ( !$param ){ return $this->result_set($interr, $strerr); }
		//查询内容
		$fieldQuery = array();
		if($param){
			//操作时间一定不可为空，因此当没有指定操作时间时必须添加!=的搜索条件
			if(!empty($param['curtime']))	{
				if (!$this->validate_datetime("操作时间",$param['curtime'],1620)){return $this->result_set();}
				$fieldQuery[] = array("curtime","like",$param['curtime']);
			}
			else{
				$fieldQuery[] = array("curtime","!=","");
			}
			if(!empty($param['user_name'])){
				if (!$this->validate_keyword("用户名",$param['user_name'],1630)){return $this->result_set();}
				$fieldQuery[] = array("user_name","==",$param['user_name']);
			}
			if(!empty($param['msg']))	{
				if (!$this->validate_keyword("详细信息",$param['msg'],1640)){return $this->result_set();}
				$fieldQuery[] = array("msg","like",$param['msg']);
			}
			if(!empty($param['user_ip']))	{
				$fieldQuery[] = array("user_ip","==",$param['user_ip']);
			}
		}
		//日志格式
		$fieldList = array(
			array("no"=>1,"name"=>"curtime"),
			array("no"=>2,"name"=>"user_ip"),
			array("no"=>3,"name"=>"user_name"),
			array("no"=>4,"name"=>"thread"),
			array("no"=>5,"name"=>"msg_level"),
			array("no"=>6,"name"=>"module"),
			//array("no"=>7,"name"=>"action"),
			array("no"=>7,"name"=>"msg")
		);
		$limit	= isset($limit) ? max(intval($limit),0) : self::_STR_LOG_PAGESIZE_;
		$start	= (max(intval($page),1)-1)*$limit;
		$this->Obj_Anly->setFile(_GLO_FILE_WEB_OPERATOR_);
		$this->Obj_Anly->setFieldList($fieldList);
		$this->Obj_Anly->setFieldQuery($fieldQuery);
		$this->Obj_Anly->pageStart = $start;
		$this->Obj_Anly->pageLimit = $limit;
		$loglist = $this->Obj_Anly->outputArrayList();
		if (!$loglist){
			return $this->result_set(1650,$this->Obj_Anly->_errtext()."(#".$this->Obj_Anly->_error().")");
		}
		//var_dump($result);
		//合并输出结果
		$result = $this->result_merge($loglist,$limit, $page, _GLO_FILE_WEB_OPERATOR_, $param);
		unset($loglist['rows']); $loglist['rows'] = NULL;
		unset($loglist);$loglist = NULL;
		unset($param);	$param	 = NULL;
		//返回结果
		return $this->result_struct(
			array(
				'stateId'	=> -160,
				'message'	=> '获取WEB操作日志完成', 
				'result'	=> $result	
			)
		);
	}

	 /*
	返回用户登录日志
	number: 1700
	*/
	 public function getClientLoginInfoLogsByShell($param=array(), $page=1, $limit=NULL){
		 if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		 if (!$this->validateFileRead(_GLO_FILE_CLIENT_LOGIN_,"用户登录日志文件",1700,$interr,$strerr)){
			return $this->result_set($interr,$strerr);
		 }
		 //转换参数
		$keys = array(
			"clientIp"	=>	array("name"=>"客户端IP","empty"=>true,	"rule"=>parent::_REG_IPADD_),
			"loginTime"	=>	array("name"=>"登录时间",	"empty"=>true),
		);
		$param	= $this->validateArgs($param,$keys,1710,$interr,$strerr);
		if ( !$param ){ return $this->result_set($interr, $strerr); }
		//查询内容
		$fieldQuery = array(
			array("type","||","UserLoginInformation,AuthenticationInformation")
		);
        if($param){
			if( !empty($param['loginTime'])){
				if (!$this->validate_datetime("登录时间",$param['loginTime'],1720)){return $this->result_set();}
				$fieldQuery[] = array("loginTime","like",$param['loginTime']);
			}
			if( !empty($param['clientIp'])){
				$fieldQuery[] = array("clientIp","==",$param['clientIp']);
			}
		}
		 //日志格式
		 $fieldList = array(
		    array("no"=>1,"name"=>"f1"),
			array("no"=>2,"name"=>"type"),
			array("no"=>3,"name"=>"loginTime"),
			array("no"=>4,"name"=>"clientIp"),
			array("no"=>5,"name"=>"des"),
			array("no"=>6,"name"=>"f6"),
			array("no"=>7,"name"=>"f7"),
			array("no"=>8,"name"=>"f8")
		);

		$limit	= isset($limit) ? max(intval($limit),0) : self::_STR_LOG_PAGESIZE_;
		$start	= (max(intval($page),1)-1)*$limit;
		
		$this->Obj_Anly->setFile(_GLO_FILE_CLIENT_LOGIN_);
		$this->Obj_Anly->setFieldList($fieldList);
		$this->Obj_Anly->setFieldQuery($fieldQuery);
		$this->Obj_Anly->pageStart = $start;
		$this->Obj_Anly->pageLimit = $limit;
		$loglist = $this->Obj_Anly->outputArrayList();
		if (!$loglist){
			return $this->result_set(1730,$this->Obj_Anly->_errtext()."(#".$this->Obj_Anly->_error().")");
		}
		//合并输出结果
		$result = $this->result_merge($loglist,$limit, $page, _GLO_FILE_WEB_OPERATOR_, $param);
		unset($loglist['rows']); $loglist['rows'] = NULL;
		unset($loglist);$loglist = NULL;
		unset($param);	$param	 = NULL;
		//返回结果
		return $this->result_struct(
			array(
				'stateId'	=> -170,
				'message'	=> '获取用户登录日志完成',
				'result'	=> $result	
			)
		);
	}


	 /*
	返回客户端操作日志
	number: 1800
	*/
	public function getClientOperatorLogsByShell($param=array(), $page=1, $limit=NULL){
	    TLOG_MSG("AuditService::getClientOperatorLogsByShell: func begin");
		if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		if (!$this->validateFileRead(_GLO_PATH_CLIENT_OPERATOR_,"客户端操作日志文件",1800,$interr,$strerr)){
			return $this->result_set($interr,$strerr);
		}
		//转换参数
		$keys = array(
			"clientIp"		=>	array("name"=>"客户端IP",	"empty"=>true,	"rule"=>parent::_REG_IPADD_),
			"clientPort"	=>	array("name"=>"客户端端口",	"empty"=>true,	"min"=>parent::_INT_PORT_MIN_,"max"=>parent::_INT_PORT_MAX_),
			"serviceIp"		=>	array("name"=>"服务器端IP",	"empty"=>true,	"rule"=>parent::_REG_IPADD_),
			"servicePort"	=>	array("name"=>"服务器端端口",	"empty"=>true,	"min"=>parent::_INT_PORT_MIN_,"max"=>parent::_INT_PORT_MAX_),
			"msg"			=>	array("name"=>"操作信息",		"empty"=>true),
			"time"			=>	array("name"=>"操作时间",		"empty"=>true),
			"id"			=>	array("name"=>"客户端ID",	"empty"=>true),
		);
		$param	= $this->validateArgs($param,$keys,1810,$interr,$strerr);
		if ( !$param ){ return $this->result_set($interr, $strerr); }
		//查询内容
		$fieldQuery = array(array("logType","==","DATA"));
        if($param){
			if( !empty($param['clientIp']) )	{
				$fieldQuery[] = array("clientIp","==",$param['clientIp']);
			}
			if( !empty($param['clientPort']) )	{
				$fieldQuery[] = array("clientPort","==",$param['clientPort']);
			}
			if( !empty($param['serviceIp']) )	{
				$fieldQuery[] = array("serviceIp","==",$param['serviceIp']);
			}
			if( !empty($param['servicePort']) )	{
				$fieldQuery[] = array("servicePort","==",$param['servicePort']);
			}
			if( !empty($param['msg']) )	{
				if (!$this->validate_keyword("操作信息",$param['msg'],1820)){return $this->result_set();}
				$fieldQuery[] = array("msg","like",$param['msg']);
			}
			if( !empty($param['id']) )	{
				if (!$this->validate_keyword("客户端ID",$param['id'],1830)){return $this->result_set();}
				$fieldQuery[] = array("id","like",$param['id']);
			}
			
			TLOG_MSG("AuditService::getClientOperatorLogsByShell: time=".$param['time']);
			if( !empty($param['time']))
			{
				if (!$this->validate_datetime("操作时间",$param['time'],1840))
				{
				    TLOG_MSG("AuditService::getClientOperatorLogsByShell: time err");
				    return $this->result_set();
			     }
			     
			     TLOG_MSG("AuditService::getClientOperatorLogsByShell: time right");
				$fieldQuery[] = array("time","like",$param['time']);
			}
		}
		//日志格式
		$fieldList = array(
			array("no"=>1,"name"=>"f1"),
			array("no"=>2,"name"=>"time"),
			array("no"=>3,"name"=>"f3"),
			array("no"=>4,"name"=>"f4"),
			array("no"=>5,"name"=>"clientIp"),
			array("no"=>6,"name"=>"clientPort"),
			array("no"=>7,"name"=>"f7"),
			array("no"=>8,"name"=>"f8"),
			array("no"=>9,"name"=>"f9"),
			array("no"=>10,"name"=>"f10"),
			array("no"=>11,"name"=>"serviceIp"),
			array("no"=>12,"name"=>"servicePort"),
			array("no"=>13,"name"=>"id"),
			array("no"=>14,"name"=>"logType"),
			array("no"=>15,"name"=>"f15"),
			array("no"=>16,"name"=>"msg"),
			array("no"=>17,"name"=>"f17")
		);

		$limit	= isset($limit) ? max(intval($limit),0) : self::_STR_LOG_PAGESIZE_;
		$start	= (max(intval($page),1)-1)*$limit;
		
		$this->Obj_Anly->setFile(_GLO_PATH_CLIENT_OPERATOR_);
		$this->Obj_Anly->setFieldList($fieldList);
		$this->Obj_Anly->setFieldQuery($fieldQuery);
		$this->Obj_Anly->pageStart = $start;
		$this->Obj_Anly->pageLimit = $limit;
		$loglist = $this->Obj_Anly->outputArrayList();
		if (!$loglist){
			return $this->result_set(1850,$this->Obj_Anly->_errtext()."(#".$this->Obj_Anly->_error().")");
		}
		if (is_array($loglist) && isset($loglist['rows']) && is_array($loglist['rows'])){
			foreach($loglist['rows'] as &$value){
				if (!isset($value['[ROWS]'])) {continue;}
				$value['msg'] .= "|".$value['f17']."|".$value['[ROWS]'];
				if (substr($value['msg'],-1)=="|"){ $value['msg'] = substr($value['msg'],0,-1); }
			}
		}
		//合并输出结果
		$result = $this->result_merge($loglist,$limit, $page, _GLO_PATH_CLIENT_OPERATOR_, $param);
		unset($loglist['rows']); $loglist['rows'] = NULL;
		unset($loglist);$loglist = NULL;
		unset($param);	$param	 = NULL;
		//返回结果
		return $this->result_struct(
			array(
				'stateId'	=> -180,
				'message'	=> '获取客户端操作日志完成',
				'result'	=> $result	
			)
		);
	}

	/*
	返回客户端错误日志
	number: 1900
	*/
	public function getClientErrorLogsByShell($param=array(), $page=1, $limit=NULL){
		if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		if (!$this->validateFileRead(_GLO_PATH_CLIENT_OPERATOR_,"客户端错误日志文件",1900,$interr,$strerr)){
			return $this->result_set($interr,$strerr);
		}
		//转换参数
		$keys = array(
			"clientIp"		=>	array("name"=>"客户端IP",	"empty"=>true,	"rule"=>parent::_REG_IPADD_),
			"clientPort"	=>	array("name"=>"客户端端口",	"empty"=>true,	"min"=>parent::_INT_PORT_MIN_,"max"=>parent::_INT_PORT_MAX_),
			"serviceIp"		=>	array("name"=>"服务器端IP",	"empty"=>true,	"rule"=>parent::_REG_IPADD_),
			"servicePort"	=>	array("name"=>"服务器端端口",	"empty"=>true,	"min"=>parent::_INT_PORT_MIN_,"max"=>parent::_INT_PORT_MAX_),
			"msg"			=>	array("name"=>"操作信息",		"empty"=>true),
			"time"			=>	array("name"=>"操作时间",		"empty"=>true),
			"id"			=>	array("name"=>"客户端ID",	"empty"=>true),
		);
		$param	= $this->validateArgs($param,$keys,1910,$interr,$strerr);
		if ( !$param ){ return $this->result_set($interr, $strerr); }
		//查询内容
		$fieldQuery = array(array("logType","==","ERROR"));
        if($param){
			if( !empty($param['clientIp']) )	{
				$fieldQuery[] = array("clientIp","==",$param['clientIp']);
			}
			if( !empty($param['clientPort']) )	{
				$fieldQuery[] = array("clientPort","==",$param['clientPort']);
			}
			if( !empty($param['serviceIp']) )	{
				$fieldQuery[] = array("serviceIp","==",$param['serviceIp']);
			}
			if( !empty($param['servicePort']) )	{
				$fieldQuery[] = array("servicePort","==",$param['servicePort']);
			}

			if( !empty($param['msg']) )	{
				if (!$this->validate_keyword("操作信息",$param['msg'],1920)){return $this->result_set();}
				$fieldQuery[] = array("msg","like",$param['msg']);
			}
			if( !empty($param['id']) )	{
				if (!$this->validate_keyword("客户端ID",$param['id'],1930)){return $this->result_set();}
				$fieldQuery[] = array("id","like",$param['id']);
			}
			if( !empty($param['time']) ){
				if (!$this->validate_datetime("操作时间",$param['time'],1940)){return $this->result_set();}
				$fieldQuery[] = array("time","like",$param['time']);
			}
		}
		//日志格式
		$fieldList = array(
			array("no"=>1,"name"=>"f1"),
			array("no"=>2,"name"=>"time"),
			array("no"=>3,"name"=>"f3"),
			array("no"=>4,"name"=>"f4"),
			array("no"=>5,"name"=>"clientIp"),
			array("no"=>6,"name"=>"clientPort"),
			array("no"=>7,"name"=>"f7"),
			array("no"=>8,"name"=>"f8"),
			array("no"=>9,"name"=>"f9"),
			array("no"=>10,"name"=>"f10"),
			array("no"=>11,"name"=>"serviceIp"),
			array("no"=>12,"name"=>"servicePort"),
			array("no"=>13,"name"=>"id"),
			array("no"=>14,"name"=>"logType"),
			array("no"=>15,"name"=>"f15"),
			array("no"=>16,"name"=>"msg"),
			array("no"=>17,"name"=>"f17")
		);
		
		$limit	= isset($limit) ? max(intval($limit),0) : self::_STR_LOG_PAGESIZE_;
		$start	= (max(intval($page),1)-1)*$limit;
		
		$this->Obj_Anly->setFile(_GLO_PATH_CLIENT_OPERATOR_);
		$this->Obj_Anly->setFieldList($fieldList);
		$this->Obj_Anly->setFieldQuery($fieldQuery);
		$this->Obj_Anly->pageStart = $start;
		$this->Obj_Anly->pageLimit = $limit;
		$loglist = $this->Obj_Anly->outputArrayList();
		if (!$loglist){
			return $this->result_set(1950,$this->Obj_Anly->_errtext()."(#".$this->Obj_Anly->_error().")");
		}
		if (is_array($loglist) && isset($loglist['rows']) && is_array($loglist['rows'])){
			foreach($loglist['rows'] as &$value){
				if (!isset($value['[ROWS]'])) {continue;}
				$value['msg'] .= "|".$value['f17']."|".$value['[ROWS]'];
				if (substr($value['msg'],-1)=="|"){ $value['msg'] = substr($value['msg'],0,-1); }
			}
		}
		//合并输出结果
		$result = $this->result_merge($loglist,$limit, $page, _GLO_PATH_CLIENT_OPERATOR_, $param);
		unset($loglist['rows']); $loglist['rows'] = NULL;
		unset($loglist);$loglist = NULL;
		unset($param);	$param	 = NULL;
		//返回结果
		return $this->result_struct(
			array(
				'stateId'	=> -190,
				'message'	=> '获取客户端错误日志完成', 
				'result'	=> $result	
			)
		);
	}

	/*
	返回用户时长
	number: 2000
	*/
	public function getClientOfflineLogsByShell($param=array(), $page=1, $limit=NULL){
		if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		if (!$this->validateFileRead(_GLO_FILE_CLIENT_OFFLINE_,"用户时长日志文件",2000,$interr,$strerr)){
			return $this->result_set($interr,$strerr);
		}
		//转换参数
		$keys = array(
			"id"			=>	array("name"=>"用户ID",		"empty"=>true),
			"clientName"	=>	array("name"=>"用户名称",		"empty"=>true),
			"ip"			=>	array("name"=>"客户端IP",	"empty"=>true,	"rule"=>parent::_REG_IPADD_),
			"loginTime"		=>	array("name"=>"登录时间",		"empty"=>true),
			"logoutTime"	=>	array("name"=>"退出时间",		"empty"=>true),
			"duration"		=>	array("name"=>"在线时长",		"empty"=>true,	"rule"=>parent::_REG_POSINT_),
		);
		$param	= $this->validateArgs($param,$keys,2010,$interr,$strerr);
		if ( !$param ){ return $this->result_set($interr, $strerr); }
		//查询内容
		$fieldQuery = array( array("f2", "==", "offlinetime") );
        if($param){
			if( !empty($param['id']) )	{
				if (!$this->validate_keyword("用户ID",$param['id'],2020)){return $this->result_set();}
				$fieldQuery[] = array("id","like",$param['id']);
			}
			if( !empty($param['clientName']) )	{
				if (!$this->validate_keyword("用户名称",$param['clientName'],2030)){return $this->result_set();}
				$fieldQuery[] = array("clientName","==",$param['clientName']);
			}
			if( !empty($param['loginTime']) )	{
				if (!$this->validate_datetime("登录时间",$param['loginTime'],2040)){return $this->result_set();}
				$fieldQuery[] = array("loginTime","like",$param['loginTime']);
			}
			if( !empty($param['logoutTime']) )	{
				if (!$this->validate_datetime("退出时间",$param['logoutTime'],2050)){return $this->result_set();}
				$fieldQuery[] = array("logoutTime","like",$param['logoutTime']);
			}
			if( !empty($param['ip']) )	{
				$fieldQuery[] = array("ip","==",$param['ip']);
			}
			if( !empty($param['duration']) ){
				$fieldQuery[] = array("duratione","==",$param['duration']);
			}
		}
		//日志格式
		$fieldList = array(
			array("no"=>1,"name"=>"f1"),
			array("no"=>2,"name"=>"f2"),
			array("no"=>3,"name"=>"id"),
			array("no"=>4,"name"=>"clientName"),
			array("no"=>5,"name"=>"ip"),
			array("no"=>6,"name"=>"loginTime"),
			array("no"=>7,"name"=>"logoutTime"),
			array("no"=>8,"name"=>"duration")
		);
		
		$limit	= isset($limit) ? max(intval($limit),0) : self::_STR_LOG_PAGESIZE_;
		$start	= (max(intval($page),1)-1)*$limit;
		
		$this->Obj_Anly->setFile(_GLO_FILE_CLIENT_OFFLINE_);
		$this->Obj_Anly->setFieldList($fieldList);
		$this->Obj_Anly->setFieldQuery($fieldQuery);
		$this->Obj_Anly->pageStart = $start;
		$this->Obj_Anly->pageLimit = $limit;
		$loglist = $this->Obj_Anly->outputArrayList();
		if (!$loglist){
			return $this->result_set(2060,$this->Obj_Anly->_errtext()."(#".$this->Obj_Anly->_error().")");
		}
		//合并输出结果
		$result = $this->result_merge($loglist,$limit, $page, _GLO_FILE_CLIENT_OFFLINE_, $param);
		unset($loglist['rows']); $loglist['rows'] = NULL;
		unset($loglist);$loglist = NULL;
		unset($param);	$param	 = NULL;
		//返回结果
		return $this->result_struct(
			array(
				'stateId'	=> -200,
				'message'	=> '获取用户时长日志完成', 
				'result'	=> $result	
			)
		);
	}

	/*
	返回用户在线时长
	number: 2100
	*/
	public function getClientOnlineLogsByShell($param=array(), $page=1, $limit=NULL){
		if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		if (!$this->validateFileRead(_GLO_FILE_CLIENT_ONLINE_,"用户在线时长日志文件",2100,$interr,$strerr)){
			return $this->result_set($interr,$strerr);
		}
		//转换参数
		$keys = array(
			"id"			=>	array("name"=>"用户ID",		"empty"=>true),
			"clientName"	=>	array("name"=>"用户名称",		"empty"=>true),
			"ip"			=>	array("name"=>"客户端IP",	"empty"=>true,	"rule"=>parent::_REG_IPADD_),
			"loginTime"		=>	array("name"=>"登录时间",		"empty"=>true),
			"duration"		=>	array("name"=>"在线时长",		"empty"=>true,	"rule"=>parent::_REG_POSINT_),
		);
		$param	= $this->validateArgs($param,$keys,2010,$interr,$strerr);
		if ( !$param ){ return $this->result_set($interr, $strerr); }
		//查询内容
		$fieldQuery = array();
        if($param){
			if( !empty($param['id'])  )	{
				if (!$this->validate_keyword("用户ID",$param['id'],2120)){return $this->result_set();}
				$fieldQuery[] = array("id","like",$param['id']);
			}
			if( !empty($param['clientName'])  )	{
				if (!$this->validate_keyword("用户名称",$param['clientName'],2130)){return $this->result_set();}
				$fieldQuery[] = array("clientName","==",$param['clientName']);
			}
			if( !empty($param['loginTime'])  )	{
				if (!$this->validate_datetime("登录时间",$param['loginTime'],2140)){return $this->result_set();}
				$fieldQuery[] = array("loginTime","like",$param['loginTime']);
			}
			if( !empty($param['ip']) )	{
				$fieldQuery[] = array("ip","==",$param['ip']);
			}
			if( !empty($param['duration']) ){
				$fieldQuery[] = array("duratione","==",$param['duration']);
			}
		}
		//日志格式
		$fieldList = array(
			array("no"=>1,"name"=>"id"),
			array("no"=>2,"name"=>"clientName"),
			array("no"=>3,"name"=>"ip"),
			array("no"=>4,"name"=>"loginTime"),
			array("no"=>5,"name"=>"duration")
		);
		
		$limit	= isset($limit) ? max(intval($limit),0) : self::_STR_LOG_PAGESIZE_;
		$start	= (max(intval($page),1)-1)*$limit;
		
		$this->Obj_Anly->setFile(_GLO_FILE_CLIENT_ONLINE_);
		$this->Obj_Anly->setFieldList($fieldList);
		$this->Obj_Anly->setFieldQuery($fieldQuery);
		$this->Obj_Anly->pageStart = $start;
		$this->Obj_Anly->pageLimit = $limit;
		$loglist = $this->Obj_Anly->outputArrayList();
		if (!$loglist){
			return $this->result_set(2150,$this->Obj_Anly->_errtext()."(#".$this->Obj_Anly->_error().")");
		}
		//合并输出结果
		$result = $this->result_merge($loglist,$limit, $page, _GLO_FILE_CLIENT_ONLINE_, $param);
		unset($loglist['rows']); $loglist['rows'] = NULL;
		unset($loglist);$loglist = NULL;
		unset($param);	$param	 = NULL;
		//返回结果
		return $this->result_struct(
			array(
				'stateId'	=> -210,
				'message'	=> '获取用户在线时长日志完成', 
				'result'	=> $result	
			)
		);
	}

	/*
	删除日志中的某一行
	number: 2200
	interface:130
	*/
	public function delline_logfile($file,$data){
		if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		$file = strtolower(strval($file));
		$data = FuncExt::object2array($data);
		if (!$data){return $this->result_set(2200,"没有指定要删除的日志内容");}
		$rule = array();
		$keys = array();
		$temp = array();
		switch($file){
			case "operator" :
				$keys = array("time"=>"","clientIp"=>"","clientPort"=>"","logType"=>"DATA");
				break;
			case "error":
				$keys = array("time"=>"","clientIp"=>"","clientPort"=>"","logType"=>"ERROR");
				break;
			case "login"	:
				$keys = array("loginTime"=>"","clientIp"=>"");
				break;
			case "online"	:
				$keys = array("ip"=>""," loginTime"=>"");
				break;
			case "offline"	:
				$keys = array("ip"=>"","loginTime"=>"","logoutTime"=>"");
				break;
			default			:
				$keys = array("curtime"=>"","user_name"=>"");
				break;
				
		}
		/* $data 是二维数组(矩阵) */
		foreach($data as $dkey=>$dval){
			if (!is_array($dval) || !$dval){continue;}
			$temp = array();
			foreach($keys as $key=>$kval){
				$kval = trim(strval($kval));
				if ($kval!=""){ $temp[$key] = $kval; }
				elseif (isset($dval[$key]) && $dval[$key]!=""){ $temp[$key] = $dval[$key];}
				else{ continue 1; }
				$temp[$key] = preg_replace(FuncExt::preg_rule("regexp"), "\\\\\\1", $temp[$key]);
			}
			if ($temp) { $rule[] = implode("\|(.*?\|)*",$temp)."\|"; }
		}
		unset($temp); $temp = NULL;
		unset($keys); $keys = NULL;
		if (!$rule){return $this->result_set(2201,"指定要删除的日志内容都无效");}
		//验证文件
		$file = $this->get_logfile($file);
		if ($file==""){return $this->result_set(2202,"无法找到要删除的日志文件");}
		$rule	= "/". implode("|",$rule) ."/d";
		$cmd	= $file."|".$rule;
		//调用socket
		$result	= $this->socket_send(130, $cmd);
		$message= !$result['state'] ? "删除日志内容失败：".$result['errtxt'] : "删除日志内容成功";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set(($result['state']?-220:2203),$message);
	}

	/*
	清除日志文件
	number: 2300
	interface:130
	*/
	public function delget_logfile($ftag){
		if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		$ftag = strtolower(strval($ftag));
		$file = $this->get_logfile($ftag);
		if ($file==""){return $this->result_set(2300,"没有指定要清空的日志文件");}
		switch($ftag){
			case "operator":
				$preg = "/\|DATA\|/d";
				break;
			case "error":
				$preg = "/\|ERROR\|/d";
				break;
			case "login":
				$preg="/\|(AuthenticationInformation|UserLoginInformation)\|/d";
				break;
			case "offline":
				$preg="/\|offlinetime\|/d";
				break;
		}
		$result	= !isset($preg) ? $this->socket_send(131, $file) : $this->socket_send(130, $file.'|'.$preg);
		$message= !$result['state'] ? "清空日志失败：".$result['errtxt'] : "清空日志成功";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set(($result['state']?-230:2301),$message);
	}
	
	/*
	清除日志文件
	number: 2400
	*/
	public function export_logfile($type,$params=array()){
		$logname	= $this->get_logname($type); //传址参数
		$loginter	= $this->get_loginter($type);
		$logtmpl	= $this->Str_PHPApp."../".self::_STR_LOG_TEMPLET_;
		$logdata	= call_user_func(array($this,$loginter), $params, 1, 0);
		if (!$logdata['state']){ return $logdata; }
		//打开模板
		$templet = $this->file_content($logtmpl,"日志模板文件",2400,$reerr,$reerrtxt);
		if ($templet===false){
			unset($logdata['result']['loglist']); $logdata['result']['loglist'] = NULL;
			return $this->result_set($reerr,$reerrtxt);
		}
		//匹配内容
		$templet = str_ireplace("{tag:title1}", $logname." - "._GLO_PROJECT_NAME_, $templet);
		$templet = str_ireplace("{tag:title2}", $logname, $templet);
		$templet = str_ireplace("{tag:user}", $this->Ary_Session['user'], $templet);
		$templet = str_ireplace("{tag:datetime}", date('Y-m-d H:i:s'), $templet);
		$templet = str_ireplace("{tag:total}", sizeof($logdata['result']['loglist']), $templet);
		//获取主体
		$tmpltable	= "";
		$tmplrow	= "";
		$tmpltrs	= array();
		if (preg_match("/\<\!\-\-start\:log\-". $type ."\-\-\>([\s\S]+)\<\!\-\-end\:log\-". $type ."\-\-\>/i",$templet,$match)){
			$tmpltable = $match[1];
			if (preg_match("/\<\!\-\-start\:log_row\-\-\>([\s\S]+)\<\!\-\-end\:log_row\-\-\>/i",$tmpltable,$match)){
				$tmplrow	= rtrim($match[1]);
			}
			unset($match); $match = NULL;
		}
		//测试数据
		/*
		$logdata['result']['loglist'] = array(
			array("curtime"=>"2012-12-12 12:23:20", "user_ip"=>"192.168.102.124","user_name"=>"sysadmin","msg"=>"登录成功"),
			array("curtime"=>"2012-12-12 12:23:20", "user_ip"=>"192.168.1.24","user_name"=>"sysadmin","msg"=>"登录成功"),
			array("curtime"=>"2012-12-12 12:23:20", "user_ip"=>"192.168.1.24","user_name"=>"sysadmin","msg"=>"登录成功"),
			array("curtime"=>"2012-12-12 12:23:20", "user_ip"=>"192.168.1.24","user_name"=>"sysadmin","msg"=>"登录成功"),
		);
		*/
		if ($tmplrow!="" && is_array($logdata['result']['loglist']) && $logdata['result']['loglist']){
			switch($type){
				case "operator":
				case "error":
					//操作时间 	客户端ID 	客户端IP 	客户端口 	服务器IP 	服务端口 	操作信息
					foreach($logdata['result']['loglist'] as $k=>$v){
						$tmpltrs[] = sprintf($tmplrow, $k%2, $v["time"], $v["id"],$v["clientIp"],$v["clientPort"],$v["serviceIp"],$v["servicePort"],$v["msg"]);
					}
					break;
				case "login":
					//登录时间 	登录IP 	事件描述
					foreach($logdata['result']['loglist'] as $k=>$v){
						$tmpltrs[] = sprintf($tmplrow, $k%2, $v["loginTime"],$v["clientIp"],$v["des"]);
					}
					break;
				case "online":
					//登录时间 	客户端ID 	登录IP 	在线时长
					foreach($logdata['result']['loglist'] as $k=>$v){
						$tmpltrs[] = sprintf($tmplrow, $k%2, $v["logoutTime"], $v["id"], $v["ip"], self::secToTimeLong($v["duration"]));
					}
					break;
				case "offline":
					//登录时间 	退出时间 	客户端ID 	登录IP 	在线时长
					foreach($logdata['result']['loglist'] as $k=>$v){
						$tmpltrs[] = sprintf($tmplrow, $k%2, $v["loginTime"], $v["logoutTime"], $v["id"], $v["ip"], self::secToTimeLong($v["duration"]));
					}
					break;
				case "webadmin":
					//操作时间 	用户IP 	用户名 	详细信息
					foreach($logdata['result']['loglist'] as $k=>$v){
						$tmpltrs[] = sprintf($tmplrow, $k%2, $v["curtime"],$v["user_ip"],$v["user_name"],$v["msg"]);
					}
				break;
			}
		}
		$tmpltrs	= implode("\r\n",$tmpltrs)."\r\n";
		$tmpltable	= preg_replace("/\<\!\-\-start\:log_body\-\-\>([\s\S]+)\<\!\-\-end\:log_body\-\-\>/i",$tmpltrs,$tmpltable);
		$content  	= preg_replace("/\<\!\-\-start\:log_table\-\-\>([\s\S]+)\<\!\-\-end\:log_table\-\-\>/i",$tmpltable,$templet);
		unset($logdata['result']['loglist']); $logdata['result']['loglist'] = NULL;	
		//返回结果
		return $this->result_struct(
			array(
				'stateId'	=> -240,
				'message'	=> '导出日志内容完成', 
				'result'	=> array("content"=>$content,"filename"=>$logname."[". date('Y-m-d') ."]")
			)
		);
	}
}
?>