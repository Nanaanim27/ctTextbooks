<?php
	// Initialize the session
	session_start();
	 
	// Check if the user is logged in, if not then redirect him to login page
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
	{
	    //header("location: login.php");
	    //exit;
	}
?>

<!DOCTYPE html>

<html>
	<head>
		<meta charset="UTF-8" />
		<link href="css/index.css" rel="stylesheet" type="text/css" />
		<link rel = "icon" type = "image/icon" href = "images/Fatcow-Farm-Fresh-Table-add.ico" />

		<title>ctTextbooks</title>
	</head>

	<body>
		<?php include_once 'includes/header.php'?>
		<div>
			<h3>
				Regardless of whether you are new to school or an old hat, let us help you find better options!
			</h3>
			<!-- TODO: https://www.w3schools.com/howto/howto_css_hero_image.asp -->
			<div class="images">
				<div id="help-me">
					<p>Help Me Out!</p>
					<a href="search.php" class="button">
						<img src="images/Unknown.jpeg" alt="Help Me Out!">
					</a>
				</div>
				<div id="help-out">
					<p>Let Me Help!</p>
					<a href="input.php" class="button">
						<img src="images/Unknown-2.jpeg" alt="Let Me Help!">
					</a>
				</div>
			</div>
		</div>

	</body>

	<footer></footer>
</html>