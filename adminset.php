<?php
require("services/AtherFrameWork.php");

global $Obj_Frame;
global $Ary_Result;
global $Ary_Group;

$Obj_Frame	= new AtherFrameWork();
$Ary_Group	= $Obj_Frame->load_page("UserManager::getgroups");
$Ary_Result = $Obj_Frame->user_popedom("UserManager::setuser",true);
$Ary_Result	= $Obj_Frame->load_page("UserManager::getuser",FuncExt::getvalue('user'),false);
//print_r($Ary_Group);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>设置管理员帐号 - <?=_GLO_PROJECT_FNAME_?></title>
<link type="text/css" rel="stylesheet" href="css.css" />
</head>
<body>
<form name="frm" id="frm" action="submit.php" onsubmit="javascript:return User_Update(this);"  submitwin="ajax" method="post">
  <table align="center" cellpadding="0" cellspacing="0" border="0" class="tab">
    <caption class="nav">
    <div><span></span>设置管理员帐号</div>
    </caption>
    <tbody>
      <tr>
        <td class="t">管理员帐号：</td>
        <td class="c"><?=$Ary_Result['result']['user']?><input name="user" type="hidden" value="<?=$Ary_Result['result']['user']?>" /></td>
      </tr>
      <tr>
        <td class="t">管理员名称：</td>
        <td class="c"><input id="name" name="name" type="text" class="input" value="<?=$Ary_Result['result']['user']?>"/></td>
      </tr>
      <tr>
        <td class="t">角色权限：</td>
        <td class="c"><select id="group" name="group">
        	<?php
				$group = $Ary_Result['result']['group'];
            	if ($group==1){
					echo("<option value=\"". $Ary_Result['result']['group'] ."\">");
					echo(isset($Ary_Group['result']['groups'][$group]) ? $Ary_Group['result']['groups'][$group]['description'] : "[". $group ."]");
					echo("</option>\r\n");
				}
				else{
               		if (is_array($Ary_Group['result']['groups'])){
                		foreach($Ary_Group['result']['groups'] as $key=>$value){
                       		if ($key==1){ continue; }
							echo("<option value=\"".$key."\"");
							if ($group==$key){ echo(" selected"); }
							echo(">".$value['description']."</option>\r\n");
						}
                    }
                }
                ?>
          </select></td>
      </tr>
      <tr>
        <td colspan="2" class="f">
      		<div class="left130">
        	<input type="submit" id="btnsave" name="btnsave" value="保存" class="btn"/>
          	<input type="reset" id="btnreset" value="重置" class="btn" />
          	<input name="php_interface" type="hidden" id="php_interface" value="UserManager::setuser" />
            <input name="php_parameter" type="hidden" id="php_parameter" value="[['user','name','group']]" />
            <input name="php_returnmode" type="hidden" id="php_returnmode" value="normal" />
            </div>
        </td>
      </tr>
    </tbody>
  </table>
</form>
<?php
require('footer.html');
require('loadjs.html');
?>
<script type="text/javascript" src="js/usermanager.js"></script>
<?php
unset($Ary_Result); $Ary_Result = NULL;
unset($Obj_Frame);	$Obj_Frame  = NULL;
unset($Ary_Group);	$Ary_Group	= NULL;
?>
</body>
</html>