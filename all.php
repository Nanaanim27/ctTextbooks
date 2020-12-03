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
			Select what you would like to browse:
			<form method="post">
				<select name="display[]" multiple="multiple" multiple size="6">
					<option value='textbooks'>Textbooks</option>
					<option value='authors'>Authors</option>
					<option value='subjects'>Subjects</option>
					<option value='universities'>Universities</option>
					<option value='professors'>Professors</option>
					<option value='courses'>Courses</option>
				</select>
				<input type="submit" name="save"></button>
			</form>
		</div>
		<?php
			if(isset($_POST['save']))
			{
				$display= $_POST['display'];
				if (!isset($display))
				{
					echo "<p>You didn't select anything to display!</p>";
					
				}
				else
				{
					echo "<div>";
					//require_once "includes/config.php";
					$count = count($display);
					foreach ($display AS $key=>$values)
					{
						switch($values)
						{
							case "textbooks": include_once 'textbooks.php'; break;
							case "authors": include_once 'authors.php'; break;
							case "subjects": include_once 'subjects.php'; break;
							case "universities": include_once 'universities.php'; break;
							case "professors": include_once 'professors.php'; break;
							case "courses": include_once 'courses.php'; break;
						}
					}

					$conn->close();
					echo "</div>";
				}
			}
		?>
	</body>

	<footer></footer>
</html>
