<?php

/**
 * Created by PhpStorm.
 * User: joel
 * Date: 2/24/17
 * Time: 12:44 PM
 */
require_once "recipe.php";
require_once "db_conn.php";

$recipe_id =  sanitize($_GET["recipeID"]);

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    // TODO: handle this gracefully - this method breaks the flow, badly
    die("Connection failed: " . $conn->connect_error);
}
else {
    $sql = "SELECT r.RecipeName, u.UserID, u.UserName, u.FirstName, u.LastName, r.PrepTime, r.CookTime
          FROM recipe r, User u 
          WHERE r.UserID = u.UserID and recipeID = " . $recipe_id;
    $result = $conn->query($sql);

    if($result->num_rows > 0) {
        // not iterating over possible multiple rows in result, since there should never be multiple rows
        $row = $result->fetch_assoc();

        if(strlen($row["FirstName"]) > 0) {
            $rec_person = $row["FirstName"] . " " . $row["LastName"];
        }
        else {
            $rec_person = $row["UserName"];
        }

        // set up the basic recipe
        $recret = Recipe::create($recipe_id)
            ->setRecipeName($row["RecipeName"])
            ->setRecipePerson($rec_person)
            ->setRecipeUID($row["UserID"])
            ->setPrepTime($row["PrepTime"])
            ->setCookTime($row["CookTime"]);
        // get steps (aka Instructions)
        $sql = "SELECT ri.OrderNumber, i.InstructionID, i.Description
                FROM recipeinstructions ri, instructions i
                WHERE
	              ri.RecipeID = " . $recipe_id .
                " and ri.InstructionID = i.InstructionID 
                ORDER BY ri.OrderNumber";

        $result = $conn->query($sql);
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $new_step = Step::create($row["InstructionID"])
                    ->setDescription($row["Description"])
                    ->setOrder($row["OrderNumber"]);
                $ing_sql = "SELECT ig.IngredientID, ig.IngredientName, ii.Quantity, un.UnitName
                            FROM instructioningredients ii, ingredients ig, unit un
                            WHERE ii.IngredientID = ig.IngredientID
	                          AND ii.UnitID = un.UnitID
	                          AND ii.InstructionID = " . $row["InstructionID"];
                $ing_result = $conn->query($ing_sql);
                if($ing_result->num_rows > 0) {
                    while($ing_row = $ing_result->fetch_assoc()) {
                        $ing = Ingredient::create($ing_row["IngredientID"])
                            ->setName($ing_row["IngredientName"])
                            ->setUnit($ing_row["UnitName"])
                            ->setQuantity($ing_row["Quantity"]);
                        $new_step->addIngredient($ing);
                    }
                }
                $recret->addStep($new_step);
            }
        }
        echo json_encode($recret);
    }
}

function sanitize($input)
{
    $output = trim($input);
    $output = stripslashes($output);
    return htmlspecialchars($output);
}

?>
