<?php 
    include('config.php');
    
    //This for unauthorized kick out
    if($_SESSION['login'] != 'yes'){    
        header('location:index.php');
    }                                   
    
    //kick out if form not submitted
    if(!isset($_POST['btnStart'])){
        header('location:menu.php');
    }

    //posted value
    if(isset($_POST['usr'])){
        $ssid = $_POST['usr'];
        $pass = $_POST['pss'];
        $type = $_POST['typ'];       
    }
    $task = $_POST['btnStart']; 

    //perform start hotspot
    if($task == 'Start'){
        $recordSession = runQuery("INSERT INTO session (session_id, ssid, password, sharing_type, start, end) VALUES (NULL, '$ssid', '$pass', '$type', CURRENT_TIMESTAMP, NULL)");
        if($recordSession){
            
            shell_exec('netsh wlan set hostednetwork mode=allow keyUsage=temporary ssid="'.$ssid.'" key="'.$pass.'"');
            shell_exec('netsh wlan start hostednetwork');    

            // write to host file
            $getDNS = fetchRows("SELECT * FROM proxy");
            if($getDNS){
                foreach($getDNS as $v){
                    $myfile = fopen('C:\Windows\System32\Drivers\\etc\hosts', 'a');
                    fwrite($myfile, "\r\n192.168.137.1    ".$v['DNS']);
                    fclose($myfile);
                }
            }      
            
            $sessionID = fetchRow("SELECT max(session_ID) FROM session"); 
            $_SESSION['hotspotID'] = $sessionID['max(session_ID)'];
            echo "<script>setTimeout(function (){window.location.href = '../menu.php';}, 3000);</script>";             

        }


    }else{
        $hotspotSessionID = $_SESSION['hotspotID'];
        $recordSession = runQuery("UPDATE session SET end = CURRENT_TIMESTAMP WHERE session_id='$hotspotSessionID'");
        if($recordSession){ 
            
            // CLEAR ARP CACHE TABLE
            exec('arp -a -d');
            shell_exec('netsh wlan stop hostednetwork');	
            unset($_SESSION['hotspotID']);	
    
            // cleanup host file
            $myfile = fopen('C:\Windows\System32\Drivers\\etc\hosts', 'w'); 
            fwrite($myfile, ""); 
            fclose($myfile);

            echo "<script>setTimeout(function (){window.location.href = '../menu.php';}, 3000);</script>"; 

        }
    }
?>

<center>
	<img src="../image/loading.gif">  
    <h4><?php echo $task.'ing'; ?> hotspot.. Please Wait</h4>
</center>    
