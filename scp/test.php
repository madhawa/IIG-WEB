<?php
require_once 'staff.inc.php';
require_once '../include/mysql.php';
$sql_heda = ' SET updated=NOW() ' .
        ',isactive=' . "'1'" .
        ',isbanned=' . "'0'" .
        ',group_id=' . "'0'" .
        ',firstname=' . "'north'" .
        ',lastname=' . "'dakota'" .
        ',email=' . "'ndakota@dakotamail.com'" .
        ',phone=' . "''" .
        ',mobile=' . "''" .
        ',passwd=' . db_input(md5('droid273')) .
        $sql_baal = 'INSERT INTO ost_client ' . $sql_heda . ',created=NOW()';

if(db_query($sql_baal) && ($uID=db_insert_id()))
                      echo   $uID;
?>