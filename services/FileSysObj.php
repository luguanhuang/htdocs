<?PHP

//****************** [Class] FSO对象 ******************

//该类必须运行于 PHP5.0 及以上的平台
if ( intval(PHP_VERSION) < 5 ){ exit("<p>Run \"FileSysObj\" invite promotion your PHP arrives at 5.0 the above editions</p>");}

/*
对象中使用的地址说明：
1.	/开头表示相对于站点根目录地址
2.	../表示相对于当前网址URL的相对地址（而不是相对于当前文件的相对地址）
3. 不支持虚拟目录！！！！！！！！
*/

class FileSysObj{

	//__________________  构造/析构函数  ________________
	
	function __construct(){	
		$this->Str_PHPName	= dirname($_SERVER['PHP_SELF']);
		$this->Str_PHPRoot	= preg_replace($this->Reg_Slash,"/",($_SERVER['DOCUMENT_ROOT']."/"));
		$this->Bln_Enabled = true;
	}
	
	function __destruct(){}

	//__________________  私有变量  ________________
	
	protected static $Str_DirLink;
	protected static $Str_DirReal;
	protected static $Reg_FtTrue;
	protected static $Reg_FtFlase;
	
	protected $Bln_Enabled	= false;
	protected $Int_Error	= 0;
	protected $Str_Error	= "";

	protected $Reg_Regexp	= "/([\^\\\$\*\?\!\|\<\>\(\)\[\]\{\}\\\\\/\-\+\=\,\.\;])/";
	protected $Reg_Slash	= "/[\/\\\\]+/";
	protected $Reg_Drive	= "/^[a-z]\:/i";
	protected $Reg_Proto	= "/^file\:[\\\\\/]{3}/i";
	protected $Reg_Name		= "/^[^\/\\\\\<\|\>\'\"\&\#\?\:\*]+\$/";
	protected $Reg_Write	= "/([\\\\\\\$])/";
	
	protected $Str_PHPName	= "";
	protected $Str_PHPRoot	= "";
	
	protected $Ary_Extend	= array(
		"txt","htm","html","shtm","shtml","asp","jsc","apsx","php","jsp","js","vbs","log","ini","inc","inf","xml","lng"
	);
	
	//__________________  只读属性(用方法代替)  ________________
	
	public function _version()		{return '2.0';}					//版本
	public function _build()		{return '09.11.25';}			//版本
	public function _create()		{return '09.05.12';}			//创建
	public function _classname()	{return __CLASS__;}				//名称
	public function _developer()	{return "OldFour";}				//开发者
	public function _copyright()	{return "ODFBBS.CORP";}			//公司
	
	public function _enabled()	{return $this->Bln_Enabled;}
	public function _error()	{return $this->Int_Error;}
	public function _errtext()	{return $this->Str_Error;}
	
	//__________________  只写属性  ________________
	
	
	//__________________  可读写属性  ________________
	
	
	//__________________  私有方法  ________________
	
	//返回错误
	protected function seterror($interr,$strerr){
		$this->Int_Error = intval($interr);
		$this->Str_Error = trim($strerr);
		return false;
	}
	
	//清除空白行
	protected function clearblankline($str=""){return preg_replace("/(^[\r\n]*)|([\r\n]$)/i","",strval($str));}
	//匹配路径
	protected function matchpath($path="",$issafe=false){
		if ((trim($path))==""){return "";}
		if (preg_match($this->Reg_Proto,$path)){$path = preg_replace($this->Reg_Proto,"",$path);}
		elseif (!preg_match($this->Reg_Drive,$path)){$path = $this->realpath($path);}
		return !file_exists($path) ? "" : $path;
	}
	//格式化匹配
	//使用,或空格作为分割符
	//使用通配符*?
	protected function matchwild($str){
		//如果是数组则合并
		if (is_array($str)){$str=implode(",",$str);}
		else{$str=str_replace(" ",",",trim($str));}
		if ($str==""){return "";}
		//将正则字符（除了,*?都转换掉）
		$str = preg_replace("/([\^\\$\!\|\<\>\(\)\[\]\{\}\/\-\+\=\;\.\\\\])/","\\\\\\1",$str);
		//去掉连续*,并将\,转换为**
		$str = preg_replace("/\*+/","*",$str);
		$str = str_replace("\\\\,","**",$str);
		//去掉连续,及前后的,
		$str = preg_replace("/(^\,+)|(\,$)/","",$str);
		$str = preg_replace("/\,+/",",",$str);
		if ($str==""){return "";}
		//转换分隔符为")|("
		$str = preg_replace("/[\,]/",")|(",$str);
		//还原,/转换*?
		$str = str_replace("**",",",$str);
		$str = str_replace("?",".?",$str);
		$str = str_replace("*",".*",$str);
		return "/^(".$str.")$/is";
	}
	//递归获取大小
	protected function foldersize($folder,$isrec=false){
		$folder = trim($folder);
		$isrec= (boolean)$isrec;
		static $bln_openrec;
		if (!$isrec){
			$folder=$this->realpath($folder,true);
			if($folder=="" || !is_dir($folder)){return NULL;}else{$bln_openrec=true;}
		}
		elseif (!$bln_openrec){ return NULL; }
		$dh = @opendir($folder);
		if (!$dh){ if (!$isrec){$bln_openrec = false;} return NULL; }
		$int_size = 0;
		while( $element = readdir($dh) ){
			if ($element=="." || $element=="..") {continue;}
			$str_path = $folder."/".$element;
			if (is_file($str_path)){$int_size+=filesize($str_path);}
			else{$int_size+= intval($this->foldersize($str_path,true));}//递归调用时一定要注明第二个参数为true，表示为递归。
		}
		closedir($dh);
		if (!$isrec){$bln_openrec = false;}
		return $int_size;
	}
	//递归复制文件(复制时是打开$folder，将其中的子目录及文件复制到$target目录下)
	protected function foldercopy($folder,$target,$isrec=false){
		$folder = trim($folder);
		$target = trim($target);
		$isrec	= (boolean)$isrec;
		static $bln_copyrec;
		if (!$isrec){
			$linkfd	= $this->linkpath($folder,false);
			$folder	= $this->Str_PHPRoot.substr($linkfd,1);
			if ($linkfd=="" || !is_dir($folder)){return NULL;}						//越界+不存在
			
			$linktg	= $target =="" ? $linkfd : $this->linkpath($target,false);
			$target	= $this->Str_PHPRoot.substr($linktg,1);
			if ($linktg==""){return $this->seterror(33,"{fso:copy_err_target_notext}");}	//越界
			
			if ( strpos(strtolower($folder),strtolower($target))===0 && strlen($target)>strlen($folder)){
				return $this->seterror(34,"{fso:copy_err_target_insour}");
			}
			$basename	= basename($target);
			$dirname	= preg_replace($this->Reg_Slash,"/",dirname($target)."/");
			while(file_exists($dirname.$basename)){$basename = "copy ".$basename; }
			$target	= $dirname.$basename."/";
			$linktg	= preg_replace($this->Reg_Slash,"/",dirname($linktg)."/").$basename."/";
			$bln_copyrec = true;
		}
		elseif (!$bln_copyrec){ return NULL; }
		//创建目录
		if (!is_dir($target) && !@mkdir($target)){
			if ($isrec){return false;}
			$bln_copyrec = false;
			return $this->seterror(35,"{fso:copy_err_mkdir_fail}");
		}
		$dh = @opendir($folder);
		if (!$dh){ if (!$isrec){$bln_copyrec = false;} return NULL; }
		$int_uncopy = 0;
		while( $element = readdir($dh) ){
			if ($element=="." || $element=="..") {continue;}
			$str_path = $folder."/".$element;
			//递归调用时一定要注明第二个参数为true，表示为递归
			if (is_dir($str_path)){$bln_copy = $this->foldercopy($str_path,$target.$element."/",true);}
			else{$bln_copy = @copy($str_path,$target.$element);}
			if (!$bln_copy ){++$int_uncopy;}
		}
		closedir($dh);
		if (!$int_uncopy){return false;}
		if ($isrec){return true;}
		$bln_copyrec = false;
		return $linktg;
	}
	//递归删除文件
	protected function folderdel($folder,$isrec=false){
		$folder = trim($folder);
		$isrec	= (boolean)$isrec;
		static $bln_delrec;
		if (!$isrec){
			$folder=$this->realpath($folder,false);
			if($folder=="" || !is_dir($folder)){return NULL;}else{$bln_delrec=true;}
		}
		elseif (!$bln_delrec){ return NULL; }
		$dh = @opendir($folder);
		if (!$dh){ if (!$isrec){$bln_delrec = false;} return NULL; }
		$int_undel = 0;
		while( $element = readdir($dh) ){
			if ($element=="." || $element=="..") {continue;}
			$str_path = $folder."/".$element;
			if (is_dir($str_path)){$bln_del = $this->folderdel($str_path,true);}//递归调用时一定要注明第二个参数为true，表示为递归
			else{$bln_del = @unlink($str_path);}
			if (!$bln_del){++$int_undel;}
		}
		closedir($dh);
		if (!$int_undel && !@rmdir($folder)){++$int_undel;}
		if (!$isrec){$bln_delrec = false;}
		return $int_undel ? false : true;
	}
	
	//__________________  公有方法  ________________
	
	//转换为标准虚拟路径(转换后格式为 /****/*** )，超出站点目录时自动舍弃
	public function linkpath($path="./",$isfile=NULL){
		//处理参数
		if (func_num_args()==1 && !is_string(func_get_arg(0))){$isfile = $path;$path = "";}
		//去掉空格后为空:指定是文件返回当前文件;否则返回当前目录
		$path=trim($path);
		if ($path==""){ return $isfile ? $_SERVER['PHP_SELF'] : preg_replace($this->Reg_Slash,"/",$this->Str_PHPName."/"); }
		//多个/或\转换为单个/:如果为空(一定是目录)则返回根目录;如果非空但起始为/且不含有./,则返回本身
		$path = preg_replace($this->Reg_Slash,"/",$path);
		if ($path==""){return "/";}
		if (substr($path,0,1)=="/" && strpos($path,"./")===false){return $path;}
		//计算路径起始部分：根目录物理地址形式时，起始路径为空；否则起始路径为当前目录
		$str_dirs = substr($path,0,1)=="/" ? "" : str_replace("\\","/",$this->Str_PHPName);
		//去掉起始路径最后一个"/"
		if (substr($str_dirs,-1)=="/"){ $str_dirs = substr($str_dirs,0,-1); }
		//开始转换路径
		$ary_dirs = explode("/",$str_dirs);	//数组第一个元素为空值，转换前先删除第一个元素
		$ary_path = explode("/",$path);		//数组最后一个元素可能为空(根据参数$path而定，这样转换后再串联为字符串是，最后一个字符为"/")
		array_shift($ary_dirs);
		foreach($ary_path as $value){
			if ($value=="" || $value=="."){continue;}
			if (substr($value,0,1)!="."){array_push($ary_dirs,$value);} elseif ($ary_dirs){array_pop($ary_dirs);}
		}
		//合并为相对于根目录的地址
		$str_dirs = "/".implode("/", $ary_dirs);
		if (substr($str_dirs,-1)!="/" && !$isfile){$str_dirs.="/";}							//如果结尾不为"/"，但不是文件，必须加上"/"
		if (substr($str_dirs,-1)=="/" && $isfile){$str_dirs = substr($str_dirs,0, -1);}		//如果结尾为"/"，但不是目录，必须舍弃"/"
		//注销数组
		unset($ary_dirs); $ary_dirs = NULL;
		unset($ary_path); $ary_path = NULL;
		return $str_dirs;
	}
	
	//转换为物理路径(转换后格式为 X:/****/*** )
	public function realpath($path="./",$isfile=NULL){
		if (func_num_args()==1 && !is_string(func_get_arg(0))){$isfile = $path; $path = "";}
		if (strtolower(substr($path,0,8))=="file:///"){
			$path = substr($path,8); return $path!="" ? $path : $this->Str_PHPRoot.substr($this->Str_PHPName,1);
		}
		$path = $this->linkpath($path,$isfile);
		return $path=="" ? NULL : preg_replace($this->Reg_Slash,"/",$this->Str_PHPRoot.$path);
	}

	//返回文件/文件夹是否存在(等价于函数"file_exists")
	public function get_exists($file=""){
		$file = $this->matchpath($file);
		if (is_file($file)){return 1;}
		elseif (is_dir($file)){return 2;}
		else{return 0;}
	}
	//判断是文件夹还是文件(等价于函数"filetype")
	public function get_type($file=""){$file = $this->matchpath($file); return filetype($file);}
	
	//返回文件的可读写属性(综合函数"is_readable"和"is_writable")
	public function get_rw($file){
		$file = $this->matchpath($file);//转为物理地址
		if(!$file){return false;}		//路径不存在
		$str_rw = "";
		if ( is_readable($file) ){$str_rw .= "r";}
		if ( is_writable($file) ){$str_rw .= "w";}
		clearstatcache(); return $str_rw;
	}
	
	//返回文件/文件夹的目录/名称/扩展名
	public function get_part($file="",$part=""){
		if (!$this->get_exists($file)){return array();}
		$ary_info = pathinfo($file);
		if ( preg_match($this->Reg_Proto,$ary_info["dirname"]) || preg_match($this->Reg_Drive,$ary_info["dirname"]) ){
			//$ary_info["dirname"]	= $ary_info["dirname"];
			$ary_info["realpath"]	= $ary_info["dirname"];
		}
		else{
			$ary_info["dirname"]	= $this->linkpath($ary_info["dirname"],false);
			$ary_info["realpath"]	= $this->realpath($ary_info["dirname"],false);
		}
		$ary_part = array(
			"basename"	=> $ary_info["basename"],
			"filename"	=> $ary_info["filename"],
			"extension" => strval(@$ary_info["extension"]),
			"linkdir"	=> preg_replace($this->Reg_Slash,"/",$ary_info["dirname"]."/"),
			"realdir"	=> preg_replace($this->Reg_Slash,"/", $ary_info["realpath"]."/")
		);
		unset($ary_info); $ary_info = NULL;
		$part = strtolower($part);
		switch($part){
			case ""  	: return $ary_part;					case "*.*"	:	return $ary_part["basename"];
			case ".*"	: return $ary_part["extension"];	case "*."	:	return $ary_part["filename"];
			case "*/"	:;									case "c:"	:	return $ary_part["dirname"];
			default		: return array();
		}
	}
	//获取文件/文件夹的各种属性
	public function get_property($file=""){
		$file = $this->matchpath($file);	//转为物理地址
		if (!$file){return array();}		//路径不存在
		$int_size = is_dir($file) ? $this->foldersize($file) : filesize($file);
		return array("size"=>$int_size,"perms"=>fileperms($file), "mtime"=>filemtime($file), "ctime"=>filectime($file));
	}
	
	//获取文件或文件夹大小
	public function get_size($path="",$unit="kb"){
		$getpath = $this->matchpath($path);	//转为物理地址
		if (!$getpath){return NULL;}		//路径不存在
		if (is_file($getpath)){$int_size = filesize($getpath);}
		else{$int_size = $this->foldersize($path);}
		$unit = strtolower($unit);
		switch($unit){
			case "b"	: return $int_size;
			case "g"	:;
			case "gb"	: return preg_replace("/(\.[0-9]{3})[0-9]+$/","\\1",strtolower($int_size/1024/1024/1024));
			case "m"	:;
			case "mb"	: return preg_replace("/(\.[0-9]{2})[0-9]+$/","\\1",strtolower($int_size/1024/1024));
			default		: return preg_replace("/(\.[0-9]{1})[0-9]+$/","\\1",strtolower($int_size/1024));
		}
		return $int_size;
	}

	//获取文件夹列表
	public function dir($folder="",$type="",$rule="",$filtre=""){
	
		self::$Str_DirLink	= $this->linkpath($folder,false);
		self::$Str_DirReal	= $this->realpath(self::$Str_DirLink,false);
		if (!self::$Str_DirReal){return $this->seterror(60,"指定的路径不是目录");}
		if (!is_readable(self::$Str_DirReal)){return $this->seterror(61,"指定的路径缺少读取权限");}
		
		self::$Reg_FtTrue	= $this->matchwild($rule);
		self::$Reg_FtFlase	= $this->matchwild($filtre);
		
		$type = strtolower($type);			//获取的类别
		if ($type=="1"){$type="file";} elseif ($type=="2"){$type="folder";} else{$type="";}

		//获取列表
		$ary_dir = scandir(self::$Str_DirReal);
		if (!is_array($ary_dir)){return $this->seterror(62,"打开目录发生错误。");}
		//过滤
		$ary_file = array();
		$ary_folder = array();
		foreach ($ary_dir as $key=>$value){
			if ($value=="." || $value=="..") { continue; }
			if (self::$Reg_FtFlase!="" && preg_match(self::$Reg_FtFlase,$value)){continue;}
			if (self::$Reg_FtTrue!="" && !preg_match(self::$Reg_FtTrue,$value)){continue;}
			if (is_file(self::$Str_DirReal.$value)){
				$ary_file[] = array(
					"type"	=> 1,
					"name"	=> $value,
					"extend"=> strpos($value,".")!==false ? substr(strrchr($value,"."),1) : "",
					"mtime"	=> filemtime(self::$Str_DirReal.$value),
					"size"	=> filesize(self::$Str_DirReal.$value),
					"keyid"	=> substr(md5(self::$Str_DirReal.$value),8,16),
				);
			}
			else{
				$ary_folder[] = array(
					"type"	=> 2,
					"name"	=> $value,
					"extend"=> "",
					"mtime"	=> filemtime(self::$Str_DirReal.$value),
					"size"	=> "*",
					"keyid"	=> substr(md5(self::$Str_DirReal.$value),8,16),
				);
			}
		}
		$x = sizeof($ary_file);
		$y = sizeof($ary_folder);

		$ary_result = array(
			"realpath"	=> self::$Str_DirReal,
			"linkpath"	=> self::$Str_DirLink,
			"dirname"	=> preg_replace($this->Reg_Slash,"/",dirname(self::$Str_DirReal)."/"),
			"basename"	=> basename(self::$Str_DirReal),
			"file"		=> $x,
			"folder"	=> $y,
			"count"		=> $x+$y,
			"flist"		=> $ary_file,
			"dlist"		=> $ary_folder,
		);
		
		unset($ary_file);	$ary_file = NULL;
		unset($ary_folder);	$ary_folder = NULL;
		return $ary_result;
	}
	
	//以下开始所有方法目录限制（带安全）--------------------------------------------------------------
	
	//创建文件夹
	public function md($folder){
		$folder = $this->linkpath($folder,false);
		if ($folder==""){return NULL;}
		$str_folder = $this->Str_PHPRoot;
		$ary_folder = explode("/",$folder);
		$int_folder = sizeof($ary_folder);
		for ($i=1;$i<$int_folder-1;++$i){
			$str_folder = $str_folder.$ary_folder[$i]."/";
			if (!is_dir($str_folder) && !@mkdir($str_folder)){return NULL;}
		}
		return array($str_folder,$folder);
	}
	//创建临时文件
	public function mdtmp($folder,$del=false){
		if ($del){return tmpfile();}
		$ary_folder = $this->md($folder);
		return !$ary_folder ? false : tempnam($ary_folder[0],"\$\$_");
	}
	//二进制输出文件
	public function filebinary($file,$name=""){
		$file = $this->realpath($file,true);
		if (!$file){return $this->seterror(20,"{fso:readbin_err_sour_over}");}
		elseif (!is_file($file)){return $this->seterror(21,"{fso:readbin_err_sour_notext}");}
		@$hanle	= fopen($file,'rb');
		if (!$hanle){return $this->seterror(22,"{fso:readbin_err_sour_deny}");}
		if ($name==""){$name = substr(strrchr($file,"/"),1);}
		$binary	= fread($hanle,1024*1024*2);
		fclose($hanle);
		header("content-type:application/octet-stream");
		header("Content-Disposition:attachment;filename=". $name .";");
		echo($binary);
		unset($hanle);	$hanle = NULL;
		unset($binary);	$binary= NULL;
		return true;
	}	
	//复制文件或文件夹(将源文件/文件夹复制到$target目录下)
	//1.$target是复制后的内容的父目录;
	//2.$savename指定复制文件时，$target是为目标目录名称还是目标文件名称(复制文件、且$target不为空时有效)
	//如：复制文件到 $target/$file.ext: 当 $savename 为true时，将文件复制到 $taregt 并改名为$file.ext;否则就是复制到 $target/$file.ext/ 下
	public function copy($path,$target="",$savename=false){
		//格式化元路径
		$path = $this->linkpath($path);
		$repath = $this->Str_PHPRoot.substr($path,1);
		if ($path==""){return $this->seterror(30,"{fso:copy_err_sour_over}");}
		elseif (!file_exists($repath)){return $this->seterror(31,"{fso:copy_err_sour_notext}");}
		//是复制目录的
		if (is_dir($repath)){return $this->foldercopy($path,$target);}
		//只有两个参数时
		if (func_num_args()==2 && is_bool($target)){$target="";}
		
		$target = trim($target);
		if ($target==""){$target = dirname($path);$savename=false;}
		$target = $this->linkpath($target,$savename);
		if ($target==""){return $this->seterror(32,"{fso:copy_err_target_over}");}
		//格式化目标路径及名称
		if (!$savename){$basename = basename($path);}
		else{$basename = substr($target,strrpos($target,"/")+1);$target = substr($target,0,strrpos($target,"/")+1);}
		$retarget = $this->Str_PHPRoot.substr($target,1);
		while(file_exists($retarget.$basename)){$basename = "copy ".$basename;}
		//创建目录和复制文件
		if (!is_dir($retarget) && !$this->md($target)){return $this->seterror(33,"{fso:copy_err_mkdir_fail}");}
		if (!@copy($repath,$retarget.$basename)){return false;}//copy函数：如果目标文件已存在，将会被覆盖。
		return $target.$basename;
	}
	//移动文件或文件夹
	//(将源文件/文件夹复制到$target目录下，$target是复制后的内容的父目录)
	public function move($path,$target=""){
		$path = $this->realpath($path);
		if ($path==""){return $this->seterror(40,"{fso:move_err__sour_over}");}
		elseif (!file_exists($path)){return $this->seterror(41,"{fso:move_err_sour_notext}");}
		
		$target = trim($target);
		if ($target==""){return $this->seterror(42,"{fso:move_err_target_empty}");}
		$target = $this->linkpath($target,false);
		if ($target==""){return $this->seterror(43,"{fso:move_err_target_over}");}
		$retarget = $this->Str_PHPRoot.substr($target,1);
		if ( strpos(strtolower($folder),strtolower($retarget))===0 ){
			$int_target = strlen($retarget);$int_folder = strlen($folder);
			if ($int_target>$int_folder){return $this->seterror(44,"{fso:move_err_target_insour}");}
			else{return $this->seterror(45,"{fso:move_err_target_self}");}
		}
		$basename = basename($path);
		if (!is_dir($target) && !@mkdir($retarget)){return $this->seterror(46,"{fso:move_err_mkdir_fail}");}
		return @rename($path,$retarget.$basename) ? $target.$basename: false;
	}
	//重命名文件或文件夹
	public function rename($path,$newname){
		$path	= $this->linkpath($path);
		$repath = $this->Str_PHPRoot.substr($path,1);
		if ($path==""){return $this->seterror(50,"{fso:rename_err_sour_over}");}
		elseif ($path=="/"){return $this->seterror(51,"{fso:rename_err_isroot}");}
		elseif (!file_exists($repath)){$this->seterror(52,"{fso:rename_err_sour_notext}");return NULL;}
		
		if ($newname==""){return $this->seterror(53,"{fso:rename_err_new_empty}");}
		elseif (!preg_match($this->Reg_Name,$newname)){return $this->seterror(54,"{fso:rename_err_new_nochar}");}
		
		$name = strtolower(basename($path));
		if ($name==strtolower($newname)){return $path;}											//等于原名
		$newpath = dirname($repath)."/".$newname;
		if (file_exists($newpath)){return $this->seterror(55,"{fso:rename_err_new_exists}");}	//新名的文件或文件夹已存在
		
		if (!@rename($repath,$newpath)){return false;}
		$path = preg_replace($this->Reg_Slash,"/",dirname($path)."/".$newname);
		if (!is_dir($newpath)){return $path;}
		else{return $path."/";}
	}
	//删除文件或文件夹
	public function delete($path){
		$delpath = $this->realpath($path);
		if ($delpath==""){return $this->seterror(60,"{fso:del_err_sour_over}");}
		if (is_file($delpath)){return @unlink($delpath);}
		elseif (is_dir($delpath)){return $this->folderdel($path);}
		$this->seterror(61,"{fso:del_err_sour_notext}");
		return NULL;
	}
	//打开文件
	public function read($file,$sign="",$trim=false){
		if (func_num_args()==2 && is_bool(func_get_arg(1))){$trim=$sign;$sign="";}
		$file	= strval($file);
		$trim	= (boolean)$trim;
		if ($file==""){return $this->seterror(100,"{fso:read_err_sour_empty}");}
		elseif(!in_array( substr(strrchr($file,"."),1), $this->Ary_Extend) ){ return $this->seterror(101,"{fso:read_err_sour_extend}");}
		//验证文件是否存在
		$file = $this->realpath($file,true);
		if (!$file){return $this->seterror(102,"{fso:read_err_sour_over}");}
		elseif (!is_file($file)){return $this->seterror(103,"{fso:read_err_sour_notext}");}
		
		if (($str_content = @file_get_contents($file))===false){ return $this->seterror(104,"{fso:read_err_sour_deny}"); }
		if (ini_get('magic_quotes_runtime')){$str_content = stripslashes($str_content);}
		if (!is_array($sign)){$sign = strval($sign);$sign = $sign == "" ? array() : array($sign=>$sign);}
		if (!$sign){return $str_content;}
		
		$ary_rs = array();
		foreach($sign as $key=>$value){
			$value = trim($value);
			if ($value==""){ $value = trim($key); if ($value!=""){continue;} }
			$value = preg_replace($this->Reg_Regexp, "\\\\\\1", $value);
			preg_match_all("/(\<region\s+name\=\"". $value ."\"\>(.*?)\<\/region\>|\<\!\-\-start\:". $value ."\-\-\>(.*?)\<\!\-\-end\:".$value."\-\-\>)/is", $str_content, $ary_match, 2);
			//print_r($ary_match);
			if (!$ary_match){$ary_rs[$value]=""; continue;}
			$ary_rs[$value] = !empty($ary_match[0][2]) ? $ary_match[0][2] : $ary_match[0][3];
			if ($trim){ $ary_rs[$value] = $this->clearblankline($ary_rs[$value]); }
		}
		return sizeof($sign)>1 ? $ary_rs : $ary_rs[$value];
	}
	//写入文件
	public function write($savefile,$value,$sourcefile="",$trim=false){
		if (func_num_args()==3 && is_bool(func_get_arg(2))){ $trim=$sourcefile;$$sourcefile=""; }
		$sourcefile = strval($sourcefile);
		$savefile	= strval($savefile);
		$trim		= (boolean)$trim;
		if ($savefile==""){return $this->seterror(110,"{fso:write_err_save_empty}");}
		if (!in_array(substr(strrchr($savefile,"."),1),$this->Ary_Extend)){
			return $this->seterror(111,"{fso:write_err_save_extend}");
		}
		$save = $this->realpath($savefile,true);
		if (!$save){return $this->seterror(112,"{fso:write_err_save_over}");}

		if (!is_array($value)){//写入的内容不是数组形式，则是全部写入，所以不需要读取源文件
			$value = $trim ? $this->clearblankline($value) : strval($value);
			$str_content = $value;
		}
		else{
			if ($sourcefile==""){$sourcefile = $savefile;}
			$source = $this->realpath($sourcefile,true);
			$iscmp = strtolower($source)==strtolower($save);
			if (!$iscmp){	//源文件与要保存的文件不同
				if (!in_array(substr(strrchr($sourcefile,"."),1),$this->Ary_Extend)){
				return $this->seterror(114,"{fso:write_err_read_extend}");}
				elseif (!$source){return $this->seterror(115,"{fso:write_err_sour_over}");}
				elseif (!is_file($source)){return $this->seterror(116,"{fso:write_err_sour_deny}");}
			}
			else{			//源文件与要保存的文件相同
				$source = $save;
			}
			if (($str_content = @file_get_contents($source))===false){
				return $this->seterror(117,"{fso:write_err_sour_notext}");
			}
			//echo($source."<br>".$str_content);
			if (ini_get('magic_quotes_runtime')){$str_content = stripslashes($str_content);}
			foreach ($value as $key=>$text){
				if (strval($key)==""){continue;}
				$regkey = preg_replace($this->Reg_Regexp,"\\\\\\1",$key);
				if ( preg_match("/\<region\s+name\=\"". $regkey ."\"\s*\>.*?\<\/region\>/is", $str_content) ){
					$rule = "/\<region\s+name\=\"". $regkey ."\"\>.*?\<\/region\>/is";
					$tag1 = "<region name=\"". $key ."\">";
					$tag2 = "</region>";
				}
				else{
					$rule = "/\<\!\-\-start\:". $regkey ."\-\-\>.*?\<\!\-\-end\:". $regkey ."\-\-\>/is";
					$tag1 = "<!--start:". $key ."-->";
					$tag2 = "<!--end:". $key ."-->";
				}
				if (is_null($text)){ $text = ""; }
				else{ $text = $trim ? $this->clearblankline($text) : strval($text); $text = $tag1.$text.$tag2; }
				$str_content= preg_replace($rule, $text, $str_content);
			}
		}
		if (!is_file($save) && !$this->md(dirname($savefile))){return $this->seterror(118,"{fso:write_mkdir_fail}");}
		$int_put = file_put_contents($save,$str_content);
		return $int_put!==false ? true : $this->seterror(119,"{fso:write_target_deny}");
	}
	
	public function close(){}
}
?>