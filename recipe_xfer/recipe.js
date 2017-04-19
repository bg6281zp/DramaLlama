/**
 * Created by joel on 3/7/17.
 */
"use strict";

function Recipe(oid, name, person, uid, type, preptime, cooktime) {
    var oid, name, person, uid, type, preptime, cooktime;
    if(oid === undefined) {
        oid = -1;
    }
    if(name === undefined) {
        name = "Recipe";
    }

    if(person === undefined) {
        person = "Nobody";
    }
    if(uid === undefined) {
        uid = 0;
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
    this.uid = uid;
    this.person = person;
    this.type = type;
    this.preptime = preptime;
    this.cooktime = cooktime;

    this.setName = function(name) {
        if(name === undefined) {
            name = "Recipe";
        }
        this.name = name;
    }

    this.setPreptime = function(time) {
        if(time === undefined) time = 0;
        this.preptime = time;
    }

    this.setCooktime = function(time) {
        if(time === undefined) time = 0;
        this.cooktime = time;
    }

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

    this.clearSteps = function() {
        this.steps = [];
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
        unit = 10; // the default 'unit' name
    }
    if (amount === undefined) {
        amount = 1;
    }

    this.oid = oid;
    this.name = name;
    this.unit = unit;
    this.amount = amount;
}
