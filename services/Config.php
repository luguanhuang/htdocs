<?php
define('_GLO_SERVER_CONFIG_',	1);
define('_GLO_SERVER_TEST_',		0);

define("_GLO_PHPINI_ERRORS_",	1);						//默认不显示错误(1:显示;0:不显示)
define("_GLO_PHPINI_ERRREP_",	E_ALL);					//显示错误级别数 (E_ALL & ~E_NOTICE)
define("_GLO_PHPINI_DTZONE_",	"PRC");					//网站所使用的时区

define('_GLO_SERVER_DIRROOT_',	preg_replace("/[\/\\\\]+/","/",(dirname(dirname(__FILE__))."/")));	//项目目录
define('_GLO_SERVER_DIRAPP_',	preg_replace("/[\/\\\\]+/","/",(dirname(__FILE__)."/")));			//当前目录
define('_GLO_SERVER_DIRFONT_',	_GLO_SERVER_DIRROOT_.'font/');
define('_GLO_SERVER_DIRMDB_',	_GLO_SERVER_DIRROOT_.'mdb/');

//define('_GLO_PROJECT_NAME_',	'可管控安全通讯平台11');
define('_GLO_PROJECT_NAME_',	'AxiCloud');
// define('_GLO_PROJECT_VER_',		'3.0 Simple');
//define('_GLO_PROJECT_LTD_',		'Aether');
define('_GLO_PROJECT_LTD_',		'a realtime cloud');
define('_GLO_PROJECT_BUILD_',	'2021.12.26');
// define('_GLO_PROJECT_FNAME_',	_GLO_PROJECT_NAME_." v"._GLO_PROJECT_VER_." - "._GLO_PROJECT_LTD_);
define('_GLO_PROJECT_FNAME_',	_GLO_PROJECT_NAME_." - "._GLO_PROJECT_LTD_);

define('_GLO_FRAME_PATH_',		_GLO_SERVER_DIRROOT_.'framework/');
define('_GLO_FRAME_LOGGER_',	_GLO_FRAME_PATH_.'log4php/');

define('_GLO_SESSION_ERROR_',	'Session:Ahter_ErrorPage');
define('_GLO_SESSION_NAME_',	'Session:Ahter_ConfManage');

define('_GLO_SESSION_USERINFO_',	'Session:User_Info');

define('_GLO_SESSION_VLOGIN_',	'Session:Ahter_VerifyCode');

define('_GLO_USER_PATH_GROP_',	'UserGroup.php');
define('_GLO_USER_PATH_MDB_',	'UserAccount.php');
define('_GLO_USER_PATH_LOG_',	'UserLogin.log');
define('_GLO_USER_PATH_LOCK_',	'UserLock.log');
define('_GLO_USER_LOCK_COUNT_',	5);
define('_GLO_USER_LOCK_TIME_',	30);
define('_GLO_USER_LOGIN_VCODE_',0);
define('_GLO_LOG_DEL_ENABLED_',	0);	//日志是否允许删除

define('_GLO_SOCKET_HOST_',				'127.0.0.1');
define('_GLO_SOCKET_PORT_',				'10240');

define('_GLO_PATH_ROOT_',				'');	#模拟'E:/linux-dir'
define('_GLO_FILE_SYSTEM_INFO_', 		_GLO_PATH_ROOT_.'/var/log/sys_info');							#系统信息
define('_GLO_FILE_IP_',					_GLO_PATH_ROOT_.'/etc/sysconfig/network-scripts/ip-cfg');		#IP配置文件路径
define('_GLO_FILE_ROUTE_',				_GLO_PATH_ROOT_.'/usr/local/comm_platform/tool/route-cfg');	#路由配置
define('_GLO_FILE_PROXY_',				_GLO_PATH_ROOT_.'/usr/local/transproxy/etc/transproxy.conf');	#客票代理配置
define('_GLO_FILE_REMOTEADM_',			_GLO_PATH_ROOT_.'/usr/local/transproxy/etc/inproxy.conf');		#远程管理配置
define('_GLO_FILE_PPPOE_DNS_',			_GLO_PATH_ROOT_.'/etc/resolv.conf');							#PPPOE DNS 文件路径
define('_GLO_FILE_PPPOE_ACCOUNT_',		_GLO_PATH_ROOT_.'/etc/ppp/pppoe.conf');							#PPPOE 帐户信息文件路径
define('_GLO_FILE_FIREWALL_',			_GLO_PATH_ROOT_.'/usr/local/comm_platform/tool/ipt-cfg');						#防火墙配置文件
define('_GLO_FILE_VPN_',				_GLO_PATH_ROOT_.'/usr/local/ydvpn/etc/client.conf');			#VPN配置文件
define('_GLO_FILE_GLOBAL_SWITCH_',		_GLO_PATH_ROOT_.'/usr/local/comm_platform/tool/global-cfg');					#全局配置文件
define('_GLO_FILE_DHCP_',				_GLO_PATH_ROOT_.'/etc/opendhcp.ini');							#DHCP配置文件
define('_GLO_PATH_CERT_',				_GLO_PATH_ROOT_.'/var/cert/');									#证书配置路径
define('_GLO_PATH_VPN_CERT_',			_GLO_PATH_ROOT_.'/usr/local/ydvpn/keys/');						#VPN证书配置路径
define('_GLO_FILE_WEB_OPERATOR_',		_GLO_PATH_ROOT_.'/www/htdocs/logs/audit.log');					#WEB操作日志
define('_GLO_FILE_CLIENT_LOGIN_',		_GLO_PATH_ROOT_.'/var/log/ydsec/aclproxy.log');					#客户登录日志
define('_GLO_PATH_CLIENT_OPERATOR_',	_GLO_PATH_ROOT_.'/var/log/ydsec/transproxy.log');				#客户操作日志
define('_GLO_FILE_CLIENT_OFFLINE_',		_GLO_PATH_ROOT_.'/var/log/ydsec/aclproxy.log');					#客户时长
define('_GLO_FILE_CLIENT_ONLINE_',		_GLO_PATH_ROOT_.'/var/log/ydsec/onlineuser.list');				#客户在线时长
define('_GLO_FILE_SERVER_CONF_',		_GLO_PATH_ROOT_.'/usr/local/tpproxy/etc/tpproxy.conf');			#认证/日志服务器配置
define('_GLO_FILE_SERVER_VERI_CONF_',	_GLO_PATH_ROOT_.'/conf/etc/sysconfig/global-cfg');				#校验服务器配置
define('_GLO_PATH_TOOLS_RESULT_',		_GLO_PATH_ROOT_.'/var/');										#工具测试结果保存目录

define('_GLO_INTERFACE_ALLOW_',			'UserManager|NetConfig|FireWall|Router|DHCPService|SafetySet|ServerConf|Nic|Bridge|Station|Company|Group|User|Channel|Tag|'.
										'CertService|AuditService|BackupRecover|SystemSet|Tools|LogService');
define('_GLO_DIALUP_TYPES_',			"1=静态IP|2=ADSL拨号|3=DHCP|4=3G拨号");
define('_GLO_SAFECLIENT_CIPHER_',		"BF-CBC|AES-128-CBC|DES-EDE3-CBC");
define('_GLO_NETCONFING_LANTYPE_',		"hwe1750=hwe1750|ztemu351=ztemu351|hwe127=hwe127|hwe1261");
define('_GLO_REMOTEADMIN_MAX_',			3);	//远程管理服务器配置上限


define('_GLO_FILE_LOGLIST_',	'scpbrlinkmon=/yd/SCPBRLINK_MONI/log/scpbrlinkmon.log|scp_app=/yd/SCP_APP/log/scp_app.log|scp_key_exchange=/yd/SCP_KEY_EXCHANGE/log/scp_key_exchange.log|scp_station=/yd/SCP_KEY_EXCHANGE/log/scp_station.log|create_ipc=/yd/YDSOC_SCP/log/create_ipc.log|ydsoc_scp=/yd/YDSOC_SCP/log/ydsoc_scp.log|transproxy=/yd/YDSOC_SCP/log/transproxy.log|ydscp_udpwritedb=/yd/YDSOC_SCP/log/ydscp_udpwritedb.log');

define('_GLO_FILE_CONFIGLIST_',	'scpbrlinkmon.conf=/yd/SCPBRLINK_MONI/etc/scpbrlinkmon.conf|scp_app_cfg=/yd/SCP_APP/etc/cfg|scp_key_exchange=/yd/SCP_KEY_EXCHANGE/etc/scp_key_exchange.cfg|scpserver.cfg=/yd/YDSOC_SCP/etc/scpserver.cfg|ydsoc_scp.cfg=/yd/YDSOC_SCP/etc/ydsoc_scp.cfg');


define('_GLO_SOCKET_REVPNSVR_',			30);
define('_GLO_SOCKET_WANSTATE_',			20);
define('_GLO_SOCKET_WANDHCP_',			30);
define('_GLO_SOCKET_DIALCONN_',			40);
define('_GLO_SOCKET_SAFESTATE_',		10);

/** PPPOE(修改帐户)脚本路径: /usr/local/sbin/pppoe-pwd **/
/** PPPOE DNS 脚本路径： /usr/local/sbin/pppoe-dns **/
/** 修改WLAN类型后调用脚本路径： /usr/local/sbin/setfwlan **/
/** 认证服务器日志： /var/log/ydsec/dsdauthserver.log **/

ini_set("default_charset",			"utf-8");
ini_set('register_globals',			0);
ini_set('magic_quotes_runtime',		0);
	//customize
ini_set('date.timezone',			_GLO_PHPINI_DTZONE_);	//设置时区
ini_set('display_errors', 			_GLO_PHPINI_ERRORS_);	//是否显示错误
ini_set('error_reporting',			_GLO_PHPINI_ERRREP_);	//定义显示错误级别数
?>