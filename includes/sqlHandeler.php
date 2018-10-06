<?php
/**
 * Created by PhpStorm.
 * User: Simen Bai - Bai Media
 * Date: 06-Oct-18
 * Time: 00:17
 */

/**
 * Class sqlUtils
 */
class sqlUtils
{
    /**
     * Adds a post to the SQL database
     *
     * @param string $content Content of the post
     * @return bool If the post was added or not.
     */
    static function addPost($content)
    {
        //Gets the userID
        global $user;

        //Checks if the user exists, or if the user should be saved as null
        if (self::userIDExists($user)) {
            $userID = $user;
        } else {
            $userID = null;
        }

        //Gets a new PDO connection
        $database = new mySQLDatabase;
        $database->connect();
        $connection = $database->getConnection();

        try {
            //Prepares the sql statement
            $sql = "INSERT INTO `posts`(`contents`, `user_id`) VALUES (:content, :userid)";
            $stmt = $connection->prepare($sql);
            $stmt->bindParam(":content", $content);
            $stmt->bindParam(":userid", $userID);

            //Executes the sql statement, closes the PDO connection and returns true or false depending on the outcome of the execution
            $stmt->execute();
            $database->disconnect();
            return true;

        } catch (Exception $exception) {
            //Something wrong happend; Closes the pdo connection and returns false.
            errorMessage("Error: " . $exception->getMessage() . " - Line number: " . $exception->getLine() . " - File: " . $exception->getFile(), $exception);
            $database->disconnect();
            return false;
        }
    }

    /**
     * Gets all the posts
     *
     * @return array Returns the posts if there are any
     */
    static function getAllPosts()
    {
        //Gets a new PDO connection
        $database = new mySQLDatabase;
        $database->connect();
        $connection = $database->getConnection();

        try {
            //Prepares the sql statement
            $sql = "SELECT posts.poststamp, posts.contents, users.username FROM posts LEFT JOIN users ON posts.user_id = users.user_id";
            $stmt = $connection->prepare($sql);

            //Executes the sql statement and gets the results
            $stmt->execute();
            $result = $stmt->fetchAll();

            //Closes the PDO connection, and returns the results.
            $database->disconnect();
            return $result;
        } catch (Exception $exception) {
            //Returns an empty array if an error occurs
            errorMessage("Error: " . $exception->getMessage() . " - Line number: " . $exception->getLine() . " - File: " . $exception->getFile(), $exception);
            $database->disconnect();
            return [];
        }
    }

    /**
     * Checks if a userID exists or not
     *
     * @param int $userID userid that shall be checked
     * @return bool If the user exists or not
     */
    static function userIDExists($userID)
    {
        //Gets a new PDO connection
        $database = new mySQLDatabase;
        $database->connect();
        $connection = $database->getConnection();

        try {
            //Prepares the sql statement
            $sql = "SELECT user_id FROM users WHERE user_id = :userid";
            $stmt = $connection->prepare($sql);
            $stmt->bindParam(":userid", $userID);

            //Executes the sql statement and gets the results
            $stmt->execute();
            $result = $stmt->fetchAll();

            //Closes the PDO connection, and checks if there is any results.
            //If no results the user doesn't exist
            $database->disconnect();
            if (empty($result)) {
                return false;
            } else {
                return true;
            }
        } catch (Exception $exception) {
            //Something failed, presume the user does not exists as it is more secure
            errorMessage("Error: " . $exception->getMessage() . " - Line number: " . $exception->getLine() . " - File: " . $exception->getFile(), $exception);
            $database->disconnect();
            return false;
        }
    }

    /**
     * Checks if a user exists or not
     *
     * @param string $username Username that shall be checked
     * @return bool If the user exists or not
     */
    static function usernameExists($username)
    {
        //Gets a new PDO connection
        $database = new mySQLDatabase;
        $database->connect();
        $connection = $database->getConnection();

        try {
            //Prepares the sql statement
            $sql = "SELECT username FROM users WHERE username = :username";
            $stmt = $connection->prepare($sql);
            $stmt->bindParam(":username", $username);

            //Executes the sql statement and gets the results
            $stmt->execute();
            $result = $stmt->fetchAll();

            //Closes the PDO connection, and checks if there is any results.
            //If no results the user doesn't exist
            $database->disconnect();
            if (empty($result)) {
                return false;
            } else {
                return true;
            }
        } catch (Exception $exception) {
            //Something failed, presume the user exists as it is more secure
            errorMessage("Error: " . $exception->getMessage() . " - Line number: " . $exception->getLine() . " - File: " . $exception->getFile(), $exception);
            $database->disconnect();
            return true;
        }
    }

    /**
     * Creates a new user
     *
     * @param string $username Username of the new user
     * @param string $password Password of the new user
     * @return bool If the user got created or not.
     */
    static function createUser($username, $password)
    {
        //Gets a new PDO connection
        $database = new mySQLDatabase();
        $database->connect();
        $connection = $database->getConnection();

        //Hashes the given password
        $hash = self::hashPassword($password);

        //Checks if the hashing succeeded, and if the username is used
        //If not, add the user
        if (empty($hash)) {
            return false;
        } else if (self::usernameExists($username)) {
            return false;
        } else {
            try {
                //Prepares the sql statement
                $sql = "INSERT INTO `users`(`username`, `password_hash`) VALUES (:username, :passwordhash);";
                $stmt = $connection->prepare($sql);
                $stmt->bindParam(":username", $username);
                $stmt->bindParam(":passwordhash", $hash);

                //Executes the sql statement, closes the PDO connection and returns true if it got created
                $stmt->execute();
                $database->disconnect();
                return true;
            } catch (Exception $exception) {
                errorMessage("Error: " . $exception->getMessage() . " - Line number: " . $exception->getLine() . " - File: " . $exception->getFile(), $exception);
                $database->disconnect();
                return false;
            }
        }
    }

    /**
     * Checks if the user exists, and the password is correct.
     *
     * @param string $username Username that should be checked
     * @param string $password Supposed password of the user.
     * @return bool If the username and password matches.
     */

    static function checkUserCredentials($username, $password)
    {
        //Gets a new PDO connection
        $database = new mySQLDatabase();
        $database->connect();
        $connection = $database->getConnection();

        try {
            //Prepares the sql statement
            $sql = "SELECT password_hash FROM users WHERE username = :username;";
            $stmt = $connection->prepare($sql);
            $stmt->bindParam(":username", $username);

            //Executes the sql statements and gets all the results
            $stmt->execute();
            $result = $stmt->fetchAll();

            //Gets the password_hash of the first result
            $storedHash = $result[0]["password_hash"];
            //Checks if the given password matches with what was retrieved
            if (self::checkPassword($storedHash, $password)) {
                //Closes the PDO connection and confirms the user credentials
                $database->disconnect();
                return true;
            }
            //Closes the PDO connection and denies the user credentials
            $database->disconnect();
            return false;
        } catch (Exception $exception) {
            errorMessage("Error: " . $exception->getMessage() . " - Line number: " . $exception->getLine() . " - File: " . $exception->getFile(), $exception);
            $database->disconnect();
            return false;
        }
    }

    /**
     * Hashes the password with the given salt. If no salt is given a new one will be generated.
     *
     * @param string $password The password that should be hashed
     * @param string $salt The salt of the password. If not set a salt will be generated
     * @return string Returns the hashed password, or empty string if something failed
     */
    static function hashPassword($password, $salt = "")
    {
        try {
            //Checks if a salt has been generated
            if (empty($salt)) {
                //Generates a cryptographically secure pseudo-random salt
                $salt = bin2hex(random_bytes(32));
            }
            //Hashes the password using sha256 with the salt prepended to make every password unique
            $hash = hash("sha256", $salt . $password);
            //Prepends the salt to the hash so it can be retrieved when checking the password
            $hashedPassord = $salt . $hash;
            return $hashedPassord;
        } catch (Exception $exception) {
            errorMessage("Error: " . $exception->getMessage() . " - Line number: " . $exception->getLine() . " - File: " . $exception->getFile(), $exception);
            return "";
        }
    }


    /**
     * Checks if the two passwords matches.
     *
     * @param string $storedHash Hash of the password gotten from the database
     * @param string $inputPassword Password of the user
     * @return bool If password is correct or not.
     */
    static function checkPassword($storedHash, $inputPassword)
    {
        //Gets the salt that was prepended to the password hash.
        $passwordSalt = substr($storedHash, 0, 64);

        //Hashes the password using the same salt as the stored password
        $hashedInputPassword = self::hashPassword($inputPassword, $passwordSalt);

        if ($storedHash !== $hashedInputPassword) {
            return false;
        }
        return true;
    }
}