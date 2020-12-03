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
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">

		<title>ctTextbooks</title>
	</head>

	<body>
		<?php include_once 'includes/header.php'?>
		<?php require_once "includes/config.php"?>
		<div>
			<form method="post">
				<table align='center'>
				<tr>
					<td><label>Enter University name:</label></td>
					<td colspan="3"><input type ="search"  value="" name="university_title"></td>
				</tr>
				<tr>
                    <td><label for="state">Select the state where your university are:</label></td>
					<td>
						<select name="state" id="state">
							<option value="">Empty</option>
							<option value="AL">Alabama</option>
							<option value="AK">Alaska</option>
							<option value="AZ">Arizona</option>
							<option value="AR">Arkansas</option>
							<option value="CA">California</option>
							<option value="CO">Colorado</option>
							<option value="CT">Connecticut</option>
							<option value="DE">Delaware</option>
							<option value="DC">District Of Columbia</option>
							<option value="FL">Florida</option>
							<option value="GA">Georgia</option>
							<option value="HI">Hawaii</option>
							<option value="ID">Idaho</option>
							<option value="IL">Illinois</option>
							<option value="IN">Indiana</option>
							<option value="IA">Iowa</option>
							<option value="KS">Kansas</option>
							<option value="KY">Kentucky</option>
							<option value="LA">Louisiana</option>
							<option value="ME">Maine</option>
							<option value="MD">Maryland</option>
							<option value="MA">Massachusetts</option>
							<option value="MI">Michigan</option>
							<option value="MN">Minnesota</option>
							<option value="MS">Mississippi</option>
							<option value="MO">Missouri</option>
							<option value="MT">Montana</option>
							<option value="NE">Nebraska</option>
							<option value="NV">Nevada</option>
							<option value="NH">New Hampshire</option>
							<option value="NJ">New Jersey</option>
							<option value="NM">New Mexico</option>
							<option value="NY">New York</option>
							<option value="NC">North Carolina</option>
							<option value="ND">North Dakota</option>
							<option value="OH">Ohio</option>
							<option value="OK">Oklahoma</option>
							<option value="OR">Oregon</option>
							<option value="PA">Pennsylvania</option>
							<option value="RI">Rhode Island</option>
							<option value="SC">South Carolina</option>
							<option value="SD">South Dakota</option>
							<option value="TN">Tennessee</option>
							<option value="TX">Texas</option>
							<option value="UT">Utah</option>
							<option value="VT">Vermont</option>
							<option value="VA">Virginia</option>
							<option value="WA">Washington</option>
							<option value="WV">West Virginia</option>
							<option value="WI">Wisconsin</option>
							<option value="WY">Wyoming</option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="4" align="center"><input type="submit" name="search" value="Search" style="font-size: 20px"></td>
				</tr>
			</table>
		</form>

		<div style="overflow-x:auto;">
			<?php
				if(isset($_POST['search']))
				{
			       	$selectopt = $_POST['state'];
			       	$ut = $_POST['university_title'];
			       	$url = 'map.php?state=' . $selectopt . '&title=' . $ut;
			       	header("Location:" . $url);
				}
				$state_keyword = "";
				$title_keyword = "";
				if(isset($_REQUEST['state']))
				{
					$state_keyword = $_REQUEST['state'];
				}
				if(isset($_REQUEST['state']))
				{
					$title_keyword = $_REQUEST['title'];
				}

				if($state_keyword == "" && $title_keyword == "")
				{
					$sql_query = "SELECT * FROM university";
				}
				else if($title_keyword == "")
				{
					$sql_query = "SELECT * FROM university WHERE Province = '" . $state_keyword . "'";
				}
				else if($state_keyword == "")
				{
					$sql_query = "SELECT * FROM university WHERE Name LIKE '%" . $title_keyword . "%'";
				}
				else
				{
					$sql_query = "SELECT * FROM university WHERE Province = '" . $state_keyword . "' AND Name LIKE '%" . $title_keyword . "%'";
				}

				$result = $conn->query($sql_query);

				if ($result->num_rows > 0) {
			  		echo "<table class='results' name='list'><tr><th>Name</th><th>Address</th><th>City</th><th>State</th><th>PostalCode</th></tr>";

					while($row = $result->fetch_assoc()) {
				    	echo "<tr><td>" . $row["Name"]. "</td><td>" . $row["Address"]. "</td><td>" . $row["City"]. "</td><td>" . $row["Province"]. "</td><td>" . $row["PostalCode"]."</td><td>" .  '<button value="' . htmlspecialchars($row["Name"]) . ' "onclick = MoreInfo(this) >More info</button>' . "</td></tr><br>";
				  	}
				  	echo "</table>";
				} 
				else 
				{
				  	echo "0 results";
				}

				$conn->close();
			?>
		</div>
       	<script>
            function MoreInfo($output)
            {
				url = "universityinfo.php?college=" + $output.value;//here
                window.open(url);
            }
        </script>
	</div>
	</body>

	<footer></footer>
</html>
