<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
session_start();
?>
<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>PDOExample</title>
  <meta name="author" content="Maciej Kolek">

  <!-- Pure CSS as a base stylesheet for the rescue :) -->
  <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.3.0/pure-min.css">
  <link rel="stylesheet" href="css/style.css?v=1.0">
  <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
  <script src="js/scripts.js"></script>

  <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->


</head>
<body>
<?php
	include_once('models/categories.php');
	include_once('models/users.php');
	$users = new Users();
	$loggedIn = $users->loggedIn();
	$categories = new Categories();
	$object = $categories->fetchCategoriesAndTree();
?>

<div id="main" class="pure-g">
	<div class="pure-u-1" id="links">
		<h1>Kategorie</h1>
		<?php if ($loggedIn) { ?>
	<div id="newform-div" class="pure-u-1-5">
		<form action="dodaj.php" method="post" class="pure-form pure-form-stacked">
    	<fieldset>
	        <legend>Dodaj kategorię</legend>

	        <label for="katname">Nazwa</label>
	        <input type="text" name="katname" placeholder="np. Pierwiastki">

	        <label for="sub">jako subkategoria:</label>
	        <select name="sub">
	        	<option value="">----------------</option>
	        	<?php
	        		foreach ($object->categories as $category)
	        		{
	        			echo '<option value="' . $category->id_category . '">'. $category->category_name .'</option>';
	        		}
	        	?>
	        </select>

	        <button type="submit" class="pure-button pure-button-primary">Dodaj</button>
    	</fieldset>
		</form>
	</div>

	<div id="moveform-div" class="pure-u-1-5">
		<form action="przenies.php" method="post" class="pure-form pure-form-stacked">
    	<fieldset>
	        <legend>Przenieś kategorię</legend>

	        <label for="katname">Kategoria początkowa</label>
	        <select name="katfrom">
	        	<option value="">----------------</option>
	        	<?php
	        		foreach ($object->categories as $category)
	        		{
	        			echo '<option value="' . $category->id_category . '">'. $category->category_name .'</option>';
	        		}
	        	?>
	        </select>

	        <label for="katto">Kategoria docelowa</label>
	        <select name="katto">
	        	<option value="">----------------</option>
	        	<?php
	        		foreach ($object->categories as $category)
	        		{
	        			echo '<option value="' . $category->id_category . '">'. $category->category_name .'</option>';
	        		}
	        	?>
	        </select>

	        <button type="submit" class="pure-button pure-button-primary">Przenieś</button>
    	</fieldset>
		</form>
	</div>
	<?php } ?>
	<div id="newform-div" class="pure-u-1-5">
		<?php
		if ($loggedIn) {
			echo '<a href="logout.php">Wyloguj się!</a>';
		} else { ?>
		<form action="login.php" method="post" class="pure-form pure-form-stacked">
    	<fieldset>
	        <legend>Zaloguj się</legend>

	        <label for="login">Login</label>
	        <input type="text" name="login" placeholder="logowanie"/>


	        <label for="haslo">Haslo</label>
	        <input type="password" name="haslo" placeholder="******"/>

	        <button type="submit" class="pure-button pure-button-primary">Dodaj</button>
    	</fieldset>
		</form>
		<?php } ?>
	</div>

	<div class="pure-u-1" id="right">

	<?php
	// Print main categories and go down in the tree
	if (count($object->main_cats) >0 )
	{
		foreach ($object->main_cats as $main_category_id)
		{
			print_categories($object, $main_category_id);
		}
	}
	else
	{
		echo "<h3>Aktualnie brak kategorii.</h3>";
	}

	?>
	</div>
</div>

<?php
function print_categories($object, $category_id)
{
	$users = new Users();
	$loggedIn = $users->loggedIn();

	$current_tree = $object->tree[$category_id];

	$current_category = $object->categories[$category_id];
	$string = '<div class="displaycat">';
	if ($current_category->category_level > 0) {
		for ($i=1; $i<=$current_category->category_level; $i++)
		{
			// just a categories separator, can be anything basically, */()<div>
			$string .= '<div class="spacer"></div>';
		}
	}

	// tu komorka tabeli
	$string .= ' &nbsp;<strong>' .$current_category->category_name . '</strong> <i>[ID: ' . $category_id . ']</i>';
	if ($loggedIn)
		$string .= ' [<a href="edytuj.php?cat=' . $category_id . '">Edytuj</a>] [<a href="usun.php?katname=' . $category_id . '">Usuń</a>]';
	$string .= ' [<a href="zadania.php?cat=' . $category_id . '">Zobacz zadania</a>] [<a href="dodaj.php?cat=' . $category_id . '">Rozwiń/Zwiń</a>]';
	$string .= "</div>";
	echo $string;

	if (!empty($current_tree))
	{
		echo '<div class="subcat">';
		foreach ($current_tree as $sub_cat)
		{
			print_categories($object, $sub_cat);
		}
		echo '</div>';
	}

}
?>
</body>
</html>


