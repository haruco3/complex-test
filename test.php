<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Complex Numbers - Test</title>
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
<h1>Test</h1>
<p>Choose a test then click "Go":</p>
<?php
// Create connection
$conn = new mysqli($_SESSION['servername'], $_SESSION['sqlUser'], $_SESSION['sqlPass'], $_SESSION['dbname']);
if($conn->connect_errno > 0){
    die('Unable to connect to database [' . $conn->connect_error . ']');
}
$sql = "SELECT * FROM tests";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
	echo("Error: No tests found.");
}
else
{
	echo('<form action="doTest.php" method="GET">
<select name="testSelection">');
	// Creates a form which lets the user select a test to take
	for ($x = 0; $x < $result->num_rows; $x++) {
		$row = $result->fetch_row();
		echo '<option value="' .  $x . '">' . $row[1] . '</option>';
	}
	// Prints an option in a dropdown menu for each test in the database
	echo('</select>
<input type="submit" value="Go" /><br />
</form>');
}
$conn->close();
?>
<br />
<p><a href="home.php">Return</a></p>
</div>
</body>
</html>
