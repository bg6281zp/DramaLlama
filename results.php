<?php
// get the q parameter from URL
$q = $_REQUEST["q"];

// use a link to recipe from database instead of these arrays
//$searchQuery = "SELECT * from RECIPE WHERE name like '%" + $q + "%';";

//$a[] = mysql_query($searchQuery);
$a[] = {{ recipe.name }};
$hint = "";

// lookup all hints from array if $q is different from "" 
if ($q !== "") {
    $q = strtolower($q);
    $len=strlen($q);
    foreach($a as $name) {
        if (stristr($q, substr($name, 0, $len))) {
            if ($hint === "") {
                $hint = $name;
            } else {
                $hint .= ", $name";
            }
        }
    }
}


// Output "no suggestion" if no hint was found or output correct values 
echo $hint === "" ? "There are no recipes" : $hint;
?>