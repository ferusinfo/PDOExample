<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
// Open your config_template file, change the values and rename it to CONFIG.PHP
include_once( __DIR__ . '/../config/config.php');

/**
 * Class for user login purposes
 */
class Users
{

	/* Db handler */
	private $Db;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// initialize pdo connection
		$this->connectDB();
	}

	public function logIn($login, $password) {
		$statement = $this->checkLogin($login, $password);
		if ($statement->rowCount() != 1)
			return false;
		$user = null;

		foreach($statement as $u) {
			$user = new User($u['id_user'], $u['login'], $u['haslo']);
		}

		session_start();
		session_regenerate_id(true);
      	$_SESSION['login'] = array($u['id_user'], $u['login'], $u['haslo']);
      	return $user;
	}

	public function logOff() {
		session_start();
		unset($_SESSION['login']);
   		setcookie('login', '', time() - 86400);
   		session_destroy();
	}

	public function loggedIn() {
		return isset($_SESSION['login']) ? $_SESSION['login'] : false;
	}

	public function checkLogin($login, $password) {
		$statement = $this->Db->prepare("SELECT * FROM user WHERE login = :login AND haslo = :haslo");
		$statement->bindValue(':login', $login, PDO::PARAM_STR);
		$statement->bindValue(':haslo', $password, PDO::PARAM_STR);
		$statement->execute();
		return $statement;
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
class User
{
	public $id_user;
	public $login;
	private $haslo;

	public function __construct($id, $login, $haslo)
	{
		$this->id_user = $id;
		$this->login = $login;
		$this->haslo = $haslo;
	}
}
