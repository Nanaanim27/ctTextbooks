<div>
	<form method="post" name="add-info">
		<table align="center" id="additional-textbook-info">
			<tr>
				<td><label>University: </label></td>
				<td><input type="text" name="university"></td>
			</tr>
			<tr>
				<td><label>Class: </label></td>
				<td><input type="text" name="class"></td>
			</tr>
			<tr>
				<td><label>Department: </label></td>
				<td><input type="text" name="department"></td>
			</tr>
			<tr>
				<td><label>Course Number: </label></td>
				<td><input type="text" name="course-number"></td>
			</tr>
			<tr>
				<td><label>Professor: </label></td>
				<td><input type="text" name="professor"></td>
			</tr>
			<tr>
				<td colspan="2" align="center"><input type="submit" name="save-info" value="Submit" style="font-size: 20px"></td>
			</tr>
		</table>
	</form>
</div>

<?php
	include_once 'includes/config.php';
	if(isset($_POST['save-info']))
	{
		$university = $_POST['university'];
		$class = $_POST['class'];
		$department = $_POST['department'];
		$number = $_POST['course-number'];
		$professor = $_POST['professor'];
	}
?>