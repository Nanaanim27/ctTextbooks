<section id="authors">
	<div>
		<?php 
			$sql = "CALL GetAllAuthors()";
			$author_result = $conn->query($sql);

			if ($author_result->num_rows > 0) {
		  		echo "<table class='results'><tr><th>Authors</th><th>View</th></tr>";
		  		
				while($author_row = $author_result->fetch_assoc()) 
				{
		  			$authorID =  $author_row["ID"];
			    	echo "<tr><td>" . $author_row["Authors"] . '<td><form method="post" action="author-results.php?id=' . $authorID . '"><input type="submit" name="view" value="View" /><input type="hidden" name="author-id" value="$authorID" /></form></td></tr>';
			  	}
			  	echo "</table>";
			} 
			else 
			{
			  	echo "0 Authors, Try adding some!";
			}
		?>
	</div>
</section>