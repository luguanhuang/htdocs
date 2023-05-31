<?php
//defined('AXITECH_CLOUD') or die('Unauthorized');
require("services/AtherFrameWork.php");
require("mdb/MenuData.php");
TLOG_INIT(TLOG_LEVEL_M, 10, 1024000, "./logs", "indexleft",0);
global $Obj_Frame;
global $Ary_ResultCompany;
global $Ary_ResultGroup;
global $Ary_ResultStation;
global $Ary_ResultDevice;
require("services/Config.php");

error_reporting(0);
session_start();
$Int_Report	= ini_get('error_reporting');
error_reporting($Int_Report);
$tmp = &$_SESSION[_GLO_SESSION_USERINFO_]['userinfo'];
//$user = &$_SESSION[_GLO_SESSION_USERINFO_]['username'];
$useinfo = explode(',',$tmp);
$param="1,";
$param.=$tmp;
$Obj_Frame = new AtherFrameWork();
//$Ary_Result= $Obj_Frame->user_getlogin(true);
$Ary_ResultCompany= $Obj_Frame->load_page("Company::getCompanyList",@FuncExt::getnumber('page'),false);
$Ary_ResultGroup= $Obj_Frame->load_page("Group::getGroupList",$param,false);
$Ary_ResultStation= $Obj_Frame->load_page("Station::getStationInfoList",$param,false);
$Ary_ResultDevice= $Obj_Frame->load_page("Station::getDeviceList",$param,false);

//$Ary_Params = $Ary_Result['result']['pagequery'];
?>


<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link type="text/css" rel="stylesheet" href="css.css" />
<base target="mainFrame" />
</head>
<body style="background: #eeeeee;text-align: left;margin-left: 15px;">
<div>
<form id="frm" name="frm" action="submit.php" onsubmit="" submitwin="ajax" method="post">
<ul id="myUL" style="font-size: 15px;">
	<?php
	

		if (is_array($Ary_ResultCompany['result']['data'])){
			foreach($Ary_ResultCompany['result']['data'] as $kCompany=>$rowCompany){
				if ($useinfo[0] == 1 || $useinfo[0] == 2 || ($useinfo[0] == 2  && ($useinfo[1] == $rowCompany['id'])))
					echo '<li>'.'<span class="caret">'.$rowCompany['companyname'].'</span>'.'<ul class="nested" >';
				
	?>
				
				
		
			
			
				<?php
				
				//echo $rowCompany['id'];
				if (is_array($Ary_ResultGroup['result']['data'])){
					
					foreach($Ary_ResultGroup['result']['data'] as $kGroup=>$rowGroup){
					
					if ($rowGroup['company_id'] != $rowCompany['id'])
						continue;
					
					if ($useinfo[0] == 1 || $useinfo[0] == 2  || (($useinfo[0] == 3 ) && ($useinfo[2] == $rowGroup['id'])))
						echo '<li>'.'<span class="caret">'.$rowGroup['name'].'</span>'.'<ul class="nested" >';
				?>
					
							 <?php
								if (is_array($Ary_ResultStation['result']['data'])){
									foreach($Ary_ResultStation['result']['data'] as $kStation=>$rowStation){
										
									if ($rowStation['company_id'] != $rowGroup['company_id']
										|| $rowStation['group_id'] != $rowGroup['id'])
										continue;
									if ($useinfo[0] == 1 || $useinfo[0] == 2 || $useinfo[0] == 3 || ($useinfo[0] == 4 && ($useinfo[3] == $rowStation['id'])))
										echo '<li>'.'<span class="caret">'.$rowStation['name'].'</span>'.'<ul class="nested" >';
								?>
								
										<?php
											if (is_array($Ary_ResultDevice['result']['data'])){
											foreach($Ary_ResultDevice['result']['data'] as $kDevice=>$rowDevice){
											TLOG_MSG("indexleft: rowDevice['company_id']="
											.$rowDevice['company_id']
											." rowDevice['group_id']=".$rowDevice['group_id']
											." rowDevice['connected']111=".$rowDevice['connected']
											
											
											." rowStation['company_id']=".$rowStation['company_id']
											." rowStation['group_id']=".$rowStation['group_id']
											
											." rowStation['id']=".$rowStation['id']);
											if ($rowDevice['company_id'] != $rowStation['company_id']
											|| $rowDevice['group_id'] != $rowStation['group_id']
											|| $rowDevice['station_id'] != $rowStation['id'])
											continue;
										?>
										<li>
											<a style="color:red"  href="livedata.php?id=<?=$rowDevice['id']?>&devname=<?=$rowDevice['devname']?>&devicedesc=<?=$rowDevice['devicedesc']?>&templatelocation=<?=$rowDevice['templatelocation']?>" onclick="return Tag_Show()" ><?=$rowDevice['devname']?></a>
										</li>
										<?php }
										}
										?>
										
									
							<?php 
							if ($useinfo[0] == 1 || $useinfo[0] == 2 || $useinfo[0] == 3 || ($useinfo[0] == 4 && ($useinfo[3] == $rowStation['id'])))
								echo '</ul>'.'</li>';
							}
							}
							?>
						
				<?php 
				if ($useinfo[0] == 1 || $useinfo[0] == 2 || ($useinfo[0] == 3 && ($useinfo[2] == $rowGroup['id'])))
					echo '</ul>'.'</li>';
				}
				}
				?>
				
			
		
	<?php
	if ($useinfo[0] == 1 || ($useinfo[0] == 2 && ($useinfo[1] == $rowCompany['id'])))
			echo '</ul>'.'</li>';
			
		}
	}
	?>
</ul>  

<input name="php_interface" type="hidden" id="php_interface" value="Router::setRoute" />
<input name="php_parameter" type="hidden" id="php_parameter" value="[['id'],'actstep']" />
<input name="php_returnmode" type="hidden" id="php_returnmode" value="normal" />

</form> 
</div>
<form id="frm2" name="frm2" action="livedata.php" onsubmit="" submitwin="tab_self" method="get">	

	<input name="devname" 		id="devname" 		type="hidden" value="" />
    <input name="php_parameter" type="hidden" id="php_parameter" value="['devname']" />
    <input name="php_returnmode" type="hidden" id="php_returnmode" value="normal" />
</form>

<script type="text/javascript" src="js/route.js"></script>
<script>
var toggler = document.getElementsByClassName("caret");
var i;

for (i = 0; i < toggler.length; i++) {
  toggler[i].addEventListener("click", function() {
    this.parentElement.querySelector(".nested").classList.toggle("active");
    this.classList.toggle("caret-down");
  });
}
</script>

</body>
</html>
