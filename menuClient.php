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

		//check hotspot status if not start, then kicked out
		if(!isset($_SESSION['hotspotID'])){
			header('location:menu.php');
		}

		//page data	
		$currentHotspot = $_SESSION['hotspotID'];	
		$getSession = fetchRow("SELECT * FROM session WHERE session_id='$currentHotspot'");
		$getClient = fetchRows("SELECT * FROM client_note JOIN client on(client_note.client=client.mac) WHERE session='$currentHotspot' ORDER by block_status DESC");
		$totalClient = numRows("SELECT * FROM client_note JOIN client on(client_note.client=client.mac) WHERE session='$currentHotspot' ORDER by block_status DESC");
	?>
	<style>
		.table-striped {
			background: white;
			border: solid thin #ddd;
		}

		.table-info {
			font-size: 13px;
		}
	</style>
</head>

<body>

	<!-- Top Bar -->
	<div class='navbar navbar-inverse navbar-static-top'>
		<div class='navbar-inner nav-collapse'>
			<center>
				<div class="title-top">Ezy <i class="fa fa-wifi"></i> Hotspot</div>
			</center>
		</div>
	</div>
	<!-- Top Bar -->

	<div id='content' class='container'>

		<!--  Middle Content -->
		<div class='span8 main'>
			<div class="title-content" style="margin-top: 50px;">Connected Device</div>

			<!--  Pages Flow -->
			<ol class="breadcrumb">
				<li><a href="menu.php">Home /</a></li>
				<li class="active">Connected Device</li>
			</ol>
			<!--  Pages Flow -->

			<div id="refresh-content">
			<div class="well">
				<table width="30%" class="table-info">
					<tr>
						<td><b>SSID</b></td>
						<td><?php echo $getSession['ssid']; ?></td>
					</tr>
					<tr>
						<td><b>Sharing Type</b></td>
						<td><?php echo $getSession['sharing_type']; ?></td>
					</tr>
					<tr>
						<td><b>Started since</b></td>
						<td><?php echo elapsed($getSession['start']); ?></td>
					</tr>
					<tr>
						<td><b>Total Connected</b></td>
						<td>
							<div class="badge"><i class="fa fa-user"></i><?php echo $totalClient; ?></div>
						</td>
					</tr>
				</table>
			</div>

			<table class="table table-striped">
				<tr>
					<td colspan="7">
						<center>
							<?php 
								if($totalClient == 0){ 
									echo 'No device connected';
								}else{
									echo 'Loading...';
								}
							?>
						</center>
					</td>
				</tr>
			</table>
		</div>
		<!--  Middle Content -->
		</div>

		<div class="span12">
			<a href="menu.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Go back</a>
		</div>

	</div>

</body>

</html>
<script>
	setInterval(function(){
		$("#refresh-content").load("inc/client_list.php");
	},800);
</script>