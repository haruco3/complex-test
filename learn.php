<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Complex Numbers - Learn</title>
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
		// Redirects to the admin homepage if the user is an admin
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
<h1>Learn</h1>
<p>Choose a topic to learn then click "Go":</p>
<form id="login" action="learn_redirect.php" method="GET">
<select name="learnSelection">
	<option value="1">Real and Imaginary Parts</option>
	<option value="2">Addition and Subtraction</option> 
	<option value="3">Argand Diagrams</option>
    <option value="4">Modulus-Argument Form</option>
</select>
<input type="submit" value="Go" />
</form>
<br />
<p><a href="home.php">Return</a></p>
</div>
</body>
</html>
