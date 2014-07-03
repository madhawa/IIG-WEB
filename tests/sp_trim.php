<?php

require_once '../main.inc.php';

$list1 = 'mail1@mail.com; mail2@mail.com; mail3@mail.com; mail4@mail.com';
$list2 = explode(',', $list1);

echo Format::sp_trim($list1);

?>