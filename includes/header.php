<link href="css/index.css" rel="stylesheet" type="text/css" />
<header>
	<h1>CREATE TABLE Textbooks;</h1>
	<h2>Better options exist!</h2>
	<nav>
		<ul>
			<li>
				<a href="index.php">Homepage</a>
			</li>
			<li>
				<a href="search.php">Search!</a>
			</li>
			<li>
				<a href="input.php">Add!</a>
			</li>
			<li>
				<a href="all.php">Browse!</a>
			</li>
			<li>
				<a href="prices.php">Check Amazon!</a>
			</li>
			<li>
				<a href="map.php">Map Out Schools!</a>
			</li>
			<div class="rightnav">
				<?php
					if(isset($_SESSION['username']) && !empty($_SESSION['username']))
					{
						echo '<li id="account">Welcome, ' . htmlspecialchars($_SESSION["username"]) . '!';
						echo '<ul class="dropdown">';
						echo '<li id="info"><a href="myinfo.php">My Info</a></li>';
						echo '<li id="books"><a href="mybooks.php">My Books</a></li>';
						echo '<li id="reviews"><a href="myreviews.php">My Reviews</a></li>';
						echo '<li id="courses"><a href="mycourses.php">My Courses</a></li>';
						echo '<li id="logout"><a href="logout.php">Logout</a></li>';
						echo '<li id="reset"><a href="reset-password.php">Reset Password</a></li>';
						echo '<li id="delete"><a href="delete.php">Delete Account</a></li>';
						echo '</ul></li>';
					}
					else
					{
						echo '<li id="login"><a href="login.php">Login</a></li>';
					}
				?>
			</div>
		</ul>
	</nav>
</header>