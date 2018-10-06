<?php
/**
 * Created by PhpStorm.
 * User: Simen Bai - Bai Media
 * Date: 24.09.2018
 * Time: 14.37
 */

/**
 * Simplify printing error message depending on if it is development or production environment
 *
 * @param string $message The error message
 * @param Exception $exception exception connected to the error
 */
function errorMessage($message, $exception = null){
    //Gets the global ini settings, and check if it's development environment
    global $ini;
    if($ini['is_development']){

        //Prints the given message if it is in development
        echo($message."<br>");
        //Prints the stacktrace if available
        if(!is_null($exception)){
            echo($exception->getTrace());
        }
    }
    else{
        //Echo general error message if not development environment
        echo("Something wrong happened!");
    }
    exit;
}