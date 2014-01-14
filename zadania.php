<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
session_start();

include_once('models/categories.php');
include_once('models/users.php');
include_once('models/exercises.php');

if (!isset($_GET['cat']) || empty($_GET['cat']))
	header("Location: index.php");

$exercises = new Exercises($_GET['cat']);
$category = $exercises->getCategory();
$exercises_array = $exercises->getExercises();

include_once('models/users.php');
$users = new Users();
$loggedIn = $users->loggedIn();

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

<div id="main" class="pure-g">
	<div class="pure-u-1">
		<h1>Zadania - <?= $category->category_name ?></h1>
	<div id="newform-div" class="pure-u-1-5">
		<a href="index.php">Powrot do listy kategorii</a>
		<?php if($loggedIn) {
			echo '<a href="dodajzadanie.php?cat=' . $category->id_category . '">Dodaj zadanie</a>';
		}
		?>

	</div>
	</div>
	<div class="pure-u-1" id="zadania">
		<table class="pure-table pure-table-bordered">
			<thead>
		        <tr>
		            <th>#</th>
		            <th>Nazwa</th>
		            <th>Trudność</th>
		            <th>Akcja</th>
		        </tr>
    		</thead>
    		<tbody>
    			<?php if (empty($exercises_array)) { ?>
    			<tr>
    				<td colspan="4">Aktualnie brak zadań</td>
    			</tr>
    			<?php } else {
    				$i = 0;
    				foreach ($exercises_array as $exe ) {
    					$i++;
    					echo "<tr>";
    						echo "<td>" . $i . "</td>";
    						echo "<td>" . $exe->name . "</td>";
    						echo "<td>" . $exe->level . "</td>";
    						echo "<td>[<a href=\"files/" . $exe->file. "\">Pobierz plik</a>]";
    						if($loggedIn) {
    							echo " [<a href=\"edytujzadanie.php?zad=" . $exe->id . "&cat=".$category->id_category."\">Edytuj zadanie</a>]";
								echo " [<a href=\"usunzadanie.php?zad=" . $exe->id . "&cat=".$category->id_category."\">Usuń zadanie</a>]";
							}
							echo "</td>";
    					echo "</tr>";
    				}
    			} ?>
    		</tbody>
    	</table>
	</div>
</body>
</html>


