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
    <div id = "amazon">
      <form method="post">
      	<table id="amazon" align="center">
      		<tr>
      			<td><label>Enter Textbook Title</label></td>
      			<td colspan="3"><input type="text" name="textbook_title"></td>
      		</tr>
      		<tr>
        		        <td>search by：<input type="radio" name="searchopt" value="title" checked>title</td>
          			<td><input name ="searchopt" type="radio" value="ISBN">ISBN</td>
      		</tr>
      		<tr>
      			<td colspan="4" align="center"><input type="submit" name="search" value="Search" style="font-size: 20px"></td>
      		</tr>
      	</table>
      </form>
</div>

<div style="overflow-x:auto;">
	<?php

		
        $search_query = "SELECT Title,ISBN10,ISBN13,EDITION,PublishYear,Price FROM Textbooks JOIN Price ON";

       	if(isset($_POST['search']))
        {
            $keyword = $_POST['textbook_title'];
            $keyword = str_replace(' ', '%20', $keyword);
            $search_url = "https://www.googleapis.com/books/v1/volumes?q=";
	    if($_POST['searchopt']=="ISBN")
               {$search_url .= "isbn:";}
            $search_url .= $keyword;
            $page = file_get_contents($search_url);
            $data = json_decode($page, true);

            echo "<table class='results'><tr><th>Title</th><th>Authors</th><th>ISBN13</th><th>Pagecount</th><th>PublishYear</th><th>Amazon</th></tr>";

            $count = 0;
            /*while ($data['items'][$count] != null)*/
            while ($count < count($data['items']))
            {
                //echo "Title = " . $data['items'][$count]['volumeInfo']['title'];
                $title = $data['items'][$count]['volumeInfo']['title'];
                //echo "<br>";
                //echo "Authors = " . @implode(",", $data['items'][$count]['volumeInfo']['authors']);    
                $Authors = @implode(", ", $data['items'][$count]['volumeInfo']['authors']);
                //echo $Authors;
                //echo "Pagecount = " . $data['items'][$count]['volumeInfo']['pageCount'];
                $Pagecount = $data['items'][$count]['volumeInfo']['pageCount'];
                //echo "<br>";
                if($data['items'][$count]['volumeInfo']['industryIdentifiers'][0]['type'] == "ISBN_13")
                {
                    //echo "ISBN_13 = " . $data['items'][$count]['volumeInfo']['industryIdentifiers'][0]['identifier'];
                    $ISBN_13 =  $data['items'][$count]['volumeInfo']['industryIdentifiers'][0]['identifier'];
                }
                else
                {
                    //echo "ISBN_13 = " . $data['items'][$count]['volumeInfo']['industryIdentifiers'][1]['identifier'];
                    $ISBN_13 =  $data['items'][$count]['volumeInfo']['industryIdentifiers'][1]['identifier'];}
                    $year = $data['items'][$count]['volumeInfo']['publishedDate'];
                    $count++;
                    echo "<tr><td>" . $title. "</td><td>" . $Authors. "</td><td>" . $ISBN_13. "</td><td>" . $Pagecount. "</td><td>" . $year.'</td><td><form method="post" action="search-amazon.php?item=' . $title . '&ISBN=' . $ISBN_13 . '"><input type="submit" name="view" value="Check Amazon" /></form></tr>';

                    //$search_query = "SELECT Title, ISBN13 FROM Textbooks WHERE ISBN13 = '" .$ISBN_13 ."'";
                    //echo $search_query;
                    //$result = $conn->query($search_query);
                    /*if (!empty($result) && $result->num_rows > 0)
                    {
                      //echo "exist";
                    }
                    else
                    {
                        //TODO: Change these queries to use stored procedures, return id, insert authors.
                        $sql_query = "CALL InsertTextbook('$title', '$isbn10', '$isbn13', '$edition', '$publication_year', @textbook_id)";
                        //echo $sql_query;
                        if($conn->query($sql_query)==TRUE) {echo "";}else{die("error");//}*/

                                        
            
            /*$g_identifiers = $data['items'][$count]['volumeInfo']['industryIdentifiers'];
            echo $g_identifiers;
            foreach ($g_identifiers as $identifier )
            {
              if($g_identifiers[$identifier]['type'] === 'ISBN_13')
                $g_isbn13 = $data['items'][$count]['volumeInfo']['industryIdentifiers'][$identifier]['identifier'];
              else if ($g_identifiers[$identifier]['type'] === 'ISBN_10')
                $g_isbn10 = $data['items'][$count]['volumeInfo']['industryIdentifiers'][$identifier]['identifier'];
            }
            if($ISBN_10 === NULL)
              $ISBN_10 = $g_isbn10;
            if($ISBN_13 === NULL)
              $ISBN_13 = $g_isbn13;*/
              if(empty($ISBN_10))
                $ISBN_10 = $NULL;

              $sql_query = "CALL InsertTextbook('$title', '$ISBN_10', '$ISBN_13', '0', $year, @textbook_id)";
              $g_authors = $data['items'][$count]['volumeInfo']['authors'];
              
            if(mysqli_query($conn, $sql_query)) 
            {
              $result = mysqli_query($conn, "SELECT @textbook_id");
            $textbook_id = $result -> fetch_array()[0] ?? '';
            $g_authors = $data['items'][$count]['volumeInfo']['authors'];
          {
            $fullName = explode(' ', $author);
            switch(count($fullName))
            {
              case 1: $first_name = ""; $middle_name = ""; $last_name = $fullName[0]; break;
              case 2: $first_name = $fullName[0]; $middle_name = ""; $last_name = $fullName[1]; break;
              default: $first_name = $fullName[0]; $middle_name = implode(' ',array_slice($fullName, 1, count($fullName) - 1)); $last_name = array_slice($fullName, -1); break;
            }

            $sql_query_auth = "CALL InsertAuthor('$first_name', '$middle_name', '$last_name', '$textbook_id')";
            if (mysqli_query($conn, $sql_query_auth))
            {
              //echo "<br>New Author inserted successfully!";
            }
            else
            {
              echo "Error: " . $sql_query_auth . "" . mysqli_error($conn);
            }
          } // foreach
            }
              else {echo $sql_query;
              }
            
    }
        //}
            echo "</table>";
        }
	   $conn->close();
	?>

  <script>
    function searchonamazon($output) {
      //window.open("passvalue.php")
      document.getElementById("test").innerHTML = $output.value;
      url = "passvalue.php?item=" + $output.value;//Probably need change
      window.open(url);
    }
  </script>

</div>                     
  </body>

  <footer></footer>
</html>
