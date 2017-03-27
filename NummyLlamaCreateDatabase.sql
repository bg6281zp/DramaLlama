create schema NummyLlama;

use NummyLlama;

create table User (
UserID varchar(100) not null, 
UserName varchar(30) not null, 
FirstName varchar(30), 
LastName varchar(50),
Email varchar(128) not null,
Password varchar(255) not null,
DateCreated datetime,
DateUpdated datetime,
DateLastLogin datetime,
primary key (UserID));

create table Recipe (
RecipeID varchar(100) not null, 
RecipeName varchar(150), 
UserID varchar(100),
PrepTime integer,
CookTime integer,
DateCreated datetime,
DateUpdated datetime,
primary key (RecipeID),
foreign key (UserID) references User(UserID));

create table Ingredients (
IngredientID varchar(100) not null, 
IngredientName varchar(100), 
DateCreated datetime,
DateUpdated datetime,
primary key (IngredientID));

create table UserFavorites (
UserID varchar(100) not null,
RecipeID varchar(100) not null, 
primary key (UserID, RecipeID),
foreign key (UserID) references User(UserID),
foreign key (RecipeID) references Recipe(RecipeID));


create table Tag (
TagID varchar(100) not null,
TagName varchar(30),
primary key (TagID));

create table RecipeTag (
RecipeID varchar(100) not null,
TagID varchar(100) not null,
primary key (RecipeID, TagID),
foreign key (RecipeID) references Recipe(RecipeID),
foreign key (TagID) references Tag(TagID));


create table Unit (
UnitID varchar(100) not null,
UnitName varchar(100),
primary key (UnitID));

create table IngredientList (
RecipeID varchar(100) not null,
IngredientID varchar(100) not null,
UnitID varchar(100),
Quantity varchar(20),
primary key (RecipeID, IngredientID),
foreign key (RecipeID) references Recipe(RecipeID),
foreign key (IngredientID) references Ingredients(IngredientID),
foreign key (UnitID) references Unit(UnitID));

create table Instructions (
InstructionID varchar(100) not null,
Description varchar(2000),
primary key (InstructionID));

create table RecipeInstructions (
RecipeID varchar(100) not null,
OrderNumber integer,
InstructionID varchar(100) not null,
primary key (RecipeID, InstructionID),
foreign key (RecipeID) references Recipe (RecipeID),
foreign key (InstructionID) references Instructions (InstructionID)

create table InstructionIngredients (
IngredientID varchar(100) not null,
InstructionID varchar(100) not null,
UnitID varchar(100),
Quantity varchar(20),
primary key (IngredientID, InstructionID),
foreign key (IngredientID) references Ingredients(IngredientID),
foreign key (InstructionID) references RecipeInstructions(InstructionID),
foreign key (UnitID) references Unit(UnitID));

create table IngredientTag (
IngredientID varchar(100) not null,
TagID varchar(100) not null,
primary key (IngredientID, TagID),
foreign key (IngredientID) references Ingredients(IngredientID),
foreign key (TagID) references Tag(TagID));

