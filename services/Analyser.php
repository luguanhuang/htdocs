<?php
/*
 * 错误编码设定规则：
 * 框架类: init=1-9; pro=10-59; pub=60-99
 * 一层类: init=100-109; pro=110-159; pub=160-999
 * 二层类: init=1000-1009; pro=1010-1059; pub=1060-1999
*/

/**
 * 日志分析器
 * author:colin
 */
class Analyser {
	
	//__________________  构造/析构函数  ________________
	
	function __construct($logFile="",$spliter="",$fieldList="",$fieldQuery=""){
		$this->Int_Report	= ini_get('error_reporting');
		$this->Str_PHPApp	= preg_replace("/[\/\\\\]+/","/",(dirname(__FILE__)."/"));
		//如果有参数则设置参数
		if ($logFile!="")			{$this->setFile($logFile);}
		if ($spliter!="")			{$this->setSpliter($spliter);}
		if (is_array($fieldList))	{$this->setFieldList($fieldList);}
		if (is_array($fieldQuery))	{$this->setFieldQuery($fieldQuery);}
		
		if (!function_exists('popen')){
			$this->Int_Error = 1;
			$this->Str_Error = "函数 \"popen\" 不可使用，请修改 PHP.ini 配置";
			return false;
		}
		$this->Bln_Enabled	= true;
	}
	function __destruct(){}
	
	//__________________  私有变量  ________________
	
	const _INT_MAX_READ_	= 1024;
	const _REG_CMD_CODE_	= "/[\~\`\^\\$\;\&\'\\\"\<\>\[\]]/";

	protected $Int_Error	=	0;
	protected $Str_Error	=	"";
	protected $Bln_Enabled	=	false;
	
	protected $Int_Report	=	0;
	protected $Str_PHPApp	=	"";
	protected $Obj_Loger	=	NULL;
	
	protected $Str_LogFile	=	"";
	protected $Str_Spliter	=	"";
	
	protected $Ary_Field	=	array();
	protected $Ary_Query	=	array();
	
	//日志字段结构，格式如 array( array("no"=>0,"name"=>"ip"),array("no"=>1,"name"=>"date","reg_source"=>"","reg_target"=>"") );
	//搜索字段，格式如array( array("ip","==","192.168.1.2"),array("date","[]","2009.5.5","2009.5.10" ))
	
	//__________________  只读属性(用方法代替)  ________________
	
	public function _error()	{return $this->Int_Error;}
	public function _errtext()	{return $this->Str_Error;}

	
	//__________________  只写属性(用方法代替)  ________________
	
	/**
	 * 设置日志文件路径(绝对路径)
	 *
	 * @param unknown_type $logFile
	 */
	public function setFile($logFile){
		//验证日志文件
		$this->Str_LogFile = "";
		$logFilee = trim(strval($logFile));
		if ($logFile==""){
			$this->Int_Error = 160;
			$this->Str_Error = "缺少日志文件";
			return false;
		}
		if (!is_file($logFile)){
			$this->Int_Error = 161;
			$this->Str_Error = "日志文件不存在";
			return false;
		}
		if (!is_readable($logFile)){
			$this->Int_Error = 162;
			$this->Str_Error = "日志文件没有读取权限";
			return false;
		}
		$this->Str_LogFile = $logFile;
		return true;
	}
	
	/**
	 * 设置日志字段分隔符
	 *
	 * @param unknown_type $fieldList
	 */
	 
	 public function setSpliter($spliter){
		$this->Str_Spliter = "";
		$spliter = strval($spliter);
		if ($spliter==""){
			$this->Int_Error = 170;
			$this->Str_Error = "缺少字段分隔符";
			return false;
		}
		$this->Str_Spliter = $spliter;
		return true;
	 }
	
	/**
	 * 设置日志字段列表
	 *
	 * @param unknown_type $fieldList
	 */
	public function setFieldList($fieldList){
		//重置
		$this->Ary_Field = array();
		//验证
		if ( !is_array($fieldList)){
			$this->Int_Error = 180;
			$this->Str_Error = "指定的日志数组格式不匹配";
			return false;
		}
		if (!$fieldList){
			$this->Int_Error = 181;
			$this->Str_Error = "指定的日志数组格式不能为空";
			return false;
		}
		foreach ($fieldList as $k => &$field){
			$field["name"]	= isset($field["name"]) ? strval($field["name"]) : "";
			$field["no"]	= isset($field["no"]) ? strval($field["no"]) : "";
			if ($field["name"]==""){
				$this->Int_Error = 182;
				$this->Str_Error = "指定的日志数组格式中第 [$key] 字段缺少字段名称";
				return false;
			}
			if (preg_match(self::_REG_CMD_CODE_,$field["name"])){
				$this->Int_Error = 183;
				$this->Str_Error = "指定的日志数组格式中第 [$key] 字段名称含有特殊字符";
				return false;
			}
			if ($field["no"]==""){
				$this->Int_Error = 184;
				$this->Str_Error = "指定的日志数组格式中第 [$key] 字段缺少字段下标";
				return false;
			}
			if (isset($field["reg_source"]) && preg_match(self::_REG_CMD_CODE_,$field["reg_source"])){
				$this->Int_Error = 185;
				$this->Str_Error = "指定的日志数组格式中第 [$key] 字段 \"reg_source\" 属性含有特殊字符";
				return false;
			}
			if (isset($field["reg_target"]) && preg_match(self::_REG_CMD_CODE_,$field["reg_target"])){
				$this->Int_Error = 186;
				$this->Str_Error = "指定的日志数组格式中第 [$key] 字段 \"reg_target\" 属性含有特殊字符";
				return false;
			}
			$result = "";
			$fedno	= $field["no"];
			$first	= substr($fedno,0,1);
			//数字（正数/负数）
			if (is_numeric($fedno)){
				$fedno	= intval($fedno);
				$result = $first !="-" ? '$'.max($fedno,1) : '$(NF '. $fedno .')';
			}
			//$+数字
			elseif ( strlen($fedno)>1 && $first=='$' && is_numeric(substr($fedno,1)) ){
				$fedno	= intval(substr($fedno,1));
				$result = $fedno==0 ? '$1' : '$'.$fedno;
			}
			else{
				switch ($fedno){
					case '$':
					case 'NF':
					case 'END':
						$result = '$NF';	//使用awk命令时，NF表示最后一个字段 
						break;
					/*
					case 'LEFT':
						//$result = "\$(NF-2)\"\" \"|\" \"\"\$(NF-1)";
						break;
					*/
					default:
						$result = $fedno;
					break;
				}
			}
			$field["code"] = $result;
		}
		$this->Ary_Field=$fieldList;
		unset($fieldList); $fieldList = NULL;
	}
	
	/**
	 * 设置查询条件
	 *
	 * @param unknown_type $fieldQuery
	 */
	public function setFieldQuery($fieldQuery=NULL){
		$this->Ary_Query=array();
		if ( is_array($fieldQuery) ){
			$this->Ary_Query=$fieldQuery;
		}
	}
	
	
	//__________________  可读写方法  ________________
	
	public $pageStart		= 0;//分页开始记录
	public $pageLimit		= 0;//分页每页记录数
	
	//__________________  私有方法  ________________
	
	
	//转换字段(构造查询条件时调用)
	protected function getFieldCode($fieldName){
		if( !is_array($this->Ary_Field) || !$this->Ary_Field ){return false;}
		for($i=0;$i<count($this->Ary_Field);$i++){
			if (
				is_array($this->Ary_Field[$i])	&&
				isset($this->Ary_Field[$i]['name'])	&&
				isset($this->Ary_Field[$i]["code"]) && 
				$this->Ary_Field[$i]["name"] == $fieldName
			){
				return $this->Ary_Field[$i]["code"];
			}
		}
		return false;
	}
	
	/**
	 * 创建查询条件表达式
	 *
	 */
	protected function createCompareExpression(){
		$result = array();
		for ($i=0;$i<count($this->Ary_Query);$i++ ){
			//查询条件格式是否合法
			$field	= $this->Ary_Query[$i];
			if (!is_array($field) || sizeof($field)<3){ continue; }
			//查询条件的字段名是否存在日志字段中
			//不需要再验证字段名称是否含有特殊字符，因为指定的字段名称已经验证过；如果存在则一定不含有特殊字符
			$fcode	= $this->getFieldCode($field[0]);
			if ( $fcode===false ){continue;}
			if (!isset($field[3])){ $field[3] = ""; }
			$field[2] = addslashes($field[2]);
			$field[3] = addslashes($field[3]);
			$expression ="";
			switch($field[1]){
				case "()":
					$expression = $fcode."> \" ". $field[2] ."\" && \"" . $fcode ."\" < \" " . $field[3] . "\" ";
					break;
				case "(]":
					$expression = $fcode."> \" ".$field[2] ."\" && \"" .$fcode ."\" <= \" " . $field[3] . "\" ";
					break;
				case "[)":
					$expression = $fcode.">= \" ".$field[2] ."\" && \"" .$fcode ."\" < \" " . $field[3] . "\" ";
					break;
				case "[]":
					$expression = $fcode.">= \" ".$field[2] ."\" && \"" .$fcode ."\" <= \" " . $field[3] . "\" ";
					break;
				case "==":
					$expression = $fcode."== \"".$field[2]."\" ";
					break;
				case "!=":
					$expression = $fcode."!= \"".$field[2]."\" ";
					break;
				case "||":
					$field[2] = str_replace(", ", ",", $field[2]);
					$field[2] = str_replace(",", "\" || ". $fcode ."== \"", $field[2]);
					$expression = "(".$fcode."== \"". $field[2] ."\") ";
					break;
				case "like":
					$expression = $fcode."~ \"".$field[2]."\" ";
					break;
				default:
					continue;
					//$expression = $fcode.$field[1]."\"".$field[2]."\" ";
					//break;
			}
			$result[] = $expression;
		}
		return implode("&&",$result);
	}
	
	/**
	 * 构造输出字段格式化表达式
	 *
	 * @return 过滤表达式
	 */
	protected function createFilterCommand(){
		if (!is_array($this->Ary_Field)){ return ""; }
		$result = array();
		for ( $i=0;$i<count($this->Ary_Field);$i++ ){
			if (!isset($this->Ary_Field[$i]["reg_source"])){continue;}
			if (!isset($this->Ary_Field[$i]["reg_target"])){$this->Ary_Field[$i]["reg_target"]="";}
			$result[] = ' gsub(' .$this->Ary_Field[$i]["reg_source"] . ",\"" . $this->Ary_Field[$i]["reg_target"] ."\",".$this->Ary_Field[$i]["code"].');';
		}
		return implode("",$result);
	}
	
	//验证各个属性是否合法
	protected function validate_property(){
		if (!$this->Str_LogFile){return false;}
		if (!$this->Str_Spliter){return false;}
		if (!$this->Ary_Field){return false;}
		return true;
	}
	
	//__________________  公有方法  ________________


	/**
	 * 创建组合查询字符串,函数执行目标为“输出数组格式”
	 *
	 */
	public function createArrayCommand($allfield=false){

		if (!$this->validate_property()){ return false; }
		
		$str_field_format	= "";
		$str_field_list		= "";
		$ary_field_format	= array();	//输出字段格式
		$ary_field_list		= array();	//输出字段列表

		$fleng = count($this->Ary_Field);
		for ( $i=0 ; $i<$fleng;$i++ ){
			if ( $i<$fleng-1 ){
				$ary_field_format[] = '%s' .$this->Str_Spliter ;
				$ary_field_list[]	= $this->Ary_Field[$i]["code"] .","; 
			}else{
				$ary_field_format[] = '%s';
				$ary_field_list[]	= $this->Ary_Field[$i]["code"] ; 
			}
		}
		$str_field_format	= implode("",$ary_field_format);
		$str_field_list		= implode("",$ary_field_list);
		$cyc_format_start	= '"'.$str_field_format .'\n"';		//循环中每行数据格式
		$cyc_format_end		= '"'.$str_field_format .'\n"';			//（循环结束）最后一行数据格式
		unset($ary_field_format);	$ary_field_format	= NULL;
		unset($ary_field_list);		$ary_field_list		= NULL;
		
		$exp_compare = $this->createCompareExpression();			//创建查询条件表达式
		$exp_filter = $this->createFilterCommand();					//构造输出字段格式化表达式
		
		$int_pstart	= max(intval($this->pageStart),0);
		$int_plimit	= max(intval($this->pageLimit),0);
		$int_allfld = intval(!!$allfield);
		
		$cmd = array("/usr/bin/tac ". $this->Str_LogFile);
		$cmd[] ="|";
		$cmd[] = "awk ";
		$cmd[] = '-F "'.$this->Str_Spliter.'" ';
		$cmd[] = "'BEGIN{";
		$cmd[] = "start=" . $int_pstart .";";
		$cmd[] = "limit=" . $int_plimit .";";
		$cmd[] = "count=0;";
		$cmd[] ="}";
		$cmd[] ="{" . ($exp_compare ? "if (" . $exp_compare . " ){" : "");
		$cmd[] = "count++;";
		$cmd[] = "if( limit==0 || (count>start  && count<=start+limit) ){ ";
		$cmd[] = $exp_filter ;
		$cmd[] = "if( ". $int_allfld ." == 1 && NF > ".$fleng."){ ";
		$cmd[] = "for(ii=1; ii<=NF; ++ii){";
		$cmd[] = "printf(\"%s\", \$ii);";
		$cmd[] = "if(ii == NF){";
		$cmd[] = "printf(\"\\n\");}";
		$cmd[] = "else{printf(\"|\");}";
		$cmd[] = "}}else{";
		$cmd[] = "printf(" .$cyc_format_start. "," . $str_field_list .");";
		$cmd[] = "}";
		$cmd[] = "}" . ($exp_compare ? "}" : "");
		$cmd[] = "}";		
		$cmd[] = "END{";
		$cmd[] = 'print(count);';
		$cmd[] ="}";
		$cmd[] ="' " ;
		
		$cmd = implode("",$cmd);
		return $cmd;
	}
	
	/**
	 * 输出数组格式组合结果
	 */	
	public function outputArrayList($allfield=false){
		if (!$this->Bln_Enabled){return false;}	
		if (!($cmd=$this->createArrayCommand($allfield))){return false;}
		TLOG_MSG("AuditService::outputArrayList: func begin cmd=".$cmd);
		//	echo '$cmd:'.$cmd ."\n";
		error_reporting(0);
		$handle=popen($cmd,"r");
		error_reporting($this->Int_Report);
		if($handle===false){
			$this->Int_Error = 170;
			$this->Str_Error = "执行查询命令时发生错误";
			return false;
		}
		
		$logs = array();
		while (!feof($handle)){$logs[] = fread($handle,self::_INT_MAX_READ_);}
		
		fclose($handle);
		TLOG_MSG("AuditService::outputArrayList: func begin 1");
		
		
		$logs	= implode("",$logs);
		
		if (_GLO_FILE_WEB_OPERATOR_!=$this->Str_LogFile){
			FuncExt::error_report(0);
			$logs = iconv("gb2312","utf-8//IGNORE",$logs);
			FuncExt::error_report(true);
		}
		
		$lines	= explode("\n", $logs);
		//	//模拟SHELL执行后结果
		//	//$lines = file($this->logFile);
		//	//$lines[] = count($lines);
		$rows	= array();		
		$total	= 0;
		while(empty($total) && !empty($lines)){
			$total = array_pop($lines);
		}
		TLOG_MSG("AuditService::outputArrayList: func begin 2");
		foreach ($lines as $line){
			$item	= array();
			$words	= explode($this->Str_Spliter, $line);
			$keynum	= -1;
			foreach ($this->Ary_Field as $field){
				$keynum = intval($field["no"])-1;
				$item[$field["name"]] = $words[$keynum];
				unset($words[$keynum]);
			}
			if ($words){$item["[ROWS]"]=$line;}
			$rows[] = $item;
		}
		
		TLOG_MSG("AuditService::outputArrayList: func begin 3");
		$result = array("total"=>$total,"rows"=>$rows,"cmd"=>$cmd);
		//var_dump($result);
		$this->Int_Error = -1;
		$this->Str_Error = "执行查询命令完成";
		return $result;
	}

}

/*
$andy = new Analyser();
$andy->setFile("/var/log/ydsec/transproxy.log");
$andy->setSpliter("|");
$andy->setFieldList(array(
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
		));
$andy->setFieldQuery();
$andy->pageStart = 0;
$andy->pageLimit = 0;
var_dump($andy->outputArrayList(true));
var_dump($andy->_errtext());
*/
?>