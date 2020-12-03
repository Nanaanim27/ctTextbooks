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
		<script src="javascript/addAuthor.js"></script>
		<script src="javascript/removeAuthor.js"></script>
		<script src="javascript/getAuthors.js"></script>

		<div>
			<form method="post">
				<table align='center' id="inputTextbooks">
					<tbody>
						<tr>
							<td><label>Enter Textbook Title</label></td>
							<td colspan="3"><input type="text" name="textbook_title"></td>
						</tr>
						<tr>
							<td><label>Enter Textbook ISBN10</label></td>
							<td colspan="3"><input type="text" name="isbn10"></td>
						</tr>
						<tr>
							<td><label>Enter Textbook ISBN13</label></td>
							<td colspan="3"><input type="text" name="isbn13"></td>
						</tr>
						<tr>
							<td><label>Enter Textbook Edition</label></td>
							<td colspan="3"><input type="number" name="edition"></td>
						</tr>
						<tr>
							<td><label>Enter Textbook Publication Year</label></td>
							<td colspan="3"><input type="text" name="publication_year"></td>
						</tr>
						<tr id="authorRow" class="author">
							<td><label>Enter Textbook Author Name</label></td>
							<td><input type="text" name="name_first" placeholder="Enter First Name"></td>
							<td><input type="text" name="name_middle" placeholder="Enter Middle Name"></td>
							<td><input type="text" name="name_last" placeholder="Enter Last Name"></td>
						</tr>
					</tbody>
					<tfooter>
						<tr id="buttonRow">
							<td colspan="2" align="right"><input type="button" value="Add Author" onclick="addAuthor()"></td>
							<td colspan="2" align="left"><input type="button" value="Remove Author" onclick="removeAuthor()"></td>
						</tr>
						<tr>
							<td colspan="4" align="center"><input type="submit" name="save" value="Submit" style="font-size: 20px"></td>
						</tr>
					</tfooter>
				</table>
			</form>
		</div>

		<?php 
			if(isset($_POST['save']))
			{
				$textbook_title = $_POST['textbook_title'];
				$isbn10 = $_POST['isbn10'];
				$isbn13 = $_POST['isbn13'];
				$edition = $_POST['edition'];
				$publication_year = $_POST['publication_year'];
				$textbook_id = 0;

				if(!empty($isbn13))
				{
					$search_url = "https://www.googleapis.com/books/v1/volumes?q=isbn:" . $isbn13;
				}
				else if (!empty($isbn10))
				{
	               $search_url = "https://www.googleapis.com/books/v1/volumes?q=isbn:" . $isbn10;
				}
				else
					$search_url = "";

				if(empty($isbn10))
					$isbn10 = NULL;
				if(empty($isbn13))
					$isbn13 = NULL;
				if(empty($edition))
					$edition = 0;
				if(empty($publication_year))
					$publication_year = NULL;

				if($search_url !== "")
				{
					$page = file_get_contents($search_url);
					$data = json_decode($page, true);
					$g_textbook_title = $data['items'][0]['volumeInfo']['title'];
					$g_authors = $data['items'][0]['volumeInfo']['authors'];
					$g_publication_date = $data['items'][0]['volumeInfo']['publishedDate'];
					$g_subjects = $data['items'][0]['volumeInfo']['categories'];
					$g_identifiers = $data['items'][0]['volumeInfo']['industryIdentifiers'];
					for ($i = 0; $i < count($g_identifiers); $i++)
					{
						if($g_identifiers[$i]['type'] === 'ISBN_13')
							$g_isbn13 = $data['items'][0]['volumeInfo']['industryIdentifiers'][$i]['identifier'];
						else if ($g_identifiers[$i]['type'] === 'ISBN_10')
							$g_isbn10 = $data['items'][0]['volumeInfo']['industryIdentifiers'][$i]['identifier'];
					}

					if($isbn10 === NULL)
						$isbn10 = $g_isbn10;
					if($isbn13 === NULL)
						$isbn13 = $g_isbn13;
					if($textbook_title !== $g_textbook_title)
						$textbook_title = $g_textbook_title;
					if($publication_year === NULL)
						$publication_year = $g_publication_date;
					//echo $g_publication_date;
				}


				$sql_query = "CALL InsertTextbook('$textbook_title', '$isbn10', '$isbn13', '$edition', '$publication_year', @textbook_id)";

				if (!empty($textbook_title) && mysqli_query($conn, $sql_query))
				{
					$result = mysqli_query($conn, "SELECT @textbook_id");
					$textbook_id = $result -> fetch_array()[0] ?? '';
					
					echo "<br>New Textbook inserted successfully!";

					foreach ($g_authors as $author)
					{
						$fullName = explode(' ', $author);
						switch(count($fullName))
						{
							case 1: $first_name = ""; $middle_name = ""; $last_name = $fullName[0]; break;
							case 2: $first_name = $fullName[0]; $middle_name = ""; $last_name = $fullName[1]; break;
							default: $first_name = $fullName[0]; $middle_name = implode(' ', array_slice($fullName, 1, count($fullName) - 1)); $last_name = array_slice($fullName, -1); break;
						}

						$sql_query_auth = "CALL InsertAuthor('$first_name', '$middle_name', '$last_name', '$textbook_id')";
						if (mysqli_query($conn, $sql_query_auth))
						{
							echo "<br>New Author inserted successfully!";
						}
						else
						{
							echo "Error: " . $sql_query_auth . "" . mysqli_error($conn);
						}
					}

					$first_name = $middle_name = $last_name = "";

					$temp = 0;
					
					foreach($_POST as $key => $value) 
					{
					 	$pos = strpos($key , "name_");
					 	if ($pos === 0) {
					 		$pos1 = strpos($key, "name_first");
					 		if ($pos1 === 0) {
					 			$first_name = $value;
					 			$temp += 1;
					 		}
					 		$pos2 = strpos($key, "name_middle");
					 		if ($pos2 === 0) {
					 			$middle_name = $value;
					 			$temp += 1;
					 		}
					 		$pos3 = strpos($key, "name_last");
					 		if ($pos3 === 0) {
					 			$last_name = $value;
					 			$temp += 1;
					 		}
					 		if (($temp%3) == 0 && !empty($last_name) && $last_name != ' ') {
					 			$sql_query_auth = "CALL InsertAuthor('$first_name', '$middle_name', '$last_name', '$textbook_id')";
								if (mysqli_query($conn, $sql_query_auth))
								{
									echo "<br>New Author inserted successfully!";
								}
								else
								{
									echo "Error: " . $sql_query_auth . "" . mysqli_error($conn);
								}
					 		}
					 	}
					}
				 	include_once "additional.php";	
				}
				else
				{
					echo "Error: " . $sql_query . "" . mysqli_error($conn);
				}

			}

			$conn->close();
		?>

	</body>

	<footer></footer>
</html>
