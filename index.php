<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login Form</title>
<link href="css/home.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
	session_start();
	if (isset($_SESSION['name']))
	{
		header( 'Location: home.php' );
		// If already logged in, redirect to homepage
	}
?>
<div id="mainbox">
<h1>Login</h1>
<form id="login" action="login2.php" method="POST">
<?php
	if (isset($_GET["login"])) {
		if ($_GET["login"] == "fail") {
			print("<p>Invalid Credentials</p>");
		}
		else if ($_GET["login"] == "timeout")
		{
			print("<p>Session timed out. Please log in again.</p>");
		}
		else if ($_GET["login"] == "false") {
			print("<p>Please log in to access this page.</p>");
		}
		// Error messages
    }
?>
Username:
<input type="text" name="username" /><br />
Password:
<input type="password" name="password"/><br />
<p><input type="submit" value="Submit" /></p>
</form>
</div>
</body>
</html>
