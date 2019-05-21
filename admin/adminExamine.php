<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Complex Numbers - Admin - Examine Results</title>
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
	}
?>

<div id="mainbox">
<h1>Examine Tests</h1>
<p>Choose a test then click "Go":</p>
<?php
// create connection
$conn = new mysqli($_SESSION['servername'], $_SESSION['sqlUser'], $_SESSION['sqlPass'], $_SESSION['dbname']);
if($conn->connect_errno > 0){
    die('Unable to connect to database [' . $conn->connect_error . ']');
	// Prints error if unable to connect
}

$sql = "SELECT testtaken_id, username, test_name, date_taken FROM testtaken
INNER JOIN users
ON testtaken.user_id = users.user_id
INNER JOIN tests
ON testtaken.test_id = tests.test_id
ORDER BY username ASC, test_name ASC, date_taken ASC";
// Selects the testtaken id, username, test name and date taken for each row in testtaken
// Since testtaken does not contain the columns username and test_name, inner joins are needed
$result = $conn->query($sql);
// Gets the result of the SQL query
if ($result->num_rows == 0) {
	echo("Error: No tests found.");
	// If no tests have been taken so far prints error message
}
else
{
	echo('<form action="viewTest.php" method="GET">
		<select name="testSelection">');
	// Prints a dropdown menu in a form which will send the test to be viewed to viewTest.php
	for ($x = 0; $x < $result->num_rows; $x++) {
		$row = $result->fetch_row();
		// Fetched row from database
		echo '<option value="' .  $row[0] . '">' . $row[1] . ': ' . $row[2] . ' - ' . $row[3] . '</option>';
		// Prints an option in the dropdown menu
	}
	echo('</select>
<input type="submit" value="Go" /><br />
</form>');
// Prints a submit button and the end of the form
}
$conn->close();
// Closes the database connection
?>
</select>
</form>
<p><a href="admin/adminReview.php">Return</a></p>
</div>
</body>
</html>
