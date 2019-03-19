<?php 
include("config.php");

//This for unauthorized kick out
if($_SESSION['login'] != 'yes'){
    header('location:index.php');
}

if(isset($_GET['blockid'])){
    $id = $_GET['blockid'];
    $remove = runQuery("DELETE FROM proxy WHERE id = '$id'");
    if($remove){
        echo '<script>alert("Removed from list");window.location.href="../menuProxy.php";</script>';
    }else{
        echo '<script>alert("Opps, something not right, try again");window.location.href="../menuProxy.php";</script>';
    }
}else{
    header('location:../menuProxy.php');
}
?>