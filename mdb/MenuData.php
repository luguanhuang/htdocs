<?php
global $Glo_Ary_Menu;

$Glo_Ary_Menu	= array();
/*$Glo_Ary_Menu[] = array(
	'group'	=> array(),	//当group为empty时，表示全部可见
	'text'	=> '运行状态',
	'url'	=> 'main.php',
	'target'=> '',
	'child'	=> NULL,
);*/

/*$Glo_Ary_Menu[] = array(
    'group'	=> array(),	//当'child'不为空时，其可见性由'child'决定
    'text'	=> '',
    'url'	=> '',
    'target'=> '',
    'child'	=> array(
        array('group'	=> array(2), 	'text'	=> '物理接口',	'url'	=> 'nic.php',	'target'=> ''),
        array('group'	=> array(2),	'text'	=> '网桥',	'url'	=> 'bridge.php',	'target'=> ''),
    ),
);*/

$Glo_Ary_Menu[] = array(
	'group'	=> array(1),
	'text'	=> 'User configure',
	'url'	=> 'user.php',
	'target'=> '',
	'child'	=> NULL,
);

$Glo_Ary_Menu[] = array(
	'group'	=> array(1),
	'text'	=> 'Company configure',
	'url'	=> 'company.php',
	'target'=> '',
	'child'	=> NULL,
);

$Glo_Ary_Menu[] = array(
	'group'	=> array(1),
	'text'	=> 'Group configure',
	'url'	=> 'group.php',
	'target'=> '',
	'child'	=> NULL,
);

$Glo_Ary_Menu[] = array(
	'group'	=> array(1),
	'text'	=> 'station manager',
	'url'	=> 'station.php',
	'target'=> '',
	'child'	=> NULL,
);

$Glo_Ary_Menu[] = array(
	'group'	=> array(1),
	'text'	=> 'Device configure',
	'url'	=> 'device.php',
	'target'=> '',
	'child'	=> NULL,
);

$Glo_Ary_Menu[] = array(
	'group'	=> array(1),
	'text'	=> 'Channel configure',
	'url'	=> 'channel.php',
	'target'=> '',
	'child'	=>  NULL,
);

$Glo_Ary_Menu[] = array(
	'group'	=> array(2),
	'text'	=> 'User configure',
	'url'	=> 'user.php',
	'target'=> '',
	'child'	=> NULL,
);

$Glo_Ary_Menu[] = array(
	'group'	=> array(2),
	'text'	=> 'Group configure',
	'url'	=> 'group.php',
	'target'=> '',
	'child'	=> NULL,
);

$Glo_Ary_Menu[] = array(
	'group'	=> array(2),
	'text'	=> 'station manager',
	'url'	=> 'station.php',
	'target'=> '',
	'child'	=> NULL,
);

$Glo_Ary_Menu[] = array(
	'group'	=> array(2),
	'text'	=> 'Device configure',
	'url'	=> 'device.php',
	'target'=> '',
	'child'	=> NULL,
);

$Glo_Ary_Menu[] = array(
	'group'	=> array(2),
	'text'	=> 'Channel configure',
	'url'	=> 'channel.php',
	'target'=> '',
	'child'	=>  NULL,
);

$Glo_Ary_Menu[] = array(
	'group'	=> array(3),
	'text'	=> 'User configure',
	'url'	=> 'user.php',
	'target'=> '',
	'child'	=> NULL,
);

$Glo_Ary_Menu[] = array(
	'group'	=> array(3),
	'text'	=> 'station manager',
	'url'	=> 'station.php',
	'target'=> '',
	'child'	=> NULL,
);

$Glo_Ary_Menu[] = array(
	'group'	=> array(3),
	'text'	=> 'Device configure',
	'url'	=> 'device.php',
	'target'=> '',
	'child'	=> NULL,
);

$Glo_Ary_Menu[] = array(
	'group'	=> array(3),
	'text'	=> 'Channel configure',
	'url'	=> 'channel.php',
	'target'=> '',
	'child'	=>  NULL,
);

$Glo_Ary_Menu[] = array(
	'group'	=> array(4),
	'text'	=> 'User configure',
	'url'	=> 'user.php',
	'target'=> '',
	'child'	=> NULL,
);

$Glo_Ary_Menu[] = array(
	'group'	=> array(4),
	'text'	=> 'Device configure',
	'url'	=> 'device.php',
	'target'=> '',
	'child'	=> NULL,
);

$Glo_Ary_Menu[] = array(
	'group'	=> array(4),
	'text'	=> 'Channel configure',
	'url'	=> 'channel.php',
	'target'=> '',
	'child'	=>  NULL,
);

/*$Glo_Ary_Menu[] = array(
	'group'	=> array(2),
	'text'	=> 'DHCP服务',
	'url'	=> 'dhcp.php',
	'target'=> '',
	'child'	=> NULL,
);*/
/*$Glo_Ary_Menu[] = array(
	'group'	=> array(),
	'text'	=> '防火墙功能',
	'url'	=> '',
	'target'=> '',
	'child'	=>  array(
		array('group'	=> array(2), 	'text'	=> '防火墙开关',	'url'	=> 'firewallonoff.php',	'target'=> ''),
		array('group'	=> array(2), 	'text'	=> '防火墙规则',	'url'	=> 'firewallrule.php',	'target'=> ''),
	),
);*/

/*$Glo_Ary_Menu[] = array(
	'group'	=> array(),	//当'child'不为空时，其可见性由'child'决定
	'text'	=> '安全接入',
	'url'	=> '',
	'target'=> '',
	'child'	=> array(
		array('group'	=> array(2),	'text'	=> '安全客户端设置',	'url'	=> 'secclient.php',		'target'=> ''),
		array('group'	=> array(2),	'text'	=> '查看安全接入状态',	'url'	=> 'secstate.php',		'target'=> ''),
		array('group'	=> array(2),	'text'	=> '重启安全接入服务',	'url'	=> 'secreboot.php',		'target'=> ''),
	), 
);*/

/*$Glo_Ary_Menu[] = array(
	'group'	=> array(),
	'text'	=> '服务器设置',
	'url'	=> '',
	'target'=> '',
	'child'	=> array(
		array('group'	=> array(2), 	'text'	=> '认证服务器设置',		'url'	=> 'auth.php',			'target'=> ''),
		array('group'	=> array(2), 	'text'	=> '校时服务器设置',		'url'	=> 'timeser.php',		'target'=> ''),
		array('group'	=> array(2),	'text'	=> '日志服务器设置',		'url'	=> 'logserver.php',		'target'=> ''),
	), 
);*/

/*$Glo_Ary_Menu[] = array(
	'group'	=> array(),	//当'child'不为空时，其可见性由'child'决定
	'text'	=> '证书管理',
	'url'	=> '',
	'target'=> '',
	'child'	=> array(
		array('group'	=> array(2), 	'text'	=> '售票终端证书',	'url'	=> 'cert1.php',		'target'=> ''),
		array('group'	=> array(2), 	'text'	=> '安全通信证书',	'url'	=> 'cert2.php',		'target'=> ''),
	), 
);*/

/*$Glo_Ary_Menu[] = array(
	'group'	=> array(),	//当'child'不为空时，其可见性由'child'决定
	'text'	=> '审计管理',
	'url'	=> '',
	'target'=> '',
	'child'	=> array(
		array('group'=> array(2,4), 	'text'	=> '客户端操作日志',	'url'	=> 'logoperation.php',	'target'=> ''),
		array('group'=> array(2,4), 	'text'	=> '客户端错误日志',	'url'	=> 'logerror.php',		'target'=> ''),
		array('group'=> array(2,4), 	'text'	=> '用户登录日志',	'url'	=> 'logenter.php',		'target'=> ''),
		array('group'=> array(2,4), 	'text'	=> '用户在线时长日志',	'url'	=> 'logonline.php',		'target'=> ''),
		array('group'=> array(2,4), 	'text'	=> '用户时长日志',	'url'	=> 'logoffline.php',	'target'=> ''),
		array('group'=> array(2,4), 	'text'	=> 'WEB管理日志',		'url'	=> 'logwebadmin.php',	'target'=> ''),
	), 
);*/

/*$Glo_Ary_Menu[] = array(
	'group'	=> array(),	//当'child'不为空时，其可见性由'child'决定
	'text'	=> '诊断工具',
	'url'	=> '',
	'target'=> '',
	'child'	=> array(
		array('group'=> array(), 	'text'	=> 'Ping测试',		'url'	=> 'ping.php',		'target'=> ''),
		array('group'=> array(), 	'text'	=> 'Telnet测试',		'url'	=> 'telnet.php',	'target'=> ''),
		array('group'=> array(), 	'text'	=> 'log查看',		'url'	=> 'logtest.php',	'target'=> ''),
		array('group'=> array(), 	'text'	=> '配置查看',		'url'	=> 'configfile.php',	'target'=> ''),
		array('group'=> array(), 	'text'	=> '系统信息',		'url'	=> 'sysinfo.php',	'target'=> ''),
		array('group'=> array(), 	'text'	=> '接口信息',		'url'	=> 'tcpdump.php',	'target'=> ''),
	), 
);*/

/*$Glo_Ary_Menu[] = array(
	'group'	=> array(),	//当'child'不为空时，其可见性由'child'决定
	'text'	=> '备份与恢复',
	'url'	=> '',
	'target'=> '',
	'child'	=> array(
		array('group'=> array(2,3), 	'text'	=> '备份配置',		'url'	=> 'backup.php',	'target'=> ''),
		array('group'=> array(2,3), 	'text'	=> '恢复配置',		'url'	=> 'restore.php',	'target'=> ''),
		//array('group'=> array(2,3), 	'text'	=> '恢复出厂设置',	'url'	=> 'reset.php',		'target'=> ''),
	),
);*/

/*$Glo_Ary_Menu[] = array(
	'group'	=> array(),	//当'child'不为空时，其可见性由'child'决定
	'text'	=> 'system manage',
	'url'	=> '',
	'target'=> '',
	'child'	=> array(
		array('group'=> array(), 		'text'	=> '密码修改',		'url'	=> 'password.php',	'target'=> ''),
		array('group'=> array(1), 		'text'	=> '管理员帐号',		'url'	=> 'adminlist.php',	'target'=> ''),
		array('group'=> array(2,3), 	'text'	=> '设置时钟',		'url'	=> 'settime.php',	'target'=> ''),
		//array('group'=> array(2,3), 	'text'	=> '软件升级',		'url'	=> 'softupdateinfo.php',	'target'=> ''),
		array('group'=> array(2,3), 	'text'	=> '重启系统',		'url'	=> 'secreboot.php',	'target'=> ''),
		array('group'=> array(), 		'text'	=> '退出系统',		'url'	=> 'javascript:gotoLogout()','target'=> '_self'),
	),
);*/
?>