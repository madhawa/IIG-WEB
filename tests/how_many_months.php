<?php
$date_from = DateTime::createFromFormat('j/n/Y', '1/4/2011');
$date_to = DateTime::createFromFormat('j/n/Y', '12/4/2014');

echo (int)(($date_from->diff($date_to)->format('%a')/365)*12).PHP_EOL;
//echo $diff->format('m');

?>