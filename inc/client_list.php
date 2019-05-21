<?php 
include('config.php');

$max = $_SESSION['hotspotID'];
//$TotalClient = 0;

$clientCount = shell_exec('netsh wlan show hostednetwork | findstr "Number of clients"');
$getClient = shell_exec('arp -a | findstr /r "192\.168\.137\.[2-9][^0-9] 192\.168\.137\.[0-9][0-9][^0-9] 192\.168\.137\.[0-1][0-9][0-9] 192\.168\.137\.2[0-46-9][0-9] 192\.168\.137\.25[0-4]"');

//get available client
$clientArry = json_encode($getClient);
$clientTrim = trim($clientArry,'"');
$clientFinal = array_map('trim',array_filter(explode('\n',$clientTrim)));

foreach($clientFinal as $c){

    $clientTrim2 = trim($c,'dynamic');
    $clientFinal2 = array_map('trim',array_filter(explode(' ',$clientTrim2)));

    foreach($clientFinal2 as $vv){

        if (strpos($vv, '192.168.') !== false){                                                
            $ipaddr = $vv;
            $ipname = shell_exec('ping -a '.$vv.' -n 1 | findstr Pinging');
            $ipname2 = str_replace(array('Pinging', '.mshome.net ['.$vv.'] with 32 bytes of data:'), null, $ipname);
        }else{
            $getClient = numRows("SELECT * FROM client WHERE mac = '$vv'");
            if($getClient != 1){
                if($vv != "ull"){
                    runQuery("INSERT INTO client(mac, host, joined, block_status) VALUES ('$vv','$ipname2',CURRENT_TIMESTAMP,'')");
                }
            }else{
                $cekLog = numRows("SELECT * FROM client_note WHERE session='$max' AND client='$vv'");
                if($cekLog != 1){
                    if($vv != "ull"){
                        runQuery("INSERT INTO client_note (id, client, session, ipaddress, usage_limit, date) VALUES (NULL, '$vv', '$max', '$ipaddr', '', CURRENT_TIMESTAMP)");

                        $getStatus = fetchRow("SELECT block_status FROM client WHERE mac='$vv'");
                        if($getStatus['block_status'] != 'No'){
                            shell_exec('route add '.$ipaddr.' mask 255.255.255.255 192.168.137.1 if 1 -p');
                        }else{
                            shell_exec('route delete IP $ipaddr');
                        }
                    }
                }
            }

            if($vv != "ull"){
                $clientCurrent[] = $vv;
            }else{
                $clientCurrent[] = "";
            }
        }
    }
}   

//get total client
/*$clientNo2 = json_encode($clientCount);
$clientNo3 = trim($clientNo2,'"');
$clientNo4 = array_map('trim',array_filter(explode('\n',$clientNo3)));
foreach($clientNo4 as $clientNo5){
    if (strpos ($clientNo5,'Max') !== false){
        unset($clientNo5);
    }else{
        $TotalClient = filter_var($clientNo5, FILTER_SANITIZE_NUMBER_INT);
    }
}*/

//check disconnected client
$clientInserted = fetchRows("SELECT client FROM client_note WHERE session='$max'");
if($clientInserted){
    foreach($clientInserted as $b){
        $clientTable[] = $b["client"];
    }
}else{
    $clientTable[] = "";
}

$clientDC = array_diff($clientTable,$clientCurrent);
if(!empty($clientDC)){
    foreach($clientDC as $nn){
        runQuery("DELETE FROM client_note WHERE client='$nn' AND session='$max'");
    } 
}

//data content
$getSession = fetchRow("SELECT * FROM session WHERE session_id='$max'");
$getClient = fetchRows("SELECT * FROM client_note JOIN client on(client_note.client=client.mac) WHERE session='$max' ORDER by block_status DESC");
$countClient = numRows("SELECT * FROM client_note JOIN client on(client_note.client=client.mac) WHERE session='$max' ORDER by block_status DESC");
?>

<div class="well">
    <table width="30%" class="table-info">
        <tr>
            <td><b>SSID</b></td>
            <td>
                <?php echo $getSession['ssid']; ?>
            </td>
        </tr>
        <tr>
            <td><b>Sharing Type</b></td>
            <td>
                <?php echo $getSession['sharing_type']; ?>
            </td>
        </tr>
        <tr>
            <td><b>Started since</b></td>
            <td>
                <?php echo elapsed($getSession['start']); ?>
            </td>
        </tr>
        <tr>
            <td><b>Total Connected</b></td>
            <td>
                <div class="badge"><i class="fa fa-user"></i> 
                    <?php echo $countClient; ?>
                </div>
            </td>
        </tr>
    </table>
</div>

<table class="table table-striped" style="text-align: center;">
    <thead>
        <tr>
            <th></th>
            <th>Host Name</th>
            <th>Mac Address</th>
            <th>Ip Address</th>
            <th>Duration</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php 
        if($getClient){
            foreach($getClient as $b => $g){ $b++;
                $client_id = $g['id'];
                $client_ip = $g['ipaddress'];
                $client_mac = $g['mac'];
                $connect_duration = $g['usage_limit'];

                //client usage limit perform
                if($g['duration_limit'] == '0' ){
                    $duration_left = '<div class="badge badge-default">Not Set</div>';                    
                }else{
                    $balance = (strtotime($connect_duration)- strtotime(date('H:i:s')));
                    $timer = round($balance);
                    if($timer >= 0){
                        runQuery("UPDATE client SET block_status = 'A' WHERE client.mac = '$client_mac' ");
                        shell_exec('route delete '.$client_ip.'');
                        $duration_left = '<div class="badge badge-success">'.$timer.' sec</div>';   
                    }else{
                        runQuery("UPDATE client_note SET duration_limit = '0' WHERE id = '$client_id' ");
                        runQuery("UPDATE client SET block_status = 'B' WHERE client.mac = '$client_mac' ");
                        shell_exec('route add '.$client_ip.' mask 255.255.255.255 192.168.137.1 if 1 -p');	
                        $duration_left = '<div class="badge badge-default">Time out</div>'; 
                    }        
                }

                //block status
                if($g['block_status'] == 'B'){
                    $status = '<div class="badge" style="background:darkred;color:white;">Blocked</div>';
                }else{
                    $status ='<div class="badge badge-success">Allowed</div>';
                }

    ?>
        <tr>
            <td><?php echo $b; ?></td>
            <td>
                <b><?php echo $g['host']; ?></b>
                <br>
                <small><?php echo elapsed($g['date']); ?></small>
            </td>
            <td><?php echo $client_mac; ?></td>
            <td><?php echo $client_ip; ?></td>
            <td><?php echo $duration_left; ?></td>
            <td><?php echo $status; ?></td>
            <td>
                <a href="menuClientSetting.php?id=<?php echo $g['id']; ?>" class="client-setting-btn">
                    <i style="font-size: 20px;" class="fa fa-gear"></i>
                </a>
            </td>
        </tr>
    <?php 
            } 
        }else{ echo '<tr><td colspan="7"><center>No connected device</center></td></tr>';}
    ?>
    </tbody>
</table>
</div>
<!--  Middle Content -->

