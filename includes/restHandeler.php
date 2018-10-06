<?php
/**
 * Created by PhpStorm.
 * User: Simen Bai - Bai Media
 * Date: 29-Sep-18
 * Time: 16:06
 */

//Checks if post request was received
if (isset($_POST) && !empty($_POST)) {
    //Gets the file of the script, and run the function that is related to the script.
    $file = $_SERVER['PHP_SELF'];

    //Checks if the script has a function that should be run.
    switch ($file) {
        case "/index.php":
            indexPost();
            break;
        default:
            break;
    }
}

//Checks if get request was received
if (isset($_GET) && !empty($_GET)) {
    //Gets the file of the script, and run the function that is related to the script.
    $file = $_SERVER['PHP_SELF'];

    //Checks if the script has a function that should be run.
    switch ($file) {
        case "/index.php":
            break;
        default:
            break;
    }
}

/**
 * Post processing for index page.
 */
function indexPost()
{
    //If post is set
    if (isset($_POST["post"]) && !is_null($_POST["post"])  && !empty($_POST["post"])) {
        //Adds the post to the database
        sqlUtils::addPost($_POST["post"]);
    }
}