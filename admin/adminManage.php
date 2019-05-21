<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Complex Numbers - Admin - Manage Tests</title>
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
<h1>Manage Tests</h1>
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
		$sql = $conn->prepare("SELECT * FROM tests");
		$sql->execute() or die($sql->error);
		// Selects both columns from tests table
		$sql->bind_result($testID, $testName);
		// Bind the results to variables
		echo('<p>Please choose a test to edit:</p>');
		echo('<form action="admin/editTest.php" method="GET">');
		echo('<select name="testSelection">');
		// Prints a dropdown menu in a form which will send the test to be edited to admin/editTest.php
		while ($sql->fetch())
		{
			echo('<option value="' .  $testID . '">' . $testName . '</option>');
			// Prints an entry in the dropdown menu for each test in the database
		}
		echo('<option value="' . ("create") . '">Create Test</option>');
		// Prints an option for creating a test
		echo('</select>
			  <input type="submit" value="Go" /><br />');
		echo('</form>');
		// Prints a submit button and the end of the form
		$sql->close();
	}
	$conn->close();
	// Closes database connection
?>
<br />
<p><a href="admin/adminHome.php">Return</a></p>
</div>
</body>
</html>
