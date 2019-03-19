<?php 
include('config.php');

$TotalClient = 0;
if(isset($_SESSION['hotspotID'])){

    $max = $_SESSION['hotspotID'];
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

                $getStatus = fetchRow("SELECT block_status FROM client WHERE mac='$vv'");

                $getClient = numRows("SELECT * FROM client WHERE mac = '$vv'");
                if($getClient != 1){
                    if($vv != "ull"){
                        runQuery("INSERT INTO client(mac, host, joined, block_status) VALUES ('$vv','$ipname2',CURRENT_TIMESTAMP, 'No')");
                    }
                }else{
                    $cekLog = numRows("SELECT * FROM client_note WHERE session='$max' AND client='$vv'");
                    if($cekLog != 1){
                        if($vv != "ull"){
                            runQuery("INSERT INTO client_note (id, client, session, date) VALUES (NULL, '$vv', '$max', CURRENT_TIMESTAMP);");
                            
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
    $clientNo2 = json_encode($clientCount);
    $clientNo3 = trim($clientNo2,'"');
    $clientNo4 = array_map('trim',array_filter(explode('\n',$clientNo3)));
    foreach($clientNo4 as $clientNo5){
        if (strpos ($clientNo5,'Max') !== false){
            unset($clientNo5);
        }else{
            $TotalClient = filter_var($clientNo5, FILTER_SANITIZE_NUMBER_INT);
        }
    }

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
    
}
echo $TotalClient;

?>