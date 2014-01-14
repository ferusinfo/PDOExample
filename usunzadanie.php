<?php
include_once('models/users.php');
include_once('models/exercises.php');
$users = new Users();
	if (!$users->loggedIn())
		//header('Location: index.php');

	if (isset($_GET['zad']) && !empty($_GET['zad'])) {
		$exercises = new Exercises($_GET['cat']);
		$exercises ->removeExercise($_GET['zad']);
		header("Location: zadania.php?cat=".$_GET['cat']);
	}

	header('Location: index.php');
?>
