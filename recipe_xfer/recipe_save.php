<?php
/**
 * Created by PhpStorm.
 * User: joel
 * Date: 4/14/17
 * Time: 12:15 PM
 */
require_once "db_conn.php";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
else {
    $raw = file_get_contents("php://input");
    $rec = json_decode($raw);

    $rec_id = $rec->oid;
    $rec_uid = $rec->uid;

    if($rec_id == -1) { // -1 is a flag indicating a new recipe from the client
        $rec_id = recipe_insert(uniqid(), $rec->name, $rec->uid, $rec->preptime, $rec->cooktime, $conn);
    }
    else {
        $rec_id = recipe_save($rec_id, $rec->name, $rec->uid, $rec->preptime, $rec->cooktime, $conn);
    }

    $order_num = 1;
    foreach ($rec->steps as $step) {
        $stepnum = step_save($rec_id, $order_num, $rec->stepID, $rec->description, $conn);
        foreach($rec->ingredients as $ingredient) {
            save_ingredient($stepnum, $ingredient, $conn);
        }
        $order_num++;
    }

}

function recipe_save($recid, $recname, $recuid, $prep, $cook, $conn) {
    $sql = "SELECT UserID FROM Recipe where RecipeID ='" . $recid . "';";
    $result = $conn->query($sql);
    if($result->num_rows > 0) { // recid exists
        $row = $result->fetch_assoc();
        if($row["UserID"] == $recuid) { // user updating is the same as who inserted
            $sql = "UPDATE Recipe " .
                "SET RecipeName='" . $recname . "', " .
                "PrepTime=" . $prep . ", " .
                "CookTime=" . $cook . ", " .
                "DateUpdated=NOW()" .
                " WHERE RecipeID='" . $recid . "';";
            $conn->query($sql);
            return $recid;
        }
        else {
            // the user saving is not the original user, so treat this as a new recipe
            $recid = uniqid();
        }
    }
    return recipe_insert($recid, $recname, $recuid, $prep, $cook, $conn);
}

function recipe_insert($recid, $recname, $recuid, $prep, $cook, $conn) {

    $sql = "INSERT INTO Recipe (RecipeID, RecipeName, UserID, PrepTime, CookTime, DateCreated) " .
        "VALUES ('" . $recid . "','" . $recname . "','" . $recuid . "'," . $prep .
        "," . $cook . ",NOW())";

    $conn->query($sql);
    return $recid;
}

function step_save($recid, $ordernum, $stepid, $desc, $conn) {
    $sql = "SELECT InstructionID FROM Instructions WHERE InstructionID=" . $stepid . ";";
    $result = $conn->query($sql);
    if($result->num_rows > 0) { // Instruction exists
        $sql = "UPDATE Instructions SET Description='" . $desc .
            "' WHERE InstructionID='". $stepid . "';";
        $conn->query($sql);
        $sql = "UPDATE RecipeInstructions SET OrderNumber=" . $ordernum .
            " WHERE InstructionID='" . $stepid . "';";
        $conn->query($sql);
        return $stepid;
    }
    else {
        $new_id = uniqid();

        $sql = "INSERT INTO Instructions (InstructionID, Description) VALUES ('" .
            $new_id . "','" . $desc . "');";
        $conn->query($sql);
        $sql = "INSERT INTO RecipeInstructions (RecipeID, OrderNumber, InstructionID) VALUES ('" .
            $recid . "'," . $ordernum . ",'" .$stepid . "');";
        $conn->query($sql);
        return $new_id;
    }
}

function save_ingredient($step_num, $ing, $conn) {
    $ingid = $ing->oid;
    $sql = "SELECT IngredientID FROM Ingredients WHERE IngredientID='" . $ingid . "';";
    $result = $conn->query($sql);
    if($ingid > 0 && $result->num_rows > 0) {
        $sql = "UPDATE Ingredients SET IngredientName='" . $ing->name .
            "' WHERE IngredientID='" . $ing->oid . "';";
        $conn->query($sql);
    }
    else {
        $ingid= uniqid();
        $sql = "INSERT INTO Ingredients (IngredientID, IngredientName) VALUES ('" .
            $ingid . "','" . $ing->name . "';";
        $conn->query($sql);
    }

    $sql = "SELECT IngredientID, InstructionID FROM InstructionIngredients WHERE " .
        "InstructionID='" . $step_num . "' AND IngredientID='" . $ing->oid . "';";
    $result = $conn->query($sql);
    if($result->num_rows > 0) { // InstructionIngredient exists
        $sql = "UPDATE InstructionIngredients SET UnitID='" . $unitid . "', Quantity='" .
            $ing->amount . "' WHERE InstructionID='" . $step_num . "' AND IngredientID='" . $ingid . "';";
        $conn->query($sql);
    }
    else {
        $sql = "INSERT INTO InstructionIngredent (IngredientID, InstructionID, UnitID, Quantity) VALUES ('" .
            $ingid . "'," . $step_num . "," . $ing->unit . ",'" . $ing->amount . "';";
        $conn->query($sql);
    }
}
?>