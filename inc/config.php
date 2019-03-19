<?php
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');
$systemTime = date("Y-m-d H:i:s");

// --------------------------------------  CRUD  ------------------------------- 
//connect to DB using PDO
function connect(){
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $database = 'psm_zana';

    try{
        $con = new PDO("mysql:host=" . $host . ";dbname=" . $database, $user, $password);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $con;
    }catch(PDOException $e){
        echo "Connection error: ".$e->getMessage();
    }
}

//row Count
function numRows($query){
    try{
        $connect = connect();
        $stmt = $connect->query($query);
        $result = $stmt->rowCount();
        return $result;
    }catch(PDOException $e){
        echo "SQL error: ". $e->getMessage();
    }
} 

//fetch multi row
function fetchRows($query){
     try{
        $connect = connect();
        $stmt = $connect->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }catch(PDOException $e){
        echo "SQL error: ". $e->getMessage();
    } 
} 

//fetch single row
function fetchRow($query){
    try{
        $connect = connect();        
        $stmt = $connect->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }catch(PDOException $e){
        echo "SQL error: ". $e->getMessage();
    }
}  

//execute sql
function runQuery($query){
    try{
        $connect = connect();
        $stmt = $connect->prepare($query);
        $stmt->execute();
        return true;
    }catch(PDOException $e){
        echo "SQL error: ". $e->getMessage();
    }

}

// -------------FIND ADAPTER NAME FUNCTION------------
function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    return $length === 0 || 
    (substr($haystack, -$length) === $needle);
}
// -------------FIND ADAPTER NAME FUNCTION------------



//time elapsed
function elapsed($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>