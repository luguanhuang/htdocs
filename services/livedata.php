
<script>
window.onload = function () {
//console.log(window.location.href)//此处会打印出当前页面的href值，为
let url = window.location.href.split('?');
//console.log(url[1]);//打印出来是一个数组
}
</script>

<?php

require_once 'testtmp.php';
require("services/AtherFrameWork.php");
//require("submitlivedata.php");
global $Obj_Frame;
global $Ary_Result;

TLOG_INIT(TLOG_LEVEL_M, 10, 1024000, "./logs", "livedata",0);
$var=explode("&",$_SERVER["QUERY_STRING"]);

$devname=explode("=",$var[1]);
TLOG_MSG("livedata: func begin22 data=".$devname[0]." devname=".$devname[1]);
$Obj_Frame = new AtherFrameWork();
$Ary_Result= $Obj_Frame->load_page("Channel::getChannelInfo",$devname[1],false);

/*if (is_array($Ary_Result['result']['tab']))
{
	foreach($Ary_Result['result']['tab'] as $k=>$row)
	{
		TLOG_MSG("chid=$row['chid'] tagid=$row['tagid'] tagname=$row['tagname'] tagdata=$row['tagdata']");
	}
}*/
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link type="text/css" rel="stylesheet" href="css.css" />

</head>
<body>

<?php
		if (is_array($Ary_Result['result']['data'])){
			foreach($Ary_Result['result']['data'] as $k=>$row){
				
		?>
		
		<button id='<?=$devname[1]?><?=$row['ch']?>' name='<?=$row['ch']?>' class="tablink" onclick="openPage('<?=$row['ch']?>', this, 'red')"><?=$devname[1]?>_Channel<?=$row['ch']?></button>
		
		
		
 <?php }
		}
		?>


<?php
		if (is_array($Ary_Result['result']['data'])){
			foreach($Ary_Result['result']['data'] as $k=>$row){
				
		?>
		
		<div id="<?=$row['ch']?>" class="tabcontent"></div>
		<h3></h3>
		
 <?php }
	}
	?>

<?php
      require('footer.html');
      require('loadjs.html');
    ?>
    <script type="text/javascript" src="js/route.js"></script>

<script>

function reqData(pageName) 
{
	console.log("reqData pageName="+pageName);
	var oStr = '';
	var postData = {};
	var oAjax = null;
	//post提交的数据
	postData = {"cmd":"updatedata","devname":"<?=$devname[1]?>","channelid":pageName};
	//这里需要将json数据转成post能够进行提交的字符串  name1=value1&name2=value2格式
	postData = (function(value){
	　　for(var key in value){
	　　　　oStr += key+"="+value[key]+"&";
	　　};
	
		oStr = oStr.substr(0, oStr.length - 1);  
	　　return oStr;
	}(postData));
	//这里进行HTTP请求
	try{
	　　oAjax = new XMLHttpRequest();
	}catch(e){
	　　oAjax = new ActiveXObject("Microsoft.XMLHTTP");
	};
	//post方式打开文件
	oAjax.open('post','submitlivedata.php?='+Math.random(),true);
	//post相比get方式提交多了个这个
	oAjax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	//post发送数据
	oAjax.send(postData);
	oAjax.onreadystatechange = function(){
	　　//当状态为4的时候，执行以下操作
	　　if(oAjax.readyState == 4){
	　　　　try{
		
				console.log("data="+oAjax.responseText);
				json = JSON.parse(oAjax.responseText);
				console.log("tab2="+json.result.tab.length);
				for(var i=0;i<json.result.tab.length;i++)
				{
					console.log("tabname="+json.result.tab[i].tagname);
				}
	　　　　}catch(e){
	　　　　　　alert('你访问的页面出错了');
	　　　　};
	　　};
	};
}

function _reqData(pageName){
 
       return function(){
             reqData(pageName);
       }
}

function openPage(pageName,elmnt,color) 
{
	//reqData(pageName);
	//console.log("page="+pageName);
	setInterval(_reqData(pageName),5000);
	
	<?php
	if (is_array($Ary_Result['result']['tab'])){
		foreach($Ary_Result['result']['tab'] as $k=>$row){
			
	?>
	document.getElementById(<?=$row['chid']?>).innerHTML = "";
	<?php }
	}
	?>
	var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablink");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].style.backgroundColor = "";
  }
  document.getElementById(pageName).style.display = "block";
  elmnt.style.backgroundColor = '#008880';
	<?php
	if (is_array($Ary_Result['result']['tab'])){
		foreach($Ary_Result['result']['tab'] as $k=>$row){
	?>
	
	if (pageName == <?=$row['chid']?>)
	{
		<?php
			$dtype = 0;
			if (is_array($Ary_Result['result']['data']))
			{
				foreach($Ary_Result['result']['data'] as $kk=>$tmprow)
				{
					if ($tmprow['ch'] == $row['chid'])
					{
						$dtype = $tmprow['dtype'];
						break;
					}
				}
			}
		?>
		var btn1 = document.getElementById(pageName);
		if (<?=$dtype?> == 1)
		{
			var input1 = document.createElement('input');
			input1.setAttribute('type', 'text');
			input1.setAttribute('style', 'width:30px; height=20px');
			input1.setAttribute('name', 'tagid');
			input1.setAttribute('id', 'tagid');
			input1.setAttribute('class', 'input');
			input1.setAttribute('className', 'input');
			input1.setAttribute('value', '<?=$row['tagid']?>');
			input1.setAttribute('disabled', 'True');  //设置文本为只读类型  
			
			btn1.insertBefore(input1,null);
			
			var input2 = document.createElement('input');
			input2.setAttribute('type', 'text');
			input2.setAttribute('style', 'width:260px; height=20px');
			input2.setAttribute('name', 'tagname');
			input2.setAttribute('id', 'tagname');
			input2.setAttribute('class', 'input');
			input2.setAttribute('disabled', 'True');  //设置文本为只读类型  
			input2.setAttribute('value', '<?=$row['tagname']?>(<?=$row['tagdesc']?>)');
			
			btn1.insertBefore(input2,null);
			
			var input4 = document.createElement('input');
			input4.setAttribute('style', 'width:80px; height=20px');
			input4.setAttribute('type', 'text');
			input4.setAttribute('name', 'tagdata');
			input4.setAttribute('id', 'tagdata');
			input4.setAttribute('class', 'input');
			input4.setAttribute('value', '<?=$row['tagdata']?>');
			input4.setAttribute('disabled', 'True');  //设置文本为只读类型  
			
			btn1.insertBefore(input4,null);
			if (<?=$row['coiltype']?> == 1)
			{
				if (<?=$row['tagdata']?> == 0)
					input4.style.background = "#FF0000";
				else
					input4.style.background = "#00FF00";
			}
			else
			{
				if (<?=$row['tagdata']?> == 1)
					input4.style.background = "#FF0000";
				else
					input4.style.background = "#00FF00";
			}
			
			if (<?=$row['writeenable']?> == 1)
			{
				
				//添加label ，存放指标名称  
				var label_var = document.createElement("label");
				label_var.setAttribute("for","checkbox");
				label_var.setAttribute('class', 'switch');
				//label_var.appendChild(label_text);

				//btn1.insertBefore(label_var,null);
				
				var input0 = document.createElement('input');
			
				input0.setAttribute('type', 'checkbox');
				input0.setAttribute('name', 'checkbox');
				input0.setAttribute('id', 'checkbox');
				input0.setAttribute('class', 'input');
				if (<?=$row['tagdata']?> == 1)
					input0.setAttribute("checked","checked");
				input0.onclick = function(event) 
				{ 
					console.log("a.onclick val="+input0.value);
					if ("on" == input0.value)
					{
						input0.value = "";
						console.log("a.onclick 2111 val="+input0.value+" chk="+input0.checked+" vvv="+input0.value);
						input0.checked = false;
						console.log("a.onclick ee val="+input0.value+" chk="+input0.checked);
					}
						
					
				}				 
				
				btn1.insertBefore(input0,null);
			}
		}
		else
		{
			var input1 = document.createElement('input');
			input1.setAttribute('type', 'text');
			input1.setAttribute('style', 'width:30px; height=20px');
			input1.setAttribute('name', 'tagid');
			input1.setAttribute('id', 'tagid');
			input1.setAttribute('class', 'input');
			input1.setAttribute('className', 'input');
			input1.setAttribute('value', '<?=$row['tagid']?>');
			input1.setAttribute('disabled', 'True');  //设置文本为只读类型  
			
			btn1.insertBefore(input1,null);
			
			var input2 = document.createElement('input');
			input2.setAttribute('type', 'text');
			input2.setAttribute('style', 'width:260px; height=20px');
			input2.setAttribute('name', 'tagname');
			input2.setAttribute('id', 'tagname');
			input2.setAttribute('class', 'input');
			input2.setAttribute('value', '<?=$row['tagname']?>(<?=$row['tagdesc']?>)');
			input2.setAttribute('disabled', 'True');  //设置文本为只读类型  
			//var btn1 = document.getElementById(pageName);
			btn1.insertBefore(input2,null);
			
			var input4 = document.createElement('input');
			input4.setAttribute('style', 'width:80px; height=20px');
			input4.setAttribute('type', 'text');
			input4.setAttribute('name', 'tagdata');
			input4.setAttribute('id', 'tagdata');
			input4.setAttribute('class', 'input');
			input4.setAttribute('value', '<?=$row['tagdata']?>');
			input4.setAttribute('disabled', 'True');  //设置文本为只读类型  
			//var btn1 = document.getElementById(pageName);
			btn1.insertBefore(input4,null);
			
			if (<?=$row['writeenable']?> == 1)
			{
				var chgdata = document.createElement('input');
				chgdata.setAttribute('style', 'width:80px; height=20px');
				chgdata.setAttribute('type', 'text');
				chgdata.setAttribute('name', 'chgdata');
				chgdata.setAttribute('id', 'chgdata');
				chgdata.setAttribute('class', 'input');
				chgdata.setAttribute('value', '0');
				
				//var btn1 = document.getElementById(pageName);
				btn1.insertBefore(chgdata,null);
			}
		}
		
		if (<?=$row['writeenable']?> == 1)
		{
			var input5 = document.createElement('input');
			input5.setAttribute('type', 'button');
			input5.setAttribute('name', 'tagdata');
			input5.setAttribute('id', 'tagdata');
			input5.setAttribute('class', 'btn');
			input5.setAttribute('value', 'write');
			input5.onclick = function(event) 
			{ 
				console.log("input5.onclick val=");
			}	
			
			btn1.insertBefore(input5,null);
		}
		
		 var br = document.createElement("br");  
          btn1.insertBefore(br,null);  
	}
	
	<?php }
	}
	?>
	 
}

// Get the element with id="defaultOpen" and click on it
//document.getElementById("defaultOpen").click();
</script>
   

   
</body>
</html> 
