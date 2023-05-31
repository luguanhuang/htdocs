<?php
/*
权限验证两种算法：
1、将所有接口存在数组，并配置各接口允许访问的用户组（弊端：获取某一用户组拥有的所有权限时效率较低）
2、将所有用户组存入数组，并配置各用户组允许访问的接口（弊端：获取某一接口允许访问的所有用户组是效率较低，当前系统采用该算法）
*/

global $Glo_Ary_Common;
global $Glo_Ary_Group;

/*
可以由两种方法实现全局权限：
1、将接口名称添加到 $Glo_Ary_Common 数组中（推荐）;
2、直接修改接口代码，将验证权限改为只验证登录
*/
$Glo_Ary_Common = array(
	'SystemSet::getSysInfo'			=> '系统：信息',
	'SafetySet::getSafeStatus'		=> '获取安全接入状态',
	'NetConfig::getLanIP'			=> '获取 LAN 口配置',
	'NetConfig::getWlanConf'		=> '获取 WAN 口状态',
	'Tools::sendTelnet'				=> '系统：Telnet',
	'Tools::getPing'				=> '系统：Ping',
	'Tools::sendPing'				=> '系统：Ping',
	'Tools::getAdsl'				=> '系统：Adsl',
	'Tools::sendAdsl'				=> '系统：Adsl',
	'LogService::getLogTypes'		=> '系统：日志类型',
	'LogService::getLog'			=> '系统：日志',
	'LogService::starttcpdumpdis'	=> '系统：开始抓包-显示',
	'LogService::distcpdump'		=> '系统：显示抓包信息',
	'LogService::stoptcpdump'		=> '系统：停止',
	'LogService::starttcpdumpdown'	=> '系统：开始抓包 下载',
	'UserManager::permissionView'	=> '用户：查看用户组权限',
	'UserManager::getSelfData'		=> '用户：获取本身资料',
	'UserManager::setSelfData'		=> '用户：修改本身资料',
	'UserManager::setPassword'		=> '用户：修改本身密码',
  'NetConfig::getSoftUpdateinfo'		=> '系统：软件版本升级',

	
);

$Glo_Ary_Group = array(
	"1"	=> array(
		'key'	=> 'admin',		'description'	=> '默认管理员',		'power' => array(
			'UserManager::addUser'				=> '用户：添加用户',
			'UserManager::getUser'				=> '用户：查看用户',
			'UserManager::setUser'				=> '用户：修改用户',
			'UserManager::delUser'				=> '用户：删除用户',
			'UserManager::getUsers'				=> '用户：查看所有用户',
			'UserManager::resetPass'			=> '用户：重置用户密码',
		)
	),
	"2"	=> array(
		'key'	=> 'system',	'description'	=> '系统管理员',		'power'=>array(
		    'Bridge::addBridgeInfo'				=> '添加网桥',
		    'NetConfig::delBridgeInfo'				=> '删除网桥',
		    'Bridge::setBridgeInfo'				=> '修改网桥',
			'NetConfig::setNicInfo'				=> '修改物理接口',
			'NetConfig::getLanIP'				=> 'LAN IP 设置：获取',
			'NetConfig::setLanIP'				=> 'LAN IP 设置：添加/修改',
			'Router::getRouteList'				=> '路由器：获取',
			'Router::setRoute'					=> '路由设置：添加/修改',
			'Router::delRoute'					=> '路由设置：删除',
			'NetConfig::getProxyTicketList'		=> '客票代理：获取所有',
			'NetConfig::getProxyTicket'			=> '客票代理：获取',
			'NetConfig::setProxyTicket'			=> '客票代理：添加/修改',
			'NetConfig::setProxyTicket2'		=> '客票代理：设置配置',
			'NetConfig::get_proxytickets2'		=> '客票代理：得到配置',
			'NetConfig::delProxyTicket'			=> '客票代理：删除',
			'NetConfig::resetProxyTicket'		=> '客票代理：重启',
			'NetConfig::getRemoteAdminList'		=> '远程管理：获取所有',
			'NetConfig::getRemoteAdmin'			=> '远程管理：获取',
			'NetConfig::setRemoteAdmin'			=> '远程管理：添加/修改',
			'NetConfig::delRemoteAdmin'			=> '远程管理：删除',
			'NetConfig::resetSslSet'		=> 'SSL管理：重启',
	
				'NetConfig::getsslsetlist'		=> 'SSL管理：获取所有',
			'NetConfig::getSslSet'			=> 'SSL管理：获取',
			'NetConfig::setSslSet'			=> 'SSL管理：添加/修改',
			'NetConfig::delSslSet'			=> 'SSL管理：删除',
			'NetConfig::resetRemoteAdmin'		=> '远程管理：重启',
			'FireWall::getFirewallRuleList'		=> '防火墙规则：获取所有',
			'FireWall::getFirewallRule'			=> '防火墙规则：获取所有',
			'FireWall::setFirewallRule'			=> '防火墙规则：设置',
			'FireWall::delFirewallRule'			=> '防火墙规则：删除',
			'FireWall::getFireWallSwitch'		=> '防火墙开关：获取',
			'FireWall::setFireWallSwitch'		=> '防火墙开关：设置',
			'SystemSet::getGlobalSwitch'		=> '全局开关：获取',
			'SystemSet::setGlobalSwitch'		=> '全局开关：保存',
			'NetConfig::getWlanConf'			=> 'WLAN配置信息：获取',
			'NetConfig::setWlanConf'			=> 'WLAN配置信息：保存',
			'DHCPService::getDHCP'				=> 'DHCP配置：获取',
			'DHCPService::setDHCP'				=> 'DHCP配置：保存',
			'NetConfig::getWanStatus'			=> '拨号：获取状态',
			'NetConfig::serverstate'			=> '拨号：服务状态',
			'NetConfig::serverstate2'			=> '拨号：服务状态',

			'NetConfig::dialPPPOE'				=> '拨号：连接',
			'NetConfig::offPPPOE'				=> '拨号：断开',
			'NetConfig::getDNS'					=> 'ADSL DNS：获取',
			'NetConfig::setDNS'					=> 'ADSL DNS：保存',
			'NetConfig::getSoftUpdate'			=> '软件更新：获取',
			'NetConfig::setSoftUpdate'			=> '软件更新：保存',
			'NetConfig::getSoftUpdateinfo'		=> '系统：软件版本升级',
			'ServerConf::getVerifyConfig'		=> '校时服务器配置：获取',
			'ServerConf::setVerifyConfig'		=> '校时服务器配置：保存',
			'ServerConf::getLogSvrConfig'		=> '日志服务器配置：获取',
			'ServerConf::setLogSvrConfig'		=> '日志服务器配置：保存',
			'CertService::getCertList'			=> '售票终端证书配置：获取',
			'CertService::deleteCert'			=> '售票终端证书配置：删除',
			'CertService::uploadCert'			=> '售票终端证书配置：上传',
			'CertService::getVpnCertList'		=> '安全通信证书配置：获取',
			'CertService::deleteVpnCert'		=> '安全通信证书配置：删除',
			'CertService::uploadVpnCert'		=> '安全通信证书配置：上传',
			'CertService::setVpnCert'			=> '安全通信证书配置：保存',
			'SafetySet::getAuthServer'			=> '获取认证服务器配置',
			'SafetySet::setAuthServer'			=> '保存认证服务器配置',
			'SafetySet::getSafeClient'			=> '获取安全客户端',
			'SafetySet::setSafeClient'			=> '保存安全客户端',
			'SafetySet::getSafeStatus'			=> '获取安全接入状态',
			'SafetySet::restartSafe'			=> '重启安全接入服务',
			'BackupRecover::defaultConf'		=> '恢复出厂设置',
			'BackupRecover::backupConf'			=> '备份系统配置',
			'BackupRecover::resumeConf'			=> '恢复系统配置',
			'SystemSet::setDateTime'			=> '设置系统时钟',
			'AuditService::getClientOperatorLogsByShell'		=> '浏览客户端操作日志',
			'AuditService::getClientErrorLogsByShell'			=> '浏览客户端错误日志',
			'AuditService::getClientLoginInfoLogsByShell'		=> '浏览用户登录日志',
			'AuditService::getClientOfflineLogsByShell'			=> '浏览用户时长日志',
			'AuditService::getClientOnlineLogsByShell'			=> '浏览用户在线时长日志',
			'AuditService::getWebAdminLogListByShell'			=> '浏览WEB管理日志',
			'AuditService::delline_logfile'						=> '删除日志内容',
			'AuditService::delget_logfile'						=> '清空日志文件',
		)
		
	),
	"3"	=> array(
		'key'	=> 'secure',	'description'	=> '安全管理员',		'power'=>array(
			'SystemSet::setDateTime'			=> '设置系统时钟',
			'BackupRecover::defaultConf'		=> '恢复出厂设置',
			'BackupRecover::backupConf'			=> '备份系统配置',
			'BackupRecover::resumeConf'			=> '恢复系统配置',
		)
	),
	"4"	=> array(
		'key'	=> 'audit', 	'description'	=> '审计管理员',		'power'=>array(
			'AuditService::getClientOperatorLogsByShell'		=> '浏览客户端操作日志',
			'AuditService::getClientErrorLogsByShell'			=> '浏览客户端错误日志',
			'AuditService::getClientLoginInfoLogsByShell'		=> '浏览用户登录日志',
			'AuditService::getClientOfflineLogsByShell'			=> '浏览用户时长日志',
			'AuditService::getClientOnlineLogsByShell'			=> '浏览用户在线时长日志',
			'AuditService::getWebAdminLogListByShell'			=> '浏览WEB管理日志',
			'AuditService::delline_logfile'						=> '删除日志内容',
			'AuditService::delget_logfile'						=> '清空日志文件',
		)
	),
);
?>