<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Complex Numbers - Admin - Summary</title>
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
	elseif ($_SESSION['isAdmin'] == 0)
	{
		header( 'Location: home.php' );
		// Redirects to the student homepage if the user is a student
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
		$userid = $_SESSION['userid'];
		// Gets the ID of the current user and stores it as a local variable
	}
?>

<div id="mainbox">
<h1>Summary</h1>
<?php
	// Create connection
	$conn = new mysqli($_SESSION['servername'], $_SESSION['sqlUser'], $_SESSION['sqlPass'], $_SESSION['dbname']);
	if($conn->connect_errno > 0)
	{
    	die('Unable to connect to database [' . $conn->connect_error . ']');
		// Prints error if unable to connect
	}
	else
	{
		$data = array();
		$sql = $conn->prepare("SELECT user_id, username FROM users WHERE is_admin=0");
		// Selects all of the student users from the database
		$sql->execute();
		$result = $sql->get_result();
		// Stores the result of the sql query in a variable
		$sql->close();
		while ($row = $result->fetch_row())
		{
			// Iterates over each row in the result; row is stored as variable $row
			$right = 0;
			// The $right variable stores the number of questions the user answered correctly
			$total = 0;
			// The $total variable stores the total amount of questions the user has answered
			$sql = $conn->prepare("SELECT test_id, score FROM testtaken WHERE user_id=?");
			$sql->bind_param("i", $row[0]);
			// Selects every test that the user has taken
			$sql->execute();
			$result2 = $sql->get_result();
			$sql->close();
			while ($row2 = $result2->fetch_row())
			{
				// Iterates over each test that the user has taken
				$sql = $conn->prepare("SELECT question_id FROM questions WHERE test_id=?");
				$sql->bind_param("i", $row2[0]);
				$sql->execute();
				// Selects all the questions in the current test
				$sql->store_result();
				$right = $right + $row2[1];
				// Adds the user's score in the current test to the $right variable
				$total = $total + $sql->num_rows;
				// Adds the total number of questions in the current test to the $total variable
				$sql->close();
			}
			array_push($data, [$row[1], $right, $total]);
			// Pushes an array of the username, number of questions answered correctly and total number of questions to the $data array
		}
		echo("<table style='width:100%'>");
		echo("<tr>");
		echo("<td><b>Username</b></td>");
		echo("<td><b>Percentage Correct</b></td>");
		echo("</tr>");
		// Creates a table for the data to go in
		foreach ($data as $x)
		{
			// This will iterate over each student
			echo("<tr>");
			echo("<td>" . $x[0] . "</td>");
			if ($x[2] == 0)
			{
				echo("<td>No Tests Taken</td>");
				// Prints if the user has not taken any tests
			}
			else
			{
				$percentage = ($x[1] / $x[2]) * 100;
				$percentage = round($percentage);
				// Works out the percentage of questions the user has answered correctly
				echo ("<td>" . strval($percentage)) . "%</td>";
			}
			echo("</tr>");
			// A new row in the table has been made, consisting of the username and the percentage of questions answered correctly
		}
		echo("</table>");
	}
	$conn->close();
?>
<p><a href=admin/adminReview.php>Return</a></p>
</div>
</body>
</html>
