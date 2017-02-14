<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login Redirect</title>
</head>
<body>
<?php
$name = $_POST['username'];
$pass = $_POST['password'];
// Gets username and password entered by user

$servername = "localhost";
$sqlUser = "root";
$sqlPass = "";
$dbname = "compnums";
// Database variables

// Create connection
$conn = new mysqli($servername, $sqlUser, $sqlPass, $dbname);
if($conn->connect_errno > 0){
    die('Unable to connect to database [' . $conn->connect_error . ']');
}

$sql = $conn->prepare('SELECT * FROM users WHERE username=? AND password=?');
$sql->bind_param('ss', $name, $pass);
// Looks for user in database where username and password match those entered
$sql->execute();
$sql->bind_result($userid, $name, $pass, $isAdmin);
$sql->store_result();
echo($sql->num_rows);
if ($sql->num_rows == 1) {
	// There will be one match if the username and password are correct
	$sql->fetch();
	$sql->close();
	session_start();
	$_SESSION['name'] = $name;
	$_SESSION['timeout'] = time();
	$_SESSION['userid'] = $userid;
	$_SESSION['isAdmin'] = $isAdmin;
	$_SESSION['servername'] = $servername;
	$_SESSION['sqlUser'] = $sqlUser;
	$_SESSION['sqlPass'] = $sqlPass;
	$_SESSION['dbname'] = $dbname;
	// Sets the session variables for the database and the user
	header( 'Location: home.php' );
	// Redirects to the homepage
}
else
{
	$sql->close();
	header( 'Location: index.php?login=fail' );
	// Goes back to login page with error message if login details were incorrect
}
$conn->close();
?>
</body>
</html>
