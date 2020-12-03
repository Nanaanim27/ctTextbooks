<section id="courses">
	<div>
		<?php 
			$sql = "CALL GetAllClasses()";
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
		  		echo "<table class='results'><tr><th>Name</th><th>Department</th><th>Course Number</th><th>View</th></tr>";

				while($row = $result->fetch_assoc()) 
				{
					$id = $row['ID'];
			    	echo "<tr><td>" . $row["Name"] . "</td><td>" . $row[
			    		"Department"] . "</td><td>" . $row["CourseNumber"]  . '<td><form method="post" action="course-results.php?id=' . $id . '"><input type="submit" name="view" value="View" /><input type="hidden" name="course-id" value="$id" /></form></td></tr>';
			  	}
			  	echo "</table>";
			} 
			else 
			{
			  	echo "0 Courses, Try adding some!";
			}
		?>
	</div>
</section>