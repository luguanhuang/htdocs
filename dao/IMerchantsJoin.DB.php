<?php

require_once dirname(__FILE__) .'/cfg/database_config.php';
require_once dirname(__FILE__) .'/../common/database_module.php';

class IMerchantsJoin_DB 
{
    var $DB;
    function __construct()
    {
        $this->DB = new DataBase();
    }
    
    function searchResult($condition)
    {
    	$sConditon = "";
        $page = 1;
        $pageSize = 20;
    	foreach($condition as $key => $value)
    	{
            if ($key == "page")
            {
                $page = $value;
                continue;
            }
            if ($key == "pageSize")
            {
                $pageSize = $value;
                continue;
            }
            if ($key == "uin" || $key == "i_classid_1" || $key == "i_classid_2" || $key == "i_classid_3" || $key == "i_status" || $key == "i_b2c_type")
            {
                $sConditon.= " and $key = $value";
                continue;
            }
            if ($key == "s_shop_name" || $key == "s_company_name" )
            {
                $sConditon.= " and $key like '%$value%'";
                continue;
            }
            if ($key == "multi_status")
            {
                $sConditon.= " and i_status in ($value)";
            }
    	}
        
        $returnArray = array("result"=>0, 
                            "data_list"=>array(),
                            "page"=>$page,
                            "totalNum"=>0,
                            "sErrMsg"=>"");

        $ssqlCount = "select count(*) as count from t_wanggou_merchants_join where 1=1 $sConditon";
        $returnArray["totalNum"] = $this->queryCount($ssqlCount);
        $numFrom = $pageSize * ($page - 1);
        $ssql = "select * from t_wanggou_merchants_join where 1=1 $sConditon order by apply_time desc limit $numFrom, $pageSize";
        //echo $ssql;
        $ret = $this->DB->execute_v2 ( $ssql, 'SELECT');

        if ($ret["result"] != 0)
        {
            $returnArray["result"] = $ret["result"];
            $returnArray["sErrMsg"] = $ret["errorMsg"];
            return $returnArray;  
        }

        $returnArray["data_list"] = $ret["data_list"];
        return $returnArray;
    }

    function queryCount($sqlCount)
    {
        $retCount = $this->DB->execute_v2 ( $sqlCount, 'SELECT');
        if ($retCount["result"] == 0)
            return $retCount["data_list"][0]["count"];
        return 0;
    }

    function searchResult_CategoryVerify($condition)
    {
        $condition["multi_status"] = "102, 103";
        return $this->searchResult($condition);
    }

    function GetShopInfoByUin($uin)
    {
        $ssql = "select * from t_wanggou_merchants_join where uin = $uin";
        $ret = $this->DB->execute_v2 ( $ssql, 'SELECT');

        return $ret;
    }

    function getAllGuarantyPayingSeller()
    {
        $ssql = "select uin from t_wanggou_merchants_join where i_status = 106 and i_guaranty_pay_status = 1";
        $ret = $$this->DB->execute_v2 ( $ssql, 'SELECT');
        return array_map(create_function('$item', 'return $item[\'uin\'];'), $ret['data_list']);
    }

    function getAllPaddingSeller()
    {
        $ssql = "select uin from t_wanggou_merchants_join where i_status in (106, 107, 108, 109) ";
        $ret = $this->DB->execute_v2 ( $ssql, 'SELECT');
        return array_map(create_function('$item', 'return $item[\'uin\'];'), $ret['data_list']);
    }

    function searchPaddingCategoryVerifyUin($optTime)
    {
        $ssql = "select * from t_wanggou_merchants_join where 1=1 and i_status in (102, 103) and apply_time < $optTime";
        //echo $ssql;
        $ret = $this->DB->execute_v2 ( $ssql, 'SELECT');
        return $ret;
    }
}
