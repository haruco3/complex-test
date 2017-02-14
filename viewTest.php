<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Complex Numbers - Examine Test</title>
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
<?php
if (!isset($_GET['testSelection']))
{
	header( 'Location: examine.php' );
	// Redirects back if no test has been set to examine
}

// create connection
$conn = new mysqli($_SESSION['servername'], $_SESSION['sqlUser'], $_SESSION['sqlPass'], $_SESSION['dbname']);
if($conn->connect_errno > 0){
    die('Unable to connect to database [' . $conn->connect_error . ']');
}
else
{
	$sql = $conn->prepare("SELECT * FROM testtaken WHERE testtaken_id=?");
	$sql->bind_param("i", $_GET['testSelection']);
	$sql->execute() or die($sql->error);
	// Fetches the data from the testtaken entity
	$result = $sql->get_result();
	$sql->close();
	$row = $result->fetch_row();
	$wrongAnswers = explode(" ", $row[6]);
	// Creates an array of the user's incorrect answers
	$breakdown = $row[5];
	echo('<p>You got ');
	if($row[4] == 1)
	{
		echo($row[4] . " question");
	}
	elseif($row[4] == 0)
	{
		echo("no questions");
	}
	else
	{
		echo($row[4] . " questions");
	}
	// row[4] is the user's score
	echo(" right!</p><p>Test Breakdown:</p>");
	$sql = "SELECT * FROM questions WHERE test_id=" . $row[2];
	$result = $conn->query($sql) or die($conn->error);
	$questions = array();
	$answers = array();
	$wrongNo = 0;
	for ($x = 0; $x < $result->num_rows; $x++) {
		$row = $result->fetch_row();
		array_push($questions, $row[2]);
		array_push($answers, $row[3]);
	}
	// Fetches all the questions in the test, as well as the correct answers
	for($x = 0; $x < count($questions); $x++)
	{
		echo("<p>Question " . ($x + 1) . ": " . $questions[$x] . "<br />" . "Your answer: ");
		if(substr($breakdown, $x, $x + 1) == 1)
		{
			// If the user answered the question correctly
			echo($answers[$x] . "<br />");
			echo("Correct!</p>");
			// Prints the answer to the question (which is the same as the user's answer)
		}
		else
		{
			echo($wrongAnswers[$wrongNo] . "<br />");
			echo("Incorrect!<br />The correct answer was: " . $answers[$x] . "</p>");
			$wrongNo++;
			// Prints the correct answer to the question
		}
	}
}
$conn->close();
?>
<p><a href="examine.php">Return</a></p>
</div>
</body>
</html>
