<?php

require("services/AtherFrameWork.php");
require("mdb/MenuData.php");

global $Obj_Frame;

require("services/Config.php");
$Obj_Frame = new AtherFrameWork();
$res = $Obj_Frame->is_user_login();
if (!$res){
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: enter.php');
	exit();
}

define('AXITECH_CLOUD', 1);
require(dirname(__FILE__).'/mainpage.php');
?>