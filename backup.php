<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Complex Numbers - Admin - Backup</title>
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
<h1>Backup</h1>
<?php
	if (isset($_GET['action']))
	{
		// Create connection
		$conn = new mysqli($_SESSION['servername'], $_SESSION['sqlUser'], $_SESSION['sqlPass'], $_SESSION['dbname']);
		if($conn->connect_errno > 0)
		{
		    die('Unable to connect to database [' . $conn->connect_error . ']');
			// Prints error if unable to connect
		}
		else
		{
			if($_GET['action'] == "save")
			{
				// This is if the user wants to create a backup
				
				// Make files blank
				file_put_contents('users.csv', "");
				file_put_contents('tests.csv', "");
				file_put_contents('questions.csv', "");
				file_put_contents('testtaken.csv', "");

				// Backup users table
				$sql = $conn->prepare("SELECT * FROM users");
				$sql->execute();
				$sql->bind_result($user_id, $username, $password, $is_admin);
				while ($sql->fetch())
				{
					file_put_contents('users.csv', $user_id . "," . $username . "," . $password . "," . $is_admin . "\n", FILE_APPEND);
					// Appends each row in the users table to the end of the users.csv file
				}
				$sql->close();

				// Backup tests table
				$sql = $conn->prepare("SELECT * FROM tests");
				$sql->execute();
				$sql->bind_result($test_id, $test_name);
				while ($sql->fetch())
				{
					file_put_contents('tests.csv', $test_id . "," . $test_name . "\n", FILE_APPEND);
					// Appends each row in the tests table to the end of the tests.csv file
				}
				$sql->close();

				// Backup questions table
				$sql = $conn->prepare("SELECT * FROM questions");
				$sql->execute();
				$sql->bind_result($question_id, $test_id, $question, $answer);
				while ($sql->fetch())
				{
					file_put_contents('questions.csv', $question_id . "," . $test_id . "," . $question . "," . $answer . "\n", FILE_APPEND);
					// Appends each row in the questions table to the end of the questions.csv file
				}
				$sql->close();

				// Backup testtaken table
				$sql = $conn->prepare("SELECT * FROM testtaken");
				$sql->execute();
				$sql->bind_result($testtaken_id, $user_id, $test_id, $date_taken, $score, $test_breakdown, $wrong_answers);
				while ($sql->fetch())
				{
					file_put_contents('testtaken.csv', $testtaken_id . "," . $user_id . "," . $test_id . "," . $date_taken . "," . $score . "," . $test_breakdown . "," . $wrong_answers . "\n", FILE_APPEND);
					// Appends each row in the testtaken table to the end of the testtaken.csv file
				}
				$sql->close();
				echo("<p>All Done!</p>");
			}
			elseif ($_GET['action'] == "load" and file_exists("users.csv") and file_exists("tests.csv") and file_exists("questions.csv") and file_exists("testtaken.csv"))
			{
				// Delete existing data
				$conn->query("DELETE FROM testtaken");
				$conn->query("DELETE FROM questions");
				$conn->query("DELETE FROM tests");
				$conn->query("DELETE FROM users");

				// Load users table
				$file = fopen('users.csv', "r");
				while ($x = fgetcsv($file))
				{
					// Iterates over each line in the csv file
					$sql = $conn->prepare("INSERT INTO users VALUES (?, ?, ?, ?)");
					$sql->bind_param("issi", $x[0], $x[1], $x[2], $x[3]);
					// Inserts the current line into the database
					$sql->execute();
					$sql->close();
				}
				fclose($file);

				// Load tests table
				$file = fopen('tests.csv', "r");
				while ($x = fgetcsv($file))
				{
					$sql = $conn->prepare("INSERT INTO tests VALUES (?, ?)");
					$sql->bind_param("is", $x[0], $x[1]);
					$sql->execute();
					$sql->close();
				}
				fclose($file);

				// Load questions table
				$file = fopen('questions.csv', "r");
				while ($x = fgetcsv($file))
				{
					$sql = $conn->prepare("INSERT INTO questions VALUES (?, ?, ?, ?)");
					$sql->bind_param("iiss", $x[0], $x[1], $x[2], $x[3]);
					$sql->execute();
					$sql->close();
				}
				fclose($file);

				// Load testtaken table
				$file = fopen('testtaken.csv', "r");
				while ($x = fgetcsv($file))
				{
					$sql = $conn->prepare("INSERT INTO testtaken VALUES (?, ?, ?, ?, ?, ?, ?)");
					$sql->bind_param("iiisiss", $x[0], $x[1], $x[2], $x[3], $x[4], $x[5], $x[6]);
					$sql->execute();
					$sql->close();
				}
				fclose($file);
				echo("<p>All Done!</p>");
			}
			elseif ($_GET['action'] == "load")
			{
				echo("Error: One or more backup files are missing.");
				// Error message if the user wants to restore a backup but one of the files is missing
			}
		}
		$conn->close();
	}
?>

<!-- These two links below execute the php code above -->
<p><a href="backup.php?action=save">Save Data</a></p>
<p><a href="backup.php?action=load">Load Data</a></p>
<p><a href="adminHome.php">Return</a></p>
</div>
</body>
</html>
