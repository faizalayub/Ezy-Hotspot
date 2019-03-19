<!DOCTYPE html>
<html lang="en">

<head>
	<?php 
	
		include('inc/config.php');
		include('inc/library.php'); 

		//kick out from this page is already login
		if(isset($_SESSION['login']) == 'yes'){    
			header('location:menu.php');
		}

		//perform login button
		if(isset($_POST['login-btn'])){
			$key = $_POST['text-field-1'];
			$pass = $_POST['text-field-2'];
			$check = numRows("SELECT * FROM admin WHERE secret_key='$key' AND pass_key='$pass'");
			if($check){
				$_SESSION['login'] = 'yes';
				header("location: menu.php");
			}else{
				echo "<script>alert('login failed, try again');</script>";
			}
		}

	?>
	<style>
		html,
		body {
			background-image: url('image/bg.jpg');
			background-size: cover;
			background-repeat: no-repeat;
			height: 100%;
			font-family: 'Numans', sans-serif;
		}
	</style>
</head>

<body>
	<div class="container">
		<div class="d-flex justify-content-center h-100">
			<div class="card">
				<div class="card-header">
					<h3 class="text-center">Ezy <i class="fa fa-wifi"></i> Hotspot</h3>
				</div>
				<form method="post">
					<div class="card-body">
						<div class="input-group form-group">
							<div class="input-group-prepend">
								<span class="input-group-text"><i class="fa fa-user fa-lg"></i></span>
							</div>

							<input type="text" name="text-field-1" class="form-control" placeholder="username" required autocomplete="off" autofocus>

						</div>
						<div class="input-group form-group">
							<div class="input-group-prepend">
								<span class="input-group-text"><i class="fa fa-lock fa-lg"></i></span>
							</div>

							<input type="password" name="text-field-2" class="form-control" placeholder="password" required autocomplete="off">

						</div>
					</div>
					<div class="card-footer">
						<input type="submit" value="Login" name="login-btn" class="btn btn-lg login_btn btn-block">
					</div>
				</form>
			</div>
		</div>
	</div>
</body>

</html>
