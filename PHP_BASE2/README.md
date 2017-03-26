ICS 325 Fall 2016 - Assignment 9
=========================

Purpose
-------
* Use Silex, Twig, and MySQL to add features to a partially built blog application.

Resources and Examples
----------------------
* Check the D2L class notes section for how to install composer in your git repo and use it to install the necessary 3rd party packages.
* The official Silex [documentation](http://silex.sensiolabs.org/doc/master/)
* The official Twig [documentation](http://twig.sensiolabs.org/documentation).

Collaboration
-------------
You can talk about the assignment with your peers in the class.  However, you should perform the work yourself and turn in a copy of your work.

Prerequisites
-------------
Use git to clone your assignment 9 repo to your computer.  Then in PhpStorm, use `File->Open Directory` and select your local repo.  You will also need to install composer and use it to install the necessary 3rd party packages in your local repo.  In addition, you will need to make sure MySQL is running and that you've imported the `silex_blog_a9` database.

Instructions
------------
### Instructions to set up the code to run
First you need to clone your git repository to your computer.  Open GitKraken and make sure you are logged into your github.com account.  Next go to `File->Clone Repo`.  Select the `GitHub.com` icon.  A list of your repositories in github.com should pop up.  Select your assignment 9 repo.  If you want, change `Where to clone to` by clicking browser and selecting a folder for your git repo to be cloned into.  Finally, hit the `Clone the repo!` button.  The repo should now clone to your computer.

Next you need to set up PhpStorm.  We will be using the built-in PHP CGI server for this assignment.  To do so, first make sure you have the git repo open in PhpStorm by using the Open Directory menu item under File in PhpStorm (`File->Open Directory`).

Next install composer and use it to install all the necessary 3rd party packages.  Refer to the D2L class notes section for instructions on how to do this.

Next go to `Run->Edit Configurations...` Click the green `+` to create a new configuration.  Select `PHP Built-in Web Server`.  Change the name to `Assignment 9`.  Leave host as `localhost`.  Set the port to `8080`.  Set the `Document root` to the `web` directory in your git repo directory by clicking the `...` button next to the field and using the file chooser to select it.  Check the checkbox next to `Use a router script`.  Then select the file `index.php` in the `web` directory by clicking on the `...` button next to the router script field.  If there is a red ! icon near the bottom right of the window, click the `Fix` button and specify your PHP interpreter.  Once done, click `Ok` to exit the Edit Configurations window.  Next hit the green run button to start the PHP CGI web server.  Then go to your web browser, and enter this url [http://localhost:8080/](http://localhost:8080/).  That page lists existing routes and other info.

Next you need to set up MySQL.  Make sure that MySQL is running.  If you have XAMPP, open the XAMPP control panel and click the "start" button next to MySQL.  If you are in the database course, MySQL may already be running in the background on your machine.

Open MySQL workbench.  If there is a button in the top left that says "Local instance 3306" click it and you will be logged into the MySQL server.

If not, click the + button next to "MySQL Connections".  Name the connection "Local instance 3306".  Hostname should be "127.0.0.1".  Port should be "3306".  Username should be "root".  The password is blank if you use XAMPP.  If you installed MySQL for the database course, you may need to enter a password.  Hit okay and then open the connection by clicking on it.

Once you are in MySQL Workbench, go to File->Run SQL Script...  Select the `resources/silex_blog_a9.sql` script.  Hit the "Run" button.  If it asks for a password, you do not need one if you use XAMPP so just hit okay. Once it is done, look at the bottom left side of the screen where there is a panel that says "SCHEMAS".  Click the 2 headed arrow button next to it.  That will show you the schemas (databases) you have running.  Hit the refresh button (2 arrows in a circle) to see the `silex_blog_a9` database that was created by `resources/silex_blog_a9.sql`.  Click on the + next to `silex_blog_a9` to see the `users`, `user_roles`, `roles`, and `posts` tables.  To run queries against a database, double click it.  The database name will turn bold.  Any queries you execute in your query window will run against the bolded database by default.

### Assignment Work
The assignment is quite large and complex compared to previous assignments.  It will have the weight of 3 assignments when used to calculate your homework grade.  Unlike previous assignmnets, there are 2 types of points for this assignment: required and optional  The assignment is broken down into feature sets.  Each feature set is described and a grading table is provided.  See the grading section for more information and a grading table which includes the totals of each feature set.  Make sure you review the grading section before you start working on the assignment!

#### Display Posts by Author
You need to create a new route for viewing all the posts by a specific author.  The route you should create is `/blog/author/{author_name}`.  Ensure that the route variable for the author name is properly escaped.  You should use the existing Twig template `list_posts.twig` to render the HTML output.  You may modify the Twig template however you want, but it should be shared among this new route and the routes that already use it.  You need to add a new method to the `PostRepostory` named `findByAuthor`.  It should take the the author's name as a string and then use the author's name to query the database for matching posts.  It should then return an array of `Post` instances found that match the query.  This code will work simliarly to the `findAll` method, except that the SQL query will filter the results by author.  If no posts are found, then a 404 exception should be thrown just like in the `find` method.  You should name the route, and then modify the `list_posts.twig` file to use either a generated URL or path instead of the existing hard coded ones.

Optional: Simliar to the `/blog/id/{id}` route, add an assert condition to only allow author names with letters, numbers, and spaces.

R|O|Requirement
---|---|-----------
1 | 0 | create a new route
1 | 0 | properly escape the route variable for author
1 | 0 | use the existing Twig template (may modify it as necessary)
1 | 0 | new method `findByAuthor` added to the `PostRepository` class
1 | 0 | 404 exception is thrown if no posts are found
1 | 0 | name the route
1 | 0 | swap out the hard coded links with ones that use the URL or path generator
0 | 1 | use assert to only allow author names with letters, numbers, and spaces
**7**| **1**| **Total**

#### Edit a Post
Currently posts can only be created.  You need to add the ability to edit a post via a new route.  For example, `/posts/edit/{id}`.  Each field in the post can be changed by the editor, except for the created time and modified time.  The created time should always stay the same.  The modified time should be updated to when the post was last modified.  The updated data should be stored to the database.  The `save` method of the `PostRepository` class should be used to save modified `Post` instances.  Only users with the role `ROLE_USER` should be able to edit posts.  You should use a Twig template to render the HTML form used to edit the post.  The existing data for the post should be pre-filled into the form for the user.  You can use the existing `new-post.twig` template for this, and modify it to fit your needs.  You should use the Silex form builder and validators to build and validate the form.  You can use the existing ones found in the new-post controller.  You can either copy them to your new update post route or find a way to share the code between the 2 controllers.

Optional: Modify all the set methods in the `Post` class to set `persisted` to false whenever an attribute of the `Post` instance is changed.  Then use the `persisted` attributed to skip updating a post in the database within the save method of the `PostRepository`.  So, in order words, if a `Post` instance is unmodified since it was loaded from the database, and it is given to the `PostRepository` to save to the database, don't issue a SQL `UPDATE` call to the database.

R|O|Requirement
---|---|-----------
3 | 0 | each field can be updated by the editor
1 | 0 | modified time is updated
4 | 0 | data is updated in the database, must use the `save` method in the `PostRepository`
1 | 0 | only users with the `ROLE_USER` role can update posts
1 | 0 | use a Twig template to render the form with the existing post data
2 | 0 | Silex form builder and validators are used
0 | 2 | modify all set methods of post to set persisted to false and then only save posts if `persisted` == `false`
**12**| **2**| **Total**

#### Delete Posts
You need to add a route that can be used to delete a post, for example `/blog/delete/{id}`.  Name the route.  Note that you are only required to create the route and ensure it deletes posts.  You do not need to modify the views to add links to delete posts unless you want to complete the optional points.  Only users with the role `ROLE_ADMIN` should be able to delete posts.  The row should be deleted from the database using a SQL query.  A new method `delete` should be added to the `PostRepository` class in order to perform the deletion.  It should accept a `Post` instance.  The controller that calls the `PostRepostory->delete` method should lookup the post by its id first using the `find` method and passing the returned `Post` instance to the `delete` method.

Optional:  Add a delete post link to the post views using the URL or path generator.  Only show the delete post link to users with the role `ROLE_ADMIN`.

R|O|Requirement
---|---|-----------
1 | 0 | create a new route
1 | 0 | name the route
1 | 0 | only admins can delete posts
1 | 0 | data is deleted from the database, create a new method `delete` in the `PostRepository` to do so that accepts as a `Post` instance as input
0 | 1 | add a delete post link to the show all posts twig template
0 | 1 | only show the delete post link to logged in admins
**4**| **2**| **Total**

#### Comments on Posts
You need to add the ability for users to comment on posts.  The display of the comments is part of the optional points.

For any steps that require a SQL statement, place the statements in a new file `resources/comments_migration.sql`.  The file should start with `use silex_blog_a9;` and then the following lines should modify the database as necessary to support your new comments feature.  `resources/comments_migration.sql` should be added to your git repo.

Add a new role named `ROLE_COMMENTER`.  Only users with that role should be able to comment.  Add a new route such as `/blog/new-comment/{id}` where id is the id of the post the comment is for.  That route should show the HTML form to collect the comments.  You only need one textarea field for the comments.  The form should submit its data either to the same route or a new route that then processes the form input and saves it to the database.  You do not need to create a `CommentsRepository` for saving the data to the database.  You can use a simple SQL statement.  The comments submitted by the user should be properly escaped.

You need to create a new database table to store the comments.  The table should be named `comments`.  It should have 4 columns: `id`, `userid`, `comments`, `created_date`.  `id` should be the primary index for the table.  It should be set to auto-increment so that new rows inserted without an id will be assigned one by the database.  The `userid` should be a foreign key index from the `users` table.  The `comments` column can be some type of text column.  It should accept at least 128 characters.  The `created_date` column can be a string, a date type, or a unix seconds from epoch.

Optional:  Display the comments for a post when a single post is displayed via the `/blog/id/{id}` route.  Allow admins to approve comments before they are shown to regular users.  Allow users to edit their own comments.  Show that a comment has been editing in some way in the view.  Allow admins to delete comments.

R|O|Requirement
---|---|-----------
1 | 0 | add a new role to the database named ROLE_COMMENTOR
1 | 0 | only allow users with the role ROLE_COMMENTER to post comments
1 | 0 | add a new route or routes to display the comments form and process the input
1 | 0 | comments should be properly escaped
1 | 0 | create a new database table named comments
1 | 0 | the table should have an id field that is numeric, the primary index, and auto-incrementing
1 | 0 |  each comment should have a numeric id that is assigned by the database when it is inserted
1 | 0 | comments should have a userid column with a foreign key index to the userid of the user that posted them
1 | 0 | there should be a column for the created date
0 | 2 | display the comments when a single post is displayed via the `/blog/id/{id}` route.
0 | 2 | allow admins to approve comments before they are shown
0 | 2 | allow users to edit their comments, mark them as edited in the display
0 | 2 | allow admin users to delete comments
**9**| **8**| **Total**

#### Add New Users
Add a new route such as `/user/new` which displays a form to create a new user.  The same route or a new one should be used to process the form input.  Only admin users should be able to create users.  Usernames must be at least 5 characters long.  Passwords cannot be blank.  The user should be saved to the database.  You do not need to use any type of repository or service to create the user.  You can use a simple SQL insert.  You also do not need to assign roles to the user.

Optional: add validation to ensure that usernames contain only numbers and letters.

Example code to create a password hash from a plaintext password:<br/>
```
$plaintext_pass = 'test1234';
$hashed_pass = $app['security.default_encoder']->encodePassword($plaintext_pass, null)
```

R|O|Requirement
---|---|-----------
1 | 0 | add a new route or routes to display the new user form and process the input
1 | 0 | a form should be displayed to allow information to be entered
1 | 0 | only admin users can create new users
1 | 0 | usernames must be at least 5 characters
1 | 0 | usernames and passwords should not be blank
1 | 0 | the new user should be saved to the database
0 | 1 | usernames may contain only numbers and letters
**6**| **1**| **Total**

#### Edit Users
This entire feature set is optional.  Create a new route such as `/user/edit/{id}`.  Users should be able to only change their own password.  Admins should be able to change the username and password of any user.  Usernames and passwords should not be blank.  Display a different form to users versus admins.  You can use the same Twig template if you like, but it should be rendered differently based on the user's role.  Enforce complexity requirements on the password: at least 8 characters long, at least 1 letter, at least 1 number, and at least 1 symbol.  For admins, allow roles to be added or removed from a user in addition to changing the username/password.

Example code to create a password hash from a plaintext password:<br/>
```
$plaintext_pass = 'test1234';
$hashed_pass = $app['security.default_encoder']->encodePassword($plaintext_pass, null)
```

R|O|Requirement
---|---|-----------
0 | 1 | only admin users can edit users' username
0 | 1 | user's can update their passsword
0 | 1 | usernames and passwords should not be blank
0 | 1 | display a different form to regular users and admin users
0 | 1 | ensure the passwords meet complexity requirements
0 | 3 | admins can add/remove roles for a user
**0**| **8**| **Total**

#### Delete Users
You need to create a route for deleting users such as `/user/delete/{id}`.  The users should be deleted from the database.  Only admin users should be able to delete users.

Optional: Instead of deleting the user, add a new column to the `users` table to indicate if a user is active or not, and set the column to false when a user is deleted.  This allows the user to be restored at a later date if need be.  Don't allow a user to delete himself/herself.  Create a new route which lists all the existing or active users in the database and generates a delete link that can be used to delete each user.  Only show the list of users to admin users.

R|O|Requirement
---|---|-----------
1 | 0 | add a new route to delete a user that accepts a user id
1 | 0 | user is deleted from the database
1 | 0 | only admin users can delete users
0 | 1 | archive the user instead of delete it so it can be restored later
0 | 1 | don't allow a user to delete himself/herself
0 | 2 | create a route which lists usernames with a delete link for each, only allow admins to see the list
**3**| **4**| **Total**

Grading
-------
In order to get 100% on this assignment, you must complete all the work that is worth required points and 10 points worth of optional work.  You can choose whatever features you'd like to implement in order to get up to 10 optional points.  You can do more than 10 optional points if you wish, but you will not receive any type of credit for them.

In the table below, the R column is for required points and the O column is for optional points.  The points below are grouped by similiar feature set.  See the assignment description for a breakdown of each feature set.

R|O|Requirement
---|---|-----------
7 |	1	| Display Posts by Author
12 | 2	| Edit a Post
4 |	2	| Delete Posts
9	| 8 |	Comment on Posts
6	| 1	| Add New Users
0	| 8	| Edit Users
3	| 4	| Delete Users
**41**| **26**| **Total**
