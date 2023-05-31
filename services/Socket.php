<?php

//****************** [Class] Socket对象 ******************

require_once 'tlog.php';

//该类必须运行于 PHP5.0 及以上的平台
if ( intval(PHP_VERSION) < 5 ){ exit("<p>Run \"Socket\" invite promotion your PHP arrives at 5.0 the above editions</p>");}

/**
 * Description of Socket
 *
 * @author Administrator
 */
class Socket {
	
	//__________________  构造/析构函数  ________________
	
	function __construct(){
		$this->Bln_Enabled = false;
		if (!extension_loaded('sockets')){
			$this->Int_Error = 1;
			$this->Str_Error = "缺少组件，请加载 \"sockets\" 扩展";
			return false;
		}
		
		$this->Int_Report	= ini_get('error_reporting');
		$this->Bln_Enabled	= true;
	}
	
	function __destruct(){
		$this->close();
	}

	//__________________  私有变量  ________________
	
	const _INT_PORT_MIN_	= 1;
	const _INT_PORT_MAX_	= 65535;
	const _INT_READ_UNIT_	= 1024;
	const _INT_READ_MAX_	= 8;
	const _INT_DEF_TOUT_	= 3;
	const _STR_DEF_HOST_	= "127.0.0.1";
	
	static $Ary_Protocol = array(
		"tnet"	=> AF_INET, //这是大多数用来产生socket的协议，使用TCP或UDP来传输，用在IPv4的地址
		"tnet6"	=> AF_INET6,//与上面类似，不过是来用在IPv6的地址
		"unix"	=> AF_UNIX, //本地协议，使用在Unix和Linux(很少使用)，一般都是当客户端和服务器在同一台机器上使用
	);

	static $Ary_PtlComm = array(
		"icmp"	=> STREAM_IPPROTO_ICMP,		//互联网控制消息协议，主要使用在网关和主机上，用来检查网络状况和报告错误信息
		"udp"	=> STREAM_IPPROTO_UDP,		//用户数据报文协议，它是一个无连接，不可靠的传输协议
		"tcp"	=> STREAM_IPPROTO_TCP,		//传输控制协议，使用最多的可靠公共协议，保证数据包能够到达接收者那儿，在传输过程中若发生错误，那么它将重新发送出错数据包。
	);
	
	static $Ary_Type = array(
		//按照顺序的、可靠的、数据完整的基于字节流的连接。使用最多的socket类型，这个socket是使用TCP来进行传输。
		"stream"	=> SOCK_STREAM,
		//无连接的、固定长度的传输调用。该协议是不可靠的，使用UDP来进行它的连接。
		"dgram"		=> SOCK_DGRAM,
		//双线路的、可靠的连接，发送固定长度的数据包进行传输。必须把这个包完整的接受才能进行读取。
		"seq"		=> SOCK_SEQPACKET,
		"seqpacket"	=> SOCK_SEQPACKET,
		//提供单一的网络访问，使用ICMP公共协议(ping、traceroute使用该协议)。
		"raw"		=> SOCK_RAW,
		//很少使用的，在大部分的操作系统上没有实现，它是提供给数据链路层使用，不保证数据包的顺序。
		"rdw"		=> SOCK_RDM
	);

	
	protected $Bln_Enabled		= false;
	protected $Int_Report		= 0;
	protected $Int_Error		= 0;
	protected $Str_Error		= "";
	protected $Int_SocketErr	= 0;
	protected $Str_SocketErr	= "";
	
	protected $Obj_Server		= NULL;
	protected $Obj_Client		= NULL;
	protected $Obj_Connect		= NULL;
	protected $Int_Type			= 0;
	protected $Int_Protocol		= 0;
	protected $Int_PtlComm		= 0;
	protected $Int_Port			= 0;
	protected $Str_Host			= "";
	protected $Str_LastSend		= "";
	
	//__________________  只读属性(用方法代替)  ________________
	
	public function _version()		{return '1.0';}					//版本
	public function _build()		{return '11.09.05';}			//版本
	public function _create()		{return '11.09.05';}			//创建
	public function _classname()	{return __CLASS__;}				//名称
	public function _developer()	{return "OldFour";}				//开发者
	public function _copyright()	{return "ODFBBS.CORP";}			//公司
	
	public function _enabled()		{return $this->Bln_Enabled;}
	public function _error()		{return $this->Int_Error;}
	public function _errtext()		{return $this->Str_Error;}
	public function _socketerror()	{return $this->Int_SocketErr;}
	public function _socketerrtxt()	{return $this->Str_SocketErr;}
	public function _server()		{return $this->validate_socket($this->Obj_Server) ? $this->Obj_Server : NULL;}
	public function _client()		{return $this->validate_socket($this->Obj_Client) ? $this->Obj_Client : NULL;}
	public function _connect()		{return $this->validate_socket($this->Obj_Connect) ? $this->Obj_Connect : NULL;}

	public function _type()			{return $this->Int_Type;}
	public function _procomm()		{return $this->Int_PtlComm;}
	public function _protocol()		{return $this->Str_Protocol;}
	public function _host()			{return $this->Str_Host;}
	public function _port()			{return $this->Int_Port;}
	public function _lastsend()		{return $this->Str_LastSend;}
	
	//__________________  只写属性  ________________
	
	
	//__________________  可读写属性  ________________
	
	public $type	= "";	//socket类型
	public $protocol= "";	//协议
	public $ptlcomm	= "";	//公共协议
	public $host	= "";	//主机地址
	public $port	= "";	//端口
	public $stimeout= 0;	//发送的超时时间
	public $atimeout= 0;	//接收的超时时间
	public $callback= NULL;	//监听回调函数
	public $callargs= NULL;	//监听回调函数的参数
	
	//__________________  私有方法  ________________

	protected function set_error($error,$errtext){
		$this->Int_Error = intval($error);
		$this->Str_Error = strval($errtext);
		return false;
	}
	
	protected function validate_socket($socket){
		return strtolower(gettype($socket))=="resource" &&
			strtolower(get_resource_type($socket))=="socket";
	}
	
	protected function validate_host(){
		$this->Str_Host = trim(strval($this->host));
		if ($this->Str_Host==""){ $this->Str_Host = self::_STR_DEF_HOST_; }
		return true;
	}
	
	protected function validate_report(){
		$this->Int_Port = intval($this->port);
		if ($this->Int_Port < self::_INT_PORT_MIN_ || $this->Int_Port > self::_INT_PORT_MAX_){
			mt_srand(number_format(microtime(),9,".","")*100000000);
			$this->Int_Port = mt_rand(self::_INT_PORT_MIN_, self::_INT_PORT_MAX_);
		}
		return true;
	}

	protected function set_options($opttype,$optval){
		$opttype = strtolower(strval($opttype));
		$optval  = strtolower(strval($optval));
		if ($opttype=="t" || $opttype=="type"){
			if (is_numeric($optval)){
				if (!in_array($optval,self::$Ary_Type)){ $optval = self::$Ary_Type['stream']; }
			}
			else{ $optval = !isset(self::$Ary_Type[$optval]) ? self::$Ary_Type['stream'] : self::$Ary_Type[$optval]; }
			$this->Int_Type = $optval;
		}
		elseif ($opttype=="p" || $opttype=="protocol"){
			if (is_numeric($optval)){
				if (!in_array($optval,self::$Ary_Protocol)){ $optval = self::$Ary_Protocol['tnet']; }
			}
			else{ $optval = !isset(self::$Ary_Protocol[$optval]) ? self::$Ary_Protocol['tnet'] : self::$Ary_Protocol[$optval]; }
			$this->Int_Protocol = $optval;
		}
		elseif ($opttype=="pc" || $opttype=="ptlcomm"){
			if (is_numeric($optval)){
				if (!in_array($optval,self::$Ary_PtlComm)){ $optval = self::$Ary_PtlComm['tcp']; }
			}
			else{ $optval = !isset(self::$Ary_PtlComm[$optval]) ? self::$Ary_PtlComm['tcp'] : self::$Ary_PtlComm[$optval]; }
			$this->Int_PtlComm = $optval;
		}
		return true;
	}
	
	protected function get_callback(){
	    TLOG_MSG("get_callback: func begin 1");
		//回调函数
		$var_func = NULL;
		$ary_args = array();
		if (!is_array($this->callback)){
		    TLOG_MSG("get_callback: func begin 2");
			$var_func = strval($this->callback);
		}
		else{
		    TLOG_MSG("get_callback: func begin 3 callback=".$this->callback);
			$int_leng = sizeof($this->callback);
			if ($int_leng==1){$var_func = strval($this->callback[0]);}
			elseif( $int_leng>1 ){ $var_func = array($this->callback[0],$this->callback[1]); }
		}
		if (!is_null($var_func)){
		    TLOG_MSG("get_callback: func begin 4");
			if (!is_array($var_func)){
				if (!function_exists($var_func)){ $var_func = NULL; }
			}
			else{
			    TLOG_MSG("get_callback: func begin 5");
				if (
				!(
					(is_string($var_func[0]) && class_exists($var_func[0])) ||
					(is_object($var_func[0]) && get_class($var_func[0]))
				 )
				){ $var_func = NULL; }
			}
		}
		if (!is_null($var_func)){
		    TLOG_MSG("get_callback: func begin 6");
			//回调参数
			if (is_array($this->callargs)){ $ary_args = $this->callargs;}
			elseif (is_null($this->callargs)){ $ary_args = array();}
			else{ $ary_args = array(strval($this->callargs)); }
			return array("func"=>$var_func,"args"=>$ary_args);
		}
		return false;
	}
	
	
	//__________________  公有方法  ________________
	
	//创建一个socket
	public function newsocket(&$socket){
		if (!$this->Bln_Enabled){return false;}
		//类型
		$this->set_options("type",$this->type);
		//协议
		$this->set_options("protocol",$this->protocol);
		//公共协议
		$this->set_options("ptlcomm",$this->ptlcomm);
		//关闭之前的
		if ($this->validate_socket($socket)){ socket_close($socket); }
		//创建对象
		$socket = socket_create($this->Int_Protocol, $this->Int_Type, $this->Int_PtlComm);
		if (!$socket){
			$this->Int_SocketErr = socket_last_error();
			$this->Str_SocketErr = socket_strerror($this->Int_SocketErr);
			return $this->set_error(100,"创建 Socket 对象失败。");
		}
		return true;
	}
	
	//创建服务器端
	public function newserver(){
		if (!$this->newsocket($this->Obj_Server)){ return false; }
		//服务器
		$this->validate_host();
		//端口
		$this->validate_report();
		//开始建立服务端
		$result = true;
		$this->err_close();
			//绑定端口
		if ( socket_bind($this->Obj_Server, $this->Str_Host, $this->Int_Port) === false) {
			$this->Int_SocketErr = socket_last_error();
			$this->Str_SocketErr = socket_strerror($this->Int_SocketErr);
			$result = $this->set_error(110,"绑定端口失败");
		}
			//监听
		elseif (socket_listen($this->Obj_Server, 5) === false) {
			$this->Int_SocketErr = socket_last_error();
			$this->Str_SocketErr = socket_strerror($this->Int_SocketErr);
			$result = $this->set_error(111,"监听端口失败");
		}
		$this->err_reduce();
		if (!$result){return false;}
		//回调函数
		$ary_callback = $this->get_callback();
		if ($ary_callback){
			while(true){
				$this->Obj_Connect = socket_accept($this->Obj_Server);
				if (!$this->Obj_Connect){return $this->set_error(112,"返回 Socket 规则接口失败");	}
				call_user_func_array($ary_callback['func'], $ary_callback['args']);
				socket_close($this->Obj_Connect);
			}
			unset($ary_callback); $ary_callback = NULL;
		}
		return true;
	}
	
	//服务器端回传给客户端
	public function response($data){
		if (!$this->validate_socket($this->Obj_Connect)){return false;}
		//设置发送超时时间
		$int_stimeout = max(intval($this->stimeout),self::_INT_DEF_TOUT_);
		socket_set_option($this->Obj_Connect, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>$int_stimeout, "usec"=>0));
		//发送数据
		$this->err_close();
		$result = socket_write($this->Obj_Connect,$data,strlen($data));
		$this->err_reduce();
		return $result;
	}
	
	//服务器端读取
	public function request($maxlen){
		if (!$this->validate_socket($this->Obj_Connect)){return false;}
		//设置接收超时时间
		$int_atimeout = max(intval($this->atimeout),self::_INT_DEF_TOUT_);
		socket_set_option($this->Obj_Connect, SOL_SOCKET, SO_SNDTIMEO, array("sec"=>$int_atimeout, "usec"=>0) );
		//读取数据
		$maxlen = min(intval($maxlen),self::_INT_READ_MAX_);
		if (!$maxlen){ $maxlen = self::_INT_READ_MAX_; }
		$this->err_close();
		$readdb = socket_read($this->Obj_Connect, $maxlen * self::_INT_READ_UNIT_);
		$this->err_reduce();
		return $readdb;
	}
	
	//创建客户端
	public function newclient($debug=false){
		//创建socket连接
		if (!$this->newsocket($this->Obj_Client)){ return false; }
		//服务器
		$this->validate_host();
		//端口
		$this->validate_report();
		//设置超时时间
		$int_stimeout = max(intval($this->stimeout),self::_INT_DEF_TOUT_);
		$int_atimeout = max(intval($this->atimeout),self::_INT_DEF_TOUT_);
		socket_set_option($this->Obj_Client, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>$int_stimeout, "usec"=>0));	//发送超时时间
		socket_set_option($this->Obj_Client, SOL_SOCKET, SO_SNDTIMEO, array("sec"=>$int_atimeout, "usec"=>0) );	//接收超时时间
		//连接服务器端
		$result = true;
		$this->err_close();
		//echo  $this->Int_Port;exit;
		if (!socket_connect($this->Obj_Client, $this->Str_Host, $this->Int_Port)){
			$this->Int_SocketErr = socket_last_error();
			$this->Str_SocketErr = socket_strerror($this->Int_SocketErr);
			$result = $this->set_error(120,"连接到 Socket 服务端失败。");
		}
		$this->err_reduce();
		if (!$result){return false;}
		//回调函数
		$ary_callback = $this->get_callback();
		if ($ary_callback){
		    TLOG_MSG("newclient: func=".$ary_callback['func']);
			$result = call_user_func_array($ary_callback['func'], $ary_callback['args']);
			unset($ary_callback); $ary_callback = NULL;
		}
		return $result;
	}
	
	//客户端向服务器端发送
	public function send($data,$length=0){
		$this->Str_LastSend = "";
		if (!$this->validate_socket($this->Obj_Client)){return false;}
		$data = strval($data);
		$dlen = strlen($data);
		$length = max(intval($length),0);
		if (!$length){ $length = $dlen; }
		elseif ($length>$dlen){ $data .= str_repeat(" ",$length-$dlen);}
		$this->err_close();
		$result = socket_write($this->Obj_Client, $data, $length);
		$this->err_reduce();
		$this->Str_LastSend = $data;
		return $result;
	}
	
	//客户端向服务器端获取
	public function get($cutoff=""){
		if (!$this->validate_socket($this->Obj_Client)){return false;}
		//读取的类型
		$strend = trim(strval($cutoff));
		$maxlen = 0;
		//读取到的终止字符(读取后的数值不包含结束符号)
		if ($strend=="" || is_numeric($strend)){
			$strend	= "";
			$maxlen = intval($strend) * self::_INT_READ_UNIT_;
		}
		//临时变量
		$read = "";			//每次读取到的数据
		$data = array();	//已经读取到的数据
		$leng = 0;
		$dlen = 0;
		$spos = -1;
		$this->err_close();
		if ($strend!=""){
			do{
				$read=socket_read($this->Obj_Client, self::_INT_READ_UNIT_, PHP_BINARY_READ);
				$read= strval($read);
				if ($read===""){ break; }
				$spos = strpos($read,$strend);
				if ($spos===false){ $data[] = $read;}
				else{ $data[] = substr($read,0, $spos); break; }
			}
			while(true);
		}
		else{
			do{
				$read=socket_read($this->Obj_Client, self::_INT_READ_UNIT_, PHP_BINARY_READ);
				$read= strval($read);
				if ($read===""){ break; }
				if (!$maxlen){ $data[] = $read; continue; }
				$dlen = strlen($read);
				if ($leng+$dlen<$maxlen){ $data[] = $read; }
				else{$data[] = substr($read,0,$maxlen-$leng); break;}
				$leng += $dlen;
				$spos = strpos($read,$strend);
				if ($spos===false){ $data[] = $read;}
				else{ $data[] = substr($read,0, $spos); break; }
			}
			while(true);
		}
		$this->err_reduce();
		$data = implode("",$data);
		return $data;
	}
	
	//接收一个现有socket连接
	public function accept(&$conn){
		if (!$this->validate_socket($this->Obj_Server)){return false;}
		$conn = socket_accept($this->Obj_Server);
		return !!$conn;
	}
	
	//断开一个现有的socket连接
	public function disc(&$conn){
		if ($this->validate_socket($conn)){ socket_close($conn); }
		unset($conn); $conn = NULL;
	}
	
	//释放socket资源
	public function close(){
		if ($this->validate_socket($this->Obj_Connect)){ socket_close($this->Obj_Connect); }
		if ($this->validate_socket($this->Obj_Server)){ socket_close($this->Obj_Server); }
		if ($this->validate_socket($this->Obj_Client)){ socket_close($this->Obj_Client); }
		unset($this->Obj_Connect);	$this->Obj_Connect	= NULL;
		unset($this->Obj_Server);	$this->Obj_Server	= NULL;
		unset($this->Obj_Clent);	$this->Obj_Clent	= NULL;
	}
	
	//+++++++++++++++++++++++ 系统辅助方法 +++++++++++++++++++++++
	
	public function err_close(){error_reporting(0);}
	
	public function err_reduce(){error_reporting($this->Int_Report);}
}
?>
