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
			<?php
				$bid = $_REQUEST['bid'];
				$sql = "CALL GetSelectedBook('$bid')";

				if (!empty($bid) && !empty($sql))
				{
					$result = $conn->query($sql);
					$row = $result->fetch_assoc();
					$title = $row["Title"];
					echo "<h3>" . $title . "</h3>";
					if (!empty($row['Authers']))
						echo "<h4> By " . $row['Authors'] . "</h4>";
				}
			?>
		</div>
		<div name='price'>
			<?php 
				$search_url = "https://www.googleapis.com/books/v1/volumes?q=isbn:" . $row['ISBN13'];
				$page = file_get_contents($search_url);
            	$data = json_decode($page, true);
            	echo "<table class='results'><tr><th>Title</th><th>ISBN10</th><th>ISBN13</th><th>Edition</th><th>Publication Year</th><th>Authors</th><th>Amazon</th></tr>";
            	for($i=0;$i<count($data['items']);$i++)
            	{
            		$g_textbook_title = $data['items'][$i]['volumeInfo']['title'];
					$g_authors = $data['items'][$i]['volumeInfo']['authors'];
					$g_publication_date = $data['items'][$i]['volumeInfo']['publishedDate'];
					$g_subjects = $data['items'][$i]['volumeInfo']['categories'];
					$g_identifiers = $data['items'][$i]['volumeInfo']['industryIdentifiers'];
					for ($j = 0; $j < count($g_identifiers); $j++)
					{
						if($g_identifiers[$j]['type'] === 'ISBN_13')
							$g_isbn13 = $data['items'][$i]['volumeInfo']['industryIdentifiers'][$j]['identifier'];
						else if ($g_identifiers[$j]['type'] === 'ISBN_10')
							$g_isbn10 = $data['items'][$i]['volumeInfo']['industryIdentifiers'][$j]['identifier'];
					}

            		if(empty($row["Edition"]))
						$row["Edition"] = "";
					$bookID = $row["ID"];
					$book = str_replace(' ', '%20', $row["Title"]);
					$g_row = array("Title"=>$g_textbook_title, "ISBN10"=>$g_isbn10, "ISBN13"=>$g_isbn13, "PublishYear"=>$g_publication_date, "Authors"=>implode(', ', $g_authors));
			    	echo "<tr><td>" . $g_row["Title"] . "</td><td>" . $g_row["ISBN10"] . "</td><td>" . $g_row["ISBN13"] . "</td><td>" . $row["Edition"] . "</td><td>" . $g_row["PublishYear"] . "</td><td>" . $g_row["Authors"] . '</td><td><form method="post" action="search-amazon.php?item=' . $book . '"><input type="submit" name="view" value="Check Amazon" /></form></td></tr>';
            	}
            	echo "</table>"
			?>

		</div>
		<div>
			<?php 
				if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)
				{
					echo '<div id="review">
						<form method="post" name="leave-review">
							<label for="own">Do you own this?</label>
							<input type="radio" id="own" name="own" value="true">
							<label for="used">Was this book used in class?</label>
							<input type="radio" id="used" name="used" value="true">
							<label for="required">Was this book required for class?</label>
							<input type="radio" id="required" name="required" value="true">
							<label for="recommend">Would you recommend this book?</label>
							<input type="radio" id="recommend" name="recommend" value="true">
							<label for="rating">How would you rate this book?</label>
							<div class="stars">
								<input type="checkbox" id="one-star" name="star1">
								<input type="checkbox" id="two-star" name="star2">
								<input type="checkbox" id="three-star" name="star3">
								<input type="checkbox" id="four-star" name="star4">
							</div>
							<input type="submit" name="submit_review">
						</form>
					</div>';
				}

				if(isset($_POST['submit_review']))
				{
					$username = $_SESSION['username'];
					$userid = $_SESSION['id'];
					$own = $_POST['own'];
					if ($own === 'true')
						$own=1;
					$used = $_POST['used'];
					$required = $_POST['required'];
					$recommend = $_POST['recommend'];
					if (!empty($_POST['star4']))
						$star = 4;
					else if (!empty($_POST['star3']))
						$star = 3;
					else if (!empty($_POST['star2']))
						$star = 2;
					else if (!empty($_POST['star1']))
						$star = 1;
					else
						$star = 0;
					$date = date("Y/m/d");
					echo $bid;
					$sql = "CALL InsertNewUserReview('$bid', '$userid', '$used', '$required', '$recommend', '$star', '$date', '$own')";
					//echo $sql;
					$result = $conn->query($sql);
					//if (!(empty($result)))
					//	echo "<p>Successfully reviewed!</p>";

					/*if($own === true)
					{
						$sql = "CALL SetUserTextbookBook('$userid', '$bid')";
						$result = $conn->query($sql);
						if (!(empty($result)))
						{
							echo "<p>Successfully Added To Shelf!</p>";
						}
					}*/
				}
			?>
		</div>
		<div id='reviews'>
			
		</div>
	</body>

	<footer></footer>
</html>
