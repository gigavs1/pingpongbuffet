
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Login</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/loginUtil.css">
	<link rel="stylesheet" type="text/css" href="css/login.css">
<!--===============================================================================================-->

<style>
	.custom-shape-divider-bottom-1679293178 {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    overflow: hidden;
    line-height: 0;
    transform: rotate(180deg);
}

.custom-shape-divider-bottom-1679293178 svg {
    position: relative;
    display: block;
    width: calc(100% + 1.3px);
    height: 416px;
}

.custom-shape-divider-bottom-1679293178 .shape-fill {
    fill: #CE1212;
}
</style>

</head>
<body>
	<wrapper>
		<div class="custom-shape-divider-bottom-1679293178">
			<svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
				<path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" class="shape-fill"></path>
				<path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" class="shape-fill"></path>
				<path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" class="shape-fill"></path>
			</svg>
		</div>
	</wrapper>
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-form-title" style="background-image: url(images/bg-01.jpg);">
					<span class="login100-form-title-1"> 
						login in
					</span>
				</div>
				
				<?php

				session_start();

				// Include the database connection file
				include('includes/connection.php');
				
					// Check if the user has submitted the login form
				if (isset($_POST['btnlogin'])) {

					// Get the username and password from the login form
					$username = mysqli_real_escape_string($conn, $_POST['username']);
					$password = mysqli_real_escape_string($conn, $_POST['password']);

					// Prepare the SQL query to select
					$sql = mysqli_query($conn, "SELECT * FROM user_tbl WHERE username = '$username'")
							or die('query failed');

					// Check if the query returned a row
					if (mysqli_num_rows($sql) > 0) {

						// User found, verify password
						$row = mysqli_fetch_assoc($sql);
						
						if (password_verify($password, $row['pass'])) {

							// Login successful, redirect to the dashboard page
							$_SESSION['user_id'] = $row['userid'];

							header('location:index.php');

						} else {

						// Login failed, display an error message
							echo"<div class='alert alert-danger' role='alert'>
									<center><h4 class='alert-heading'><strong>Unable to process!</strong></h4></center>
										<hr>
											<center><p class='mb-0'>Sorry, Incorrect <strong>PASSWORD</strong> and <strong>USERNAME</strong>.</p></center>
								</div>";

						}
					} else {
						// User not found, display an error message
						echo"<div class='alert alert-danger' role='alert'>
								<center><h4 class='alert-heading'><strong>Unable to process!</strong></h4></center>
									<hr>
										<center><p class='mb-0'>Sorry, <strong>USERNAME</strong> and <strong>PASSWORD</strong> not found!</p></center>
							</div>";
					}
				}

				?>



				<!-- start of input fields on login web page -->
				<form class="login100-form validate-form" action="" method="POST">

					<!-- input username -->
					<div class="wrap-input100 validate-input m-b-23" data-validate="Username is required">
						<span class="label-input100">Username</span>
						<input class="input100" type="text" name="username" placeholder="Enter username">
						<span class="focus-input100"></span>
					</div>

					<!-- input password -->
					<div class="wrap-input100 validate-input m-b-18" data-validate = "Password is required">
						<span class="label-input100">Password</span>
						<input class="input100" type="password" name="password" placeholder="Enter password">
						<span class="focus-input100"></span>
					</div>

					<!-- end of input fields on login web page -->


					<div class="flex-sb-m w-full p-b-30">
						<div te>
							<p class="text-muted">
								Don't have an account yet?
							</p>
						</div>

						<!-- redirect to sign up page -->
						<div>
							<a href="signup.php" class="txt1">
								Click Here! Sign Up
							</a>
						</div>
					</div>

					<!-- start of submit button -->
					<div class="container-login100-form-btn">
						<button class="login100-form-btn" type="submit" name="btnlogin">
							Login
						</button>
					</div>
					<!-- end of submit button -->
					
				</form>
				<!-- start of input fields on login web page -->
			</div>
		</div>
	</div>

<!--===============================================================================================-->
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>

</body>
</html>