<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Complex Numbers - Admin - Edit User</title>
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
<h1>Manage Users</h1>
<?php
	// Create connection
	$conn = new mysqli($_SESSION['servername'], $_SESSION['sqlUser'], $_SESSION['sqlPass'], $_SESSION['dbname']);
	if($conn->connect_errno > 0)
	{
    	die('Unable to connect to database [' . $conn->connect_error . ']');
		// Prints error if unable to connect
	}
	elseif (isset($_GET['change']) and $_GET['change'] == true)
	{
		// If the user want to change a password
		if ($_POST['pass1'] == $_POST['pass2'])
		{
			// Checks if the user entered matching passwords (since there is a 'confirm password' box)
			$sql = $conn->prepare("UPDATE users SET password=? WHERE user_id=?");
			$sql->bind_param("si", $_POST['pass1'], $_GET['user_id']);
			$sql->execute() or die($sql->error);
			// Sets the user's password to the password chosen
			$sql->close();
			echo("<p>All Done!</p>");
		}
		else
		{
			header("Location: editUser.php?action=Change+Password&userSelection=" . $_GET['user_id'] . "&match=false");
			// If the user made a mistake in the 'confirm password' box, redirects back to the change password page with an error
		}
	}
	elseif (isset($_GET['newUser']) and $_GET['newUser'] == true)
	{
		// If the admin is creating a new user
		if ($_POST['username'] == "" or $_POST['password'] == "")
		{
			echo('Please insert a username and password.');
			// Prints error if the admin did not enter a username or password for the new user
		}
		else
		{
			$sql = $conn->prepare("SELECT user_id, username FROM users ORDER BY user_id DESC");
			$sql->execute();
			// Selects all users in the database
			$sql->bind_result($dbUser_id, $dbUsername);
			$sql->fetch();
			$user_id = $dbUser_id;
			// This is the highest user id in the database
			$unique = 1;
			// This variable signifies whether the username entered for the new user already exists or not
			if (strtolower($_POST['username']) == strtolower($dbUsername))
			{
				// Checks if the current username fetched from the database is the same as the entered username
				$unique = 0;
			}
			else
			{
				// Otherwise, checks against all the other users in the database
				while ($sql->fetch())
				{
					if (strtolower($_POST['username']) == strtolower($dbUsername))
					{
						$unique = 0;
					}
				}
			}
			$sql->close();
			if ($unique == 0)
			{
				header("Location: editUser.php?action=Create+User&error=1");
				// Prints an error if the username entered for the new user already exists in the database
			}
			else
			{
				$user_id++;
				// The ID for the new user will be the current highest user ID + 1
				$sql = $conn->prepare("INSERT INTO users VALUES (?, ?, ?, ?)");
				$sql->bind_param("issi", $user_id, $_POST['username'], $_POST['password'], $_POST['is_admin']);
				// Inserts the new user into the database
				$sql->execute() or die($sql->error);
				$sql->close();
				echo('All Done!');
			}
		}
	}
	else
	{
		if ($_GET['action'] == "Change Password")
		{
			if (isset($_GET['match']) and $_GET['match'] == 'false')
			{
				echo('<p>Please make sure the two passwords match.</p>');
				// If the user entered non-matching passwords on this page
			}
			else
			{
				echo('<p>Please enter the new password:');
			}
			echo('<form action="editUser.php?change=true&user_id=' . $_GET['userSelection'] . '" method="POST">');
			echo('<p>New Password: <input type="password" name="pass1" /></p>');
			echo('<p>Confirm Password: <input type="password" name="pass2" /></p>');
			// A form for the admin to enter a new password for a user.
			// There is a confirmation box; the user has to enter the new password twice
			// This means that there is less chance of the admin making an error
			echo('<p><input type="submit" value="Submit" /></p>');
		}
		elseif ($_GET['action'] == "Delete User")
		{
			$sql = $conn->prepare("DELETE FROM users WHERE user_id=?");
			$sql->bind_param("i", $_GET['userSelection']);
			$sql->execute() or die($sql->error);
			// Deletes a user from the database if the admin has chosen to do so
			$sql->close();
			echo("<p>All Done!</p>");
		}
		elseif ($_GET['action'] == "Toggle Admin")
		{
			// If the admin has chosen to toggle the admin status of a user
			$sql = $conn->prepare("SELECT is_admin FROM users WHERE user_id=?");
			$sql->bind_param("i", $_GET['userSelection']);
			// Fetches the user's current admin status
			$sql->execute() or die($sql->error);
			$sql->bind_result($is_admin);
			$sql->fetch();
			$sql->close();
			if ($is_admin == 0)
			{
				$sql = $conn->prepare("UPDATE users SET is_admin=1 WHERE user_id=?");
				$sql->bind_param("i", $_GET['userSelection']);
				// Makes the user an admin if the user is currently a student
				$sql->execute() or die($sql->error);
				echo("<p>All Done!</p>");
			}
			else
			{
				$sql = $conn->prepare("UPDATE users SET is_admin=0 WHERE user_id=?");
				$sql->bind_param("i", $_GET['userSelection']);
				// Makes the user a student if the user is currently an admin
				$sql->execute() or die($sql->error);
				echo("<p>All Done!</p>");
			}
		}
		elseif ($_GET['action'] == "Create User")
		{
			// If the admin wishes to create a new user
			echo('<form action=editUser.php?newUser=true method="POST">');
			if (isset($_GET['error']) && $_GET['error'] == 1)
			{
				// Print an error if the admin entered a username that already exists in the database
				echo('<p>Please enter a username that does not already exist.</p>');
			}
			echo('<p>Username: <input type="text" name="username" /></p>');
			echo('<p>Password: <input type="password" name="password" /></p>');
			echo('<p>User Type: <select name="is_admin">');
			echo('<option value="0">Student</option>');
			echo('<option value="1">Admin</option></select></p>');
			echo('<p><input type="submit" value="Submit" /></p></form>');
			// Prints a form which lets the admin create a new user
		}
		else
		{
			header("Location: adminUsers.php");
			// If for some reason no action has been set, go to the page where an action can be chosen
		}
	}
	$conn->close();
?>
<p><a href="adminUsers.php">Return</a></p>
</div>
</body>
</html>
