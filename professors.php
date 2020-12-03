<section id="professors">
	<div>
		<?php 
			$sql = "";
			$sql = "CALL GetAllProfessors()";
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
		  		echo "<table class='results'><tr><th>Professors</th><th>View</th></tr>";

				while($row = $result->fetch_assoc())
				{
					$id = $row['ID'];
			    	echo "<tr><td>" . $row["Professors"] . '<td><form method="post" action="professors-results.php?id=' . $id . '"><input type="submit" name="view" value="View" /><input type="hidden" name="professor-id" value="$id" /></form></td></tr>';
			  	}
			  	echo "</table>";
			} 
			else 
			{
			  	echo "0 Professors, Try adding some!";
			}
		?>
	</div>
</section>