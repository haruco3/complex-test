<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Complex Numbers - Student Home</title>
<link href="css/home.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
	session_start();
	// Starts the session
	if (isset($_SESSION['name']) == false)
	{
		header( 'Location: index.php?login=false' );
		// Redirects to login page if user is not logged in
	}
	elseif ($_SESSION['isAdmin'] == 1)
	{
		header( 'Location: admin/adminHome.php' );
		// Redirects to the admin home page if the user is an admin
	}
	elseif ($_SESSION['timeout'] + 60 * 60 < time())
	{
    	header( 'Location: logout.php?login=timeout' );
		// Logs the user out if the system if they have not performed any activity within an hour
    }
	else
	{
		$_SESSION['timeout'] = time();
		// Resets the 'most recent activity' time
	}
?>

<div id="mainbox">
<?php
	echo("<h1>Welcome, " . $_SESSION['name'] . "!</h1>");
	// Prints welcome message with the student's name

	// Create connection
	$conn = new mysqli($_SESSION['servername'], $_SESSION['sqlUser'], $_SESSION['sqlPass'], $_SESSION['dbname']);
	if($conn->connect_errno > 0)
	{
	    die('Unable to connect to database [' . $conn->connect_error . ']');
	}
	elseif (file_exists('perWeek.txt'))
	{
		// If the students are set to do an amount of questions per week
		$sql = $conn->prepare("SELECT date_taken FROM testtaken WHERE user_id=?");
		$sql->bind_param("i", $_SESSION['userid']);
		// Selects all of the tests that the user has taken
		$sql->execute();
		$sql->bind_result($date_taken);
		$weekTotal = 0;
		while ($sql->fetch())
		{
			// Iterates over each test
			$day = date('w');
			// This gets the day of the week as a number, with sunday = 0 etc...
			$week_start = date('Y-m-d', strtotime('-'.$day.' days'));
			// Gets the starting day of the week (i.e. sunday) by subtracting $day from the current date
			if ($date_taken > $week_start)
			{
				// If the current test was taken after the start of the week
				$weekTotal++;
				// Add 1 to this variable
			}
		}
		$sql->close();
		$file = fopen('perWeek.txt', "r");
		$perWeek = fgets($file);
		fclose($file);
		if ($weekTotal < $perWeek and ($perWeek - $weekTotal) > 1)
		{
			// If the user has completed less tests this week than the value in the perWeek.txt file
			echo("<p>This week you need to complete " . ($perWeek - $weekTotal) . " more tests.</p>");
			// Tell the user how many tests they have left to complete
		}
		elseif ($weekTotal < $perWeek and ($perWeek - $weekTotal) == 1)
		{
			echo("<p>This week you need to complete 1 more test.</p>");
			// For grammar correctness
		}
		else
		{
			echo("<p>Well Done! You have completed your tests for this week.</p>");
			// Tell the user if they have completed all their tests for the week
		}
	}
	$conn->close();
?>
<p><a href="learn.php">Learn</a></p>
<p><a href="test.php">Test</a></p>
<p><a href="review.php">Review</a></p>
<p><a href="logout.php">Logout</a></p>
</div>
</body>
</html>
