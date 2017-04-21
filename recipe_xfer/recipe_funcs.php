<?php
/**
 * Created by PhpStorm.
 * User: joel
 * Date: 4/19/17
 * Time: 10:57 AM
 */

require_once "db_conn.php";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

date_default_timezone_set("America/Chicago");
$time_stamp = date("Y-m-d H:m:s");

$func =  sanitize($_GET["func"]);

switch ($func) {
    case "get_rec_id" :

        echo get_rec_id($conn, sanitize($_GET["uid"]));
        break;
    case "get_step_id" :
        echo get_step_id($conn);
        break;
    default:
        echo "I know nothing";
}

function get_rec_id($conn, $uid) {
    $retval = [];

    $retval['recid'] = uniqid();
    $retval['stepid'] = uniqid();
    /*
    $sql = "SELECT RecipeID from Recipe ORDER BY RecipeID * 1 DESC LIMIT 1;";
    $result = $conn->query($sql);
    $retval['recid'] = $result->fetch_assoc()["RecipeID"] + 1;
    $sql = "INSERT INTO Recipe (RecipeID, UserID, RecipeName, DateCreated) VALUES (" .
        $retval['recid'] .",". $uid . ",'placeholder',NOW());";
    $conn->query($sql);

    $sql = "SELECT InstructionID FROM Instructions ORDER BY InstructionID DESC LIMIT 1";
    $result = $conn->query($sql);
    $retval['stepid'] = $result->fetch_assoc()["InstructionID"] + 1;
    $sql = "INSERT INTO Instructions (InstructionID, Description) VALUES (" .
        $retval['stepid'] . ",'');";
    $conn->query($sql);
    $sql = "INSERT INTO RecipeInstructions (RecipeID, OrderNumber, InstructionID) VALUES (" .
        $retval['recid'] . ",1," .$retval['stepid'] . ");";
    $conn->query($sql);
    */
    $sql = "SELECT FirstName, LastName FROM User WHERE UserID=" . $uid . ";";
    $result = $conn->query($sql);
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $retval['username'] = $row["FirstName"] . " " . $row["LastName"];
    }
    else {
        $retval['username'] = "Anonymous";
    }
    return json_encode( $retval );

}

function get_step_id($conn) {
    return uniqid();

    /*
    $sql = "SELECT InstructionID FROM Instructions ORDER BY InstructionID * 1 DESC LIMIT 1";
    $result = $conn->query($sql);
    $retval = $result->fetch_assoc()["InstructionID"] + 1;
    $sql = "INSERT INTO Instructions (InstructionID, Description) VALUES (" .
        $retval . ",'');";
    $conn->query($sql);
    $sql = "INSERT INTO RecipeInstructions (RecipeID, OrderNumber, InstructionID) VALUES (" .
        $recid . "," . $ordernum . "," .$retval . ");";
    $conn->query($sql);
    return $retval;
    */
}

function sanitize($input)
{
    $output = trim($input);
    $output = stripslashes($output);
    return htmlspecialchars($output);
}