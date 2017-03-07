<?php

/**
 * Created by PhpStorm.
 * User: joel
 * Date: 2/24/17
 * Time: 12:44 PM
 */
require_once "recipe.php";

class Recipe_xfer
{



}

$testrec = Recipe::create(1)
    ->setRecipeName("Jumbolier")
    ->setRecipePerson("Joel")
    ->setPrepTime(65);

$teststep = Step::create(1)
    ->setDescription("mix")
    ->setOrder(1);

$testrec->addStep($teststep);

$teststep2 = Step::create(2)
    ->setDescription("cook")
    ->setOrder(2);

$testrec->addStep($teststep2);


//echo $testrec->getJson();
echo json_encode($testrec);

