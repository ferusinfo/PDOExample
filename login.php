<?php
include_once('models/users.php');
$users = new Users();
if (isset($_POST['login']) && !empty($_POST['login'])
	&& isset($_POST['haslo']) && !empty($_POST['haslo']))
{
	$users->logIn($_POST['login'], $_POST['haslo']);
}

header('Location: index.php');
?>
