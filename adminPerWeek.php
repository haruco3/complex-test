<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Complex Numbers - Admin - Set Questions per Week</title>
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
<h1>Set Tests Per Week</h1>
<?php
	if (isset($_POST['perWeek']) and $_POST['perWeek'] > 0)
	{
		file_put_contents('perWeek.txt', $_POST['perWeek']);
		// The user can choose a value for the tests per week from the form on this page, which posts the result to this page
		// If the user has chosen a non-zero value for the tests per week, places the value in the test per week text file
	}
	elseif (isset($_POST['perWeek']))
	{
		unlink('perWeek.txt');
		// If the value is zero, deletes the test per week file
	}
	if (file_exists('perWeek.txt'))
	{
		$file = fopen('perWeek.txt', "r");
		$perWeek = fgets($file);
		fclose($file);
		echo("Currently students are set to do " . $perWeek . " tests per week.");
		// If the tests per week file exists, prints the value of it
	}
	else
	{
		echo("Currently students are not set to do an amount of tests per week.");
		// Otherwise, prints that students are not set to do any amount of tests per week
	}
?>
<p>How many test would you like students to do per week?</p>
<form action=adminPerWeek.php method="POST">
<!-- This is a form where the user can choose a value for the tests per week -->
<p><select name="perWeek">
<option value="0">0</option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option></select>
<input type="submit" value="Submit" /></p>
</form>
<p><a href="adminHome.php">Return</a></p>
</div>
</body>
</html>
