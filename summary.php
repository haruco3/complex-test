<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Complex Numbers - Summary</title>
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
		header( 'Location: adminHome.php' );
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
	// create connection
	$conn = new mysqli($_SESSION['servername'], $_SESSION['sqlUser'], $_SESSION['sqlPass'], $_SESSION['dbname']);
	if($conn->connect_errno > 0){
    	die('Unable to connect to database [' . $conn->connect_error . ']');
	}
	else
	{
		$sql = $conn->prepare("SELECT * FROM testtaken WHERE user_id=?");
		$sql->bind_param("i", $userid);
		$sql->execute();
		// Select all of the tests that the current user has taken
		$result = $sql->get_result();
		$testCount = $result->num_rows;
		$sql->close();
	}
?>
<h1>Review Summary</h1>
<p>Since you joined you have taken 
<?php
	if ($testCount == 0)
	{
		echo("no tests.");
	}
	else if ($testCount == 1)
	{
		echo("1 test.");
	}
	else
	{
		echo($testCount . " tests.");
	}
	// Prints the amount of tests that the user has taken
	$score = 0;
	$total = 0;
	for ($x = 0; $x < $result->num_rows; $x++)
	{
		$row = $result->fetch_row();
		$score = $score + $row[4];
		
		$sql = $conn->prepare("SELECT question_id FROM questions WHERE test_id=?");
		$sql->bind_param("i", $row[2]);
		$sql->execute();
		$sql->store_result();
		$total = $total + $sql->num_rows;
	}
	// Works out the total number of questions the user has answered correctly and the total number of questions the user has answered
	$sql->close();
	$conn->close();
?>
<br />
You have answered 
<?php
	echo ($total . " ");
?>
questions and got 
<?php
	echo ($score . " ");
?>
of them right.
<br />
Overall you have got 
<?php
	if ($total == 0)
	{
		echo('100% ');
	}
	else
	{
		$percentage = ($score / $total) * 100;
		$percentage = round($percentage);
		echo (strval($percentage)) . "% ";
		// Works out a percentage of questions the user has answered correctly
	}
?>
of questions right.
<p><a href="review.php">Return</a></p>
</div>
</body>
</html>
