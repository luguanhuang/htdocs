<?php
//****************** [Class] 路由对象 ******************

//该类必须运行于 PHP5.0 及以上的平台
if ( intval(PHP_VERSION) < 5 ){ exit("<p>Run \"Router\" invite promotion your PHP arrives at 5.0 the above editions</p>");}

if (!class_exists('AtherFrameWork')){ require(dirname(__FILE__)."/AtherFrameWork.php"); }

class Router extends AtherFrameWork{
	
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
	返回路由器设置列表
	number: 1600
	*/
	public function getRouteList(){
		if (!$this->user_popedom(
			array(__METHOD__, __CLASS__."::setRoute",__CLASS__."::deleteRoute")
		)){return $this->Ary_Popedom;}
		$lines= $this->file_array(_GLO_FILE_ROUTE_,"路由配置文件",1600,$interr,$strerr);
		$result = array();
		if ($lines===false)
		{
		    //return $this->result_set($interr,$strerr);
		    return $this->result_struct(
		        array(
		            "stateId"	=> -160,
		            "message"	=> "获取路由器设置完成",
		            "result"	=> $result
		        )
		    );
		}
		
		foreach ($lines as $line){
			$line = trim($line);
			if( $line=="" || substr($line,0,1)=="#" ) {continue;}
			$fields = explode("|", $line);
			for ($l=sizeof($fields); $l<5; $l++){ $fields[$l] = NULL; }
			$result[] = array(
				"type"		=> $fields[0],
				"ip"		=> $fields[1],
				"mask"		=> $fields[2],
				"netgate"	=> $fields[3],
				"ifname"	=> $fields[4]
			);
		}
		return $this->result_struct(
			array(
				"stateId"	=> -160,
				"message"	=> "获取路由器设置完成",
				"result"	=> $result
			)
		);
	}
	/*
	保存路由器设置
	number: 1700
	*/
	public function setRoute( $param,$action="add" ){
		$action = strtolower(strval($action));
		if ($action =="del" ){
			$method = __CLASS__."::delRoute";
			$metnum = "022";
			$title	= "删除路由配置";
		}
		/*
		elseif ($action =="mod" ){
			$method = __METHOD__;
			$metnum = "021";
			$title	= "修改路由配置";
		}
		*/
		else{
			$method = __METHOD__;
			$metnum = "020";
			$title	= "添加路由配置";
		}
		if (!$this->user_popedom($method)){return $this->Ary_Popedom;}
		
		if ($param['type'] == "net")
		{
		    $keys	= array(
		        'type'		=>	array('name'=>'路由类别', explode(",",parent::_STR_ROUTER_TYPE_)),
		        'ip'		=>	array('name'=>'网段', 	'rule'=>parent::_REG_IPADD_),
		        'mask'		=>	array('name'=>'子网掩码','rule'=>parent::_REG_IPMASK_, 'default'=>parent::_STR_MASK_DEF_),
		        'netgate'	=>	array('name'=>'网关地址','rule'=>parent::_REG_IPADD_),
		        'ifname'	=>	array('name'=>'接口名称', explode(",",parent::_STR_ROUTER_INAME_)),
		    );
		    
		    $param	= $this->validateArgs($param,$keys,1080,$interr,$strerr);
		}
		else 
		{
		    $keys	= array(
		        'type'		=>	array('name'=>'路由类别', explode(",",parent::_STR_ROUTER_TYPE_)),
		        'ip'		=>	array('name'=>'网段', 	'rule'=>parent::_REG_IPADD_),
		        'ifname'	=>	array('name'=>'接口名称', explode(",",parent::_STR_ROUTER_INAME_)),
		    );
		    
		    $param	= $this->validateArgs($param,$keys,1080,$interr,$strerr);
		}
		
		if ( !$param ){ return $this->result_set($interr, $strerr); }
		$sendarg = _GLO_FILE_ROUTE_."|".$param['type']."|".$param['ip']."|".$param['mask']."|".$param['netgate']."|".$param['ifname'];
		//发送socket
		$result	= $this->socket_send($metnum,$sendarg);
		$message= !$result['state'] ? $title."失败：".$result['errtxt']:$title."成功";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set(($result['state'] ? -108 : 1807),$message);
	}
	/*
	删除路由器设置
	number: 1900
	*/
	public function delRoute($param){
		return $this->setRoute($param,"del");
	}
}
?>