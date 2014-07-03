<?php
require_once '../main.inc.php';

require_once '../classes/class.cin.php';

$cin = new cin('SAMPLE/CIN/NUMBER/101010', '1034109585311b872ef56c');

echo $cin->get_to_location();

?>