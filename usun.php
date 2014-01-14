<?php
include_once('models/categories.php');
$categories = new Categories();
	if (isset($_GET['katname']) && !empty($_GET['katname'])) {
		$categories->removeCategory($_GET['katname']);
	}
	header('Location: index.php');
?>
