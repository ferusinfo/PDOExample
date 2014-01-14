<?php
include_once('models/categories.php');
$categories = new Categories();
include_once('models/users.php');
$users = new Users();
	if (!$users->loggedIn())
		header('Location: index.php');
	if (isset($_POST['katfrom']) && !empty($_POST['katfrom']) && ($_POST['katfrom'] != $_POST['katto'])) {
		$new_cat = (!empty($_POST['katto'])) ? $_POST['katto'] : null;
		$categories->moveCategory($_POST['katfrom'], $new_cat);
	}
	header('Location: index.php');
?>
