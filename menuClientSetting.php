<!DOCTYPE html>
<html lang="en">

<head>
	<?php 
		include('inc/config.php');
		include('inc/menu-library.php'); 

		//client id
		$clientID = $_GET['id'];
		$clientInfo = fetchRow("SELECT * FROM client_note JOIN client on(client_note.client=client.mac) WHERE id='$clientID'");
		$host = $clientInfo['host'];
		$macSaya = $clientInfo['mac'];
		$ipSaya = $clientInfo['ipaddress'];

		//perform submit button
		if(isset($_POST['SaveSetting'])){

			$blockAction = $_POST['block-toggle'];

			//perform duration
			if(isset($_POST['limit-duration'])){ 
				$durationValue = $_POST['limit-duration'];
				if($durationValue != '0'){
					$timeAdded = '+'.$durationValue.' minute';
					$resulttime = strtotime ($timeAdded , strtotime (date('H:i:s')));
					$newtime = date ('H:i:s',$resulttime );
					runQuery("UPDATE client_note SET usage_limit='$newtime', duration_limit='$durationValue' WHERE id='$clientID'");
				}else{
					runQuery("UPDATE client_note SET usage_limit='', duration_limit='0' WHERE id='$clientID'");
				}
			}

			//perform block task
			runQuery("UPDATE client SET block_status='$blockAction' WHERE mac='$macSaya'");
			if($blockAction == 'A'){
				shell_exec('route delete '.$ipSaya.'');
			}else{
				shell_exec('route add '.$ipSaya.' mask 255.255.255.255 192.168.137.1 if 1 -p');	
			}

			sleep(3);
			header("location: menuClient.php");
		}

	?>
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
		<form method="post" onsubmit="return confirm('Save setting?');">
			<!--  Middle Content -->
			<div class='span8 main'>
				<div class="title-content" style="margin-top: 50px;">Client Setting</div>

				<!--  Pages Flow -->
				<ol class="breadcrumb">
					<li><a href="menu.php">Home /</a></li>
					<li><a href="menuClient.php">Connected Device /</a></li>
					<li class="active">Setting</li>
				</ol>
				<!--  Pages Flow -->
				<div id="refresh-content">
				<div class="well">
					
						<table cellpadding="6" width="40%" class="table-info" style="font-size: 13px;">
							<tr>
								<td><b>Host Name</b></td>
								<td><?php  echo $host; ?></td>
							</tr>
							<tr>
								<td><b>Mac Address</b></td>
								<td><?php  echo $macSaya; ?></td>
							</tr>
							<tr>
								<td><b>Ip Address</b></td>
								<td><?php  echo $ipSaya; ?></td>
							</tr>
							<tr>
								<td><b>Duration</b></td>
								<td>
									<select name="limit-duration" class="form-control">
										<?php if($clientInfo['duration_limit'] == 0){ ?>
										<option value="" selected disabled>choose duration</option>
										<?php }else{ ?>
											<option selected disabled value="<?php echo $clientInfo['duration_limit']; ?>"><?php echo $clientInfo['duration_limit']; ?> Min</option>
										<?php } ?>
										<option value="10">10 Min</option>
										<option value="20">20 Min</option>
										<option value="30">30 Min</option>
										<option value="40">40 Min</option>
										<option value="50">50 Min</option>
										<option value="60">60 Min</option>
										<option value="0">None</option>
									</select>
								</td>
							</tr>
							<tr>
								<td><b>Block</b></td>
								<td>

									<select name="block-toggle" class="form-control">
										<?php 
										echo $clientInfo['block_status'];
											if($clientInfo['block_status'] == 'A'){
												echo '<option value="A" selected>Allow</option>';
												echo '<option value="B">Block</option>';
											}else{
												echo '<option value="B" selected>Block</option>';
												echo '<option value="A">Allow</option>';
											}
										?>
									</select>
								</td>
							</tr>					
						</table>
				</div>

			</div>
			<!--  Middle Content -->
			</div>

			<div class="span12">
				<a href="menuClient.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Go back</a>
				<button type="submit" name="SaveSetting" class="btn btn-success">Save Setting</button>
			</div>
		</form>
	</div>

</body>

</html>