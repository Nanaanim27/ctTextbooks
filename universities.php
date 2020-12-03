<section id="universities">
	<div>
		<?php 
			$sql = "CALL GetAllUniversities()";
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
		  		echo "<table class='results'><tr><th>Name</th><th>View</th></tr>";

				while($row = $result->fetch_assoc()) {
					$universityID = $row["ID"];
			    	echo "<tr><td>" . $row["Name"] . '<td><form method="post" action="university-results.php?id=' . $universityID . '"><input type="submit" name="view" value="View" /><input type="hidden" name="university-id" value="$universityID" /></form></td></tr>';
			  	}
			  	echo "</table>";
			} 
			else 
			{
			  	echo "0 Universities, Try adding some!";
			}
		?>
	</div>
</section>