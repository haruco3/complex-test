<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Complex Numbers - Learn</title>
<link href="../css/home.css" rel="stylesheet" type="text/css" />
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
<p>A complex number is made up of a real part and an imaginary part. The real number 1 is denoted by the letter i and the imaginary number &radic;<span class="sqrt">-1</span> is denoted by the letter j.</p>
<br />
<p>For example, the complex number (3i + 4j) is equal to 3 + &radic;<span class="sqrt">-1</span>.</p>
<br />
<p>If x = (3i + 4j) then Re(x) = 3 and Im(x) = 4.</p>
<br />
<p>Since j = &radic;<span class="sqrt">-1</span>, j<sup>2</sup> = -1, j<sup>3</sup> = -j and j<sup>4</sup> = 1.</p>
<br />
<p><a href="../learn.php">Return</a></p>
</div>

</body>
</html>
