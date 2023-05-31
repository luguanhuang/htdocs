<?php
  require("services/AtherFrameWork.php");
  require("services/LogService.php");
  global $Obj_Frame;
  global $Ary_Result;
  $Obj_Frame = new AtherFrameWork();
  //$Ary_Result	= $Obj_Frame->load_page("LogService::getLog()");
  //$Ary_Result= $Obj_Frame->user_getlogin(true);
  //返回菜单选项
  $Ary_List	= array(
  	'logtype'	=> LogService::getLogTypes()
  );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Adsl - <?=_GLO_PROJECT_FNAME_?></title>
    <link type="text/css" rel="stylesheet" href="css.css">
  </head>
  <body >
    <div class="nav">
      <div><span></span>日志</div>
    </div>
    <form method="post" name="Adslresult" id="Adslresult" action="submit.php" submitwin="ajax">
    	<table align="center" cellpadding="0" cellspacing="0" border="0"  border="0" class="tab2 b" style="width: 99%; height: 95%;">   
        <tr>
          <td style="width: 10%; padding: 7px 0;">
            <select style="padding: 3px 0;" name="logtype" id="logtype">
              <?php
                foreach($Ary_List['logtype'] as $key=>$value){
      			      ?>
                    <option value="<?=$value?>"><?=$key?></option>
                  <?php
      			    }
      		  	?>
            </select>
            <input name="exportfile" type="hidden" id="exportfile" value="0">
            <input name="php_interface" type="hidden" id="php_interface" value="LogService::getNewLog">
            <input name="php_parameter" type="hidden" id="php_parameter" value="[['logtype','exportfile']]">
            <input name="php_returnmode" type="hidden" id="php_returnmode" value="json">
            <input type="button" id="logbt" name="logbt" value="查看"  onclick="Log_GetLog();" class="btn">
            <input type="submit" id="logexport" name="logexport" value="导出" onclick="exportfile.value=1;return true;" class="btn">
          </td>
        </tr>
        <tr style="width: 100%; height: 100%;">
          <td align="center" id="cmdbox2" style="width: 100%; height: 100%;">
            <textarea style="width: 100%; height: 600px;" name="Adslmessage" id="Adslmessage"></textarea>
          </td>
        </tr>
      </table>
    </form>
    <?php
      require('footer.html');
      require('loadjs.html');
    ?>
    <script type="text/javascript" src="js/logtest.js"></script>
    <?php
      unset($Ary_Result); $Ary_Result = NULL;
      unset($Obj_Frame);	$Obj_Frame  = NULL;
    ?>
  </body>
</html>