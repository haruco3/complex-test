<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Complex Numbers - Test Results</title>
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
		header( 'Location: admin/adminHome.php' );
		// Redirects to the admin home page if the user is an admin
	}
	$_SESSION['timeout'] = time();
?>
<div id="mainbox">
<h1>Test</h1>
<?php
$test = $_GET['test'];
// Gets ID of test user took

// Create connection
$conn = new mysqli($_SESSION['servername'], $_SESSION['sqlUser'], $_SESSION['sqlPass'], $_SESSION['dbname']);
if($conn->connect_errno > 0){
    die('Unable to connect to database [' . $conn->connect_error . ']');
}
$sql = $conn->prepare("SELECT question, answer FROM questions WHERE test_id =?");
$sql->bind_param("s", $test);
$sql->execute();
// Selects each question, and the relevant answer, in the test
$sql->store_result();
if ($sql->num_rows == 0) {
	$sql->close();
	echo("Error: No questions found.");
	// This is unlikely to happen but just in case
}
else
{
	$questions = array();
	$answers = array();
	$rightAnswers = array();
	$binaryRightAnswers = array();
	$wrongAnswers = array();
	// These arrays will be used later
	$rightCount = 0;
	$sql->bind_result($question, $answer);
	for ($x = 0; $x < $sql->num_rows; $x++) {
		$sql->fetch();
		// Fetches each question + answer in the test
		if(strtolower($_POST['q' . $x]) == strtolower($answer))
		{
			// If the user's answer matches the actual answer
			array_push($questions, $question);
			// Pushes the question into the questions array
			array_push($rightAnswers, NULL);
			// This array is used for the right answers to the questions which the user answered incorrectly
			// If the user answered it correctly, there is no need to store the actual answer
			array_push($answers, $_POST['q' . $x]);
			// Pushes the user's (correct) answer onto the answers array
			array_push($binaryRightAnswers, 1);
			// This is an array of whether the user got the question right or not
			// 1 = correct, 0 = incorrect
			$rightCount++;
			// The student's score is increased by 1
		}
		else
		{
			array_push($questions, $question);
			array_push($answers, $_POST['q' . $x]);
			array_push($binaryRightAnswers, 0);
			array_push($rightAnswers, $answer);
			// Pushes the actual answer onto the rightAnswers array
			array_push($wrongAnswers, $_POST['q' . $x]);
			// Pushes the user's incorrect answer onto the wrongAnswers array
		}
	}
	echo("<p>You got ");
	if($rightCount == 1)
	{
		echo($rightCount . " question");
	}
	elseif($rightCount == 0)
	{
		echo("no questions");
	}
	else
	{
		echo($rightCount . " questions");
	}
	// Prints how many questions the user got right
	echo(" right!</p><p>Test Breakdown:</p>");
	for($x = 0; $x < count($questions); $x++)
	{
		// For each question in the test
		echo("<p>Question " . ($x + 1) . ": " . $questions[$x] . "<br />Your answer: " . $answers[$x]);
		// Prints the question and the user's answer
		if($binaryRightAnswers[$x] == 1)
		{
			// If the user got the question right
			echo("<br />Correct!</p>"); 
		}
		else
		{
			// If the user got the question wrong
			echo("<br />Incorrect!<br />The correct answer was: " . $rightAnswers[$x] . "</p>");
			// Print the correct answer
		}
	}
	$sql->close();
	$sql = "SELECT testtaken_id FROM testtaken ORDER BY testtaken_id DESC";
	$result = $conn->query($sql);
	$row = $result->fetch_row();
	$testtaken_id = $row[0] + 1;
	// New testtaken_id will be current highest testtaken_id + 1
	$sql = $conn->prepare("INSERT INTO testtaken VALUES (?, ?, ?, CURDATE(), ?, ?, ?)");
	$test_breakdown = "";
	$wrong_answers_str = "";
	foreach($binaryRightAnswers as $x)
	{
		$test_breakdown = $test_breakdown . $x;
		// A string version of the binaryRightAnswers array, to be submitted to the database
	}
	foreach($wrongAnswers as $x)
	{
		$wrong_answers_str = $wrong_answers_str . $x . " ";
		// A string version of the wrongAnswers array, separated by spaces, to be submitted to the database
	}

	$sql->bind_param("iiisss", $testno, $_SESSION['userid'], $test, $rightCount, $test_breakdown, $wrong_answers_str);

	$sql->execute() or die($sql->error);
	// Submit the test results to the database
	$sql->close();
}
$conn->close();
?>
<p><a href="test.php">Return</a></p>
</div>
</body>
</html>
