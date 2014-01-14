<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
// Open your config_template file, change the values and rename it to CONFIG.PHP
include_once( __DIR__ . '/../config/config.php');
include_once('categories.php');
/**
 * Class for user login purposes
 */
class Exercises
{

	/* Db handler */
	private $Db;

	private Category category;
	private $exercises = array();
	private $category_id;

	/**
	 * Constructor
	 */
	public function __construct($category_id)
	{
		// initialize pdo connection
		$this->connectDB();
		$this->category_id = $category_id;
		$this->category = $this->setCategory();
	}

	public function getCategory()
	{
		return $this->category;
	}

	private function setCategory()
	{
		$categories = new Categories();
		return $categories->fetchCategory($this->category_id);
	}

	public function addExercise($eCat, $eName, $eLevel, $eFile)
	{
		try
		{
			$statement = $this->Db->prepare("INSERT INTO zad (id_kat, nazwa, plik_pdf, trudnosc) VALUES (:catid, :ename, :epdf, :elevel)");
			$statement->bindValue(':catid', $eCat, PDO::PARAM_INT);
			$statement->bindValue(':ename', $eName, PDO::PARAM_STR);
			$statement->bindValue(':epdf', $eFile, PDO::PARAM_STR);
			$statement->bindValue(':clevel', $eLevel, PDO::PARAM_INT);
			$statement->execute();
		}
		catch (PDOException $e)
		{
			 trigger_error("Error in " . __METHOD__ . ": " . $e->getMessage(), E_USER_ERROR);
		}
	}

	private function getExercises()
	{
		try
			{
				$statement = $this->Db->prepare("SELECT * FROM zad WHERE id_kat = :id");
				$statement->bindValue(':id', $this->category_id, PDO::PARAM_INT);
				$statement->execute();
			}
			catch (PDOException $e)
			{
				 trigger_error("Error in " . __METHOD__ . ": " . $e->getMessage(), E_USER_ERROR);
			}

			foreach ($statement as $row) {
				$this->exercises = new Exercise($row['id_zad'], $row['nazwa'], $row['plik_pdf'], $row['trudnosc']);
			}

		return $this->exercises;
	}

	public function editExercise($id_exercise, $eName, $eLevel)
	{
			try
			{
				$statement = $this->Db->prepare("UPDATE zad set nazwa = :ename, trudnosc = :elevel WHERE id_zad = :id");
				$statement->bindValue(':id', $id_exercise, PDO::PARAM_INT);
				$statement->bindValue(':ename', $eName, PDO::PARAM_STR);
				$statement->bindValue(':elevel', $eLevel, PDO::PARAM_INT);
				$statement->execute();
			}
			catch (PDOException $e)
			{
				 trigger_error("Error in " . __METHOD__ . ": " . $e->getMessage(), E_USER_ERROR);
			}
	}

	public function removeExercise($id_exercise)
	{
		$statement = null;

		try
		{
			$statement = $this->Db->prepare("DELETE FROM zad WHERE id_zad = :id");
			$statement->bindValue(':id', $id_exercise, PDO::PARAM_INT);
			$statement->execute();
		}
		catch (PDOException $e)
		{
			 trigger_error("Error DELETE in " . __METHOD__ . ": " . $e->getMessage(), E_USER_ERROR);
		}
	}

	/**
	 * Connect to DB
	 */
	private function connectDB()
	{
		try
		{
		    $this->Db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE, DB_USER, DB_PASS);
		}
		catch (PDOException $e)
		{
		    trigger_error("Could not connect to database: " . $e->getMessage(), E_USER_ERROR);
		}

		if (DEBUG)
			$this->debug("Success, connected to database.");

		// http://stackoverflow.com/a/12202218
		// Prevent PDO from failing to more advanced SQL Injection
		$this->Db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

		// Set Exception error mode, even when you hate it - just like me.
		$this->Db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	/**
	 * Close your connection to DB
	 */
	public function __destruct()
	{
		// http://stackoverflow.com/a/5772638
		// PDO doesn't have an explicit "close" function.
		$this->Db = null;
	}


}

/** Class for a Category object **/
class Exercise
{
	public $id;
	public $name;
	public $file;
	public $level;

	public function __construct($id, $name, $file, $level)
	{
		$this->id = $id;
		$this->name = $name;
		$this->file = $file;
		$this->level = $level;
	}
}
