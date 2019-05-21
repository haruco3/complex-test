<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Complex Numbers - Take Test</title>
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
		header( 'Location: admin/adminHome.php' );
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
<h1>Test</h1>
<?php
$test = $_GET["testSelection"];

// Create connection
$conn = new mysqli($_SESSION['servername'], $_SESSION['sqlUser'], $_SESSION['sqlPass'], $_SESSION['dbname']);
if($conn->connect_errno > 0)
{
    die('Unable to connect to database [' . $conn->connect_error . ']');
	// Prints error if unable to connect
}

$sql = $conn->prepare("SELECT * FROM questions WHERE test_id=?");
$sql->bind_param("i", $test);
$sql->execute();
$result = $sql->get_result();
if ($result->num_rows == 0) {
	echo("Error: No questions found.");
	// Prints error message if there are no questions in the selected test
}
else
{
	echo '<form action="submitTest.php?test=' . $test . '" method="POST">';
	// Creates a form which posts the student's answers to submitTest.php
	for ($x = 0; $x < $result->num_rows; $x++) {
		// Iterates over each question in the test
		$row = $result->fetch_row();
		echo '<p>' . $row[2] . '</p><input type="text" name="q' . $x . '" /><br />';
		// Prints the current question, then creates an answer box for it
	}
	echo '<p><input type="submit" value="Submit" /></p></form>';
}
$conn->close();
?>
<p><a href="test.php">Return</a></p>
</div>
</body>
</html>
