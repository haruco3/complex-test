<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<?php
	$learn = $_GET["learnSelection"];
	if ($learn == 1)
	{
		header( 'Location: learn/realcomplex.php' );
	}
	elseif ($learn == 2)
	{
		header( 'Location: learn/addsubtract.php' );
	}
	elseif ($learn == 3)
	{
		header( 'Location: learn/argand.php' );
	}
	elseif ($learn == 4)
	{
		header( 'Location: learn/modulusargument.php' );
	};
	// Redirects the user to the learn page that they requested in learn.php
?>
</body>
</html>
