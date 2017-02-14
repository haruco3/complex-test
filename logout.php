<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<?php
session_start();
session_unset();
session_destroy();
// Deletes all session variables
if (isset($_GET['login']))
{
	if ($_GET['login']== timeout)
	{
		header( 'Location: index.php?login=timeout' );
		// Redirects to homepage with timeout error message
	}
}
else
{
	header( 'Location: index.php' );
	// Redirects to login page
}
?>
</body>
</html>
