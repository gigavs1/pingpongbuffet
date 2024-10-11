<!DOCTYPE html>
<html lang="en">

<head>
	<title>SignUp</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--===============================================================================================-->
	<link rel="icon" type="image/png" href="images/icons/favicon.ico" />
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
	<link rel="stylesheet" type="text/css" href="css/signUpUtil.css">
	<link rel="stylesheet" type="text/css" href="css/signUp.css">
	<!--===============================================================================================-->

	<style>
		.custom-shape-divider-bottom-1679293914 {
			position: absolute;
			top: 70%;
			left: 0;
			width: 100%;
			overflow: hidden;
			line-height: 0;
			transform: rotate(180deg);
		}

		.custom-shape-divider-bottom-1679293914 svg {
			position: relative;
			display: block;
			width: calc(130% + 1.3px);
			height: 650px;
		}

		.custom-shape-divider-bottom-1679293914 .shape-fill {
			fill: #CE1212;
		}
	</style>
</head>

<body>
	<div class="custom-shape-divider-bottom-1679293914">
		<svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
			<path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" class="shape-fill"></path>
			<path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" class="shape-fill"></path>
			<path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" class="shape-fill"></path>
		</svg>
	</div>





	<!-- start inserting inputs sign up data on database -->
	<?php

	session_start();

	include('includes/connection.php');

	
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;

	require 'PHPMailer/src/Exception.php';
	require 'PHPMailer/src/PHPMailer.php';
	require 'PHPMailer/src/SMTP.php';

	// check if the user submitted the sign up form
	if ((isset($_POST['btnsignup'])) && (isset($_SESSION['token']))) {

		//  start to check if email and password match
		if (strcmp($_POST['emailadd1'], $_POST['emailadd2']) == 0) {
			
			if (strcmp($_POST['pass'], $_POST['confirmpass']) == 0) {
				// start to check if email is existing

				// prepare the sql query for checking if email and username is taken
				$querycheck = "SELECT username, emailadd FROM user_tbl WHERE username=? AND emailadd=?";

				$statmentcheck = $conn->prepare($querycheck);

				$statmentcheck->bind_param("ss", $_POST['username'], $_POST['emailadd1']);

				$statmentcheck->execute();

				$result = $statmentcheck->get_result();

				if ($result->num_rows > 0) {
					echo "<div class='alert alert-danger m-t-100 p-b-200 p-t-200' role='alert'>
									<center><h4 class='alert-heading'>Unable to process!</h4></center>
										<hr>
											<center><p class='mb-0'>Sorry, username or email address address already taken. Please try again later or contact support for assistance.</p></center>
											<center><a href='signup.php'><u>Retry Again<u></a></center>
								</div>";
				} else {
					
						$mail = new PHPMailer(true);
				
						try {
							$mail->isSMTP();
							$mail->Host = 'smtp.gmail.com';
							$mail->SMTPAuth = true;
							$mail->Username = 't3st12356789@gmail.com';
							$mail->Password = 'yasgjfpfvsljavki';
							$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
							$mail->Port = 465;
				
							$mail->setFrom('mindependent966@gmail.com');
							$mail->addAddress('mindependent966@gmail.com'); 
				
							$otp = rand(100000, 999999);
				
							$mail->isHTML(true);
							$mail->Subject = 'OTP Verification';
							$mail->Body = '<p>Hi,</p><p>Your OTP for login is: <strong>' . $otp . '</strong></p>';
							$mail->AltBody = 'Your OTP for login is: ' . $otp;
							
							if ($mail->send()) {

								// encrypt the password for security
							$encrypt = password_hash($_POST['pass'], PASSWORD_DEFAULT);

							$datetoday = date('Y-m-d');

							$timetoday = date('H:i:s');

							// prepare sql query for inserting the date in the datebase
							$q = "INSERT INTO user_tbl (firstname, lastname, middlename, emailadd, address, contact, username, pass, dateregistered, timeregistered, dateupdated, timeupdated,otp,uservalidated) 
									values (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

							$statment = $conn->prepare($q);

							$statment->bind_param(
								"ssssssssssssss",
								$_POST['firstname'],
								$_POST['lastname'],
								$_POST['middlename'],
								$_POST['emailadd1'],
								$_POST['address'],
								$_POST['contact'],
								$_POST['username'],
								$encrypt,
								$datetoday,
								$timetoday,
								$datetoday,
								$timetoday,
								$otp,
								$datetoday
							);

							$statment->execute();

							// if ($statment->execute() > 0) {
							// 	echo "<div class='alert alert-success m-t-100 p-b-200 p-t-200' role='alert'>
							// 					<center><h4 class='alert-heading'>Successful!</h4></center>
							// 						<hr>
							// 							<center><p class='mb-0'>Your data has been successfully recorded.</p></center>
							// 							<center><a href='login.php'><u>Go to Login Page<u></a></center>
							// 				</div>";
							// } else {
							// 	echo "<div class='alert alert-success m-t-100 p-b-200 p-t-200' role='alert'>
							// 					<center><h4 class='alert-heading'>Unsuccessful!</h4></center>
							// 						<hr>
							// 							<center><p class='mb-0'>Unable to record your data.</p></center>
							// 							<center><a href='signup.php'><u>Retry Again<u></a></center>
							// 				</div>";
							// }

							header('Location: verifyOTP.php?otp=' . urlencode($otp));
							exit();
							}
							
							
							
						} catch (Exception $e) {
							echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
						}
					
				}
				// end to check if email is existing

			} else {

				echo "<div class='alert alert-danger m-t-200 p-b-100 p-t-200' role='alert'>
								<center><h4 class='alert-heading'>Unsuccessful!</h4></center>
									<hr>
										<center><p class='mb-0'>Sorry, passwords not matched and therefore we could not record your data. Please try again later or contact support for assistance.</p></center>
										<center><a href='signup.php'><u>Retry Again<u></a></center>
							</div>";
			}
		} else {

			echo "<div class='alert alert-danger m-t-200 p-b-100 p-t-200' role='alert'>
								<center><h4 class='alert-heading'>Unsuccessful!</h4></center>
									<hr>
										<center><p class='mb-0'>Sorry, emails not matched and therefore we could not record your data. Please try again later or contact support for assistance.</p></center>
										<center><a href='signup.php'><u>Retry Again<u></a></center>	
							</div>";
		}
		//  end to check if email and password match

		unset($_POST['btnsignup']);

		unset($_SESSION['token']);
	} else {
		$_SESSION['token'] = bin2hex(random_bytes(32));

	?>

		<!-- start of input fields on sign up web page -->
		<div class="limiter">
			<div class="container-login100">
				<div class="wrap-login100">
					<div class="login100-form-title" style="background-image: url(images/bg-01.jpg);">
						<span class="login100-form-title-1">
							Sign Up
						</span>
					</div>

					<form class="login100-form validate-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

						<input type="hidden" value="<?php echo $_SESSION['token'] = bin2hex(random_bytes(32)); ?>" name="token">

						<!-- input firstname -->
						<div class="wrap-input100 validate-input m-b-20" data-validate="First Name is required">
							<input class="input100" type="text" name="firstname" placeholder="Enter First Name">
							<span class="focus-input100"></span>
						</div>

						<!-- input lastname -->
						<div class="wrap-input100 validate-input m-b-20" data-validate="Last Name is required">
							<input class="input100" type="text" name="lastname" placeholder="Enter Last Name">
							<span class="focus-input100"></span>
						</div>

						<!-- input middlename -->
						<div class="wrap-input100 validate-input m-b-20" data-validate="Middle Name is required">
							<input class="input100" type="text" name="middlename" placeholder="Enter Middle Name">
							<span class="focus-input100"></span>
						</div>

						<!-- input email address -->
						<div class="wrap-input100 validate-input m-b-20" data-validate="Email address is required">
							<input class="input100" type="email" name="emailadd1" placeholder="Enter Email Address">
							<span class="focus-input100"></span>
						</div>

						<!-- input confirm email address -->
						<div class="wrap-input100 validate-input m-b-20" data-validate="Email address is required">
							<input class="input100" type="email" name="emailadd2" placeholder="Confirm Email Address">
							<span class="focus-input100"></span>
						</div>

						<!-- input address -->
						<div class="wrap-input100 validate-input m-b-20" data-validate="Address is required">
							<input class="input100" type="text" name="address" placeholder="Enter Address">
							<span class="focus-input100"></span>
						</div>

						<!-- input phone number -->
						<div class="wrap-input100 validate-input m-b-20" data-validate="Phone Number is required">
							<input class="input100" type="text" name="contact" placeholder="Phone Number">
							<span class="focus-input100"></span>
						</div>

						<!-- input username -->
						<div class="wrap-input100 validate-input m-b-20" data-validate="Username is required">
							<input class="input100" type="text" name="username" placeholder="Enter Username">
							<span class="focus-input100"></span>
						</div>

						<!-- input password -->
						<div class="wrap-input100 validate-input m-b-20" data-validate="Password is required">
							<input class="input100" type="password" name="pass" placeholder="Enter Password">
							<span class="focus-input100"></span>
						</div>

						<!-- input confirm password -->
						<div class="wrap-input100 validate-input m-b-20" data-validate="Confirm Password is required">
							<input class="input100" type="password" name="confirmpass" placeholder="Confirm Password">
							<span class="focus-input100"></span>
						</div>

						<div class="flex-sb-m w-full p-b-30 p-t-20">
							<div te>
								<p class="text-muted">
									Already have an account yet?
								</p>
							</div>

							<!-- redirect to login page -->
							<div>
								<a href="login.php" class="txt1">
									Click Here! Log In
								</a>
							</div>
						</div>

						<!-- start of submit button -->
						<div class="container-login100-form-btn">
							<button class="login100-form-btn" type="submit" name="btnsignup">
								Sign Up
							</button>
						</div>
						<!-- end of submit button -->

					</form>
					<!-- end of input fields on sign up web page -->

				</div>
			</div>
		</div>

	<?php
	}
	?>
	<!-- end inserting inputs sign up data on database -->



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