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
	}
	else
	{
		$change_ar = array();
		foreach ($_SESSION['idArray'] as $x)
		{
			// idArray is an array of the IDs of all the questions to be altered
			$cur_question = $_POST['question' . $x];
			$cur_answer = $_POST['answer' . $x];
			// Gets the changed question and answer for each id
			if ($cur_question != "" or $cur_answer != "")
			{
				// If the user has made a change to the question, answer or both
				array_push($change_ar, [$x, $cur_question, $cur_answer]);
				// Push an array of the id + the changed question + answer into the array chage_ar
			}
		}
		unset($_SESSION['idArray']);
		// Delete the now useless idArray session variable

		if (isset($_GET['create']) and $_GET['create'] == 'true')
		{
			// If the user is creating a new test
			if ($_POST['testName'] != "")
			{
				$sql = $conn->prepare("SELECT test_id FROM tests ORDER BY test_id DESC");
				$sql->execute();
				$sql->bind_result($test_id);
				$sql->fetch();
				// Gets the highest test ID in the database
				$sql->close();
				$test_id++;
				// test_id will be the test id of the new test to be inserted
				$sql = $conn->prepare("INSERT INTO tests VALUES (?, ?)");
				$sql->bind_param('is', $test_id, $_POST['testName']);
				// Creates the new test
				$sql->execute();
				$sql->close();
			}
			else
			{
				// If the admin has not set a test name, redirect back with an error
				header('Location: admin/createTest.php?error=1');
			}
		}

		foreach ($change_ar as $x)
		{
			// For each question that is going to be changed
			if (isset($_GET['create']) == false && $x[1] == "")
			{
				// If the user is only changing the question
				$sql = $conn->prepare("UPDATE questions SET answer=? WHERE question_id=?");
				$sql->bind_param("si", $x[2], $x[0]);
				$sql->execute();
				// Change the question
				$sql->close();
			}
			elseif (isset($_GET['create']) == false && $x[2] == "")
			{
				// If the user is only changing the answer
				$sql = $conn->prepare("UPDATE questions SET question=? WHERE question_id=?");
				$sql->bind_param("si", $x[1], $x[0]);
				$sql->execute();
				// Change the answer
				$sql->close();
			}
			else
			{
				if (isset($_GET['create']) and $_GET['create'] == 'true')
				{
					// If the user is creating a new test
					if($x[1] == "" or $x[2] == "")
					{
						header('Location: admin/createTest.php?error=2');
						// Redirect back with an error if the user has forgotten to fill in a question or answer
					}
					else
					{
						$sql = $conn->prepare("INSERT INTO questions VALUES (?, ?, ?, ?)");
						$sql->bind_param("iiss", $x[0], $test_id, $x[1], $x[2]);
						// Insert the new question and answer into the questions table
					}
				}
				else
				{
					// If the user is changing a question and an answer
					$sql = $conn->prepare("UPDATE questions SET question=?, answer=? WHERE question_id=?");
					$sql->bind_param("ssii", $x[1], $x[2], $x[0]);
					// Update the question and answer
				}
				$sql->execute();
				$sql->close();
			}
		}
		
		if (isset($_GET['create']) == false and $_POST['newQuestion'] != "" and $_POST['newAnswer'] != "")
		{
			// If the user is adding a new question to a test
			$sql = $conn->prepare("SELECT question_id FROM questions ORDER BY question_id DESC");
			$sql->execute();
			$sql->bind_result($count);
			$sql->fetch();
			$count++;
			// Get the highest question id in the database, add 1
			$sql->close();
			$sql = $conn->prepare("INSERT INTO questions VALUES (?, ?, ?, ?)");
			$sql->bind_param("iiss", $count, $_GET['test_id'], $_POST['newQuestion'], $_POST['newAnswer']);
			$sql->execute() or die($sql->error);
			// Insert new question into the database
			$sql->close();
			echo("<p>New Question successfully inserted.</p>");
		}
		elseif (isset($_GET['create']) == false and ($_POST['newQuestion'] != "" xor $_POST['newAnswer'] != ""))
		{
			// If the user has entered either the new question or the answer to the new question (but not both)
			echo("<p>New Question not inserted - you need to enter both a question and an answer.</p>");
			// Print error message
		}

		echo("<p>All done!</p>");
	}
	$conn->close();
?>
<br />
<p><a href="admin/adminManage.php">Return</a></p>
</div>
</body>
</html>
