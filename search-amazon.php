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
				$item = $_REQUEST['item'];
		                $isbn = $_REQUEST['ISBN'];
				$item = str_replace(' ', '%20', $item);
				$book = str_replace('%20', ' ', $item);

				echo "<h3>Searching Amazon For: " . $book . "</h3>";
				// https://rapidapi.com/logicbuilder/api/amazon-product-reviews-keywords/endpoints
				$curl = curl_init();
				$search_url = "https://rapidapi.p.rapidapi.com/product/search?keyword=" . $item . "&category=aps&country=US";
		                $ISBN_url = "https://amazon-product-reviews-keywords.p.rapidapi.com/product/search?keyword=" . $isbn . "&category=aps&country=US";
                                                                /*echo $search_url;
                                                                echo '<br>';
				echo $ISBN_url;
                                                                echo '<br>';*/
				curl_setopt_array($curl, 
				[
					CURLOPT_URL => $search_url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "GET",
					CURLOPT_HTTPHEADER => 
					[
						//API key
					],
				]);

				$response = curl_exec($curl);
				$err = curl_error($curl);
		                curl_close($curl);
		
                                $curl_isbn = curl_init();

				curl_setopt_array($curl_isbn, 
				[
					CURLOPT_URL => $ISBN_url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "GET",
					CURLOPT_HTTPHEADER => 
					[
						//API KEY
					],
				]);

				$response_isbn = curl_exec($curl_isbn);
				$err_isbn = curl_error($curl_isbn);
				curl_close($curl_isbn);

				if ($err)
				{
					echo "cURL Error #: " . $err;
				}
				
				if (!empty($response) && !$err && !empty($response_isbn) && !$err_isbn)
				{
					function get_string_between($string, $start, $end){
						$string = ' ' . $string;
						$ini = strpos($string, $start);
						if ($ini == 0) return '';
						$ini += strlen($start);
						$len = strpos($string, $end, $ini) - $ini;
						return substr($string, $ini, $len);
					}

					echo "<table class='results'><tr><th>Title</th><th>Price</th><th>Link</th></tr>";
					$response = strstr($response, '[{"');
					$response_isbn = strstr($response_isbn, '[{"');
					
					while (strpos($response_isbn, ',"position":') !== false)
					{
						$title = get_string_between($response_isbn, '"title":"', '","thumbnail"');
						//$title = preg_replace('/\s+/', '_',$title);
						$title = str_replace('_', ' ', $title);
						$price = get_string_between($response_isbn,'"current_price":', ',"currency"');
						$url = get_string_between($response_isbn,'"url":"', '","score"');
						echo "<tr><td>" . $title. "</td><td>" . $price ."</td><td><a href='" . $url."'>Buy Now!</a></td></tr>";
				        $response_isbn = ltrim(stristr($response_isbn, ',{"position"'),',{"position"');
					}
					echo "<tr><td>" . "--------------------------------------------------------Above is suggested product--------------------------------------------------------" . "</td><td></td><td></td></tr>";

		
		
					while (strpos($response, ',"position":') !== false)
					{
						$title = get_string_between($response, '"title":"', '","thumbnail"');
						//$title = preg_replace('/\s+/', '_',$title);
						$title = str_replace('_', ' ', $title);
						$price = get_string_between($response,'"current_price":', ',"currency"');
						$url = get_string_between($response,'"url":"', '","score"');
						echo "<tr><td>" . $title. "</td><td>" . $price ."</td><td><a href='" . $url."'>Buy Now!</a></td></tr>";
				        $response = ltrim(stristr($response, ',{"position"'),',{"position"');
					}
					echo "</table>";
				}
				else
					echo "Amazon returned 0 results";
			?>

		</div>
		
	</body>

	<footer></footer>
</html>
