<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
// Open your config_template file, change the values and rename it to CONFIG.PHP
include_once( __DIR__ . '/../config/config.php');

/**
 * Class for fetching Categories from Database
 */
class Categories
{
	/* Category level counter */
	private $level_counter = 0;

	/* Categories array */
	private $categories = array();

	/* Main categories array */
	private $main_categories = array();

	/* Categories tree array */
	private $tree = array();

	/* Db handler */
	private $Db;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// initialize pdo connection
		$this->connectDB();
		$this->getCategories();
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

	/**
	 * Run some test for moving, adding, removing
	 */
	public function runTests()
	{
		// Move category to its old tree
		$this->moveCategory(7,6);

		// Add new main category
		$this->addCategory("New Main Category");

		// Add new sub category binded to category with ID 3
		$this->addCategory("New Sub category", 3);

		// Move this category 7 to be main category
		$this->moveCategory(7);

		// Remove category
		$this->removeCategory(2);

		// Remove category and switch parent
		$this->removeCategory(6,4);
	}

	/* Chech if category got any childs */
	private function hasChild($parent_id)
	{
		$statement = null;
		try
		{
		    $statement = $this->Db->prepare('SELECT COUNT(*) as count FROM kat WHERE id_nadkat = :id');
			$statement->bindValue(':id', $parent_id, PDO::PARAM_INT);
			$statement->execute();
		}
		catch (PDOException $e)
		{
		    trigger_error("Error in " . __METHOD__ . ": " . $e->getMessage(), E_USER_ERROR);
		}

		$count = 0;

		foreach ($statement as $row)
		{
			$count = $row['count'];
		}
		return $count;
	}

	/* Get subcategories */
	private function getSubcategories($category_id)
	{
		$statement = null;

		try
		{
			$statement = $this->Db->prepare('SELECT * FROM kat WHERE id_nadkat = :id');
			$statement->bindValue(':id', $category_id, PDO::PARAM_INT);
			$statement->execute();
		}
		catch (PDOException $e)
		{
			 trigger_error("Error in " . __METHOD__ . ": " . $e->getMessage(), E_USER_ERROR);
		}
		$this->level_counter++;

		foreach ($statement as $row)
		{


			if (!isset($this->categories[$row['id_kat']]))
			{
				$this->categories[$row['id_kat']] = null;
			}

			$category = new Category($row['id_kat'], $row['nazwa'], $row['id_nadkat'], $this->level_counter);

			$this->categories[$row['id_kat']] = $category;

			$this->tree[$category->id_category] = array();

			// add this category id to the tree
			$this->tree[$category_id][] = $category->id_category;

			if ($this->hasChild($category->id_category))
			{
				$this->getSubcategories($category->id_category);
			}
		}
	}

	/* get categories */
	private function getCategories()
	{
		$statement = null;

		try
		{
			$statement = $this->Db->query('SELECT * FROM kat WHERE id_nadkat IS NULL');
		}
		catch (PDOException $e)
		{
		    trigger_error("Error in " . __METHOD__ . ": " . $e->getMessage(), E_USER_ERROR);
		}

		foreach ($statement as $row)
		{
			$this->level_counter = 0;

			$this->main_categories[] = $row['id_kat'];

			if (!isset($this->categories[$row['id_kat']]))
			{
				$this->categories[$row['id_kat']] = null;
			}

			$category = new Category($row['id_kat'], $row['nazwa'], $row['id_nadkat'], $this->level_counter);

			$this->categories[$row['id_kat']] = $category;

			$this->tree[$category->id_category] = array();

			if ($this->hasChild($category->id_category))
			{
				$this->getSubcategories($category->id_category);
			}
		}

		if (DEBUG)
		{
			$this->debug("CATEGORIES");
			$this->debug_result($this->categories);
			$this->debug("TREE");
			$this->debug_result($this->tree);
			$this->debug("MAIN CATEGORIES");
			$this->debug_result($this->main_categories);
		}
	}

	/* add a category */
	public function addCategory($category_name, $category_parent_id = NULL)
	{
		if (isset($category_parent_id) && !$this->categoryExists($category_parent_id)) {
			$category_parent_id = NULL;
		}

		try
		{
			$statement = $this->Db->prepare("INSERT INTO kat (nazwa, id_nadkat) VALUES (:catname, :parent)");
			$statement->bindValue(':catname', $category_name, PDO::PARAM_STR);
			$statement->bindValue(':parent', $category_parent_id, PDO::PARAM_INT);
			$statement->execute();
		}
		catch (PDOException $e)
		{
			 trigger_error("Error in " . __METHOD__ . ": " . $e->getMessage(), E_USER_ERROR);
		}
	}

	/* move a category */
	public function moveCategory($id_category, $id_new_parent = null)
	{
		try
		{
			$statement = $this->Db->prepare("UPDATE kat set id_nadkat = :parent WHERE id_kat = :id");
			$statement->bindValue(':id', $id_category, PDO::PARAM_INT);
			$statement->bindValue(':parent', $id_new_parent, PDO::PARAM_INT);
			$statement->execute();
		}
		catch (PDOException $e)
		{
			 trigger_error("Error in " . __METHOD__ . ": " . $e->getMessage(), E_USER_ERROR);
		}
	}

	/* edit a category */
	public function editCategory($id_category, $nazwa, $parent)
	{
		if ($this->categoryExists($id_category))
		{
			try
			{
				$statement = $this->Db->prepare("UPDATE kat set id_nadkat = :parent, nazwa = :nazwa WHERE id_kat = :id");
				$statement->bindValue(':id', $id_category, PDO::PARAM_INT);
				$statement->bindValue(':nazwa', $nazwa, PDO::PARAM_STR);
				$statement->bindValue(':parent', $parent, PDO::PARAM_INT);
				$statement->execute();
			}
			catch (PDOException $e)
			{
				 trigger_error("Error in " . __METHOD__ . ": " . $e->getMessage(), E_USER_ERROR);
			}
		}
	}

	private function categoryExists($category_id)
	{
		$statement = $this->getCategory($category_id);
		return ($statement->rowCount() > 0) ? true : false;
	}

	private function getCategory($category_id)
	{
		$statement = $this->Db->prepare("SELECT * FROM kat WHERE id_kat = :id");
		$statement->bindValue(':id', $category_id, PDO::PARAM_INT);
		$statement->execute();
		return $statement;
	}

	public function fetchCategory($category_id)
	{
		if ($this->categoryExists($category_id))
		{
			$statement = $this->getCategory($category_id);
			foreach ($statement as $row)
				return new Category($row['id_kat'], $row['nazwa'], $row['id_nadkat']);
		}
	}

	/* remove category */
	public function removeCategory($category_id)
	{
		$statement = null;

		if ($this->categoryExists($category_id))
		{
			try
			{
				$statement = $this->Db->prepare("SELECT * FROM kat WHERE id_nadkat = :id");
				$statement->bindValue(':id', $category_id, PDO::PARAM_INT);
				$statement->execute();
			}
			catch (PDOException $e)
			{
				 trigger_error("Error MOVE in " . __METHOD__ . ": " . $e->getMessage(), E_USER_ERROR);
			}

			if ($statement->rowCount() > 0)
			{
				foreach ($statement as $row)
				{
					$this->moveCategory($row['id_kat']);
				}
			}

			try
			{
				$statement = $this->Db->prepare("DELETE FROM kat WHERE id_kat = :id");
				$statement->bindValue(':id', $category_id, PDO::PARAM_INT);
				$statement->execute();
			}
			catch (PDOException $e)
			{
				 trigger_error("Error DELETE in " . __METHOD__ . ": " . $e->getMessage(), E_USER_ERROR);
			}
		}
	}

	/* get full categories object */
	public function fetchCategoriesAndTree()
	{
		$fetched_object = new stdClass;
		$fetched_object->categories = $this->categories;
		$fetched_object->tree = $this->tree;
		$fetched_object->main_cats = $this->main_categories;

		return $fetched_object;
	}

	/* dead simple debug function */
	private function debug($string)
	{
		echo "<h5>" . $string . "</h5>";
	}

	/* debug array result, useful */
	private function debug_result($array)
	{
		echo "<pre>";
		print_r(var_dump($array), true);
		echo "</pre>";
	}
}

/** Class for a Category object **/
class Category
{
	public $id_category;
	public $category_name;
	public $id_parent;
	public $category_level;

	public function __construct($id, $name, $parent, $level = null)
	{
		$this->id_category = $id;
		$this->category_name = $name;
		$this->id_parent = $parent;
		$this->category_level = $level;
	}
}
