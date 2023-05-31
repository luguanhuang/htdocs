<?php
//****************** [Class] 证书服务对象 ******************

//该类必须运行于 PHP5.0 及以上的平台
if ( intval(PHP_VERSION) < 5 ){ exit("<p>Run \"CertService\" invite promotion your PHP arrives at 5.0 the above editions</p>");}

if (!class_exists('AtherFrameWork')){ require(dirname(__FILE__)."/AtherFrameWork.php"); }

class CertService extends AtherFrameWork{
	
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
	
	protected function upload_certfile($param){
		$keys	= array(
		 	"upload_type"	=> array("name"=>"上传证书的类别"),
			"upload_fname"	=> array("name"=>"上传证书的文件域名称"),
		);
		$param	= $this->validateArgs($param,$keys,2100,$interr,$strerr);
		if ( !$param ){ return $this->result_set($interr, $strerr); }
		//验证文件
		$file	= FuncExt::getvalue($param["upload_fname"],"file");
		if (!$file){
			return $this->result_set(2016,"没有FILE控件或上传的文件可能超过系统配置规定的大小");
		}
		//验证文件名
		$fname	= is_array($file['tmp_name']) ? $file['name'][0] : $file['name'];
		$fname	= strtr($fname,"\\","/");
		$fname	= strrpos($fname,"/")!==false ? substr(strrchr($fname,"/"),0,1) : $fname;
		if (!preg_match(parent::_REG_CERT_,$fname)){
			return $this->result_set(2107,"证书文件名称只能由数字、字母和下划线组成");
		}
		//验证证书类型
		$ctype = strtolower(strval($param["upload_type"]));
		if ($ctype=="ticket_cert"){
			$str_updir	= "file:///"._GLO_PATH_CERT_;
			$int_size	= 2048;
			$str_extend	= "*";
		}
		elseif ($ctype=="vpn_cert"){
			$str_updir	= "file:///"._GLO_PATH_VPN_CERT_;
			$int_size	= 2048;
			$str_extend	= "*";
		}
		else{
			return $this->result_set(2108,"上传的证书类型不匹配");
		}
		//创建对象
		if (!class_exists('UpLoad')){require_once($this->Str_PHPApp."UpLoad.php");}
 		$obj_upload = new UpLoad();
		$obj_upload->batch		= 0;									//批量上传
		$obj_upload->overflow	= 1;									//允许跨域
		$obj_upload->saveauto	= 3;									//使用原文件名
		$obj_upload->overwrite	= 1;									//覆盖目标文件
		$obj_upload->savename	= "";									//指定文件名前缀
		$obj_upload->savepath	= $str_updir;							//保存的地址
		$obj_upload->maxbits	= $int_size;							//上传的最大尺寸(k为单位)
		$obj_upload->type		= $str_extend;							//允许上传的文件格式(/分隔开)
		$obj_upload->request($param["upload_fname"]);
		
		$result = array();
		if (!$obj_upload->save()){
			if ($obj_upload->_error()){
				$this->Int_Error	= $obj_upload->_error();
				$this->Str_Error	= $obj_upload->_errtext();
			}
			else{
				$ary_rs = $obj_upload->_fileerr(0);
				$this->Int_Error	= $ary_rs['error'];
				$this->Str_Error	= $ary_rs['errtext'];
				unset($ary_rs); $ary_rs = NULL;
			}
		}
		else{
			$this->Int_Error	= -210;
			$this->Str_Error	= "上传文件成功";
			$result	= $obj_upload->_filesave(0);
		}
		unset($obj_upload); $obj_upload = NULL;
		
		if ($this->Int_Error>0){
			return $this->result_set($this->Int_Error, $this->Str_Error);
		}
		return $this->result_struct(
			array(
				"stateId"	=> $this->Int_Error,
				"message"	=> $this->Str_Error,
				"result"	=> $result
			)
		);
	 }
	
	//__________________  公有方法  ________________
	
	/**
	获取普通证书列表
	number: 1600
	*/
	public function getCertList(){
		if (!$this->user_popedom(
			array(__METHOD__, __CLASS__."::uploadCert", __CLASS__."::deleteCert")
		)){return $this->Ary_Popedom;}
		if (!$this->validateFolderRead(_GLO_PATH_CERT_,"普通证书目录",1600,$interr,$strerr)){
			return $this->result_set($interr,$strerr);
		}
		$handle = opendir(_GLO_PATH_CERT_);
		if($handle===false){ return $this->result_set(1601,"访问证书目录发生错误，获取普通证书失败"); }
		$result = array();
		while( false !== ($file = readdir($handle))){
			if ($file == "." || $file == "..") {continue;}
			$ext = strrpos($file,".")!== false ? substr(strrchr($file,"."),1) : "";
			$result[] = array("name"=>$file, "type"=>$ext);
		}
		return $this->result_struct(
			array(
				"stateId"	=> -160,
				"message"	=> "获取普通证书完成",
				"result"	=> $result
			)
		);
	}

	/**
	删除证书
	number: 1700
	interface: 100
	*/
	public function deleteCert($param){
		if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		$keys	= array( "certfile"	=> array('name'=>"证书文件名称","rule"=>parent::_REG_CERT_));
		$param	= $this->validateArgs($param,$keys,1700,$interr,$strerr);
		if ( !$param ){ return $this->result_set($interr, $strerr); }
		$file = _GLO_PATH_CERT_.$param['certfile'];
		if(!is_file($file)){return $this->result_set(1701,'证书文件不存在');}
		//发送socket
		$result	= $this->socket_send(100,$file);
		$message= !$result['state'] ? "删除证书失败：".$result['errtxt']:"删除证书成功";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set((!$result['state'] ? 1702 : -170),$message);
	}
	
	/**
	保存普通证书配置
	number: 2100
	*/
	public function uploadCert($fname){
		if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		return $this->upload_certfile(array(
			"upload_type"	=> 'ticket_cert',
			"upload_fname"	=> strval($fname)
		));
	}
	
	/**
	获取VPN证书列表
	number: 1800
	*/
	public function getVpnCertList(){
		if (!$this->user_popedom(
			array(__METHOD__, __CLASS__."::uploadVpnCert", __CLASS__."::deleteVpnCert")
		)){return $this->Ary_Popedom;}
		if (!$this->validateFolderRead(_GLO_PATH_VPN_CERT_,"VPN证书目录",1800,$interr,$strerr)){
			return $this->result_set($interr,$strerr);
		}
		if (!$this->validateFileRead(_GLO_FILE_VPN_,"VPN证书配置文件",1801,$interr,$strerr)){
			return $this->result_set($interr,$strerr);
		}
		//读取所有证书
		$handle=opendir(_GLO_PATH_VPN_CERT_);
		if($handle===false){ return $this->result_set(1801,"访问证书目录发生错误，获取VPN证书失败"); }
		$result = array();
		while( false !== ($file = readdir($handle))){
			if ($file == "." || $file == "..") {continue;}
			$ext = strrpos($file,".")!== false ? substr(strrchr($file,"."),1) : "";
			$result[] = array("name"=>$file, "type"=>$ext, "seting"=>NULL);
		}
		//var_dump($result);
		//获取当前设置的证书
		$content	= file_get_contents(_GLO_FILE_VPN_);
		$crtconf	= array();
		preg_match_all("/[\r\n]+(ca|cert|key)[\t\ ]+([^\r\n]+)/i",$content,$match,2);
		if ($match){
			foreach($match as $key=>$value){
				if (strrpos($value[2],"/") !== false) {$crtconf[$value[1]]= substr(strrchr($value[2],"/"),1);}
			}
			unset($match); $match = NULL;
		}
		//状态赋值
		if ($crtconf){
			foreach($result as $key=>$value){
				$type = array_search($value['name'],$crtconf);
				if ($type!==false){ $result[$key]['seting'] = $type; }
			}
		}
		unset($crtconf); $crtconf = NULL;
		return $this->result_struct(
			array(
				"stateId"	=> -180,
				"message"	=> "获取VPN证书完成",
				"result"	=> $result
			)
		);
	}
	
	/**
	保存VPN证书配置
	number: 1900
	interface: 155
	*/
	 public function setVpnCert($param){
		 if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		 $keys	= array(
		 	"ca"	=> array("name"=>"VPN根证书名称",	"rule"=>parent::_REG_CERT_),
			"cert"	=> array("name"=>"VPN证书名称",	"rule"=>parent::_REG_CERT_),
			"key"	=> array("name"=>"证书私钥",		"rule"=>parent::_REG_CERT_),
		);
		$param	= $this->validateArgs($param,$keys,1900,$interr,$strerr);
		if ( !$param ){ return $this->result_set($interr, $strerr); }
		//验证文件扩展名
		/*
		$extend	= strpos($param['ca'],".")!=false ? substr(strrchr($param['ca'],"."),1): "";
		if (strtolower($extend)!="crt"){
			return $this->result_set(1910,"VPN根证书文件扩展名不正确");
		}
		$extend	= strpos($param['cert'],".")!=false ? substr(strrchr($param['cert'],"."),1): "";
		if (strtolower($extend)!="crt"){
			return $this->result_set(1911,"VPN证书文件扩展名不正确");
		}
		$extend	= strpos($param['key'],".")!=false ? substr(strrchr($param['key'],"."),1): "";
		if (strtolower($extend)!="key"){
			return $this->result_set(1912,"证书私钥文件扩展名不正确");
		}
		*/
		//发送数据
		$sendaeg= $param['ca'].'|'.$param['cert'].'|'.$param['key'];
		$result = $this->socket_send(155, $sendaeg);
		$message= !$result['state'] ? "保存VPN证书配置失败：".$result['errtxt'] : "保存VPN证书配置成功 重新启动服务才生效";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set(($result['state'] ? -190 : 1913),$message);
	 }
	 
	/**
	删除VPN证书
	number: 2000
	interface: 101
	*/
	public function deleteVpnCert($param){
		if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		$keys	= array( "certfile"	=> array('name'=>"证书文件名称","rule"=>parent::_REG_CERT_));
		$param	= $this->validateArgs($param,$keys,2000,$interr,$strerr);
		if ( !$param ){ return $this->result_set($interr, $strerr); }
		$file = _GLO_PATH_VPN_CERT_ . $param['certfile'];
		if(!is_file($file)){ return $this->result_set(2001,'证书文件不存在');}
		//发送socket
		$result	= $this->socket_send(101,$file);
		$message= !$result['state'] ? "删除VPN证书失败：".$result['errtxt']:"删除VPN证书成功";
		$this->log($this->Ary_Session['user'],$message);
		return $this->result_set(($result['state'] ? -200 : 2002),$message);
	}
	 
	/**
	保存VPN证书配置
	number: 2100
	*/
	public function uploadVpnCert($fname){
		if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
		return $this->upload_certfile(array(
			"upload_type"	=> 'vpn_cert',
			"upload_fname"	=> strval($fname)
		));
	}
	
}
?>