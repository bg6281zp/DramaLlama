<?php
/**
 * Created by PhpStorm.
 * User: joel
 * Date: 2/24/17
 * Time: 1:01 PM
 */

class Recipe {
    public $recipeID;
    public $recipeName;
    public $recipeType; // I don't remember what this records - joel
    public $recipePerson; // username of the recipe creator
    public $prepTime;
    public $cookTime;
    public $steps; // array of step classes
    public $tags; // array of tag

    /**
     * recipe constructor.
     * @param $recipeID
     */
    public function __construct($recipeID)
    {
        $this->recipeID = $recipeID;
        $this->steps = array();
        $this->tags = array();
    }

    public static function create($recipeID) {
        $instance = new self($recipeID);
        return $instance;
    }
    // setters return a copy of the instance, which, when paired with create(),
    // makes for a factory:
    // $recipe = Recipe::create(1233)->setRecipeName("Baked Nebraska")->setPrepTime(600);

    /**
     * @return int recipeID
     */
    public function getRecipeID()
    {
        return $this->recipeID;
    }

    /**
     * @return string recipeName
     */
    public function getRecipeName()
    {
        return $this->recipeName;
    }

    /**
     * @param string $recipeName
     * @return $this
     */
    public function setRecipeName($recipeName)
    {
        $this->recipeName = $recipeName;
        return $this;
    }

    /**
     * @return string recipeType
     */
    public function getRecipeType()
    {
        return $this->recipeType;
    }

    /**
     * @param string $recipeType
     * @return $this
     */
    public function setRecipeType($recipeType)
    {
        $this->recipeType = $recipeType;
        return $this;
    }

    /**
     * @return string recipePerson
     */
    public function getRecipePerson()
    {
        return $this->recipePerson;
    }

    /**
     * @param string $recipePerson
     * @return $this
     */
    public function setRecipePerson($recipePerson)
    {
        $this->recipePerson = $recipePerson;
        return $this;
    }

    /**
     * @return int prepTime
     */
    public function getPrepTime()
    {
        return $this->prepTime;
    }

    /**
     * @param int $prepTime
     * @return $this
     */
    public function setPrepTime($prepTime)
    {
        $this->prepTime = $prepTime;
        return $this;
    }

    /**
     * @return int
     */
    public function getCookTime()
    {
        return $this->cookTime;
    }

    /**
     * @param int $cookTime
     * @return $this
     */
    public function setCookTime($cookTime)
    {
        $this->cookTime = $cookTime;
        return $this;
    }

    /**
     * @param Step $step
     */
    public function addStep($step) {
        $this->steps[] = $step;
    }

    /**
     * @return Step[]
     */
    public function getSteps() {
        return $this->steps;
    }

    public function getStep($index) {
        return $this->steps[$index];
    }

    public function getJson() {
        $ret = json_encode($this->getRecipeName()) .
            json_encode($this->getSteps());
        return $ret;
    }
}

class Step {
    public $stepID;
    public $order; // order that this step is in, relative to other steps
    public $description;
    public $ingredients; // array of ingredient

    public function __construct($stepID)
    {
        $this->stepID = $stepID;
        $this->ingredients = array();
    }

    public static function create($stepID) {
        $instance = new self($stepID);
        return $instance;
    }

    /**
     * @return mixed
     */
    public function getStepID()
    {
        return $this->stepID;
    }

    /**
     * @param int $stepID
     * @return $this
     */
    public function setStepID($stepID)
    {
        $this->stepID = $stepID;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIngredients()
    {
        return $this->ingredients;
    }

    /**
     * @param mixed $ingredients
     * @return $this
     */
    public function setIngredients($ingredients)
    {
        $this->ingredients = $ingredients;
        return $this;
    }

    /**
     * @param $ingredient
     */
    public function addIngredient($ingredient) {
        $this->ingredients[] = $ingredient;
    }


}

class Ingredient {
    public $ingredientID;
    public $unit; // the type of unit, e.g. 'cup', 'each', 'liter'
    public $quantity;
}
