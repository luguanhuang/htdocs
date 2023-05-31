<?PHP
//****************** [Class] 生成验证码 ******************

//该类必须运行于 PHP5.0 及以上的平台
if ( intval(PHP_VERSION) < 5 ){ exit("<p>Run \"Cls_Vcode\" invite promotion your PHP arrives at 5.0 the above editions</p>");}

class Cls_Vcode{

	//__________________  构造/析构函数  ________________
	
	function __construct(){
		if (!defined('_GLO_SERVER_DIRFONT_')){return $this->set_error(1,"{com:exists_err_config}");}
		if (!extension_loaded('gd')){return $this->set_error(2,"{com:exists_err_ext_gd2}");}
		$this->Bln_Enabled	= true;
	}
	
	function __destruct(){}
	
	//__________________  私有变量  ________________
	
	public function _version()		{return '1.2';}					//版本
	public function _build()		{return '09.11.27';}			//版本
	public function _create()		{return '09.04.21';}			//创建
	public function _classname()	{return "PHP_Vcode";}			//名称
	public function _developer()	{return "OldFour";}				//开发者
	public function _copyright()	{return "ODFBBS.CORP";}			//公司
	
	protected $Bln_Enabled	= false;
	protected $Int_Error	= 0;
	protected $Str_Error	= "";
	protected $Str_Session	= "";
	
	//__________________  只读属性(用方法代替)  ________________
	
	public function _session()	{return $this->Str_Session;}
	public function _error()	{return $this->Int_Error;}
	public function _errtext()	{return $this->Str_Error;}
	public function _enabled()	{return $this->Bln_Enabled;}
	
	//__________________  只写属性(用方法代替)  ________________
	
	
	//__________________  可读写属性  ________________
	
	public $session		= "vcode_session";	//Session名称
	public $width		= 160;				//图片宽度(取值30-300)
	public $height		= 60;				//图片高度(取值30-300)
	public $codetype	= 1;				//Session取值的类型(1:数字;2:字母;3:数字+字母)
	public $codesize	= 6;				//Session的长度(最多8个，默认6个，最少4个)
	public $extend		= "png";			//输出的图片格式
	public $font_w		= 40;				//每个字符占用的宽度
	public $font_x		= 0;				//字符的水平起始位置
	public $font_y		= 0;				//字符的水平垂直位置
	public $font		= "";				//字体文件的路径
	public $size		= 20;				//字体大小
	public $border		= 1;				//图片边框(取值0-1)
	public $disturb		= 3;				//干扰线(最多5条，默认3条，0表示不需要)
	public $snow		= 100;				//雪花点(最多180点，默认100点，0表示不需要)
	public $color		= "";		//字体颜色：英文(white,black,blue,red,yellow,green,gray)、10进制(***,***,***)、16进制(#******)
	public $bgcolor		= "";		//背景颜色：英文(white,black,blue,red,yellow,green,gray)、10进制(***,***,***)、16进制(#******)
	public $brcolor		= "";		//边框颜色：英文(white,black,blue,red,yellow,green,gray)、10进制(***,***,***)、16进制(#******)
	
	//_________________ 私有方法 _________________
	
	//返回错误
	protected function set_error($interr,$strerr){
		$interr = intval($interr);
		$strerr = trim($strerr);
		$this->Int_Error = $interr;
		$this->Str_Error = $strerr;
		return false;
	}
	
	protected function between($int,$min,$max){
		$int = intval($int);
		if ($min!="*"){$min	= intval($min);}
		if ($max!="*"){$max	= intval($max);}
		if ($max==$min){return ($max=="*" ? $int : $max );}
		if ($max != "*" && $min != "*" && $max<$min){$int_tmp = $max;$max = $min;$min = $int_tmp;}
		if ($min!= "*" && $int<$min){$int=$min;}
		if ($max!= "*" && $int>$max){$int=$max;}
		return $int;
	}
	
	protected function colorformat($color){
		$color = strtolower($color);
		if ( preg_match("/^(white)|(black)|(blue)|(red)|(yellow)|(green)|(gray)$/i",$color) ){
			switch($color){
				case "white" :	return array(255,255,255);
				case "gray" :	return array(128,128,128);
				case "blue" :	return array(0,0,255);
				case "red" :	return array(255,0,0);
				case "yellow" :	return array(255,255,0);
				case "green" :	return array(0,255,0);
				default:		return array(0,0,0);
			}
		}
		elseif ( preg_match("/^\#[0-9a-fA-F]{6}$/i",$color)){
			return array(hexdec(substr($color,1,2)),hexdec(substr($color,3,2)),hexdec(substr($color,5,2)));
		}
		elseif ( preg_match("/^(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]{1,2}))\,){2}((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]{1,2}))$/i",$color)){
			$color = preg_replace("/^0+/i","0",$color);
			$color = preg_replace("/\,0+/i",",0",$color);
			return explode(",",$color);
		}
		return array(0,0,0);
	}
	
	//__________________  公有方法  ________________
	
	public function writeimage(){

		if (!$this->Bln_Enabled){return false;}
		
		//必备条件————————————————————————————————————————————————————
		//Session名称
		$str_session = strval($this->session);
		if ($str_session == ""){return $this->set_error(10,"{cls_vocde:sesname_empty}");}
		
		//字体文件
		$str_font = strval($this->font);
		if ($str_font==""){return $this->set_error(11,"{cls_vocde:fontfile_empty}");}
		$str_font = _GLO_SERVER_DIRFONT_."ttf/".$str_font;
		if (!is_file($str_font)){return $this->set_error(12,"{cls_vocde:fontfile_notext}");}
		
		//数字类的可选条件————————————————————————————————————————————————————
		
		//宽度
		$int_width 		= $this->between($this->width,30,300);
		//高度
		$int_height 	= $this->between($this->height,30,300);
		//边框
		$int_border		= $this->between($this->border,0,1);
		//字符类型
		$int_codetype 	= $this->between($this->codetype,1,3);
		//字符长度
		$int_codesize	= $this->between($this->codesize,4,8);
		//干扰线数量
		$int_disturb	= $this->between($this->disturb,0,9);
		//雪花点数量
		$int_snow		= $this->between($this->snow,0,180);
		//字体大小
		$int_size		= $this->between($this->size,1,"*");
		//单个字符宽度
		$int_unit		= $this->between($this->font_w,1,$int_width);
		//起始位置
		$int_place_x	= $this->between($this->font_x,0,$int_width);
		$int_place_y	= $this->between($this->font_y,0,$int_height);
		
		//数字类的可选条件————————————————————————————————————————————————————
		//图片格式
		$str_extend		= strtolower($this->extend);
		if ($str_extend!="png"){$str_extend="gif";}
		//字体颜色
		$ary_color		= $this->colorformat($this->color);
		//背景颜色
		$ary_bgcolor	= $this->colorformat($this->bgcolor);
		//边框颜色
		$ary_brcolor	= $this->colorformat($this->brcolor);
		
		//预备值————————————————————————————————————————————————————
		//倾斜角度
		$ary_angle = array(330,335,340,345,350,355,0,10,15,20,25,30);
		//可选字符
		if ($int_codetype==3){$str_code="346789ABCDEFGHIJKLMNPQRTUVWXY";}	//去掉（字母OolSsZz；数字0125）
		elseif ($int_codetype==2){$str_code = "ABCDEFGHIJKLMNPQRTUVWXY";}	//去掉（字母OolSsZz）
		else{$str_code ="0123456789";}
		//随机种子
		mt_srand(microtime()*100000000);
		
		//开始生成图片————————————————————————————————————————————————————
		$gd_image		= imagecreate($int_width,$int_height); 												//创建一张空白图片
		$gd_bgcolor		= imagecolorallocate($gd_image, $ary_bgcolor[0], $ary_bgcolor[1], $ary_bgcolor[2]);	//第一次调用为指定背景颜色
		$gd_fontcolor	= imagecolorallocate($gd_image, $ary_color[0],$ary_color[1],$ary_color[2]);			//定义字体使用的颜色
		$gd_brcolor		= imagecolorallocate($gd_image, $ary_brcolor[0],$ary_brcolor[1],$ary_brcolor[2]);	//定义边框使用的颜色
		
		//生成随机字符
		$str_rndcode = "";
		for ($i=0;$i<$int_codesize;++$i){ $str_rndcode .= substr($str_code, mt_rand(0,strlen($str_code)-1), 1);}
		//echo $str_rndcode;exit();
		
		//将字符写到图片上
		for ($i=0;$i<$int_codesize;++$i){
			imagettftext($gd_image, $int_size, $ary_angle[array_rand($ary_angle)], $int_place_x+($i*$int_unit), $int_place_y, $gd_fontcolor, $str_font, substr($str_rndcode,$i,1) );
		}
		//生成雪花点
		for ($i=0;$i<$int_snow;++$i){
			$gd_snowcolor = imagecolorallocate($gd_image,  mt_rand(0,255),  mt_rand(0,255),  mt_rand(0,255));
			imagettftext($gd_image, 2, 0, mt_rand(2,$int_width-1), mt_rand(2,$int_height-1), $gd_snowcolor, $str_font, ".");
		}
		//生成干扰线
		for ($i=0;$i<$int_disturb;++$i){
			$gd_discolor = imagecolorallocate($gd_image,  mt_rand(0,255),  mt_rand(0,255),  mt_rand(0,255));
			imageline($gd_image,mt_rand(0,$int_width-1),mt_rand(0,$int_height-1),mt_rand(0,$int_width-1),mt_rand(0,$int_height-1),$gd_discolor);
		}
		//加上边框
		if ($int_border){
			imageline($gd_image,	0,				0,				$int_width-1,		0,				$gd_brcolor);
			imageline($gd_image,	0,				0,				0,					$int_height-1,	$gd_brcolor);
			imageline($gd_image,	$int_width-1,	$int_height-1,	$int_width-1,		0,				$gd_brcolor);
			imageline($gd_image,	$int_width-1,	$int_height-1,	0,					$int_height-1,	$gd_brcolor);
		}
		//输出图
		@session_start();
		$this->Str_Session			= $str_rndcode;
		$_SESSION[$this->session]	= $str_rndcode;
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Content-type: image/".$str_extend);
		if ($str_extend=="png"){imagepng($gd_image);}
		else{imagegif($gd_image);}
		imagedestroy($gd_image);
	}
	
}
?>