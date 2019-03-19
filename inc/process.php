<?php
require("config.php");

$action = $_POST['action'];

switch($action){
    case 'clientSidebar':
        $id = $_POST['hotspot'];
        $getClient = fetchRows("SELECT * FROM client_note JOIN client on(client_note.client=client.mac) WHERE session='$id' ORDER by block_status DESC");
        if($getClient){
            foreach($getClient as $b){
                if($b['block_status'] != 'No'){
                    $block = 'Blocked';
                }else{
                    $block = 'Active';
                }
                echo '<li  class="right-list">'.$b['host'].' <br> '.$b['mac'].'<br> <small>'.elapsed($b['date']).'</small></li>';
            }
        }else{
            echo '<li  class="right-list">No client</li>';
        }
    break;
}

?>