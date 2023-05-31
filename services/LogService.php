<?php
//****************** [Class] 安全设置对象 ******************

require_once 'tlog.php';

//该类必须运行于 PHP5.0 及以上的平台
if ( intval(PHP_VERSION) < 5 ){ exit("<p>Run \"SafetySet\" invite promotion your PHP arrives at 5.0 the above editions</p>");}

if (!class_exists('AtherFrameWork')){ require(dirname(__FILE__)."/AtherFrameWork.php"); }

class LogService extends AtherFrameWork{

	
	function __construct(){
		parent::__construct();
	}
	
	
	function __destruct(){
		parent::__destruct();
	}
	
	
/* 获取所有配置的类型 */
	public static function getLogTypes(){

		static $ary_list;
		if (!isset($ary_list)){$ary_list = parent::config2array(_GLO_FILE_LOGLIST_);}
		return $ary_list;
	}

/* 获取所有配置的类型 */
	public static function getConfigTypes(){

		static $ary_list;
		if (!isset($ary_list)){$ary_list = parent::config2array(_GLO_FILE_CONFIGLIST_);}
		return $ary_list;
	}
	
	
//$param
public function getLog($param){
//if (!$this->user_popedom()){return $this->Ary_Popedom;}
		$adsllog = $param['logtype'];
	//	$adsllog = "/usr/bin/tail -n 200 /var/log/ydvpn.log";
	// $adslres=shell_exec($adsllog);
		$adslres = $this->file_content($adsllog,"adsl结果日志文件",1700,$interr,$strerr);
		if ($adslres===false){return $this->result_set($interr,$strerr);}
		FuncExt::error_report(0);
		$adslres = iconv("gb2312","utf-8//IGNORE",$adslres);
		FuncExt::error_report(true);
		
		$arrtmpa=explode("/",$adsllog);
		
		header('Content-Type:application/force-download');			//强制下载
		header('Content-Transfer-Encoding: binary');				//使用二进制传输
		header("Content-Disposition:attachment;filename=".$arrtmpa[count($arrtmpa)-1]);	//保存文件名(http协议)
		header("Cache-Control: public");							//令IE支持
		header('Pragma: public'); 									//令IE支持
		header('Expires: 0');										//立即过期
		echo($adslres);
		$adslres = NULL;
		exit();
		
	}
	
	
	
//$param static $session;
public function getNewLog($param){

	if ($param['exportfile']=="1")
	{
		return $this->getLog($param);
	}
	
$this->user_getlogin();
		$result = $this->socket_send(169, session_id().'|'.'1000'.'|'.$param['logtype']);
		 if ($result["error"]>0)
		 {
		 	return $this->result_set(($result["error"]<=0?-160:1606), "日志查看命令失败：".$result['errtxt']);
		 }
		
		 
		 $adsllogtype = $param['logtype'];
		 $arrtmpa=explode("/",$adsllogtype);
		 
		 $adsllog = _GLO_PATH_TOOLS_RESULT_.session_id()."-".$arrtmpa[count($arrtmpa)-1];
		 
		$adslres = $this->file_content($adsllog,"日志文件",1700,$interr,$strerr);
		if ($adslres===false){return $this->result_set($interr,$strerr);}
		FuncExt::error_report(0);
		$adslres = iconv("gb2312","utf-8//IGNORE",$adslres);
		FuncExt::error_report(true);
		return $this->result_struct(
			array(
				"stateId"	=> -170,
				"message"	=> "获取日志结果成功",
				"result"	=>$adslres
			)
		);
	}
	
	

	//$param
public function getconfig($param){
    TLOG_MSG("getconfig: func begin");
//if (!$this->user_popedom()){return $this->Ary_Popedom;}
		$adsllog = $param['logtype'];
	   
		$adslres = $this->file_content($adsllog,"配置文件",1700,$interr,$strerr);
		if ($adslres===false){return $this->result_set($interr,$strerr);}
		FuncExt::error_report(0);
		$adslres = iconv("gb2312","utf-8//IGNORE",$adslres);
		FuncExt::error_report(true);
		return $this->result_struct(
			array(
				"stateId"	=> -170,
				"message"	=> "获取配置结果成功",
				"result"	=>$adslres
			)
		);
	}
	
	
	
//$param static $session;
public function getsysinfo($param){
		$this->user_getlogin();
		
		$systype=$param['infotype'];
		
		
		 $arrtmpa=explode("-",$systype);
		 
		$result = $this->socket_send($arrtmpa[0], session_id());
		 if ($result["error"]>0)
		 {
		 	return $this->result_set(($result["error"]<=0?-160:1606), "系统信息查看命令失败：".$result['errtxt']);
		 }
		 
		// $adsllog = _GLO_PATH_TOOLS_RESULT_.session_id()."-".$arrtmpa[count($arrtmpa)-1];
		 
		  $adsllog = _GLO_PATH_TOOLS_RESULT_.session_id()."-".$arrtmpa[count($arrtmpa)-1].'.log';
		 
		$adslres = $this->file_content($adsllog,"系统信息文件",1700,$interr,$strerr);
		if ($adslres===false){return $this->result_set($interr,$strerr);}
		FuncExt::error_report(0);
		$adslres = iconv("gb2312","utf-8//IGNORE",$adslres);
		FuncExt::error_report(true);
		return $this->result_struct(
			array(
				"stateId"	=> -170,
				"message"	=> "获取系统信息结果成功",
				"result"	=>$adslres
			)
		);
	}
	

	//写入文件
	public function setconfig($param){
		$this->user_getlogin();
			
		$systype=$param['logtype'];

		$content=$param['Adslmessage'];
		
		 $arrtmpa=explode("-",$systype);
		$content=str_replace("^M","",$content);
		$content=str_replace("\'","'",$content);
			$content=str_replace('\"','"',$content);
	    //$file = "/var/tmp/config_".session_id()."-".$arrtmpa[count($arrtmpa)-1];
		$file = "/var/tmp/config_".session_id();
		//$file = $arrtmpa[count($arrtmpa)-1]."_tmp";
		//$file = "/tmp/file"."_tmp";
	    TLOG_MSG("logservice: filename=".$file);
		$int_put = file_put_contents($file,$content);
	//	return getconfig($param);
	
		
	   $result = $this->socket_send(174, $file.'|'.$param['logtype']);
		 if ($result["error"]>0)
		 {
		 	return $this->result_set(($result["error"]<=0?-160:1606), "配置修改命令失败：".$result['errtxt']);
		 }
		 
		return $this->result_struct(
			array(
				"stateId"	=> -171,
				"message"	=> "获取系统信息结果成功",
				"result"	=>$content
			)
		);
		
	//	return $file;
	}
	
	
	
//开始抓包-显示
	public function starttcpdumpdis($param){
			$this->user_getlogin();
		
		$systype=$param['infotype'];
		$filter=$param['filter_info'];
		$count=$param['count'];
		
		TLOG_MSG("starttcpdumpdis: infotype=".$systype." filter=".$filter." count=".$count);
		 $arrtmpa=explode("-",$systype);
		 
		$result = $this->socket_send(175, session_id()."|".$systype."|".$filter."|".$count);
		 if ($result["error"]>0)
		 {
		 	return $this->result_set(($result["error"]<=0?-160:1606), "抓包命令失败：".$result['errtxt']);
		 }
		
		return $this->result_struct(
			array(
				"stateId"	=> -170,
				"message"	=> "开始抓包信息成功",
				"result"	=>""
			)
		);
	}
	
	
//显示抓包信息
	public function distcpdump($param){
			$this->user_getlogin();
		
			 
		  $adsllog = _GLO_PATH_TOOLS_RESULT_.session_id()."-tcpdump.log";
		 
		$adslres = $this->file_content($adsllog,"抓包文件",1700,$interr,$strerr);
		if ($adslres===false){return $this->result_set($interr,$strerr);}
		FuncExt::error_report(0);
		$adslres = iconv("gb2312","utf-8//IGNORE",$adslres);
		FuncExt::error_report(true);
		return $this->result_struct(
			array(
				"stateId"	=> -170,
				"message"	=> "获取抓包信息结果成功",
				"result"	=>$adslres
			)
		);
	}
	
	
//下载抓包信息
	public function downtcpdump($param){
			$this->user_getlogin();
		  $adsllog = "/tmp/".session_id()."-tcpdump.pcap";
			return  $adsllog;
	}
	
	
	
//下载抓包操作开始
	public function starttcpdumpdown($param){
			$this->user_getlogin();
		
		$systype=$param['infotype'];
		$filter=$param['filter_info'];
		$count=$param['count'];
		
		
		 $arrtmpa=explode("-",$systype);
		 
		$result = $this->socket_send(176, session_id()."|".$systype."|".$filter."|".$count);
		 if ($result["error"]>0)
		 {
		 	return $this->result_set(($result["error"]<=0?-160:1606), "抓包命令失败：".$result['errtxt']);
		 }
		 
		
		return $this->result_struct(
			array(
				"stateId"	=> -170,
				"message"	=> "开始抓包信息成功",
				"result"	=>""
			)
		);
	}
	
	
//停止抓包
	public function stoptcpdump($param){
			$this->user_getlogin();
		TLOG_MSG("stoptcpdump: func begin");
	//	$systype=$param['infotype'];
	//	$filter=$param['filter'];
			 
		$result = $this->socket_send(177, "");
		TLOG_MSG("stoptcpdump: call send result=".$result);
		 if ($result["error"]>0)
		 {
		 	return $this->result_set(($result["error"]<=0?-160:1606), "停止抓包命令失败：".$result['errtxt']);
		 }
		
		return $this->result_struct(
			array(
				"stateId"	=> -170,
				"message"	=> "停止抓包命令成功",
				"result"	=>""
			)
		);
	}
	
	/**
	 保存普通配置配置
	 number: 2100
	 */
	public function uploadCfg($param){
	    TLOG_MSG("uploadCfg: func begin upload_file=".$param['upload_file']." logtype=".$param['logtype']);
	    $str_updir = "";
	    $str_extend = "";
	    $int_size = "";
	   
	    //if (!$this->user_popedom(__METHOD__)){return $this->Ary_Popedom;}
	    
	    //验证文件
	    $file	= FuncExt::getvalue($param["upload_fname"],"file");
	    if (!$file){
	        TLOG_MSG("uploadCfg: func begin error 111");
	        return $this->result_set(2016,"没有FILE控件或上传的文件可能超过系统配置规定的大小");
	    }
	    
	    $fname	= is_array($file['tmp_name']) ? $file['name'][0] : $file['name'];
	    $fname	= strtr($fname,"\\","/");
	    $fname	= strrpos($fname,"/")!==false ? substr(strrchr($fname,"/"),0,1) : $fname;
	    if (!preg_match(parent::_REG_CERT_,$fname)){
	        return $this->result_set(2107,"证书文件名称只能由数字、字母和下划线组成");
	    }
	    
	    TLOG_MSG("uploadCfg: func begin fname=".$fname);
	    if ($fname == "scpbrlinkmon.conf")
	    {
	        $str_updir	= "file:///"."/yd/SCPBRLINK_MONI/etc/";
	        $int_size	= 2048;
	        $str_extend	= "*";
	    }
	    else if ($fname == "cfg.conf")
	    {
	        $str_updir	= "file:///"."/yd/SCP_APP/etc/";
	        $int_size	= 2048;
	        $str_extend	= "*";
	    }
	    else if ($fname == "scpserver.cfg")
	    {
	        $str_updir	= "file:///"."/yd/YDSOC_SCP/etc/";
	        $int_size	= 2048;
	        $str_extend	= "*";
	    }
	    else if ($fname == "ydsoc_scp.cfg")
	    {
	        $str_updir	= "file:///"."/yd/YDSOC_SCP/etc/";
	        $int_size	= 2048;
	        $str_extend	= "*";
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
	     TLOG_MSG("uploadCfg: func begin 222 savepath=".$obj_upload->savepath);
	     $obj_upload->maxbits	= $int_size;							//上传的最大尺寸(k为单位)
	     $obj_upload->type		= $str_extend;							//允许上传的文件格式(/分隔开)
	     //$obj_upload->request($param["upload_fname"]);
	     //$obj_upload->request($param["upload_file"]);
	     $obj_upload->request();
	     //TLOG_MSG("uploadCfg: func begin 222 name=".$_FILES['file']['name']." error=".$_FILES['file']['error']);
	     $result = array();
	     if (!$obj_upload->save()){
	     if ($obj_upload->_error()){
	         TLOG_MSG("uploadCfg: func begin 333 savepath=".$obj_upload->savepath);
	     $this->Int_Error	= $obj_upload->_error();
	     $this->Str_Error	= $obj_upload->_errtext();
	     }
	     else{
	         TLOG_MSG("uploadCfg: func begin 444");
	     $ary_rs = $obj_upload->_fileerr(0);
	     $this->Int_Error	= $ary_rs['error'];
	     $this->Str_Error	= $ary_rs['errtext'];
	     unset($ary_rs); $ary_rs = NULL;
	     }
	     }
	     else{
	         TLOG_MSG("uploadCfg: func begin 555");
	     $this->Int_Error	= -210;
	     $this->Str_Error	= "上传文件成功";
	     $result	= $obj_upload->_filesave(0);
	     }
	     unset($obj_upload); $obj_upload = NULL;
	     TLOG_MSG("uploadCfg: func begin 666");
	     if ($this->Int_Error>0){
	         TLOG_MSG("uploadCfg: func begin 777");
	     return $this->result_set($this->Int_Error, $this->Str_Error);
	     }
	     
	     TLOG_MSG("uploadCfg: func begin 888");
	     return $this->result_struct(
	     array(
	     "stateId"	=> $this->Int_Error,
	     "message"	=> $this->Str_Error,
	     "result"	=> $result
	     )
	     );
	     }
}
?>