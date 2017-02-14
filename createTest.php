<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Complex Numbers - Admin - Create Test</title>
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
<h1>Create Test</h1>
<?php
	// Create connection
	$conn = new mysqli($_SESSION['servername'], $_SESSION['sqlUser'], $_SESSION['sqlPass'], $_SESSION['dbname']);
	if($conn->connect_errno > 0)
	{
    	die('Unable to connect to database [' . $conn->connect_error . ']');
		// Prints error if unable to connect
	}
	elseif (isset($_GET["error"]) && $_GET["error"] == 1)
	{
		echo("<p>Please enter a valid test name.</p>");
		// If the user does not enter a test name, this error message is shown
	}
	elseif (isset($_GET["error"]) && $_GET["error"] == 2)
	{
		echo("<p>Please enter all questions and answers.</p>");
		// If the user does not fill in all of the questions and answers, this error message is shown
	}
	elseif (isset($_POST['count']) == false or $_POST['count'] == '')
	{
		echo('Please enter the number of questions.');
		// If the user did not enter a number of questions in the previous page, this error message is shown
	}
	elseif (isset($_POST['count']) && (is_int($_POST['count']) == true or $_POST['count'] < 1))
	{
		echo('Please enter a valid number of questions.');
		// If the user did not enter a positive integer amount of questions, this error message is shown
	}
	else
	{
		// Get the highest question id so far
		$sql = $conn->prepare("SELECT question_id FROM questions ORDER BY question_id DESC");
		$sql->execute();
		$sql->bind_result($id);
		$sql->fetch();

		// create the form, which posts the created questions and answers to saveTest.php
		echo('<form action="saveTest.php?question_sum=' . $_POST['count'] . '&create=true" method="POST">');
		echo('<p>Test Name:</p>');
		echo('<p><input type="text" name="testName" /></p>');
		$idArray = array();
		// A variable to show normal question numbers to the user
		$y = 1;
		// Iterates over the set number of questions - $x is the id of the new question to be added, since it starts at the highest question id + 1
		for ($x=$id + 1; $x < ($_POST['count'] + $id + 1); $x++)
		{
			//Creates boxes to enter questions and answers
			echo('<p>Question ' . $y . ':</p>');
			echo('<p><input type="text" name="question' . ($x) . '" /></p>');
			echo('<p>Answer:</p>');
			echo('<p><input type="text" name="answer' . ($x) . '" /></p>');
			$y++;
			array_push($idArray, $x);
		}
		echo('<p><input type="submit" value="Submit" /></p>');
		echo('</form>');
		$_SESSION['idArray'] = $idArray;
		// Saves an array of the IDs of the questions to be added as a session variable
	}
?>
<p><a href='editTest.php?testSelection=create'>Return</a></p>
</div>
</body>
</html>
