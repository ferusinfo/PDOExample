<?php
include_once('models/users.php');
$users = new Users();
$users->logOff();
header('Location: index.php');
?>
