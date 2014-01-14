<?php
	include_once('models/categories.php');
	$categories = new Categories();
	include_once('models/users.php');
	$users = new Users();
	if (!$users->loggedIn())
		header('Location: index.php');

if (isset($_POST['katname']) && !empty($_POST['katname']))
{
	$sub = (!empty($_POST['sub'])) ? $_POST['sub'] : null;
	$categories->editCategory($_POST['idkat'], $_POST['katname'], $sub);
	header('Location: index.php');
}
else
{
	if (!isset($_GET['cat']) && empty($_GET['cat']))
		header('Location: index.php');

	$categoriesObj = $categories->fetchCategoriesAndTree();
	$category = $categories->fetchCategory($_GET['cat']);
?>
<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>PDOExample - Edytuj kategorię</title>
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
		<h1>Edycja</h1>
	<div id="newform-div" class="pure-u-2-5">
		<form action="edytuj.php" method="post" class="pure-form pure-form-stacked">
    	<fieldset>
	        <legend>Edytuj kategorię <?php echo $category->category_name; ?></legend>

	        <label for="katname">Nazwa</label>
	        <input type="text" name="katname" placeholder="np. Pierwiastki" value="<?php echo $category->category_name; ?>"/>
	        <label for="sub">jako subkategoria:</label>
	        <select name="sub">
	        	<option value="">----------------</option>
	        	<?php
	        		foreach ($categoriesObj->categories as $cat)
	        		{
	        			if ($category->id_parent == $cat->id_category)
	        			{
	        				echo '<option value="' . $cat->id_category . '" selected="selected">'. $cat->category_name .'</option>';
	        			}
	        			else
	        			{
	        				echo '<option value="' . $cat->id_category . '">'. $cat->category_name .'</option>';
	        			}
	        		}
	        	?>
	        </select>
	        <input type="hidden" name="idkat" value="<?= $category->id_category ?>"/>
	        <button type="submit" class="pure-button pure-button-primary">Edytuj</button>
    	</fieldset>
		</form>
	</div>
</body>
</html>

<?php
}
?>
