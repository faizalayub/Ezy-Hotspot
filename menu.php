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

		//hotspot status ON/OFF
		$wifiStatus = shell_exec('netsh wlan show hostednetwork | findstr "Status"');
		if (strpos ($wifiStatus, 'Started') !== false){
			$hotspot = 'yes';
		}elseif (strpos ($wifiStatus, 'Not started') !== false) {
			$hotspot = 'no';
		}  	

		//count
		$blockedProxy = numRows("SELECT * FROM proxy");
		$blockedUser = numRows("SELECT * FROM client WHERE block_status !='A'");		
	?>
</head>
<body>

	<!-- Top Bar -->
	<div class='navbar navbar-inverse navbar-static-top'>
		<div class='navbar-inner nav-collapse'>
			<center><div class="title-top">Ezy <i class="fa fa-wifi"></i> Hotspot</div></center>		
		</div>
	</div>
	<!-- Top Bar -->

	<div id='content' class='row-fluid'>

		<!-- Left Bar -->
		<div class='span3 sidebar'>

			<div class="title-content">Logged in as Admin 
				<a href="logout.php" class="pull-right logout" onclick="return confirm('Exit now?');">
					<i class="fa fa-sign-out"></i> exit
				</a>
			</div>

			<div class="title-content"><i class="fa fa-cogs"></i> Hotspot Setting</div>

			<!-- Start Hospot -->
			<form method="post" id="form-hotspot" action="inc/hotspot_start_stop.php" onsubmit="return confirm('Are you sure?');">
				<?php if($hotspot != 'yes'){ ?>
					<input type="text" class="form-control" placeholder="SSID" name="usr" required>
					<input type="password" class="form-control" placeholder="Password" name="pss"  minlength="8" required>
					<input type="hidden" name="typ" value="Wi-Fi">
					<input type="submit" class="btn btn-block btn-primary" value="Start" name="btnStart">
				<?php
					}else{ 
						$info = fetchRow("SELECT * FROM session WHERE session_id=(SELECT max(session_id) FROM session)")	
				?>
					<input type="text" class="form-control" value="<?php echo $info['ssid']; ?>" name="usr" readonly>
					<input type="password" class="form-control" value="<?php echo $info['password']; ?>" name="pss" readonly>
					<input type="text" class="form-control" value="<?php echo $info['sharing_type']; ?>" name="typ" readonly>			
					<input type="submit" class="btn btn-block btn-danger" value="Stop" name="btnStart">
				<?php } ?>
			</form>
			<!-- Start Hospot -->

		</div>
		<!-- Left Bar -->

		<!--  Menu -->
		<div class='span6 main'>
			<div class="title-content" style="margin-top: 50px;">
				Home Menu
			</div>
			<center>
			<?php 
				if($hotspot == 'yes'){ 
					$button1_img = 'image/connected.png';
					$button2_img = 'image/block-no.png';
					$button3_img = 'image/log-no.png';

					$button1_link = 'menuClient.php';
					$button2_link = '#';
					$button3_link = '#';
				}else{
					$button1_img = 'image/connected-no.png';
					$button2_img = 'image/block.png';
					$button3_img = 'image/log.png';

					$button1_link = '#';
					$button2_link = 'menuProxy.php';
					$button3_link = 'menuLog.php';
				}
			?>
				<a href="<?php echo $button1_link; ?>" class="span4"><img src="<?php echo $button1_img; ?>">Connected Device</a>
				<a href="<?php echo $button2_link; ?>" class="span4 menu-img"><img src="<?php echo $button2_img; ?>">Web Filter</a>
				<a href="<?php echo $button3_link; ?>" class="span4 menu-img"><img src="<?php echo $button3_img; ?>">Activity Log</a>
			</center>
		</div>
		<!--  Menu -->

		<!-- Right Bar -->
		<div class='span3 sidebar'>

			<!-- status info -->
			<div class="title-content">Status Info</div>
			<ul class="status-right">
				<li>
					<i class="fa fa-wifi"></i> Hotspot :
					<?php if($hotspot == 'yes'){ ?>
						<div class="badge badge-success">ON</div>
					<?php }else{ ?>
						<div class="badge badge-danger">OFF</div>
					<?php } ?>
				</li>
				<li><i class="fa fa-users"></i> Connected Device: <font class="badge badge-success" id="refreshInsertClient">0</font></li>
				<li><i class="fa fa-ban"></i> Blocked Client : <font class="badge badge-success"><?php echo $blockedUser; ?></font></li>
				<li><i class="fa fa-shield"></i> Blocked Websites : <font class="badge badge-success"><?php echo $blockedProxy; ?></font></li>
			</ul>
			<!-- status info -->

			<!-- hotspot info -->
			<?php if($hotspot == 'yes'){ ?>
				<div class="title-content">Hotspot Info</div>
				<ul class="status-right">
					<li><i class="fa fa-user"></i> Username : <?php echo $info['ssid']; ?></li>
					<li><i class="fa fa-lock"></i> Password : <?php echo $info['password']; ?></li>
				</ul>	
			<?php } ?>
			<!-- hotspot info -->	

		</div>
		<!-- Right Bar  pointer-events: none;-->

	</div>

</body>
</html>

<script>

	//connected count
	setInterval(function(){
		$("#refreshInsertClient").load("inc/client_total.php");
	},800);

	//visited history button perform
	$("#visitedLog").click(function(){
		if(confirm("Show visited history?")){
			$(this).css( "pointer-events", "none" );
			$(".visited-log").show();
			setTimeout(function(){
				$.ajax({
				url: "inc/process.php",
				type: "POST",
				data: "action=visitHistory",
				success: function (out) {
					window.location.href="menu.php";
				}
			});
			},2700);
		}
	});

</script>
