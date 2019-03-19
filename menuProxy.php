<!DOCTYPE html>
<html lang="en">
<head>
	<?php 
		include('inc/config.php');
		include('inc/menu-library.php'); 

		//This for unauthorized kick out
		if($_SESSION['login'] != 'yes'){
			header('location:index.php');
		}

		//perform dns block
		if(isset($_POST['dns'])){
			$DNS = $_POST['dns'];
			$record = runQuery("INSERT INTO proxy (id, DNS, date) VALUES (NULL, '$DNS', CURRENT_TIMESTAMP)");
			if($record){				
				echo '<script>alert("'.$DNS.' blocked");window.location.href="menuProxy.php";</script>';
			}else{
				echo '<script>alert("Opps, something not right, try again");window.location.href="menuProxy.php";</script>';
			}
		}
	?>
	<style>
		.enterDNS{ 
			margin-top:10px;
			min-height:35px;
		}
		.nav-stacked{background:white;}
	</style>
</head>
<body>

	<!-- Top Bar -->
	<div class='navbar navbar-inverse navbar-static-top'>
		<div class='navbar-inner nav-collapse'>
			<center><div class="title-top">Ezy <i class="fa fa-wifi"></i> Hotspot</div></center>		
		</div>
	</div>
	<!-- Top Bar -->

	<div id='content' class='container'>

		<div class='span12 main'>
			<div class="title-content" style="margin-top: 50px;">Website Filter</div>

			<!--  Pages Flow -->
			<ol class="breadcrumb">
				<li><a href="menu.php">Home /</a></li>
				<li class="active">Website Filter</li>
			</ol>			
			<!--  Pages Flow -->

			<!--  Content -->
			<div class="span8">
				<form method="post" onsubmit="return confirm('Block this DNS?')">
					<input type="text" placeholder="Enter DNS to block and click Enter" class="span8 enterDNS" name="dns" autocomplete="off">
				</form>

				<ol class="nav nav-tabs nav-stacked">
					<li class="disabled title-block"><a href="#"><center><b><i>Blocked Website List</i></b></center></a></li>
					<?php 
						$getProxy = fetchRows("SELECT * FROM proxy");
						if($getProxy){
							foreach($getProxy as $g=>$v){ $g++;
								echo '<li><a class="remove" href="inc/web_block_remove.php?blockid='.$v['id'].'">'.$g.'. '.$v['DNS'].' <small class="pull-right">'.elapsed($v['date']).'</small></a></li>';
							}
						}else{
							echo '<li><a href="#">no record</a></li>';
						}
					?>
				</ol>
			</div>
			<!--  Content -->
			
			<div class="span12">
				<a href="menu.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Go back</a>
			</div>
		</div>

	</div>

</body>
</html>
<script>
$('.remove').click(function(){
	if(confirm('Remove this?')){
		return true;
	}else{
		return false;
	}
});
</script>