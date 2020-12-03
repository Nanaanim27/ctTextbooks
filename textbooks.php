<section id="textbooks">
	<div>
		<?php
			$sql = "CALL GetAllTextbooks()";
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
		  		echo "<table class='results'><tr><th>Title</th><th>ISBN10</th><th>ISBN13</th><th>Edition</th><th>Publication Year</th><th>Authors</th><th>View</th></tr>";

				while($row = $result->fetch_assoc()) {
					if(empty($row["Edition"]))
						$row["Edition"] = "";
					$bookID = $row["ID"];
			    	echo "<tr><td>" . $row["Title"] . "</td><td>" . $row["ISBN10"] . "</td><td>" . $row["ISBN13"] . "</td><td>" . $row["Edition"] . "</td><td>" . $row["PublishYear"] . "</td><td>" . $row["Authors"] . '</td><td><form method="post" action="results.php?bid=' . $bookID . '"><input type="submit" name="view" value="View" /><input type="hidden" name="book-id" value="$bookID" /></form></td></tr>';
			  	}
			  	echo "</table>";
			} 
			else 
			{
			  	echo "0 Textbooks, Try adding some!";
			}
		?>
	</div>
</section>