<?php
	// Initialize the session
	session_start();
	 
	require_once "includes/config.php";

	$username = $_SESSION["username"];

	if(!empty($username) && $username !== "")
	{
		$sql = "DELETE FROM basic_users WHERE username='$username'";
		if (mysqli_query($conn, $sql))
		{
			echo "<div id='sorry'>Sorry to see you go!</div>";
		}
	}

	mysqli_close($conn);

	// Unset all of the session variables
	$_SESSION = array();
	 
	// Destroy the session.
	session_destroy();
	 
	// Redirect to home page
	header("location: index.php");
	include_once "includes/header.php";
	exit;
?>