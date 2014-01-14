<?php
include_once('models/categories.php');
$categories = new Categories();
include_once('models/users.php');
$users = new Users();
	if (!$users->loggedIn())
		header('Location: index.php');
	if (isset($_POST['katname']) && !empty($_POST['katname'])) {
		$subkat = (!empty($_POST['sub'])) ? $_POST['sub'] : null;
		$categories->addCategory($_POST['katname'], $subkat);
	}
	header('Location: index.php');
?>
