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
function get_string_between($string, $start, $end){
$string = ' ' . $string;
$ini = strpos($string, $start);
if ($ini == 0) return '';
$ini += strlen($start);
$len = strpos($string, $end, $ini) - $ini;
return substr($string, $ini, $len);
}
$keyword = $_REQUEST['item'];
echo $keyword;
echo "<br>";
echo "<br>";
$curl = curl_init();
$search_url = "https://rapidapi.p.rapidapi.com/product/search?keyword=";
$keyword = str_replace(' ', '%20', $keyword);
//echo '<br>';
//echo $keyword;
$search_url .= $keyword;
$search_url .= "&category=aps&country=US";
curl_setopt_array($curl, [
CURLOPT_URL => $search_url,
CURLOPT_RETURNTRANSFER => true,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_ENCODING => "",
CURLOPT_MAXREDIRS => 10,
CURLOPT_TIMEOUT => 30,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => "GET",
CURLOPT_HTTPHEADER => [
// API KEY
],
]);
$response = curl_exec($curl);
$err = curl_error($curl);
echo "<table class='results'><tr><th>Title</th><th>Price</th><th>url</th></tr>";
//echo $response;
//echo "<br><br><br><br>";
curl_close($curl);


$response = strstr($response, '[{"');
while(strpos($response,',{"position":') !== false){
$price = get_string_between($response,'"current_price":', ',"currency"');
$currency = get_string_between($response,'"currency":"', '","before_price');
$name = get_string_between($response,'"title":"', '","thumbnail"');
$name = preg_replace('/\s+/', '_',$name);
$url = get_string_between($response,'"url":"', '","score"');
echo "<tr><td>" . $name. "</td><td>" . $price."</td><td>" . $url."</td></tr><br>";
/*echo $price;
echo "<br>";
echo $currency;
echo "<br>";
echo $name;
echo "<br>";
echo $url;
echo "<br>";*/
//$response = strstr($response, ',{"position"');
$response = ltrim(stristr($response, ',{"position"'),',{"position"');
}

?>
</div>
		
	</body>

	<footer></footer>
</html>
