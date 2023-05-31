<?php
require_once dirname(__FILE__) .'/IMerchantsJoin.DB.php';

$MerchantsJoinDB = new IMerchantsJoin_DB();

$ret = $MerchantsJoinDB->searchResult();
var_dump($ret);

?>
