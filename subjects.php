<section id="subjects">
	<div>
		<?php 
			$sql = "CALL GetAllSubjects()";
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
		  		echo "<table class='results'><tr><th>Name</th><th>View</th></tr>";

				while($row = $result->fetch_assoc()) 
				{
					$id = str_replace(' ', '%20', $row['name']);
			    	echo "<tr><td>" . $row["Name"] . '<td><form method="post" action="subject-results.php?id=' . $id . '"><input type="submit" name="view" value="View" /><input type="hidden" name="subject-id" value="$id" /></form></td></tr>';
			  	}
			  	echo "</table>";
			} 
			else 
			{
			  	echo "0 Subjects, Try adding some!";
			}
		?>
	</div>
</section>