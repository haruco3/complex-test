<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Complex Numbers - Admin Home</title>
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
<?php
	echo("<h1>Welcome, " . $_SESSION['name'] . "!</h1>");
	// Displays a welcome message to the user with the user's username
?>
<p><a href="adminManage.php">Manage Tests</a></p>
<p><a href="adminUsers.php">Manage Users</a></p>
<p><a href="adminReview.php">Review Student Results</a></p>
<p><a href="adminPerWeek.php">Set Tests per Week</a></p>
<p><a href="backup.php">Backup</a></p>
<p><a href="logout.php">Logout</a></p>
</div>
</body>
</html>
