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
				echo "<h3>" . $_SESSION["username"] . "'s Courses</h3>";

				$id = $_SESSION["id"];
				$sql = "CALL GetUserClasses('$id')";
				$result = $conn->query($sql);
				if ($result->num_rows > 0) 
				{
			  		echo "<table class='results'><tr><th>Name</th><th>Department</th><th>Course Number</th><th>View</th></tr>";

					while($row = $result->fetch_assoc()) 
					{
						$id = $row['ID'];
				    	echo "<tr><td>" . $row["Name"] . "</td><td>" . $row[
				    		"Department"] . "</td><td>" . $row["CourseNumber"]  . '<td><form method="post" action="course-results.php?id=' . $id . '"><input type="submit" name="view" value="View" /><input type="hidden" name="course-id" value="$id" /></form></td></tr>';
				  	}
				  	echo "</table>";
				} 
				else 
				{
				  	echo "0 Courses, Try adding some!";
				}
			?>
		</div>
	</body>

	<footer></footer>
</html>
