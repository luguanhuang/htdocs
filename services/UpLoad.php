<?PHP

//****************** [Class] 上传文件（允许批量上传） ******************

require_once 'tlog.php';
//该类必须运行于 PHP5.0 及以上的平台
if ( intval(PHP_VERSION) < 5 ){ exit("<p>Run \"CLS_UpLoad\" invite promotion your PHP arrives at 5.0 the above editions</p>");}

class UpLoad{

	//__________________  构造/析构函数  ________________
	
	function __construct(){
		if (!class_exists("FuncExt")){
			$this->Int_Error = 1;
			$this->Str_Error = "缺少公用函数对象";
			return false;
		}
		
		$this->Reg_Slash	= FuncExt::preg_rule("\\/");
		$this->Str_PHPRoot	= preg_replace($this->Reg_Slash,"/",($_SERVER['DOCUMENT_ROOT']."/"));
		$this->Str_PHPName	= strtr(dirname($_SERVER['PHP_SELF']),"\\","/");
		
		$this->Bln_Enabled = true;
	}
	
	function __destruct(){
		unset($this->Ary_ErrLast);	$this->Ary_ErrLast	= NULL;
		unset($this->Ary_File);		$this->Ary_File		= NULL;
		unset($this->Ary_FileID);	$this->Ary_FileID	= NULL;
		unset($this->Ary_ErrFile);	$this->Ary_ErrFile	= NULL;
		unset($this->Ary_SaveFile);	$this->Ary_SaveFile	= NULL;
	}
	
	//__________________  私有变量  ________________
	
	const _STR_ALL_EXTEND_		= "*";
	const _INT_BYTE_CARRY_		= 1024;
	
	protected $Bln_Enabled		= false;		//对象是否可以运行
	protected $Bln_Request		= false;		//程序是否读取信息
	protected $Bln_Overflow		= false;		//保存路径溢出站点目录
	
	protected $Int_Error		= 0;			//错误代码
	protected $Str_Error		= "";			//错误描述
	protected $Ary_ErrLast		= array();		//最后发生错误的控件
	protected $Int_UpSum		= 0;			//上传成功的文件数
	
	protected $Ary_FileID		= array();		//所有上传的控件的信息(不用于返回)
	protected $Ary_File			= array();		//所有上传的控件
	protected $Ary_ErrFile		= array();		//所有上传错误的信息
	protected $Ary_SaveFile		= array();		//所有上传成功的信息
	
	protected $Int_UpHit		= -1;			//上传成功的计数(不用于返回)
	protected $Int_UpErr		= -1;			//上传失败的计数(不用于返回)
	
	protected $Reg_Slash		= NULL;
	protected $Str_PHPRoot		= "";
	protected $Str_PHPName		= "";
	
	protected $Str_Extend		= "";			//最终确定的所有可上传的文件格式
	protected $Int_MaxBits		= 0;			//最终确定可上传的最大文件大小
	
	
	//__________________  只读属性(用方法代替)  ________________
	
	public function _version()		{return '3.0';}					//版本
	public function _build()		{return '11.12.02';}			//版本
	public function _create()		{return '09.04.09';}			//创建
	public function _classname()	{return "PHP_UPLOAD";}			//名称
	public function _developer()	{return "OldFour";}				//开发者
	public function _copyright()	{return "Aether";}				//公司
	
	public function _enabled()		{return $this->Bln_Enabled;}
	public function _error()		{return $this->Int_Error;}		//错误代码
	public function _errtext()		{return $this->Str_Error;}		//错误描述
	
	public function _errlast()		{return $this->Ary_ErrLast;}	//最后发生错误的文件序号
	public function _count()		{return $this->Int_UpSum;}		//request得到的可上传文件数
	public function _files()		{return $this->Ary_File;}		//上传的文件的信息
	
	public function _fileerr($i=-1)	{return $this->return_upresult(1,$i);}	//文件保存失败返回的参数
	public function _filesave($i=-1){return $this->return_upresult(0,$i);}	//文件保存成功的参数
	
	public function _extend()		{return $this->Str_Extend;}
	public function _maxbits()		{return $this->Int_MaxBits;}
	
	/*
	_fileerr() :
		返回数组，每个元素为数组，表示一个上传失败的文件的参数，使用文字索引，分别为"id"=控件名称,"index"=控件下标,"filename"=原文件名,"error"=错误代码，"errtext"=错误描述；
	_filesave()
		返回数组，每个元素为数组，表示一个上传成功的文件的参数，使用文字索引，分别为："id"=控件名称,"index"=控件下标,"filename"=原文件名,"url"=全路径,"realpath"=保存的物理路径,"linkpath"=保存的虚拟路径(等于"url"),"realdir"=保存的物理目录,"linkdir"=保存的虚拟目录,"name"=保存的文件名,"extend"=扩展名,"width"=宽度,"height"=高度；
	*/
	
	//__________________  只写属性(用方法代替)  ________________
	
	
	//__________________  可读写属性  ________________
	
	public $overflow	= 0;					//是否允许保存目录跨域
	public $saveauto	= 1;					//自动命名（1:自动命名;2:手动命名;3:使用原文件名）
	public $savepath	= "";					//保存路径;
	public $savename	= "";					//手动自动命名时的文件名;
	public $overwrite	= false;				//是否覆盖源文件
	public $maxbits		= NULL;					//最大尺寸(单位k)
	public $type		= 'image';				//上传的类型(根据该属性确定上传文件的基本格式)
	public $appendext	= '';					//为支持的基本格式附加的文件格式
	public $filterext	= '';					//对所有支持上传的文件过滤的格式
	public $batch		= 0;					//是否批量上传(<1:批量上传;>=1:表示上传单文件;)
	public $maxsize		= "";					//显示的最大尺寸
	
	//__________________  私有方法  ________________
	
	//验证扩展名是否合法
	protected function check_extend($str_type, $str_name, &$str_err){
		$str_mimtype= strtolower($str_type);
		$str_extend	= strpos($str_name,".")!==false ? strtolower(substr(strrchr($str_name,"."),1)) : "";
		//验证文件类型确认扩展名
		$ary_allow	= array();
		switch($str_mimtype){
			case "application/octet-stream":		break;										//任意的扩展名
			case "application/x-x509-ca-cert":		$ary_allow	= array("cer","crt");	break;	//cer/crt
			case "application/vnd.ms-powerpoint":	$ary_allow	= array("ppt","pps");	break;	//ppt/pps
			case "text/plain":						$ary_allow	= array("txt","key","ini","inf");		break;	//txt/key/ini/inf
			case "text/html":						$ary_allow	= array("htm","html","shtm","shtml");	break;	//htm/html/shtm/shtml
			case "video/mpeg":						$ary_allow	= array("mpeg","mp3","vob","dat");		break;	//mpeg/mp3/vob/dat
			case "application/x-javascript":		$ary_allow	= array("js");	break;
			case "text/xml":						$ary_allow	= array("xml");	break;
			case "application/vnd.ms-excel":		$ary_allow	= array("xls");	break;
			case "application/msword":				$ary_allow	= array("doc");	break;
			case "application/vnd.ms-powerpoint":	$ary_allow	= array("ppt");	break;
			case "application/x-msaccess":			$ary_allow	= array("mdb");	break;
			case "application/x-msdownload":		$ary_allow	= array("exe");	break;
			case "application/pdf":					$ary_allow	= array("pdf");	break;
			case "text/css":						$ary_allow	= array("css");	break;
			case "application/x-rar-compressed":	$ary_allow	= array("rar");	break;
			case "application/zip":
			case "application/x-zip-compressed": 	$ary_allow	= array("zip");	break;
			case "image/gif":						$ary_allow	= array("gif");	break;
			case "image/pjpeg":;
			case "image/jpeg":;
			case "image/jpg":						$ary_allow	= array("jpg");	break;
			case "image/bmp":						$ary_allow	= array("bmp");	break;
			case "image/x-png":						$ary_allow	= array("png");	break;
			case "video/avi":						$ary_allow	= array("avi");	break;
			case "video/x-ms-wmv":					$ary_allow	= array("wmv");	break;
			case "audio/wav":						$ary_allow	= array("wav");	break;
			case "video/avi":						$ary_allow	= array("avi");	break;
			case "video/x-ms-asf":					$ary_allow	= array("asf");	break;
			case "application/x-shockwave-flash":	$ary_allow	= array("swf");	break;
			default:								$ary_allow	= NULL;
		}
		//以下格式的文件可能会返回"application/octet-stream"：
		/*
		asp/aspx/jsp/php/iso/nrg/bat/com/mdb/psd/flv/pdg/exe/rar/rmvb/rm/shtm/ini/inc/inf
		*/
		//未知的mime-type
		if (is_null($ary_allow)){
			$str_err = "不允许上传 ". $str_mimtype ." 类型的文件"; return false;
		}
		else{
			//扩展名与mime_type不匹配
			if ($ary_allow && !in_array($str_extend,$ary_allow)){
				$str_err = "文件扩展名与 ". $str_mimtype ." 类型不匹配"; return false;
			}
			else{
				//被过滤的
				if ( strpos("/".$this->filterext."/" , "/$str_extend/")!==false ){
					$str_err = "不允许上传 ". $str_mimtype ." 类型的文件"; return false;
				}
				//不允许任意格式的
				elseif ( $this->Str_Extend != self::_STR_ALL_EXTEND_ ){
					//不在支持的格式中的
					if ( strpos("/".$this->Str_Extend."/" , "/$str_extend/")===false){
						$str_err = "不允许上传 ". $str_mimtype ." 类型的文件"; return false;
					}
					//不存在扩展名
					elseif ( $str_extend =="" ){
						$str_err = "无法识别文件的类型"; return false;
					}
				}
			}
		}
		return $str_extend;
	}
	
	
	//创建保存的文件名
	protected function create_filename($int_uporder,$str_name=""){
		$int_uporder	= intval($int_uporder);
		$int_saveauto	= intval($this->saveauto);
		$bln_nameauto	= $int_saveauto == 1 ? true : false;
		
		if ($int_saveauto==1 || $int_saveauto==2){	//指定的文件名(当$int_saveauto==1时，$str_name是作为自动命名的前缀)
			$str_name = trim($this->savename);
		}
		else{										//使用原文件名的
			$int_point = strrpos($str_name,".");
			if ($int_point!==false){$str_name = trim(substr($str_name,0,$int_point));}
		}
		//无论任何情况，当$str_name为空，则采用自动命名。
		if ($str_name==""){$bln_nameauto = true;}
		//无论任何清空，当$str_name不合法，则采用自动命名，且清空$str_name。
		elseif (preg_match("/[\/\\\\\<\|\>\'\"\&\#\?\:\*]$/",$str_name)){ $str_name=""; $bln_nameauto = true; }
		
		if ($bln_nameauto){
			$ary_de	= getdate();
			$str_sec = "00000" . ($ary_de["hours"] * 3600 + $ary_de["minutes"] * 60 + $ary_de["seconds"]);
			$str_sec = substr($str_sec,strlen($str_sec)-5);
			
			$str_msec = microtime();
			$str_msec = "000" . intval(number_format($str_msec,3,".","")*1000);
			$str_msec = substr($str_msec,strlen($str_msec)-3);
			
			unset($ary_de);
			return $str_name . date("Ymd") . $str_sec . $str_msec;
		}
		else{
			if ($int_saveauto==2){
				if ($int_uporder<1){return $str_name;}
				else{return $str_name.strval($int_uporder);}
				}
			elseif ($int_saveauto==3){
				return $str_name;
			}
		}
	}
	//转换为标准虚拟路径(转换后格式为 /****/*** )
	public function linkpath($path="./"){
		$path = trim(strtolower($path));													//转换为小写字符并去掉前后空格
		if ($path==""){return preg_replace($this->Reg_Slash,"/",$this->Str_PHPName."/");}	//如果为空返回当前目录
		$path = preg_replace($this->Reg_Slash,"/",$path);									//多个/或\转换为单个/
		if ($path==""){return "/";}															//如果为空一定是目录，返回根目录
		if (substr($path,0,1)=="/" && strpos($path,"./")===false){
			if (substr($path,-1)!="/"){$path.="/";}
			return $path;
		}
		//echo($path."<br>");
		//-----------------------------------------------------------------------------------------------------
		$str_root = strtolower($this->Str_PHPRoot);									//站点根目录物理地址,统一为以\结束
		$str_dirs = $str_root;														//起始路径为根目录物理地址
		//如果参数不是以"/"开头(则非根目录的形式)时，起始路径全部加上当前路径
		if (substr($path,0,1)!="/"){$str_dirs .= str_replace("\\","/",substr($this->Str_PHPName,1));}
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
		$str_dirs = implode("/",$ary_dirs)."/";										//合并数组，最后一个字符是"/"
		unset($ary_dirs);	$ary_dirs = NULL;
		unset($ary_path);	$ary_path = NULL;
		if (strpos($str_dirs,$str_root)!==0){return "";}						//如果超过站点根目录
		$str_dirs = substr($str_dirs,strlen($str_root)-1);						//去掉站点根目录物理地址部分
		return $str_dirs;
	}
	//创建目录
	protected function create_dir($folder){
	    TLOG_MSG("create_dir: func begin folder=".$folder);
		$folder = strval($folder);
		$incept = "";
		TLOG_MSG("create_dir: func begin 111 folder=".$folder);
		if (preg_match("/^(file\:\/\/\/)(([a-z]\:)[\/\\\\])*/i",$folder,$match)){
			$folder	= strtr($folder,"\\","/");
			$folder = substr($folder,strlen($match[1]));
			$iswin	= isset($match[3]) && $match[3]!="" ? $match[3] : "";
			unset($match); $match = NULL;
			//win32下的根目录
			if ($iswin!=""){
				$incept = $iswin."/";
				$folder = substr($folder,strlen($iswin)); //以"/"开头
			}
			//linux下的根目录
			elseif (substr($folder,0,1)=="/"){
				$incept = "/";
			}
			//非根目录的
			else{
				$int_post	= strpos($this->Str_PHPName, "/");
				$incept		= substr($this->Str_PHPName, 0, $int_post+1);
				$folder		= substr($str_app,$int_post)."/".$folder;
			}
		}
		else{
			$incept = $this->Str_PHPRoot;
		}
		
		TLOG_MSG("create_dir: func begin 222 folder=".$folder);
		//格式化：
		//$folder = $this->linkpath($folder,false);
		//if ($folder==""){return NULL;}
		TLOG_MSG("create_dir: func begin 222.1 folder=".$folder);
		//分解
		$ary_folder = array_slice(explode("/",$folder),1,-1);
		TLOG_MSG("create_dir: func begin 222.2 folder=".$folder);
		$str_folder	= $incept;
		$int_errrep	= ini_get('error_reporting');
		TLOG_MSG("create_dir: func begin 333 folder=".$folder);
		//创建
		error_reporting(0);
		foreach($ary_folder as $k=>$v)
		{
			if (!is_dir($str_folder.$v))
			{
			    TLOG_MSG("create_dir: func begin 333.0 folder=".$folder." str_folder=".$str_folder." v=".$str_folder.$v);
				if (!is_writeable($str_folder) || !mkdir($str_folder.$v))
				{
				    TLOG_MSG("create_dir: func begin 333.1 folder=".$folder);
					$str_folder = NULL;
					break;
				}
			}
			$str_folder = $str_folder.$v."/";
		}
		error_reporting($int_seting);
		if (!isset($str_folder))
		{ 
		    TLOG_MSG("create_dir: func begin 333.2 folder=".$folder);
		    return false; 
		}
		//目录是否溢出
		$this->Bln_Overflow = stripos($str_folder,$this->Str_PHPRoot)!==0;
		TLOG_MSG("create_dir: func begin 444 folder=".$folder);
		return array($str_folder,$folder);
	}
	
	protected function return_upresult($type,$i){
		$type	= intval($type);
		$ary_db = $type==1 ? $this->Ary_ErrFile : $this->Ary_SaveFile;
		$i = !is_numeric($i) ? -1 : intval($i);
		if ($i<0){return $ary_db;}
		elseif (!$ary_db){return "";}
		if ($i>=sizeof($ary_db)){$i=sizeof($ary_db)-1;}
		return $ary_db[$i];
	}
	
	//__________________  公有方法  ________________
	
	//读取要上传的文件信息
		//如果指定参数$file则表示读取名称等于$file的File控件
	public function request($file=""){
	    //TLOG_MSG("request: func begin name=".$_FILES['file']['name'] );
		if (!$this->Bln_Enabled)
		{
		    TLOG_MSG("request: func begin 111");
		    return;
		}
		
		$file = trim($file);
		if ($file==""){
		    TLOG_MSG("request: func begin 222");
			$ary_up	= $_FILES;
		}
		else{
		    
			if ( !is_array($file) ){ $file = array($file); }
			$ary_up = array();
			$int_up = sizeof($file);
			TLOG_MSG("request: func begin 333 int_up=".$int_up);
			for ($i=0;$i<$int_up;$i++)
			{
			    TLOG_MSG("request: func begin 333.1 int_up=".$int_up);
				if ( array_key_exists($file[$i],$_FILES)!==false )
				{
				    TLOG_MSG("request: func begin 333.2 int_up=".$int_up);
				    $ary_up[$file[$i]] = $_FILES[$file[$i]]; 
				}
			}
		}
		TLOG_MSG("request: func begin 444");
		//php.ini 的 upload_max_filesize 造成直接影响
		if (!$ary_up){
		    TLOG_MSG("request: func begin 555");
			$this->Int_Error = 10;
			$this->Str_Error = "没有FILE控件或上传的文件可能超过系统配置规定的大小";
			return false;
		}

		//读出所有file控件信息
		$this->Int_UpSum = 0;
		foreach ($ary_up as $key => $value){
		    TLOG_MSG("request: func begin 666");
			//如果同名空间存在多个
			if (is_array($ary_up[$key]["tmp_name"])){
				for($i=0;$i<count($ary_up[$key]["tmp_name"]);$i++){
					$this->Ary_File[$this->Int_UpSum] = array(
						"name"		=> $ary_up[$key]["name"][$i],
						"type"		=> $ary_up[$key]["type"][$i],
						"tmp_name"	=> $ary_up[$key]["tmp_name"][$i],
						"size"		=> $ary_up[$key]["size"][$i],
						"error"		=> $ary_up[$key]["error"][$i],
					);
					$this->Ary_FileID[$this->Int_UpSum] = array($key,$i);	//记住$key和对应的$i
					$this->Int_UpSum++;
				}
			}
			else{
				$this->Ary_File[$this->Int_UpSum] = array(
					"name" 		=> $ary_up[$key]["name"],
					"type" 		=> $ary_up[$key]["type"],
					"tmp_name" 	=> $ary_up[$key]["tmp_name"],
					"size" 		=> $ary_up[$key]["size"],
					"error" 	=> $ary_up[$key]["error"],
				);
				$this->Ary_FileID[$this->Int_UpSum] = array($key,-1);
				$this->Int_UpSum++;
			}
		}
		unset($ary_up);
		//原数组$ary_up可删除
		
		$this->Bln_Request = true;
		TLOG_MSG("request: func begin 777");
	}
	
	public function save(){
	    TLOG_MSG("save: func begin 111");
		if (!$this->Bln_Request){return false;}
		$int_upsum	= 0;
		$int_batch	= intval($this->batch)-1;

		//批量上传或只上传Request出来的其中一个
		if ($int_batch<0){$int_upsum = sizeof($this->Ary_File);}
		elseif(isset($this->Ary_File[$int_batch]) && is_uploaded_file($this->Ary_File[$int_batch]["tmp_name"])){$int_upsum=1;}
		if (!$int_upsum){
			$this->Int_Error = 20;
			$this->Str_Error = "没有可上传的文件或文件已经保存过";
			return false;
		}
		TLOG_MSG("save: func begin 222");
		//自动保存
		$this->saveauto = FuncExt::number_between($this->saveauto,1,3);
		
		//保存的文件名
		$this->savename = trim($this->savename);
		if ($this->savename!="" && !preg_match(FuncExt::preg_rule("file"),$this->savename)){
			$this->Int_Error = 21;
			$this->Str_Error = "指定保存的文件名不正确";	//
			return false;
		}
		TLOG_MSG("save: func begin 333");
		//指定保存的目录
		$this->savepath = trim($this->savepath);
		//$str_savepath	= strtolower($this->savepath);
		$str_savepath	= $this->savepath;
		$str_readproto	= "";
		if ($str_savepath == ""){
			$str_savepath = "/upfile/". date("Ymd") ."/";
		}
		else{
			if (preg_match("/^file\:\/\/\/([a-z]\:[\/\\\\])*/i",$str_savepath,$match)){
				$str_readproto	= $match[0];
				$str_savepath	= substr($str_savepath,strlen($match[0]));
				unset($match); $match = NULL;
			}
			else{
				if(strtolower(substr($str_savepath,0,7))=="http://"){
					$str_savepath	= "/".preg_replace("/(^http\:\/\/[^\/]+)/i","",$str_savepath);
				}
				$str_savepath	= preg_replace($this->Reg_Slash, "/", $str_savepath."/");//格式化保存目录
			}
		}
		
		TLOG_MSG("save: func begin 444");
		$this->savepath = $str_savepath;	//将格式化的结果返回给"savepath"属性

		if (preg_match(FuncExt::preg_rule("url"),$str_savepath) == false){
		    TLOG_MSG("save: func begin 444.1");
			$this->Int_Error = 22;
			$this->Str_Error = "指定的保存目录路径不正确";
			return false;
		}
		$ary_path = $this->create_dir($str_readproto.$str_savepath);
		if (!is_array($ary_path)){
		    TLOG_MSG("save: func begin 444.2 str_readproto=".$str_readproto." str_savepath=".$str_savepath." savepath=".$this->savepath);
			$this->Int_Error = 23;
			$this->Str_Error = "创建保存目录失败，失败位于 $str_savepath";
			return false;
		}
		$str_realpath	= $ary_path[0];		//保存目录的物理路径
		$str_savepath	= $ary_path[1];		//保存目录的虚拟路径
		TLOG_MSG("save: func begin 555");
		//目录溢出或规定检查是否有执行权限时
		if ( $this->Bln_Overflow && !$this->overflow){
		    TLOG_MSG("save: func begin 444.3");
			$this->Int_Error = 24;
			$this->Str_Error = "保存目录不在站点下，当前完全设置不允许上传到该目录";
			return false;
		}
		TLOG_MSG("save: func begin 666");
		//获取默认的支持的扩展名和默认的最大尺寸
		//=="*"时表示可上传任意文件;
		//默认的最大尺寸暂时以M为单位;
		$this->type = strtolower(trim($this->type));
		switch($this->type){
			case ""		: ;
			case "*"	: $this->Str_Extend = "*";								$this->Int_MaxBits = 50;	break;
			case "file" : $this->Str_Extend = "rar/zip/tar/7z/iso/exe/txt";		$this->Int_MaxBits = 10;	break;
			case "flash": $this->Str_Extend = "swf/flv";						$this->Int_MaxBits = 10;	break;
			case "real"	: $this->Str_Extend = "rm/ra/rmvb";						$this->Int_MaxBits = 50;	break;
			case "media": $this->Str_Extend = "avi/asf/wmv/wma/wav/mpeg/mp3";	$this->Int_MaxBits = 50;	break;
			case "multi": $this->Str_Extend = "avi/asf/wmv/wma/wav/mpeg/mp3/rm/ra/rmvb/swf/flv"; $this->Int_MaxBits = 50; break;
			case "avp"  : $this->Str_Extend = "avi/asf/wmv/wma/wav/mpeg/mp3/rm/ra/rmvb/swf/flv/gif/jpeg/jpg/png"; $this->Int_MaxBits = 50; break;
			case "doc"	: $this->Str_Extend = "doc/xls/ppt/pps/txt/pdf/pdg";	$this->Int_MaxBits = 10;	break;
			default		: $this->Str_Extend = "gif/jpeg/jpg/png/bmp";  $this->Int_MaxBits = 0.1;	$this->type = "image";
		}
		$str_extend = $this->Str_Extend;
		//附加的扩展名
		$this->appendext = strtolower(trim($this->appendext));		//转为小写
		if ($this->appendext!=""){
			$this->appendext = preg_replace($this->Reg_Slash,"/",$this->appendext);
			$this->appendext = preg_replace("/(\/(\*)?\.)/","/",$this->appendext);
			//等于空或/视为无效
			if ($this->appendext!="/"){
				if ($this->Str_Extend!=self::_STR_ALL_EXTEND_){
					$ary_ext1 = array_flip(explode("/",$this->appendext));		//打散->对置
					$ary_ext2 = array_flip(explode("/",$this->Str_Extend));
					$array_ext = array_keys($ary_ext1+$ary_ext2);				//合并(保留前面的)->取键
					$this->Str_Extend = implode("/",$array_ext);
					unset($ary_ext1); $ary_ext1 = NULL;
					unset($ary_ext2); $ary_ext2 = NULL;
					unset($array_ext); $array_ext = NULL;
				}
				else{
					$this->Str_Extend = $this->appendext;
				}
			}
		}
		
		TLOG_MSG("save: func begin 777");
		//要过滤的扩展名
		$this->filterext = strtolower(trim($this->filterext));		//转为小写
		if ($this->filterext!=''){
			$this->filterext = preg_replace($this->Reg_Slash,"/",$this->filterext);
			$this->filterext = preg_replace("/(\/(\*)?\.)/","/",$this->filterext);
		}
		
		//最大尺寸(null是没有设置,<=0是不限制大小)
		if (isset($this->maxbits)){$this->Int_MaxBits = intval($this->maxbits);}
		else{$this->Int_MaxBits = $this->Int_MaxBits * self::_INT_BYTE_CARRY_;}
		
		//初始化
		TLOG_MSG("save: func begin 888");
		//创建副本（只是复制文件信息，不复制文件内容）
		if ($int_batch>=0){
			$ary_file	= array($this->Ary_File[$int_batch]);
			$ary_fileid	= array($this->Ary_FileID[$int_batch]);
		}
		else{
			$ary_file	= $this->Ary_File;
			$ary_fileid	= $this->Ary_FileID;
		}
		TLOG_MSG("save: func begin 999");
		//重置下标
		$this->Int_UpHit = -1;
		$this->Int_UpErr = -1;
		for ($i=0;$i<count($ary_file);$i++){
			$bln_up = true;
			//检查尺寸
			if ( $this->Int_MaxBits >0 && $ary_file[$i]["size"] > $this->Int_MaxBits * 1024 ){
				$this->Int_Error = 25;
				$this->Str_Error = "文件超过上传允许的最大尺寸";
				$bln_up = false;
			}
			//检查扩展名
			else{
				$str_extend = $this->check_extend($ary_file[$i]["type"],$ary_file[$i]["name"], $str_error);
				if ( !$str_extend ){
					$this->Int_Error = 26;
					$this->Str_Error = $str_error;
					$bln_up = false;
				}
				else{
					if ($str_extend!=""){ $str_extend = ".".$str_extend; }
					//生成保存的文件名
					$str_savefile = $this->create_filename($i,$ary_file[$i]["name"]);
					//如果不覆盖保存
					if (!$this->overwrite && file_exists($str_realpath.$str_savefile.$str_extend)){
						$this->Int_Error = 27;
						$this->Str_Error = "相同名称的文件已经存在";
						$bln_up = false;
					}
				}
			}
			
			TLOG_MSG("save: func begin 000");
			//开始上传文件
			if ($bln_up){
				$str_save0	= $str_realpath.$str_savefile.$str_extend;				//php接收到的编码
				$str_save1	= iconv("utf-8","gbk//IGNORE",$str_save0);				//转换为gbk(如果源码不是gbk可能就乱码)
				$str_save2	= iconv("gbk","utf-8//IGNORE",$str_save1);				//再转会utf-8(如果源码不是utf-8也可能乱码) 					
				$str_savef	= $str_save0!==$str_save2 ? $str_save0 : $str_save1;	//不相等说明编码不是utf-8的
				$result = @move_uploaded_file($ary_file[$i]["tmp_name"],$str_savef);
				if (!$result){
					$this->Int_Error = 28;
					$this->Str_Error = "move_uploaded_file 保存文件失败";
					$bln_up = false;
				}
				else{
					$ary_info = array(-1,-1);
					if (function_exists("getimagesize")){
						if ( strstr("/gif/jpg/png/bmp/psd/tiff/swf/" , "/". substr($str_extend,1) ."/") !== false){
							$ary_info	= @getimagesize($str_savef);
							$str_maxsize= is_array($this->maxsize) ? $this->maxsize : trim($this->maxsize);
							if( $ary_info && $str_maxsize ){
								$ary_info=$this->thumbsize(array($ary_info[0],$ary_info[1]),$str_maxsize);
							}
						}
					}
					$this->Int_UpHit++;
					$this->Ary_SaveFile[$this->Int_UpHit] = array(
						"id"		=>	$ary_fileid[$i][0],
						"index"		=>	$ary_fileid[$i][1],
						"size"		=>	number_format($ary_file[$i]["size"]/1024,3,".",","),
						"filename"	=>	$ary_file[$i]["name"],	//name:包括扩展名
						"realpath"	=>	$str_realpath.$str_savefile.$str_extend,
						"linkpath"	=>	$str_savepath.$str_savefile.$str_extend,
						"url"		=>	$str_savepath.$str_savefile.$str_extend,
						"realdir"	=>	$str_realpath,
						"linkdir"	=>	$str_savepath,
						"name"		=>	$str_savefile,			//name:不包括扩展名
						"extend"	=>	substr($str_extend,1),	//extend:不包含"点"
						"type"		=>	$ary_file[$i]["type"],
						"width"		=>	$ary_info[0],
						"height"	=>	$ary_info[1]
					);
					continue;
				}
			}
			if (!$bln_up){
				$int_lasti = $i;
				$this->Int_UpErr++;
				$this->Ary_ErrLast = $ary_fileid[$int_lasti];
				$this->Ary_ErrFile[$this->Int_UpErr] = array(
					"id"		=>	$ary_fileid[$int_lasti][0],
					"index"		=>	$ary_fileid[$int_lasti][1],
					"filename"	=>	$ary_file[$int_lasti]["name"],
					"error"		=>	$this->Int_Error,
					"errtext"	=>	$this->Str_Error
				);
				$this->Int_Error = 0;	//重置错误
				$this->Str_Error = "";
			}
		}
		
		TLOG_MSG("save: func begin 1000");
		//全部保存成功;
		if (intval($this->Int_UpErr)<0){$this->Int_Error = -1;$this->Str_Error = "所有文件上传都成功";}
		//部分保存成功;
		elseif (intval($this->Int_UpHit)>=0){$this->Int_Error = -2;$this->Str_Error = "文件上传完成";}
		//全部保存失败;
		else{$this->Int_Error = 0; $this->Str_Error = "所有文件上传都失败";}
		unset($ary_info);	$ary_info	= NULL;
		unset($ary_file);	$ary_file	= NULL;
		unset($ary_fileid);	$ary_fileid	= NULL;
		return $this->Int_Error>=0 ? false : true;
	}
	
	public function thumbsize($size,$normal){
		if (!is_array($normal)){$normal = explode("*",preg_replace("/[\,\|\/\-\ ]/","*",$normal));}
		$normal[0] = isset($normal[0]) ? max(intval($normal[0]),0) : 0;
		$normal[1] = isset($normal[1]) ? max(intval($normal[1]),0) : 0;
		if (!is_array($size)){$size = explode("*",preg_replace("/[\,\|\/\-\ ]/","*",$size));}
		$size[0] = isset($size[0]) ? max(intval($size[0]),0) : 0;
		$size[1] = isset($size[1]) ? max(intval($size[1]),0) : 0;
		if ( ($normal[0]<=0 && $normal[1]<=0) || ($size[0]<=0 || $size[1]<=0) ){$int_w=0;$int_h=0;}
		else{
			$int_caclew = $normal[0] ? $size[0]/$normal[0] : 0;
			$int_cacleh = $normal[1] ? $size[1]/$normal[1] : 0;
			$int_cacle	= max($int_caclew,$int_cacleh);
			if ($int_cacle>1){$int_w=$size[0]/$int_cacle; $int_h=$size[1]/$int_cacle;}
			else{$int_w = $size[0]; $int_h = $size[1];}
		}
		unset($normal);$normal = NULL;
		unset($size);$size = NULL;
		return array($int_w,$int_h,'width'=>$int_w,'height'=>$int_h);
	}
	//关闭对象
	public function close(){}
}
?>