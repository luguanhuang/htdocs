<?php
class FuncExt {
	
	
	//__________________  构造/析构函数  ________________
	
	protected function __construct(){}
	protected function __destruct(){}
	
	//__________________  只读属性(用方法代替)  ________________
	
	public static function _version()	{return '1.0';}				//版本
	public static function _build()		{return '10.08.19';}		//版本
	public static function _create()	{return '10.08.19';}		//创建
	public static function _classname()	{return __CLASS__;}			//名称
	public static function _developer()	{return "SSOC";}			//开发者
	public static function _copyright()	{return "Aether.CORP";}		//公司
	
	//__________________  公有方法  ________________
	
	public static function error_report($report=true){
		static $reporting;
		if (!isset($reporting)){ $reporting = ini_get('error_reporting'); }
		$int_seting = !func_num_args() || $report===true ? $reporting : max(intval($report), 0);
		return error_reporting($int_seting);
	}
	
	public static function timeout($sec=1800){
		static $def_limit;
		if (is_null($def_limit)){ $def_limit = max(intval(ini_get('max_execution_time')),0); }
		$sec = intval($sec);
		if ($sec && $sec<$def_limit){ $sec = $def_limit; }
		set_time_limit($sec);
	}

	public static function nocache($charset="UTF-8"){
		$charset = strval(trim($charset));
		if ($charset=='' || $charset=='utf8'){ $charset='utf-8'; }
		header('Content-Type:text/html;charset='. strtoupper($charset));
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
	}

	public final static function preg_rule($type="az09"){
		$type = strtolower($type);
		switch ($type){
			case "d":		return "/^[0-9]+$/";
			case "w":		return "/^[a-zA-Z]+$/";
			case "dw":		return "/^[0-9a-zA-Z]+$/";
			case "email":	return "/^[-_a-zA-Z0-9.]+@([-_a-zA-Z0-9]+\.)+[a-zA-Z0-9]{2,3}$/";
			case "pass":	return "/^[\~\!\@\%\^\*\(\)\_\+\-\=\\\\\{\}\[\]\;\:\,\.\?\/a-zA-Z0-9]+$/";
			case "postal":	return "/^[0-9]{6}$/";
			case "paper":	return "/^[0-9\-]{6,30}$/";
			case "real":	return "/^(0|[\-\+]?([1-9][0-9]*(\.\d+)?)|(0\.[0-9]+))$/";
			case "int":		return "/^(0|[\-\+]?[1-9][0-9]*)$/";
			case ">0":		return "/^[1-9]\d*$/";
			case "<0":		return "/^\-[1-9]\d*$/";
			case "<=0":		return "/^(0|\-[1-9]\d*)$/";
			case ">=0":		return "/^(0|[1-9]\d*)$/";
			case "<>0":		return "/^([1-9]\d*|\-[1-9]\d*)/";
			case "phone":	return "/^(\+[1-9]\d{0,3}\-[1-9]\d{1,2}\-?|((0{1,2}[1-9]\d{1,2}\-0?|0)[1-9]\d{1,2}\-?))?[1-9]\d{6,7}(\-[1-9]\d{0,4})?$/";
			case "handset":	return  "/^(\+[1-9]\d{0,3}\-?|0(0?[1-9]\d{1,2}\-?)?)?1[35]\d{9}$/";
			case "sql":		return "/^[^\<\>\'\"\&\`]+$/";
			case "html":	return "/([\<\>\'\"\#\&\|])/";
			case "html-":	return "/([\<\>\'\"\&])/";
			case "regexp":	return "/([\^\\$\*\?\!\|\<\>\(\)\[\]\{\}\\\\\/\-\+\=\,\.\;])/";
			case "point":	return "/([\ \`\~\!\@\#\\$\%\^\&\*\(\)\_\+\|\-\=\\\\\{\}\[\]\:\;\'\"\<\>\?\,\.\/])/";
			case "\\/":		return "/([\\\\\/]+)/";
			case "file":	return "/^[^\/\\\\\<\|\>\'\"\&\#\?\:\*]+$/";
			case "path":	return "/^[A-Za-z]\:\\\\([^\\\\\/\<\>\|\*\:\?\"\']\\\\?)*$/";
			case "url": 	return  "/^((\/|\/?([^\\\\\/\<\>\|\*\:\?\"\'\s]\/?)+|http\:\/\/([a-zA-Z0-9][a-zA-Z0-9\-]*\.)*[a-zA-Z0-9][a-zA-Z0-9\-]*(\:[1-9]\d*)?(\/[^\\\\\/\<\>\|\*\:\?\"\'\s]+)*\/?)([\?\#][^\s\"\']*)?|[\?\#][^\s\"\']*)$/";
			case "ip":		return "/^((25[0-5]|2[0-4][0-9]|[0-1]?[0-9]{1,2})\.){3}(25[0-5]|2[0-4][0-9]|[0-1]?[0-9]{1,2})$/";
			case "varphp":	return "/^\\$[a-zA-Z_][a-zA-Z0-9_]{0,243}$/";
			case "varjs":	return "/^\\$?[a-zA-Z_][a-zA-Z0-9_\\$]{0,243}$/";
			case "varvb":	return "/^[a-zA-Z][a-zA-Z0-9_\\$]{0,244}$/";
			default :		return "/^[_a-zA-Z0-9]+$/";
		}
	}
	
	public final static function preg_test($regexp,$value){
		$regexp	= is_array($regexp) ? $regexp : array(strval($regexp));
		$value	= strval($value);
		$return	= NULL;
		$boolean= true;
		foreach($regexp as $rule){
			$rule = trim(strval($rule));
			if ($rule==""){ continue; }
			if (substr($rule,0,1)!="!"){ $boolean = true; }
			else{ $boolean = false;  $rule = substr($rule,1); }
			if (substr($rule,0,1)=="/"){
				if(preg_match("/\/[imsxeAEU]*$/",$rule)){
					$return |= (preg_match(self::preg_rule($rule),$value)==$boolean); continue;
				}
			}
			$return |= (preg_match(self::preg_rule($rule), $value)==$boolean);
		}
		return $return;
	}
	
	//读取变量($case:0=原型，1=小写,2=大写,3=数字,4=实数)
	public final static function getvalue($name=NULL,$type="",$case=0,$decode=false){
		$int_args	= func_num_args();
		//参数非1/2/3可忽略
		if ($int_args>0 && $int_args<4){
			$str_larg	= func_get_arg($int_args-1);
			$str_ltype	= gettype($str_larg);
			$ary_type = array("boolean","float","integer");
			if ($str_ltype=="boolean"){$decode=$str_larg;}						//确定$decode(不是boolean则就是默认值)
			if ($str_ltype=="float"||$str_ltype=="integer"){$case=$str_larg;}	//预定$case(如果为数字类型则可能就是)
			if($int_args==1){		//一个参数的可退出
				if( array_search($str_ltype,$ary_type) ){$name = NULL;}
			}
			else{					//最后一个参数可能还未被分配出
				$str_larg2	= func_get_arg($int_args-2);
				$str_ltype2	= gettype($str_larg2);
				$int_search	= array_search($str_ltype2,$ary_type);
				if($int_search){$case=intval($str_larg2); $decode=$str_larg;}
				if($int_args==2){	//二个参数的可退出
					if($int_search){$name=NULL; $type="";}
					else{$name=$str_larg2; $type=!array_search($str_ltype,$ary_type) ? strval($str_larg) : "";}
				}
				elseif($int_search){$name=strval($name);$type="";}
			}
			unset($ary_type);
		}
		$name = trim($name); if ($name==""){$name = "php_request";}
		$case = intval($case); if ($case<0){$case=0;}elseif ($case>3){$case=3;}
		$type = strtolower(trim($type)); if ($type==""){$type="request";}
		$value = "";
		TLOG_MSG("FuncExt::getvalue: func begin type=".$type);
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
			if (($case==1 || $case==2) && $decode){$value = rawurldecode($value);}
			switch($case){
				case 1 : $value = strtolower($value);	break;
				case 2 : $value = strtoupper($value);	break;
				case 3 : $value = intval($value);		break;
				case 4 : $value = is_numeric($value) ? $value * 1 : 0;	break;
				case 5 : $value = array($value);		break;
			}
		}
		//echo("name:".$name."<br>type:".$type."<br>case:".$case."<br>decode:".$decode."<br>value:".$value."<br>");
		return $value;
	}
	
	public function getnumber($name=NULL,$type=""){
		return self::getvalue($name,$type,3);
	}
	
	//获取变量值
	/* $case参数说明：
	$var为对象时，该参数无效
	$case=0:保留原型
	$case=1:强制为字符;
	$case=2:小写字符;
	$case=3:大写字符;
	$case=-1:trim;
	$case=-2:trim并小写;
	$case=-3:trim并大写;
	*/
	public static function str_value($var,$key=NULL,$case=1){
		$vtype = strtolower(gettype($var));
		$ktype = strtolower(gettype($key));
		//只有两个参数时
		if (func_get_args()==2 && strpos(',integer,float,', ",$ktype,") && strpos(',0,1,2,3,-1,-2,-3,', ",$ktype,")!==false){
			$case=$key; $key = NULL;
		}
		if (is_object($var)){ return serialize($var); }
		$key = strval($key); $cvar = NULL;
		if ($vtype=="null"){$cvar="";}
		elseif ($vtype=="boolean"){ $cvar = strval(intval($var)); }
		elseif ($vtype=="integer" || $vtype=="float"){ $cvar = strval($var); }
		elseif ($vtype=="array"){
			if ($key==""){ $cvar=implode("",$var); }
			elseif (!isset($var[$key])){ $cvar=""; $var=NULL; }
			else{ $cvar=strval($var[$key]); $var=$var[$key]; }
		}
		else{
			$cvar=$var=strval($var);
		}
		//要返回原型
		if (!$case){return $var;}
		//剩下的情况都是转换为字符串(其中"1"就是保持不变的字符)
		switch($case){
			case -1	: return trim($cvar);
			case -2	: return trim(strtolower($cvar));
			case -3	: return trim(strtoupper($cvar));
			case 2	: return strtolower($cvar);
			case 3	: return strtoupper($cvar);
			default : return $cvar;
		}
	}
	
	public final static function number_rand($n=6){
		$n = intval($n); $int_bit = ''; 
		if ($n < 6){$n = 6 ;} elseif ($n > 12) {$n=12;}
		$int_bit = implode("",array_fill(0,$n-1,"9"));
		$int_min = ceil($int_bit)+1;
		$int_max = ceil($int_bit.'9');
		mt_srand(number_format(microtime(),9,".","")*100000000);
		return mt_rand($int_min,$int_max);
	}
	
	public final function number_fromdt($t="stamp"){
		$t = strtolower($t);
		switch($t){
			case "s":;
			case "second": $int_dt = date("YmdHis").substr("00".(number_format(microtime(),2)*100),-2);
			case "t":;
			case "time": $int_dt=date("Ymd").substr("00000".(time()%86400),-5).substr("000".(number_format(microtime(),3)*1000),-3);
			case "a":;
			case "stamp":;
			default :  $int_dt = time(). substr("0000".(number_format(microtime(),4)*10000),-4);
		}
		return $int_dt;
	}
	
	public final static function number_between($int,$min=0,$max=0){
		$int  = intval($int);
		$args = func_num_args();
		if ($args<=1){return $int;}					//一个参数：返回本身
		else if ($args==2){return intval($min);}	//两个参数：返回指定值
		$min = isset($min) ? intval($min) : NULL;
		$max = isset($max) ? intval($max) : NULL;
		if ($min!==NULL && $max!==NULL && $min>$max){$temp = $min; $min = $max; $max = $min;}
		if ($min!==NULL && $int<$min){return $min;}
		if ($max!==NULL && $int>$max){return $max;}
		return $int;
	}

	public final static function str_concat($str="",$split=",",$int=1){

		if ( func_num_args()==2 && is_numeric(func_get_arg(1)) ){$int=func_get_arg(1);$split = "";}
		
		$split = strval($split); if ($split==""){$split=",";}
		$int = self::number_between($int,-1,1);
		$str = is_array($str) ? implode($split,$str) : trim(strval($str));
		if ($str==""){return "";}
		
		$str_sreg = preg_replace(self::preg_rule("regexp"),"\\\\\\1",$split);
		$str = preg_replace("/(". $str_sreg .")+/i",$split,$str);
		$str = preg_replace("/(^". $str_sreg .")|(". $str_sreg ."$)/i","",$str);
		if ($int>=0){
			if ($int>0){$str = preg_replace("/(^|". $str_sreg .")0+([1-9][0-9]*|0)/","\\1\\2",$str);}
			if (!preg_match("/^((([1-9][0-9]*)|0)(". $str_sreg ."))*(([1-9][0-9]*)|0)$/i",$str)){return "";}
			if ($int>0 && str_replace($split."0", "", $str)=="0" ){return "";}
		}
		return $str;
	}
	
	public final static function str_sqllike($dbtype, $field, $value, $split=",", $int=1){
		//数据库类型
		$dbtype = strtolower(trim(strval($dbtype)));
		if ($dbtype==""){ $dbtype = "mysql"; }
		//字段名
		$field = trim(strval($field));
		if ($field==""){ return ""; }
		//查询值
		$valstr= !is_array($value) ? strval($value) : implode("",$value);
		if ($valstr==""){ return ""; }
		if (is_array($value)){ $value = implode($split[0],$value); }//如果值不是数组则需要处理分隔符号
		//分隔符
		$splitstr= !is_array($split) ? strval($split) : implode("", $split);
		if ($splitstr==""){ $split = array(","); }
		if (!is_array($split)){ $split = array($split); }
		//有多个分隔符时
		$splitint = sizeof($split);
		for($i=1;$i<$splitint;++$i){ $value = str_replace($split[$i],$split[0], $value); }
		$split = $split[0];
		//分隔
		$value = FuncExt::str_concat($value,$split,$int);
		if ($value==""){ return NULL; }
		//生成SQL
		$value = FuncExt::str2quotes($value, '+'.$dbtype, true);
		$value = "(". $field ." like '%". str_replace(",", "%' or ". $field ." like '%", $value) ."%'";
		$value .= $dbtype=="mysql" ? " Escape '\\\\'" : " Escape '\\'";
		$value .= ")";
		return $value;
	}
	
	public final static function str_length($string,$strunit=1,$charset=""){
		$str	= strval($string);
		$unit	= strtolower($strunit);
		$char	= strtolower($charset);
		if ($str==""){return 0;}
		//只有两个参数的情况下确定了$unit的取值范围(如果是3个参数则不止6种取值)
		if (func_num_args()==2 && strpos(",,1,2,byte,char,place,",','. $unit .',')===false){$char=$unit;$unit=1;}
		//验证字节时则等价于strlen
		if ($unit=="" || $unit=="byte"){return strlen($string);}
		//确定$char
		if (strpos(",gbk,gb2312,big5,utf-8,utf8,",','.$charset.',')===false){$char=strtolower(ini_get("default_charset"));}
		//确定要替换的字符
		if ( $char!="utf-8" && $char!='utf8'){$str = iconv($char,"utf-8//IGNORE",$string);}	//保留使用$string转换
		if ($unit!="2" && $unit!="place"){return strlen(preg_replace("/(?:[\x09\x0A\x0D\x20-\x7E]|[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})/xs","*",$str));}
		else{return strlen(preg_replace("/(?:[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2}|\xF0[\x90-\xBF][\x80-\xBF]{2})/xs","**",$str))/2;}
		/*
		"utf-8"	: "/(?:[\x09\x0A\x0D\x20-\x7E]|[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})/xs"
		"gb2312" : "/([\xb0-\xf7][\xa0-\xfe])|(\xa1[\xa2-\xfe])|(\xa2([\xa1-\xaa]|[\xb1-\xbf]|[\xc0-\xdf]|[\xe0-\xe2]|[\xe5-\xee]|[\xf1-\xfc]))|(\xa3[\xa1-\xfe])|(\xa4[\xa1-\xf3])|(\xa5[\xa1-\xf6])/"
		"gbk" : "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/"
		"big5": "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|[\xa1-\xfe])/"
		*/
	}
	
	public final static function str_randcode($len,$type=array(1,1,5)){
		$len = self::number_between($len,1,50);
		if (!is_array($type)){$type=array($type);}
		if (!sizeof($type)){$type=array(1,1,5);}
		$ary_code1 = array("","0123456789","346789");
		$ary_code2 = array("","`~!@#\$%^&*()_-+={[}]|\\:;\"'<>,./?","@%*()_+-=\\{}?/");
		$ary_code3 = array("","ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz","ABCDEFGHIJKLMNPQRTUVWXY","abcdefghijkmnpqrtuvwxy");
		$str_code = "";
		if (isset($type[0])){$type[0]=self::number_between($type[0],0,2);$str_code .= $ary_code1[$type[0]];}
		if (isset($type[1])){$type[1]=self::number_between($type[1],0,2);$str_code .= $ary_code2[$type[1]];}
		if (isset($type[2])){
			$type[2]=self::number_between($type[2],0,6);
			if ($type[2]<5){$str_code .= $ary_code3[$type[2]];}
			elseif ($type[2]==5){$str_code .= $ary_code3[1].$ary_code3[2];}
			elseif ($type[2]==6){$str_code .= $ary_code3[3].$ary_code3[4];}
		}
		if ($str_code==""){return "";}
		unset($ary_code1);unset($ary_code2);unset($ary_code3);
		$int_length		= strlen($str_code)-1;
		$str_rndcode	= "";
		mt_srand(microtime()*100000000);
		for ($i=0;$i<$len;++$i){$str_rndcode .= substr($str_code, mt_rand(0,$int_length), 1);}
		return $str_rndcode;
	}
	
	public final static function str2js($str=""){
		$str = strval($str);
		if ($str==""){return "";}
		$js = addslashes($str);
		$js = str_replace("\n","\\n",$js);
		$js = str_replace("\r","\\r",$js);
		return $js;
	}
	
	public final static function str2easyhtml($str="",$io="i"){
		$str = strval($str);
		if ($str==""){return "";}
		//写入数据库
		if (strtolower($io) == "i"){return str_replace(array(" ","\r","\n"),array("&nbsp;","","<br/>"),	$str);}
		$str = preg_replace("/\&nbsp\;/i"," ",$str);
		$str = preg_replace("/\<br(\ ?\/)?\>/i","\n",$str);
		return $str;
	}
	
	public final static function str2request($str="",$io="i",$quotes=""){
		$str = strval($str); if ($str==""){return "";}
		$quotes = strval($quotes); if ($quotes!=""){$str = self::str2quotes($str,$quotes,true);}
		if (strtolower($io)=="i"){
			return str_replace(
				array("&",		"\"",	"'",	"<",	">",	" ",	"\r","\n"),
				array("&#38;",	"&#34;","&#39;","&#60;","&#62;","&nbsp;","", "<br/>"),
				$str
			);	
		}
		$str = preg_replace("/\<br(\ ?\/)?\>/i","\n",$str);
		return str_replace(
			array("&nbsp;","&#62;","&#60;","&#39;","&#34;","&#38;"),
			array(" ",">","<","'","\"","&"),
			$str
		);
	}
	
	public final static function str2quotes($str="",$io="+request",$ini=true){
		if (($str=strval($str))==""){return "";}
		$io=strtolower(strval($io)).','; $ini=!!$ini; $rep=true;
		if ($ini){
			$gets = strpos("+exec,-exec,",$io)!==false ? intval(get_magic_quotes_runtime()) : intval(get_magic_quotes_gpc());
			$type = substr($io,0,1);
			if ( $gets == 0 && $type=="-" ){$rep = false;}	// $gets  == 0 的其他情况($type=+)就需要转换
			if ( $gets == 1 && $type=="+" ){$rep = false;} 	// $gets  == 1 的其他情况($type=-)就需要转换
		}
		//对字符 ' " \ 的处理
		if ($rep){ $str=($type=="-") ? stripslashes($str) : addslashes($str); }
		//以下六种情况必须对'重新转义
		if (strpos("+oracle,-oracle,+mssql,-mssql,+access,-access,",$io)!==false){
			$str=($type=="-") ? str_replace("''","'",$str) : preg_replace("/\\?\'/","''",$str);
		}
		//不管$rep与否，都必须对通配符进行处理（适合于LIKE查询）
		if (strpos("+oracle,+mssql,+access,+mysql,",$io)!==false){$str=preg_replace("/([\%\_\[\]\!\^\&])/i","\\\\\\1",$str);}
		elseif(strpos("-oracle,-mssql,-access,-mysql,",$io)!==false){$str=preg_replace("/\\\\([\%\_\[\]\!\^\&])/i","\\\\1",$str);}
		return $str;
	}
	
	public final static function str2url($format,$path="",$return=false){
		$int_args = func_num_args();
		if ($int_args==1){$path = $format; $format = "";}
		else if ($int_args==2 && is_bool($path)){ $return = $path; $path = $format; $format = "";}
		
		$path		= strtolower($path);
		$protocol 	= substr($path,0,7);
		$format		= preg_replace("/^url\:?/","",strtolower($format));
		if (strpos($format,"-?")!==false){//不得带有?参数
			if ($return){ $path = preg_replace('/\?[\s\S]+$/i','',$path);}
			elseif (strpos($path,"?")!==false) {return false;}
			$format = str_replace("-?","",$format);
		}
		if (strpos($format,"-#")!==false){//不得带有#瞄点
			if ($return){ $path = preg_replace('/\#[\s\S]+$/i','',$path);}
			elseif (strpos($path,"#")!==false) {return false;}
			$format = str_replace("-#","",$format);
		}
		if ($path==""){return ($format=="http" || $format=="!http")? false : ($return? "" : true);}	//要求绝对地址或相对地址
		if ($format=="!http" && $protocol =="http://")	{return false;}								//要求相对路径(不得以http://开头)
		if ($format=="http" && $protocol !="http://")	{return false;}								//要求绝对路径(必须以http://开头)
		if ($format=="+http" && $protocol !="http://")	{$path="http://".$path;}					//要求相对路径(如果不以http://开头)
		if ($format=="-http" && $protocol =="http://")	{$path=substr($path,7);}					//要求绝对路径(如果不以http://开头)
		if (!preg_match(self::preg_rule("url"),$path))	{return false;}
		return $return ? $path : true;
	}
	
	public final static function unique_filter($array,$split=NULL){
		if (!is_array($array)){return array_filter(array_unique($array));}
		if (func_num_args()==1 || is_null($split)){$split = ",";} else {$split = strval($split);}
		if ($split==""){ return array($array); }
		$array = explode($split,$array);
		$array = array_filter(array_unique($array));
		return implode($split,$array);
	}
	
	public final static function is_datetime($timestring, $format="", &$error=NULL){
		if ( ($timestring=trim($timestring)) ==""){$error="缺少验证的日期时间表达式"; return false;}
		$format = !is_array($format) ? strtolower(strval($format)) : strtolower(implode("|",$format));
		$rule					= array();
		$rule["y"]				= array("[1-9][0-9]*",					"yyyy");
		$rule["m"]				= array("(1[0-2]|0?[1-9])",				"mm");
		$rule["d"]				= array("(3[0-1]|[1-2][0-9]|0?[1-9])",	"dd");
		$rule["h"]				= array("(2[0-3]|[0-1]?[0-9])",			"hh");
		$rule["i"]				= array("([0-5]?[0-9])",				"ii");
		$rule["s"]				= array("([0-5]?[0-9])",				"ss");
		$rule["h:i"]			= array($rule["h"][0]."\\:".$rule["i"][0],	"hh:ii");
		$rule["i:s"]			= array($rule["i"][0]."\\:".$rule["s"][0],	"ii:ss");
		$rule["h:i:s"]			= array($rule["h"][0]."\\:".$rule["i"][0]."\\:".$rule["s"][0],	"hh:ii:ss");
		$rule["time"]			= $rule["h:i:s"];
		$rule["3"]				= $rule["h:i:s"];
		$rule["y-m-d"]			= array($rule["y"][0]."\\-".$rule["m"][0]."\\-".$rule["d"][0],	"yyyy-mm-dd");
		$rule["y-m"]			= array($rule["y"][0]."\\-".$rule["m"][0],	"yyyy-mm");
		$rule["date"]			= $rule["y-m-d"];
		$rule["2"]				= $rule["y-m-d"];
		$rule["y-m-d h:i"]		= array($rule["y-m-d"][0]."\\ ".$rule["h:i"][0],  "yyyy-mm-dd hh:ii");
		$rule["y-m-d h:i:s"]	= array($rule["y-m-d"][0]."\\ ".$rule["h:i:s"][0],"yyyy-mm-dd hh:ii:ss");
		$rule['datetime']		= $rule["y-m-d h:i:s"];
		$rule['full']			= $rule["y-m-d h:i:s"];
		$rule['1']				= $rule["y-m-d h:i:s"];
		list($re,$rl,$dt,$fm) = array(false, "", NULL, NULL);
		$fm= strpos("|$format|","||")===false ? explode("|",$format) : array("1","2","3","y-m-d h:i","y-m","h:i","i:s");
		$fr= array(); $fe=array();
		foreach($fm as $k=>$v ){if (isset($rule[$v]) && !in_array($rule[$v][0],$fr)){ list($fr[],$fe[])=$rule[$v]; }}
		if (!$fr){ list($fr, $fe) = array(array($rule['1'][0]),array($rule['1'][1])); } //没有一个匹配的
		$re = preg_match("/^(". implode("|",$fr) .")$/",$timestring);
		if (!$re){$error="格式必须是：".implode(", ", $fe);}
		else{
			$dt = explode("-",preg_replace("/[\ \:]/","-",$timestring));
			if (sizeof($dt)>2 && strpos(implode("",$fm),'-')!==false){
				if (!( $re==!!checkdate(intval($dt[1]),intval($dt[2]),intval($dt[0])) )){$error="日期部分的表示式错误";}
			}
		}
		unset($dt);$dt=NULL; unset($fm);$fm=NULL; unset($fe);$fe=NULL; unset($rule);$rule=NULL;
		return $re;
	}
	
	//计算分页参数
	public final static function pagination($count,$psize,$pnumber,$pview=10){
		$count	= max(intval($count),0);
		$psize	= max(intval($psize),1);
		$pcount	= max(ceil($count/$psize),1);
		$pnumber= self::number_between($pnumber,1,$pcount);
		$ary_num = array();
		$ary_num[0] = intval($pview / 2);
		$ary_num[1] = $pnumber - $ary_num[0];
		$ary_num[2] = $pnumber + $ary_num[0];
		if (!($pview%2)){$ary_num[2]--;}
		if ($ary_num[1]<1){$ary_num[2] += -$ary_num[1]+1; $ary_num[1] =1;}	//实际上必须往右偏移 -$ary_num[1]+1 位
		if ($ary_num[2]>$pcount){$ary_num[1] = max($ary_num[1]-($ary_num[2]-$pcount),1); $ary_num[2] = $pcount;}
		return array(
			"recordcount"	=> $count,
			"pagecount"		=> $pcount,
			"pagesize"		=> $psize,
			"absolutepage"	=> $pnumber,
			"previouspage"	=> max($pnumber-1,1),
			"nextpage"		=> min($pnumber+1,$pcount),
			"startpage"		=> $ary_num[1],
			"endpage"		=> $ary_num[2],
		);
	}
	
	//获取指定的Reqest串成url字符
	public final static function pagequery($name,$split=", "){
		$name 	= is_array($name) ? array_values($name) : array($name);
		$split	= ($split=trim(strval($split)))=="" ? ", " : $split;
		$sraw	= rawurlencode($split);
		$query	= array();
		foreach($name as $key=>$val){
			$val = strval($val);
			if ($val=="" || !isset($_REQUEST[$val])){continue;}
			if (!is_array($_REQUEST[$val])){ $query[$key] = $val."=".rawurlencode($_REQUEST[$val]); continue; }
			$query[$key] = rawurldecode(implode($split,$_REQUEST[$val]));
			$query[$key] = $val."=".str_replace($sraw, $split, $query[$name]);
		}
		$string = implode("&",$query);
		unset($query); $query = NULL;
		return $string;
	}
	
	//获取客户端真实IP
	public function clientip(){
		$ip = false;
		if (isset($_SERVER["HTTP_CLIENT_IP"])){$ip=$_SERVER["HTTP_CLIENT_IP"];}
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ip){ array_unshift($ips, $ip); $ip = NULL; }
			foreach($ips as $k=>$v){if (!preg_match('/^(10|172\.16|192\.168)\./i', $v)){$ip=$v; break;}}
		}
		return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
	}
	
	//amf返回=>2
	public final static function jsuri($val){
		$val = rawurlencode(strval($val));
		return str_replace(
			//array('%7E',	'%21',	'%27',	'%28',	'%29',	'%2A',	'%0D',	'%0A',	'+'),
			//array('~',	'!',	'\'',	'(',	')',	'*',	'',		'',		' '),
			array('%0D',	'%0A'),
			array("\\\\r",	"\\\\n"),
			$val
		);
	}
	
	//amf接收=>1
	public final static function json($val){
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
	
	//amf接收=>2
	public final static function object2array($args,$defkeys=NULL){
		if (!is_object($args) && !is_array($args)){
			$key	= strval($defkeys);
			$val	= is_null($args) || is_bool($args) || is_integer($args) || is_float($args) ? $args : strval($args);
			return $key=="" ? array($val) : array($key=>$val);
		}
		$result = array();
		foreach($args as $p=>$v){
			if (is_object($v) || is_array($v)){$result[$p] = self::object2array($v);}
			elseif(is_null($v)|| is_bool($v) || is_integer($v) || is_float($v)){$result[$p]=$v;}
			else{$result[$p] = strval($v);}
		}
		return $result;
	}
	
	public final static function validate_basic($name,$str,$min,$max,$rule=false,$interr=1){
		$str	= strval($str);
		$name	= strval($name);
		$min	= intval($min);
		$max	= intval($max);
		$interr = intval($interr) < 1 ? 1 : intval($interr);
		if ( $str=="" ){return (( $min === 0 ) ? array(0,"") : array($interr+=0,"{com:input_please}".$name));}
		//以下情况$str肯定不为空
		//验证长度
		if (!is_array($rule)){
			if ($min>0 && self::str_length($str)<$min){return array($interr+1,$name."{com:input_noless} ".$min." {com:input_char}");}
			if ($max>0 && self::str_length($str)>$max){return array($interr+2,$name."{com:input_nothan} ".$max." {com:input_char}");}
		}
		//验证格式
		if (is_bool($rule)){
			if ( !$rule && preg_match(self::preg_rule("html"),$str)){return array($interr+=3,$name."{com:input_nohtml}");}
		}
		elseif (is_array($rule)){
			if ( !preg_match(self::preg_rule("int"),$str)){return array($interr+=5,$name."{com:input_error}");}
			$rule[0]= isset($rule[0]) ? intval($rule[0]) : NULL;
			$rule[1]= isset($rule[1]) ? intval($rule[1]) : NULL;
			$str = intval($str);
			if ($rule[0]!==NULL && $rule[1]!==NULL){
				if ($rule[0]>$rule[1]){$temp = $rule[0]; $rule[0] = $rule[1]; $rule[1] = $rule[0];}
				elseif ($rule[0]==$rule[1] && $rule[0]!=$str){return array($interr+=5,$name."{com:input_equal} ".$rule[0]);}
			}
			if ($rule[0]!==NULL && $str<$rule[0]){return array($interr+=5, $name."{com:input_needless} ".$rule[0]);}
			if ($rule[1]!==NULL && $str>$rule[1]){return array($interr+=5, $name."{com:input_needthan} ".$rule[1]);}
		}
		else{
			$result = false;
			if ( substr($rule,0,1)!="/" || intval(strrpos($rule,"/"))<2 ){
				$ary_rule = explode("|",$rule);
				$srt_error= "";
				foreach ($ary_rule as $key=>$value){
					if ($value==""){continue;}
					$int_post = strpos($rule,":");
					if (!$int_post){$str_rtpe = ""; $str_nrule= $rule;}
					else{$str_rtpe = substr($rule,0,$int_post); $str_nrule= substr($rule,$int_post+1);}
					if ($str_rtpe=="url"){
						if ($result=self::str2url($str_nrule,$str,$srt_error)){break;}
						$srt_error = $name."输入误错，".$srt_error;							#记住最后一次错误
					}
					elseif ($str_rtpe=="datetime" || $str_rtpe=="dt"){
						$vrule	= explode(",",$str_nrule);
						if ( in_array("",$vrule) || in_array("*",$vrule) ){
							if ($reuslt=self::is_datetime($str,"")){ break; }
							$srt_error = $name."输入错误，必须为有效的日期时间格式";			#记住最后一次错误
						}
						else{
							foreach($vrule as $k=>$v){if(($reuslt=self::is_datetime($str,$v))){break 2;}}
							$srt_error = $name."输入错误，格式必须为：".implode("/",$vrule);	#记住最后一次错误
						}
						unset($vrule); $vrule=NULL;
					}
					else{
						if (substr($value,0,1)!="!"){$valida = true;} else{$valida=false; $value = substr($value,1);}
						$let = $valida ? "必须" : "不能";
						if ($value=="html"){ $srt_error = $name.$let."含有 < > \' \" & # | 等字符";}
						elseif($value=="html-"){$srt_error= $name.$let."含有 < > \' \" & | 等字符";}
						else { $srt_error = $name."input error"; }
						if ($result = (preg_match(self::preg_rule($value),$str)==$valida)){ break; }
					}
				}
				unset($ary_rule); $ary_rule = NULL;
				if (!$result){return array($interr+=5,$srt_error);}
			}
			elseif (!preg_match($rule,$str)){return array($interr+=4,$name."{com:input_error}");}
		}
		return array(0,"");
	}

	public static function destroy($var){
		if (!is_array($var)){unset($var);$var=NULL;}
		else{for($i=0;$i<sizeof($var);$i++){unset($var[$i]);$var[$i]=NULL;}}
	}
}
?>