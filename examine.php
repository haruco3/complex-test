<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Complex Numbers - Examine Tests</title>
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
		header( 'Location: admin/adminExamine.php' );
		// Redirects to the admin examine page if the user is an admin
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
$userid = $_SESSION['userid'];

// create connection
$conn = new mysqli($_SESSION['servername'], $_SESSION['sqlUser'], $_SESSION['sqlPass'], $_SESSION['dbname']);
if($conn->connect_errno > 0)
{
    die('Unable to connect to database [' . $conn->connect_error . ']');
}

$sql = $conn->prepare("SELECT testtaken_id, test_name, date_taken FROM testtaken
INNER JOIN users
ON testtaken.user_id = users.user_id
INNER JOIN tests
ON testtaken.test_id = tests.test_id
WHERE testtaken.user_id=?
ORDER BY username ASC, date_taken ASC, test_name ASC");
// Selects the testtaken id test name and date taken for each row in testtaken
// Since testtaken does not contain the columns username and test_name, inner joins are needed
$sql->bind_param("i", $userid);
$sql->execute();
$result = $sql->get_result();
$sql->close();

if ($result->num_rows == 0) {
	echo("Error: No tests found.");
}
else
{
	echo('<form action="viewTest.php" method="GET">
<select name="testSelection">');
	// A form that lets the student select a test he/she has taken 
	for ($x = 0; $x < $result->num_rows; $x++) {
		$row = $result->fetch_row();
		echo '<option value="' .  $row[0] . '">' . $row[1] . " - " . $row[2] . '</option>';
		// Creates an entry in the dropdown menu for each test taken by the student
		// the value is equal to the ID of the test
	}
	echo('</select>
<input type="submit" value="Go" /><br />
</form>');
}
$conn->close();
?>
</select>
</form>
<p><a href="review.php">Return</a></p>
</div>
</body>
</html>
