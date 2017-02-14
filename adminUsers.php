<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Complex Numbers - Admin - Edit Users</title>
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
<h1>Manage Users</h1>
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
		echo('<p>Choose a user to manage then select an option, or use the link below to change your password:</p>');
		echo('<form action="editUser.php" method="GET">');
		echo('<select name="userSelection">');
		// Prints a dropdown menu in a form which will send the user to be edited to editUser.php
		$sql = $conn->prepare("SELECT user_id, username, is_admin FROM users WHERE user_id<>?");
		$sql->bind_param("i", $_SESSION['userid']);
		// Selects all users from the database, except for the current user
		$sql->execute();
		$sql->bind_result($user_id, $username, $is_admin);
		while ($sql->fetch())
		{
			// Iterates over each user in the database
			echo('<option value="' .  $user_id . '">' . $username);
			if ($is_admin == 0)
			{
				echo(' - Student');
			}
			else
			{
				echo(' - Admin');
			}
			echo('</option>');
			// Prints an entry in the dropdown menu for the current user
		}
		$sql->close();
		echo('</select>');
		echo('<p><input type="submit" name="action" value="Change Password" />');
		echo('<input type="submit" name="action" value="Delete User" />');
		echo('<input type="submit" name="action" value="Toggle Admin" /></p>');
		// The three options for editing a user selected in the dropdown menu
		echo('</form>');
		echo('<p><a href=editUser.php?action=Change+Password&userSelection=' . $_SESSION['userid'] . '>Change your Password</a></p>');
		// An option for changing the logged in user's password
		echo('<p><a href=editUser.php?action=Create+User>Create New User</a></p>');
		// An option for creating a new user
	}
	$conn->close();
?>
<p><a href="adminHome.php">Return</a></p>
</div>
</body>
</html>
