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
		
		//get week record
		$mon = [];
		$tue = [];
		$wed = [];
		$thu = [];
		$fri = [];
		$sat = [];
		$sun = [];
		$data = fetchRows("SELECT start,end FROM session WHERE (start >= '2019-05-27' AND start <= '2019-06-02') AND end != '' ");
		foreach($data as $k){
			$cut = explode(' ',$k['start']);
			if($cut[0] == '2019-05-27'){
				$mon[] = (strtotime($k['end'])- strtotime($k['start']))/60;
			}
			if($cut[0] == '2019-05-28'){
				$tue[] = (strtotime($k['end'])- strtotime($k['start']))/60;
			}
			if($cut[0] == '2019-05-29'){
				$wed[] = (strtotime($k['end'])- strtotime($k['start']))/60;
			}
			if($cut[0] == '2019-05-30'){
				$thu[] = (strtotime($k['end'])- strtotime($k['start']))/60;
			}
			if($cut[0] == '2019-05-31'){
				$fri[] = (strtotime($k['end'])- strtotime($k['start']))/60;
			}
			if($cut[0] == '2019-06-01'){
				$sat[] = (strtotime($k['end'])- strtotime($k['start']))/60;
			}
			if($cut[0] == '2019-06-02'){
				$sun[] = (strtotime($k['end'])- strtotime($k['start']))/60;
			}
		}
		$result = [
				round(array_sum($mon),2), 
				round(array_sum($tue),2), 
				round(array_sum($wed),2), 
				round(array_sum($thu),2), 
				round(array_sum($fri),2), 
				round(array_sum($sat),2), 
				round(array_sum($sun),2)
			];
		$weekgraph = json_encode($result);
		
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
			<div class="title-content" style="margin-top: 50px;">Home Menu</div>
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

			<div class="title-content" style="margin-top: 50px;">Weekly Hotspot Used</div>
			<center>
				<canvas id="barChart" style="background-color: #f5f8fa;"></canvas>
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

<script src="dist/chart/Chart.min.js"></script>

<script>
	//chart js
	var canvas = document.getElementById("barChart");
	var ctx = canvas.getContext('2d');

	Chart.defaults.global.defaultFontColor = 'black';
	Chart.defaults.global.defaultFontSize = 16;

	var data = {
		labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
		datasets: [{
			label: "Duration",
			fill: false,
			lineTension: 0.1,
			backgroundColor: "rgba(225,0,0,0.4)",
			borderCapStyle: 'square',
			borderDashOffset: 0.0,
			borderJoinStyle: 'miter',
			pointBorderColor: "black",
			pointBackgroundColor: "white",
			pointBorderWidth: 1,
			pointHoverRadius: 8,
			pointHoverBackgroundColor: "yellow",
			pointHoverBorderColor: "brown",
			pointHoverBorderWidth: 2,
			pointRadius: 4,
			pointHitRadius: 10,
			// notice the gap in the data and the spanGaps: true
			data: <?php echo $weekgraph; ?>,
			spanGaps: true,
			}

		]
	};

	var options = {
		tooltips: {
			callbacks: {
				label: function(tooltipItem) {
					return Number(tooltipItem.xLabel) + " Min ";
				}
			}
		},
        title: {
			display: true,
			text: '27 May - 02 Jun, 2019',
			position: 'bottom'
        },
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
	};

	// Chart declaration:
	var myBarChart = new Chart(ctx, {
		type: 'horizontalBar',
		data: data,
		options: options
	});

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
