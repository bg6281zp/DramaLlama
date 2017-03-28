/**
 * Created by joel on 3/7/17.
 */
"use strict";

function Recipe(oid, name, person, type, preptime, cooktime) {
    if(oid === undefined) {
        oid = -1;
    }
    if(name === undefined) {
        name = "Recipe";
    }
    if(person === undefined) {
        person = "Nobody";
    }
    if(preptime === undefined) {
        preptime = 0;
    }
    if(cooktime === undefined) {
        cooktime = 0;
    }
    this.steps = [];
    this.tags = [];

    this.oid = oid;
    this.name = name;
    this.person = person;
    this.type = type;
    this.preptime = preptime;
    this.cooktime = cooktime;

    this.addStep = function(step) {
        if(step === undefined) return;
        this.steps.push(step);
    }

    this.addStepAt = function(step, index) {
        if(step === undefined) return;
        if(index === undefined) {
            index = 0;
        }
        this.steps.splice(index, 1, step);
    }

    this.removeStep = function(index) {
        if(index === undefined) return;
        this.steps.splice(index, 1);
    }
};

function Step(stepID, description) {
    if(stepID === undefined) {
        stepID = -1;
    }
    if(description === undefined) {
        description = "";
    }


    this.stepID = stepID;
    this.description = description;
    this.ingredients = [];

    this.addIngredient = function(ingredient) {
        if(ingredient === undefined) return;
        this.ingredients.push(ingredient);
    }

    this.removeIngredient = function(index) {
        if(ingredient == undefined) return;
        this.ingredients.splice(index, 1);
    }

}

function Ingredient(oid, name, unit, amount) {
    if (oid === undefined) {
        oid = -1;
    }
    if (name === undefined) {
        name = "something";
    }
    if (unit === undefined) {
        unit = "unit";
    }
    if (amount === undefined) {
        amount = 1;
    }

    this.oid = oid;
    this.name = name;
    this.unit = unit;
    this.amount = amount;
}
