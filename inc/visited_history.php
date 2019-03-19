<?php 
    shell_exec("ipconfig /displaydns > websiteslist.txt");
    shell_exec("websiteslist.txt");
    header("location:../menu.php");
?>
