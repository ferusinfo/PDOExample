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
	$allowedExts = array("pdf");
	$temp = explode(".", $_FILES["file"]["name"]);
	$extension = end($temp);
	if(in_array($extension, $allowedExts))
	{
		  if ($_FILES["file"]["error"] > 0)
		  {
		    echo "Error: " . $_FILES["file"]["error"] . "<br>";
		    die();
		  } else {
		    move_uploaded_file($_FILES["file"]["tmp_name"],"files/" . $_FILES["file"]["name"]);

		    $exercises = new Exercises($_POST['cat']);
		    $exercises->addExercise($_POST["zadname"], $_POST["level"], $_FILES["file"]["name"]);
		    header("Location: zadania.php?cat=".$_POST["cat"]);
		  }
	 }
	 else
	 {
	 	echo 'Plik ma niepoprawny format. <a href="dodajzadanie.php?cat='.$_POST["cat"].'">Sprobuj jeszcze raz!</a>';
	 	die();
	 }
}
else
{
	if (!isset($_GET['cat']) || empty($_GET['cat']))
		header('Location: index.php');
}
?>
<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>PDOExample - Dodaj zadanie</title>
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
<?php $category = $categories->fetchCategory($_GET['cat']); ?>
<div id="main" class="pure-g">
	<div class="pure-u-1" id="links">
		<h1>Dodaj zadanie</h1>
	<div id="newform-div" class="pure-u-2-5">
		<form action="dodajzadanie.php" method="post" enctype="multipart/form-data" class="pure-form pure-form-stacked">
    	<fieldset>
	        <legend>Dodaj zadanie - <?= $category->category_name ?></legend>

	        <label for="zadname">Nazwa</label>
	        <input type="text" name="zadname" placeholder="np. Prosta kombinatoryka"/>
	        <label for="file">Plik</label>
	        <input type="file" name="file" id="file"/>
	        <label for="level">Trudność</label>
	        <select name="level">
	        <?php for($i=1;$i<=5;$i++) {
	        	echo '<option value="'.$i.'">'.$i.'</option>';
	        }
	        ?>
	    </select>
	    	<input type="hidden" name="cat" value="<?= $category->id_category ?>"/>
	        <button type="submit" class="pure-button pure-button-primary">Dodaj</button>
    	</fieldset>
		</form>
	</div>
</body>
</html>
