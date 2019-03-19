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
	?>

	<style>
		.table-content {
			background: white;
			border: solid thin #ddd;
		}

		.right-list{
			background: white;
			border: solid thin #ddd;
			padding:8px;
			font-size:13px;
		}

		.client-list-title{
			border-bottom:solid thin #ddd;
			margin-bottom:10px;
			padding:10px;
			font-weight:bold;
			text-transform:uppercase;
		}

		.table-info {
			font-size: 13px;
		}
		.span12{margin-bottom:100px;}

		.table-active{
			background: lightblue;
			border: solid thin #ddd;
			color:black;
		}
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
			<div class="title-content" style="margin-top: 50px;">Activity Log</div>

			<!--  Pages Flow -->
			<ol class="breadcrumb">
				<li><a href="menu.php">Home /</a></li>
				<li class="active">Activity Log</li>
			</ol>
			<!--  Pages Flow -->

			<div class="row">
				<!--  table -->
				<div class="span8">
					<table class="table table-content">
						<thead>
							<tr>
								<th>No</th>
								<th>SSID</th>
								<th>Password</th>
								<th>Sharing Type</th>
								<th>Duration</th>
								<th>Client</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$getSession = fetchRows("SELECT * FROM session WHERE end !='' ");
								if($getSession){
									foreach($getSession as $h => $j){ $h++;
										$duration = strtotime($j['end']) - strtotime($j['start']);
										$client = numRows("SELECT * FROM client_note WHERE session='".$j['session_id']."'");
							?>
										<tr id="<?php echo 'table'.$j['session_id']; ?>" class="highlight">
											<td><?php echo $h; ?></td>
											<td>
												<b><?php echo $j['ssid']; ?></b><br>
												<small><?php echo elapsed($j['start']); ?></small>
											</td>
											<td><?php echo $j['password']; ?></td>
											<td><?php echo $j['sharing_type']; ?></td>
											<td><?php echo gmdate("H:i:s", $duration); ?></td>
											<td>
												<a href="#" class="btn btn-default btnClient" data-session="<?php echo $j['session_id'];?>">
													<i class="fa fa-user"></i> <?php echo $client; ?>
												</a>
											</td>
										</tr>
							<?php 
									} 
								}else{ echo "<tr><td colspan='6'>No Record</td></tr>";}
							?>
						</tbody>
					</table>
				</div>
				<!--  Table -->

				<!--  Side Right Content -->
				<div class="span4">
					<div class="client-list-title">Client List</div>
					<ol id="sideBarClient"></ol>
				</div>
				<!--  Side Right Content -->
			</div>

			<!--  Back button -->					
			<div class="row">
				<div class="span12">
					<a href="menu.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Go back</a>
				</div>
			</div>
			<!--  Back button -->			

		</div>

	</div>

</body>

</html>
<script>
$(document).ready(function(){
	$('.btnClient').click(function(){
		var id = $(this).data('session');
		$.ajax({
			url:"inc/process.php",
			type:"post",
			data:"action=clientSidebar&hotspot="+id,
			success:function(g){
				$('#sideBarClient').html(g);
				$('.highlight').removeClass('table-active');
				$('#table'+id).addClass('table-active');
			}
		});
	});
});
</script>