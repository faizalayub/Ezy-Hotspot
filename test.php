<?php


date_default_timezone_set("Asia/Kuala_Lumpur");

$date = date('H:i:s');
$newdate = strtotime ( '+60 minute' , strtotime ( $date ) ) ;
$time_in_db = date ( 'H:i:s' , $newdate );

echo $date.'<br>';
echo $time_in_db.'<br>';

$balance = (strtotime($time_in_db)- strtotime($date))/60;
echo $balance;

