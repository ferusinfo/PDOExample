<?php
	session_start();
	include_once('models/exercises.php');
	include_once('models/users.php');
	include_once('models/categories.php');
	$categories = new Categories();
	$users = new Users();
	if (!$users->loggedIn())
		header('Location: index.php');

if (isset($_POST['zadname']) && !empty($_POST['zadname']))
{
	$exercises = new Exercises($_POST['cat']);
	$exercise = $exercises->editExercise($_POST['zadid'], $_POST['zadname'], $_POST['level']);
	header('Location: zadania.php?cat=' . $_POST['cat']);
}
else
{
	if (!isset($_GET['zad']) || empty($_GET['zad']))
		header('Location: index.php');

	if (!isset($_GET['cat']) || empty($_GET['cat']))
		header('Location: index.php');

	$exercises = new Exercises($_GET['cat']);
	$category = $exercises->getCategory();
	$exercise = $exercises->getExercise($_GET['zad']);
?>
<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>PDOExample - Edytuj zadanie</title>
  <meta name="author" content="Maciej Kolek">

  <!-- Pure CSS as a base stylesheet for the rescue :) -->
  <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.3.0/pure-min.css">
  <link rel="stylesheet" href="css/style.css?v=1.0">
  <script src="js/scripts.js"></script>

  <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->


</head>
<body>
<div id="main" class="pure-g">
	<div class="pure-u-1" id="links">
		<h1>Dodaj zadanie</h1>
	<div id="newform-div" class="pure-u-2-5">
		<form action="edytujzadanie.php" method="post" class="pure-form pure-form-stacked">
    	<fieldset>
	        <legend>Edytuj zadanie - <?= $exercise->name ?></legend>

	        <label for="zadname">Nazwa</label>
	        <input type="text" name="zadname" placeholder="np. Prosta kombinatoryka" value="<?= $exercise->name ?>"/>
	        <label for="level">Trudność</label>
	        <select name="level">
	        <?php for($i=1;$i<=5;$i++) {
	        	$selected = ($i == $exercise->level) ? 'selected="selected"' : null;
	        	echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
	        }
	        ?>
	    </select>
	    	<input type="hidden" name="cat" value="<?= $category->id_category ?>"/>
	    	<input type="hidden" name="zadid" value="<?= $exercise->id ?>"/>
	        <button type="submit" class="pure-button pure-button-primary">Edytuj</button>
    	</fieldset>
		</form>
	</div>
</body>
</html>
<?php
}
?>
