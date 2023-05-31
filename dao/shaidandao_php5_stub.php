<?php
// source idl: c2cent.dao.shaidan.ShaiDanDao.java
namespace c2cent;
require_once "shaidandao_php5_xxoo.php";

namespace c2cent\dao\shaidan;
class NewShaiDanReq{
	private $_arr_value=array();	//数组形式的类
	private $tradeid;	//<uint64_t> 大订单ID(版本>=0)
	private $dealid;	//<uint64_t> 小订单ID(版本>=0)
	private $selleruin;	//<uint64_t> (版本>=0)
	private $buyeruin;	//<uint64_t> (版本>=0)
	private $timeout;	//<uint64_t> 晒单超时时间(版本>=0)
	private $evaltime;	//<uint64_t> 评价时间(版本>=0)
	private $evallevel;	//<int> 评价等级(版本>=0)
	private $commid;	//<uint64_t> 商品ID(版本>=0)
	private $url;	//<std::vector<std::string> > 大图小图URL(版本>=0)
	private $content;	//<std::vector<std::string> > (版本>=0)
	private $machineKey;	//<std::string> (版本>=0)
	private $source;	//<std::string> (版本>=0)
	private $inReserve;	//<std::string> (版本>=0)

	function __construct() {
		$this->tradeid = 0;	//<uint64_t>
		$this->dealid = 0;	//<uint64_t>
		$this->selleruin = 0;	//<uint64_t>
		$this->buyeruin = 0;	//<uint64_t>
		$this->timeout = 0;	//<uint64_t>
		$this->evaltime = 0;	//<uint64_t>
		$this->evallevel = 0;	//<int>
		$this->commid = 0;	//<uint64_t>
		$this->url = new \stl_vector2('stl_string');	//<std::vector<std::string> >
		$this->content = new \stl_vector2('stl_string');	//<std::vector<std::string> >
		$this->machineKey = "";	//<std::string>
		$this->source = "";	//<std::string>
		$this->inReserve = "";	//<std::string>
	}

	function __set($name,$val){
		if(isset($this->$name)){
			if(is_object($this->$name)){
				if(!is_array($val)) exit("NewShaiDanReq\\{$name}：请直接赋值为数组，无需new ***。");
				if(strpos(get_class($this->$name), 'stl_')===0){
					$class=$this->$name->element_type;
					$arr = array();	
					if(class_exists($class,false)){						
						for($i=0;$i<count($val);$i++){
							$arr[$i]=new $class();
							foreach($val[$i] as $k => $v){
								$arr[$i]->$k=$v;
							}	
						}
					}else{
						$arr=$val;
					}
					$this->$name->setValue($arr);				
				}else{
					foreach($val as $k => $v){
						$this->$name->$k=$v;
					}
				}
			}else{
				if($name=="version" && ($val < 0 || $val > $this->version)){
					exit("Version error.It must be > 0 and < {$this->version} (default version).Now value is {$val}.");
				}
				$this->$name=$val;
			}
			if(isset($this->{$name.'_u'})){
				$this->{$name.'_u'}=1;
			}
		}else{
			exit("NewShaiDanReq\\{$name}：不存在此变量，请查询stub。");
		}
	}

	function Serialize($bs){
		$bs->pushUint64_t($this->tradeid);	//<uint64_t> 大订单ID
		$bs->pushUint64_t($this->dealid);	//<uint64_t> 小订单ID
		$bs->pushUint64_t($this->selleruin);	//<uint64_t> 
		$bs->pushUint64_t($this->buyeruin);	//<uint64_t> 
		$bs->pushUint64_t($this->timeout);	//<uint64_t> 晒单超时时间
		$bs->pushUint64_t($this->evaltime);	//<uint64_t> 评价时间
		$bs->pushInt32_t($this->evallevel);	//<int> 评价等级
		$bs->pushUint64_t($this->commid);	//<uint64_t> 商品ID
		$bs->pushObject($this->url,'stl_vector');	//<std::vector<std::string> > 大图小图URL
		$bs->pushObject($this->content,'stl_vector');	//<std::vector<std::string> > 
		$bs->pushString($this->machineKey);	//<std::string> 
		$bs->pushString($this->source);	//<std::string> 
		$bs->pushString($this->inReserve);	//<std::string> 

		return $bs->isGood();
	}
	
	function getCmdId(){
		return 0x52031801;
	}
}

class NewShaiDanResp{
	private $result;	
	private $_arr_value=array();	//数组形式的类
	private $sid;	//<uint64_t> 晒单ID(版本>=0)
	private $outReserve;	//<std::string> (版本>=0)

	function __get($name){
		if($name=="errmsg" && !array_key_exists('errmsg', $this->_arr_value)){
			return "errmsg is not define.";
		}
		return $this->_arr_value[$name];
	}
	
	function Unserialize($bs){
		$this->_arr_value['result'] = $bs->popUint32_t();
		$this->_arr_value['sid'] = $bs->popUint64_t();	//<uint64_t> 晒单ID
		$this->_arr_value['outReserve'] = $bs->popString();	//<std::string> 

	}

	function getCmdId() {
		return 0x52038801;
	}
}

namespace c2cent\dao\shaidan;
class ShaiDanInfoReq{
	private $_arr_value=array();	//数组形式的类
	private $dealid;	//<uint64_t> (版本>=0)
	private $machineKey;	//<std::string> (版本>=0)
	private $source;	//<std::string> (版本>=0)
	private $inReserve;	//<std::string> (版本>=0)

	function __construct() {
		$this->dealid = 0;	//<uint64_t>
		$this->machineKey = "";	//<std::string>
		$this->source = "";	//<std::string>
		$this->inReserve = "";	//<std::string>
	}

	function __set($name,$val){
		if(isset($this->$name)){
			if(is_object($this->$name)){
				if(!is_array($val)) exit("ShaiDanInfoReq\\{$name}：请直接赋值为数组，无需new ***。");
				if(strpos(get_class($this->$name), 'stl_')===0){
					$class=$this->$name->element_type;
					$arr = array();	
					if(class_exists($class,false)){						
						for($i=0;$i<count($val);$i++){
							$arr[$i]=new $class();
							foreach($val[$i] as $k => $v){
								$arr[$i]->$k=$v;
							}	
						}
					}else{
						$arr=$val;
					}
					$this->$name->setValue($arr);				
				}else{
					foreach($val as $k => $v){
						$this->$name->$k=$v;
					}
				}
			}else{
				if($name=="version" && ($val < 0 || $val > $this->version)){
					exit("Version error.It must be > 0 and < {$this->version} (default version).Now value is {$val}.");
				}
				$this->$name=$val;
			}
			if(isset($this->{$name.'_u'})){
				$this->{$name.'_u'}=1;
			}
		}else{
			exit("ShaiDanInfoReq\\{$name}：不存在此变量，请查询stub。");
		}
	}

	function Serialize($bs){
		$bs->pushUint64_t($this->dealid);	//<uint64_t> 
		$bs->pushString($this->machineKey);	//<std::string> 
		$bs->pushString($this->source);	//<std::string> 
		$bs->pushString($this->inReserve);	//<std::string> 

		return $bs->isGood();
	}
	
	function getCmdId(){
		return 0x52031802;
	}
}

class ShaiDanInfoResp{
	private $result;	
	private $_arr_value=array();	//数组形式的类
	private $tradeid;	//<uint64_t> 返回信息(版本>=0)
	private $sid;	//<uint64_t> (版本>=0)
	private $selleruin;	//<uint64_t> (版本>=0)
	private $buyeruin;	//<uint64_t> (版本>=0)
	private $createtime;	//<uint64_t> (版本>=0)
	private $timeout;	//<uint64_t> (版本>=0)
	private $evaltime;	//<uint64_t> (版本>=0)
	private $evallevel;	//<int> (版本>=0)
	private $commid;	//<uint64_t> (版本>=0)
	private $photonum;	//<int> (版本>=0)
	private $url;	//<std::vector<std::string> > (版本>=0)
	private $content;	//<std::vector<std::string> > (版本>=0)
	private $state;	//<int> 晒单状态(版本>=0)
	private $reason;	//<std::string> 晒单拒绝原因(版本>=0)
	private $outReserve;	//<std::string> (版本>=0)

	function __get($name){
		if($name=="errmsg" && !array_key_exists('errmsg', $this->_arr_value)){
			return "errmsg is not define.";
		}
		return $this->_arr_value[$name];
	}
	
	function Unserialize($bs){
		$this->_arr_value['result'] = $bs->popUint32_t();
		$this->_arr_value['tradeid'] = $bs->popUint64_t();	//<uint64_t> 返回信息
		$this->_arr_value['sid'] = $bs->popUint64_t();	//<uint64_t> 
		$this->_arr_value['selleruin'] = $bs->popUint64_t();	//<uint64_t> 
		$this->_arr_value['buyeruin'] = $bs->popUint64_t();	//<uint64_t> 
		$this->_arr_value['createtime'] = $bs->popUint64_t();	//<uint64_t> 
		$this->_arr_value['timeout'] = $bs->popUint64_t();	//<uint64_t> 
		$this->_arr_value['evaltime'] = $bs->popUint64_t();	//<uint64_t> 
		$this->_arr_value['evallevel'] = $bs->popInt32_t();	//<int> 
		$this->_arr_value['commid'] = $bs->popUint64_t();	//<uint64_t> 
		$this->_arr_value['photonum'] = $bs->popInt32_t();	//<int> 
		$this->_arr_value['url'] = $bs->popObject('stl_vector<stl_string>');	//<std::vector<std::string> > 
		$this->_arr_value['content'] = $bs->popObject('stl_vector<stl_string>');	//<std::vector<std::string> > 
		$this->_arr_value['state'] = $bs->popInt32_t();	//<int> 晒单状态
		$this->_arr_value['reason'] = $bs->popString();	//<std::string> 晒单拒绝原因
		$this->_arr_value['outReserve'] = $bs->popString();	//<std::string> 

	}

	function getCmdId() {
		return 0x52038802;
	}
}

namespace c2cent\dao\shaidan;
class UpdateShaiDanReq{
	private $_arr_value=array();	//数组形式的类
	private $dealid;	//<uint64_t> 用户更新晒单信息(版本>=0)
	private $sid;	//<uint64_t> (版本>=0)
	private $selleruin;	//<uint64_t> (版本>=0)
	private $url;	//<std::vector<std::string> > (版本>=0)
	private $content;	//<std::vector<std::string> > (版本>=0)
	private $machineKey;	//<std::string> (版本>=0)
	private $source;	//<std::string> (版本>=0)
	private $inReserve;	//<std::string> (版本>=0)

	function __construct() {
		$this->dealid = 0;	//<uint64_t>
		$this->sid = 0;	//<uint64_t>
		$this->selleruin = 0;	//<uint64_t>
		$this->url = new \stl_vector2('stl_string');	//<std::vector<std::string> >
		$this->content = new \stl_vector2('stl_string');	//<std::vector<std::string> >
		$this->machineKey = "";	//<std::string>
		$this->source = "";	//<std::string>
		$this->inReserve = "";	//<std::string>
	}

	function __set($name,$val){
		if(isset($this->$name)){
			if(is_object($this->$name)){
				if(!is_array($val)) exit("UpdateShaiDanReq\\{$name}：请直接赋值为数组，无需new ***。");
				if(strpos(get_class($this->$name), 'stl_')===0){
					$class=$this->$name->element_type;
					$arr = array();	
					if(class_exists($class,false)){						
						for($i=0;$i<count($val);$i++){
							$arr[$i]=new $class();
							foreach($val[$i] as $k => $v){
								$arr[$i]->$k=$v;
							}	
						}
					}else{
						$arr=$val;
					}
					$this->$name->setValue($arr);				
				}else{
					foreach($val as $k => $v){
						$this->$name->$k=$v;
					}
				}
			}else{
				if($name=="version" && ($val < 0 || $val > $this->version)){
					exit("Version error.It must be > 0 and < {$this->version} (default version).Now value is {$val}.");
				}
				$this->$name=$val;
			}
			if(isset($this->{$name.'_u'})){
				$this->{$name.'_u'}=1;
			}
		}else{
			exit("UpdateShaiDanReq\\{$name}：不存在此变量，请查询stub。");
		}
	}

	function Serialize($bs){
		$bs->pushUint64_t($this->dealid);	//<uint64_t> 用户更新晒单信息
		$bs->pushUint64_t($this->sid);	//<uint64_t> 
		$bs->pushUint64_t($this->selleruin);	//<uint64_t> 
		$bs->pushObject($this->url,'stl_vector');	//<std::vector<std::string> > 
		$bs->pushObject($this->content,'stl_vector');	//<std::vector<std::string> > 
		$bs->pushString($this->machineKey);	//<std::string> 
		$bs->pushString($this->source);	//<std::string> 
		$bs->pushString($this->inReserve);	//<std::string> 

		return $bs->isGood();
	}
	
	function getCmdId(){
		return 0x52031803;
	}
}

class UpdateShaiDanResp{
	private $result;	
	private $_arr_value=array();	//数组形式的类
	private $outReserve;	//<std::string> 返回信息(版本>=0)

	function __get($name){
		if($name=="errmsg" && !array_key_exists('errmsg', $this->_arr_value)){
			return "errmsg is not define.";
		}
		return $this->_arr_value[$name];
	}
	
	function Unserialize($bs){
		$this->_arr_value['result'] = $bs->popUint32_t();
		$this->_arr_value['outReserve'] = $bs->popString();	//<std::string> 返回信息

	}

	function getCmdId() {
		return 0x52038803;
	}
}

namespace c2cent\dao\shaidan;
class UpdateShaiDanStateReq{
	private $_arr_value=array();	//数组形式的类
	private $dwDealId;	//<uint64_t> 小订单ID(版本>=0)
	private $dwSId;	//<uint64_t> (版本>=0)
	private $dwState;	//<uint32_t> (版本>=0)
	private $strReason;	//<std::string> (版本>=0)
	private $strMachineKey;	//<std::string> (版本>=0)
	private $strSource;	//<std::string> (版本>=0)
	private $strInReserve;	//<std::string> (版本>=0)

	function __construct() {
		$this->dwDealId = 0;	//<uint64_t>
		$this->dwSId = 0;	//<uint64_t>
		$this->dwState = 0;	//<uint32_t>
		$this->strReason = "";	//<std::string>
		$this->strMachineKey = "";	//<std::string>
		$this->strSource = "";	//<std::string>
		$this->strInReserve = "";	//<std::string>
	}

	function __set($name,$val){
		if(isset($this->$name)){
			if(is_object($this->$name)){
				if(!is_array($val)) exit("UpdateShaiDanStateReq\\{$name}：请直接赋值为数组，无需new ***。");
				if(strpos(get_class($this->$name), 'stl_')===0){
					$class=$this->$name->element_type;
					$arr = array();	
					if(class_exists($class,false)){						
						for($i=0;$i<count($val);$i++){
							$arr[$i]=new $class();
							foreach($val[$i] as $k => $v){
								$arr[$i]->$k=$v;
							}	
						}
					}else{
						$arr=$val;
					}
					$this->$name->setValue($arr);				
				}else{
					foreach($val as $k => $v){
						$this->$name->$k=$v;
					}
				}
			}else{
				if($name=="version" && ($val < 0 || $val > $this->version)){
					exit("Version error.It must be > 0 and < {$this->version} (default version).Now value is {$val}.");
				}
				$this->$name=$val;
			}
			if(isset($this->{$name.'_u'})){
				$this->{$name.'_u'}=1;
			}
		}else{
			exit("UpdateShaiDanStateReq\\{$name}：不存在此变量，请查询stub。");
		}
	}

	function Serialize($bs){
		$bs->pushUint64_t($this->dwDealId);	//<uint64_t> 小订单ID
		$bs->pushUint64_t($this->dwSId);	//<uint64_t> 
		$bs->pushUint32_t($this->dwState);	//<uint32_t> 
		$bs->pushString($this->strReason);	//<std::string> 
		$bs->pushString($this->strMachineKey);	//<std::string> 
		$bs->pushString($this->strSource);	//<std::string> 
		$bs->pushString($this->strInReserve);	//<std::string> 

		return $bs->isGood();
	}
	
	function getCmdId(){
		return 0x52031804;
	}
}

class UpdateShaiDanStateResp{
	private $result;	
	private $_arr_value=array();	//数组形式的类
	private $dwResult;	//<uint32_t> 返回结果(版本>=0)
	private $strOutReserve;	//<std::string> (版本>=0)

	function __get($name){
		if($name=="errmsg" && !array_key_exists('errmsg', $this->_arr_value)){
			return "errmsg is not define.";
		}
		return $this->_arr_value[$name];
	}
	
	function Unserialize($bs){
		$this->_arr_value['result'] = $bs->popUint32_t();
		$this->_arr_value['dwResult'] = $bs->popUint32_t();	//<uint32_t> 返回结果
		$this->_arr_value['strOutReserve'] = $bs->popString();	//<std::string> 

	}

	function getCmdId() {
		return 0x52038804;
	}
}
