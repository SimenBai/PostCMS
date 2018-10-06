<?php
/**
 * Created by PhpStorm.
 * User: Simen Bai - Bai Media
 * Date: 14-Sep-18
 * Time: 16:59
 */

/**
 * Class mySQLDatabase
 */
class mySQLDatabase
{
    //Defines variables that will be used
    private static $hostname, $username, $password, $dbname, $charset;
    private $connection;

    /**
     * Creates a PDO connection
     */
    public function connect()
    {
        try {
            //Creates the Data Source Name that will be used connect to mysql database.
            $dsn = "mysql:host=" . self::$hostname . ";dbname=" . self::$dbname . ";charset=" . self::$charset;

            //Creates a PDO connection with the DSN and the username and password
            $conn = new PDO($dsn, self::$username, self::$password);

            //Set attributes for the PDO connection
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //Sets the PDO connection of the instance
            $this->setConnection($conn);
        } catch (Exception $exception) {
            //Sets everything to null if an error occurs.
            errorMessage("Error: " . $exception->getMessage());
            $conn = null;
            $this->disconnect();
        }
    }

    /**
     * Closes the PDO connection of the instance
     */
    public function disconnect()
    {
        $this->connection = null;
    }

    /**
     * Returns the PDO connection of the instance
     *
     * @return PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Sets the PDO connection of the instance
     *
     * @param PDO $connection A PDO connection
     */
    private function setConnection($connection): void
    {
        $this->connection = $connection;
    }


    /**
     *  Initializes the database, sets up the database, tables and keys
     */
    public static function init()
    {
        //Assigns the database values gotten from the settings file statically to the class
        global $ini;
        self::$hostname = $ini['hostname'];
        self::$username = $ini['username'];
        self::$password = $ini['password'];
        self::$dbname = $ini['dbname'];
        self::$charset = $ini['charset'];

        try {
            //Creates PDO instance
            $conn = new PDO(
                "mysql:host" . self::$hostname . ";charset=" . self::$charset,
                self::$username,
                self::$password
            );
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //Executes sql for database creation
            $sql = "CREATE DATABASE IF NOT EXISTS wf_cms CHARSET = utf8 COLLATE utf8_general_ci;";
            $conn->exec($sql);

        } catch (Exception $exception) {
            //Sets the connection to null, and runs the error message
            errorMessage("Error: " . $exception->getMessage());
            $conn = null;
        }

        //Gets a new PDO connection
        $database = new mySQLDatabase();
        $database->connect();
        $conn = $database->getConnection();
        try {

            //Prepares and executes the sql statement
            $sql = "SET
                        FOREIGN_KEY_CHECKS = 0;
                    CREATE TABLE IF NOT EXISTS `wf_cms`.`users`(
                        `user_id` INT NOT NULL AUTO_INCREMENT,
                        `username` VARCHAR(255) NOT NULL,
                        `password_hash` VARCHAR(255) NOT NULL,
                        PRIMARY KEY(`user_id`),
                        UNIQUE(`username`)
                    ) ENGINE = InnoDB CHARSET = utf8 COLLATE utf8_general_ci; 
                    CREATE TABLE IF NOT EXISTS `wf_cms`.`posts`(
                        `post_id` INT NOT NULL AUTO_INCREMENT,
                        `poststamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        `contents` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                        `user_id` INT NULL DEFAULT NULL,
                        PRIMARY KEY(`post_id`),
                        FOREIGN KEY(`user_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
                    ) ENGINE = InnoDB CHARSET = utf8 COLLATE utf8_general_ci;";
            $conn->exec($sql);

            //Ends the PDO connection
            $conn = null;
            $database->disconnect();
        } catch (Exception $exception) {
            //Runs the error message and ends the PDO connection
            errorMessage("Error: " . $exception->getMessage());
            $conn = null;
            $database->disconnect();
        }
    }
}

//Initalizes the sql database when file gets loaded to assure the database and the database values always exists.
mySQLDatabase::init();