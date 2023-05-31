<?php
//****************** [Class] 防火墙对象 ******************

//该类必须运行于 PHP5.0 及以上的平台
if ( intval(PHP_VERSION) < 5 ){ exit("<p>Run \"FireWall\" invite promotion your PHP arrives at 5.0 the above editions</p>");}

if (!class_exists('AtherFrameWork')){ require(dirname(__FILE__)."/AtherFrameWork.php"); }

class FireWall extends AtherFrameWork{
	
	//__________________  构造/析构函数  ________________
	
	/**/
	function __construct(){
		parent::__construct();
	}
	
	
	function __destruct(){
		parent::__destruct();
	}
	
	//__________________  私有变量  ________________
	
	protected static $Ary_RField = array(
		"sport" 	=> "",
		"sip" 		=> "",
		"smask" 	=> "",
		"smac"		=> "",
		"dport" 	=> "",
		"dip" 		=> "",
		"dmask" 	=> "",
		"ptype" 	=> "",
		"link" 		=> "",
		"action"	=> "",
	);
	
	
	//__________________  只读属性(用方法代替)  ________________
	
	public static function _version()	{return '1.0';}					//版本
	public static function _build()		{return '11.11.24';}			//版本
	public static function _create()	{return '11.11.24';}			//创建
	public static function _classname()	{return __CLASS__;}				//名称
	public static function _developer()	{return "OldFour";}				//开发者
	public static function _copyright()	{return "Aether.CORP";}			//公司
	
	//__________________  只写属性  ________________
	
	
	//__________________  可读写属性  ________________
	
	
	//__________________  私有方法  ________________
	
	/**
	 * 将防火墙的行规则转化为一个数组
	 * Enter description here ...
	 * @param unknown_type $line
	 */
	private function fwLine2Array($line){
	    TLOG_MSG("getFirewallRuleList: func begin line=".$line);
		$line	= ltrim(strval($line));
		$first	= substr($line,0,1);
		$result	= array();	//返回结果
		$rows	= array();	//行配置
		$cell	= array();	//列配置
		$vals	= array();	//值配置
		$befk	= "";
		//分解词组关键字
		if ($first!="#" && $first!="'"){
			$rows = preg_split('/\-+/', $line);
			foreach ($rows as $row){
				$cell	= preg_split('/\s+/',trim($row));
				$key	= $cell[0];
				$value	= sizeof($cell)>1 ? $cell[1] : NULL;
				if ($key!="" && isset($value)){
					switch ($key){
						case 'A':		$result["link"]		=	$value; break;
						case "p":		$result["ptype"]	=	$value; break;
						case "m":		$result["mtype"]	=	$value; break;
						case "j":		$result["action"]	=	$value; break;
						case "sport":	$result["sport"]	=	$value; break;
						case "dport":	$result["dport"]	=	$value; break;
						case 'source':
							if ($befk=="mac"){$result["smac"]=$value;}
							break;
						case "s":
							$vals = explode("/", $value);
							$result["sip"]		=	$vals[0];
							$result["smask"]	=	count($vals)>1? $vals[1] : "";
							break;
						case "d":
							$vals = explode("/", $value);
							$result["dip"]		= $vals[0];
							$result["dmask"]	= count($vals)>1?$vals[1]:"";
							break;
					}
				}
				$befk = $key;
			}
			if(!isset($result["ptype"]) || !$result["ptype"]){ $result["ptype"] = 'all';}
		}
		unset($rows);	$rows = NULL;
		unset($cell);	$cell = NULL;
		unset($vals); 	$vals = NULL;
		return $result;
	}

	
	//__________________  公有方法  ________________
	
	
	/* 
	获取 防火墙规则列表
	number: 1600
	*/
	public function getFirewallRuleList($page=1){
		if (!$this->user_popedom(
			array(__METHOD__, __CLASS__."::getFirewallRule", __CLASS__."::delFirewallRule",__CLASS__."::setFirewallRule")
		)){return $this->Ary_Popedom;}
		$content=$this->file_content(_GLO_FILE_FIREWALL_,"防火墙规则配置文件",1600,$interr,$strerr);
		if ($content===false){return $this->result_set($interr,$strerr);}
		
		//预置结果
		$result		= array();
		$datalist	= array();
		$matches1	= NULL;
		$matches2	= NULL;
		//截取*filter...COMMIT部分
		preg_match('/\*filter\b.*?\bcommit\b/is', $content,$matches1);
		if ($matches1){
			preg_match_all('/[\n\r]([\'\#])?\-a\b[^\n\r]+/is', $matches1[0], $matches2);//截取-A...\n部分
			if ($matches2){
				$lines = $matches2[0];
				foreach ($lines as $line){$row=$this->fwLine2Array($line); if($row){$datalist[]=$row;} }//分解行
				unset($lines); $lines = NULL;
				$psize		= 20;
				$pshow		= 10;
				$result		= FuncExt::pagination(sizeof($datalist), $psize, $page, $pshow);			//分页
				//print_r($result);
				$datalist	= array_chunk($datalist, $psize);											//分割
				$result['datalist']	= $datalist ? $datalist[$result['absolutepage']-1] : array();		//合并
				//统一输出
				foreach($result['datalist'] as $k=>$v){$result['datalist'][$k]=array_merge(self::$Ary_RField,$result['datalist'][$k]);}
				unset($datalist);	$datalist = NULL;
			}
		}
		unset($matches1); $matches1 = NULL;
		unset($matches2); $matches2 = NULL;
		//var_dump($result);
		return $this->result_struct(
			array(
				'stateId'	=> -160,
				'message'	=> '获取防火墙规则配置完成', 
				'result'	=> $result
			)
		);
	}
	
	/* 
	获取 防火墙规则
	number: 1700
	*/
	public function getFirewallRule($id){
		if (!$this->user_popedom(
			array(__METHOD__, __CLASS__."::getFirewallRuleList", __CLASS__."::delFirewallRule",__CLASS__."::setFirewallRule")
		)){return $this->Ary_Popedom;}
		$content=$this->file_content(_GLO_FILE_FIREWALL_,"防火墙规则配置文件",1700,$interr,$strerr);
		if ($content===false){return $this->result_set($interr,$strerr);}
		//验证ID
		$str_id = strval($id);
		$int_id	= intval($id);
		if ($str_id==""){return $this->result_set(1710,"缺少防火墙规则ID");}
		elseif (!is_numeric($str_id) || $int_id<0){return $this->result_set(1711,"防火墙规则ID错误");}
		//预置结果
		$result		= array();
		$matches1	= NULL;
		$matches2	= NULL;
		//截取*filter...COMMIT部分
		preg_match('/\*filter\b.*?\bcommit\b/is', $content,$matches1);
		if ($matches1){
			preg_match_all('/[\n\r]([\'\#])?\-a\b[^\n\r]+/is', $matches1[0], $matches2);//截取-A...\n部分
			if ($matches2 && isset($matches2[0][$int_id])){
				$result = array_merge(self::$Ary_RField,$this->fwLine2Array($matches2[0][$int_id]));
			}
		}
		if (!$result){ return $this->result_set(1712,"指定的防火墙规则不存在"); }
		return $this->result_struct(
			array(
				'stateId'	=> -170,
				'message'	=> '获取防火墙规则内容完成', 
				'result'	=> $result
			)
		);
	}
	
	/* 
	获取 保存防火墙规则
	number: 1800
	*/
	public function setFirewallRule($param,$action="add"){
		$action = strtolower(strval($action));
		if ($action =="del" ){
			$method = __CLASS__."::delFirewallRule";
			$metnum = "042";
			$title	= "删除防火墙规则";
		}
		/*
		elseif ($action =="mod" ){
			$method = __METHOD__;
			$metnum = "041";
			$title	= "修防火墙规则";
		}
		*/
		else{
			$method = __METHOD__;
			$metnum = "040";
			$title	= "添加防火墙规则";
		}
		if (!$this->user_popedom($method)){return $this->Ary_Popedom;}
		$keys = array(
			"sport"	=>	array("name"=>"源端口",		"min"=>parent::_INT_PORT_MIN_,"max"=>parent::_INT_PORT_MAX_,"empty"=>true),
			"sip"	=>	array("name"=>"源IP",		"rule"=>parent::_REG_IPADD_,  "empty"=>true),
			"smask"	=>	array("name"=>"源子网掩码",	"rule"=>parent::_REG_IPMASK_, "empty"=>true),
			"smac"	=>	array("name"=>"源MAC地址",	"rule"=>parent::_REG_MACADD_, "empty"=>true),
			"dport"	=>	array("name"=>"目标端口",		"min"=>parent::_INT_PORT_MIN_,"max"=>parent::_INT_PORT_MAX_,"empty"=>true),
			"dip"	=>	array("name"=>"目标IP",		"rule"=>parent::_REG_IPADD_,  "empty"=>true),
			"dmask"	=>	array("name"=>"目标子网掩码",	"rule"=>parent::_REG_IPMASK_, "empty"=>true),
			"link"	=>	array("name"=>"规则链",		"rule"=>explode(",",parent::_STR_RULELINK_ALL_)),
			"action"=>	array("name"=>"动作",		"rule"=>explode(",",parent::_STR_ACTION_ALL_)),
			"ptype"	=>	array("name"=>"协议类型",		"rule"=>explode(",",parent::_STR_PROTOCOL_ALL_))
		);
		#数据包长度|数据包类型|文件名|源端口|源ip|源子网掩码|目标端口|目标ip|目标子网掩码|规则链|动作|协议类型。如：
		#0080|040|/etc/sysconfig/ipt-cfg|585|1.1.2.5|255.255.255.0| | | |INPUT|ACCEPT|udp没有的以空格表示，如目标端口。
		$param	= $this->validateArgs($param,$keys,1800,$interr,$strerr);
		if ( !$param ){ return $this->result_set($interr, $strerr); }
		//验证其他1
		if ($param['sip']==""){ $param["smask"] =""; }
		elseif ($param['smask']==""){ $param["smask"] = parent::_STR_MASK_DEF_; }
		if ($param['dip']==""){ $param["dmask"] =""; }
		elseif ($param['dmask']==""){ $param["dmask"] = parent::_STR_MASK_DEF_; }
		//验证其他2
		if (($param["sport"]!="" || $param["dport"]!="") ){
			$str_ptype = strtolower($param["ptype"]);
			if ($str_ptype=="icmp"){return $this->return_result(1806,"使用ICMP协议时不可指定源端口或目标端口");}
			elseif ($str_ptype=="all"){return $this->return_result(1807,"指定源端口或目标端口时，必须使用UDP/TCP协议");}
		}
		//使用空格代替
		foreach($param as $k=>$v){ if (trim($v)==""){ $param[$k]=" ";} }
		//合并
		$sendarg = _GLO_FILE_FIREWALL_."|".$param["sport"]."|".$param["sip"]."|".$param["smask"]."|".$param["smac"]."|".$param["dport"]."|".$param["dip"]."|".$param["dmask"]."|".$param["link"]."|".$param["action"]."|".$param["ptype"];
		//$this->debugLog("saveFirewall.txt",$sendarg);
		//exit($sendarg);
		//发送socket
		$result	= $this->socket_send($metnum,$sendarg);
		$message= !$result['state'] ? $title."失败：".$result['errtxt']:$title."成功";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set(($result['state'] ? -180 : 1808),$message);
	}
	/* 
	获取 删除防火墙规则
	number: 1900
	*/
	public function delFirewallRule($param){
		return $this->setFirewallRule($param,"del");
	}
	/* 
	获取 防火墙规则排序
	number: 2000
	*/
	public function orderFirewall($param){
	}
	
	/*
	获取防火墙开关状态
	number: 2100
	*/
	public function getFireWallSwitch(){
		if (!$this->user_popedom(array(__METHOD__,__CLASS__."::setFireWallSwitch"))){return $this->Ary_Popedom;}
		//获取当前全局配置
		$gloconf = $this->get_globalConf(2100,$this->Int_Error,$this->Str_Error);
		if ($gloconf===false){return $this->result_set();}
		$fwallsvr = !!intval($gloconf['firewall']);
		unset($gloconf); $gloconf = NULL;
		return $this->result_struct(
			array(
				'stateId'	=> -210,
				'message'	=> '获取防火墙服务状态完成', 
				'result'	=> $fwallsvr
			)
		);
	}
	/*
	设置防火墙开关
	number: 2200
	interface: 50
	*/
	public function setFireWallSwitch($param){
		if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		$keys = array(
			"enabled"	=>	array("name"=>"防火墙服务", "rule"=>array(0,1), "empty"=>true)
		);
		$param	= $this->validateArgs($param,$keys,2200,$interr,$strerr);
		if ( !$param ){ return $this->result_set($interr, $strerr); }
		//发送socket
		$handle	= !!$param["enabled"] ? "启动" : "关闭";
		$result	= $this->socket_send(50,_GLO_FILE_GLOBAL_SWITCH_."|".$param['enabled']."|-1");
		$message= !$result['state'] ? $handle."防火墙失败：".$result['errtxt'] : $handle."防火墙成功";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set(($result['state'] ? -220 : 2206),$message);
	}
}
?>