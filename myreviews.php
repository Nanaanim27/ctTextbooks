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
		<?php require_once "includes/config.php"?>
		<div>
			<?php 
				echo "<h3>" . $_SESSION["username"] . "'s Reviews</h3>";

				$id = $_SESSION["id"];
				$sql = "CALL GetUserReviews('$id')";
				$result = $conn->query($sql);
				if ($result->num_rows > 0)
				{
			  		echo "<table class='results'><tr><th>Title</th><th>ISBN10</th><th>ISBN13</th><th>Edition</th><th>Publication Year</th><th>Authors</th><th>View</th></tr>";

					while($row = $result->fetch_assoc()) {
						if(empty($row["Edition"]))
							$row["Edition"] = "";
						$bookID = $row["ID"];
				    	echo "<tr><td>" . $row["Title"] . "</td><td>" . $row["ISBN10"] . "</td><td>" . $row["ISBN13"] . "</td><td>" . $row["Edition"] . "</td><td>" . $row["PublishYear"] . "</td><td>" . $row["Authors"] . '<td><form method="post" action="results.php?bid=' . $bookID . '"><input type="submit" name="view" value="View" /><input type="hidden" name="book-id" value="$bookID" /></form></td></tr>';
				  	}
				  	echo "</table>";
				} 
				else 
				{
				  	echo "0 Reviews, Try adding some!";
				}
			?>
		</div>
	</body>

	<footer></footer>
</html>