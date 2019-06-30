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
	}
?>
<div id="mainbox">
<h1>Edit Test</h1>
<?php
	// Create connection
	$conn = new mysqli($_SESSION['servername'], $_SESSION['sqlUser'], $_SESSION['sqlPass'], $_SESSION['dbname']);
	if($conn->connect_errno > 0)
	{
    	die('Unable to connect to database [' . $conn->connect_error . ']');
		// Prints error if unable to connect
	}
	elseif ($_GET['testSelection'] == "create")
	{
		// If user has chosen to create a test
		echo('<p>How many questions would you like the test to have?</p>');
		echo('<form action="createTest.php" method="POST">');
		echo('<input type="text" name="count" /><br />');
		echo('<p><input type="submit" value="Submit" /></p>');
		echo('</form>');
		// Prints a form asking the user how manu questions they would like the test to have
		// Posts the result to createTest.php
	}
	else
	{
		$sql = $conn->prepare("SELECT question_id, question, answer FROM questions WHERE test_id =? ORDER BY test_id");
		$sql->bind_param('i', $_GET['testSelection']);
		// Selects all questions in the test the user has chosen to edit
		$sql->execute();
		$sql->bind_result($question_id, $question, $answer);
		$sql->store_result();
		$idArray = array();
		// $idArray is an array of all the IDs of the questions that are being edited
		echo('<form action="saveTest.php?question_sum=' . ($sql->num_rows()) . '&test_id=' . $_GET['testSelection'] . '" method="POST">');
		$x = 1;
		// $x is the question number in the test
		while($sql->fetch())
		{
			// Iterates over each question in the test
			array_push($idArray, $question_id);
			echo('<p>Question ' . ($x) . '</p>');
			echo('<p>Current Question: ' . $question . '</p>');
			// Prints the current question
			echo('<p>Change Question:</p>');
			echo('<p><input type="text" name="question' . $question_id . '" /></p>');
			// A box to edit the question
			echo('<p>Current Answer: ' . $answer . '</p>');
			// Prints the answer to the current question
			echo('<p>Change Answer:</p>');
			echo('<p><input type="text" name="answer' . $question_id . '" /></p><br />');
			// A box to edit the answer
			$x++;
		}
		$_SESSION['idArray'] = $idArray;
		echo('<p>Add New Question:</p>');
		echo('<p><input type="text" name="newQuestion" /></p>');
		echo('<p>Answer:</p>');
		echo('<p><input type="text" name="newAnswer" /></p><br />');
		// Boxes to add a new question
		echo('<input type="submit" value="Submit" /><br />');
		echo('</form>');
		$sql->close();
	}
	$conn->close();
?>
<p><a href="adminManage.php">Return</a></p>
</div>
</body>
</html>
