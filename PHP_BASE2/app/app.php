<?php

// Load 3rd party libraries using composer.
// Notice the use of the magic constant __DIR__ which returns the directory the current file is in.
//   http://php.net/manual/en/language.constants.predefined.php
require_once __DIR__.'/../vendor/autoload.php';

// Various Symfony components we will use later in the application
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;

// Load our custom Application class that also loads other required classes.
require_once __DIR__.'/SilexBlog/SilexBlogApplication.php';

// Instantiate the Silex service container (the application)
$app = new SilexBlog\SilexBlogApplication();
// Enable debugging so that errors are displayed via web pages.
$app['debug'] = true;

// Register a twig service provider with the path to the twig templates given.
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
    'twig.options' => [ 'autoescape' => true ],
));

// Register a doctrine provider with the MySQL connection parameters given.
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_mysql',
        'host'     => '192.168.30.130',
        'dbname'   => 'silex_blog_a9',
        'port'     => 3306,
        'username' => 'root',
        'password' => 'P@$$w0rd',
        'charset'   => 'utf8mb4',
    ),
));

// Register a Post repository
$app['repository.post'] = function ($app) {
    return new SilexBlog\PostRepository($app);
};

// Register a form handler
$app->register(new Silex\Provider\FormServiceProvider());

// Required by the TranslationServiceProvider
$app->register(new Silex\Provider\LocaleServiceProvider());

// Required for using the default form handler with twig and validators
$app->register(new Silex\Provider\TranslationServiceProvider());

// Register a validator to be used with forms
$app->register(new Silex\Provider\ValidatorServiceProvider());

// Register a session provider
$app->register(new Silex\Provider\SessionServiceProvider());

// Register a custom error handler for SilexBlog\PostNotFoundException exceptions
$app->error(function(SilexBlog\PostNotFoundException $e, Request $request, $code) use ($app) {
    if ($code != 404) {
        return;
    }

    return $app->render('404.twig', array('message' => 'Post not found!'));
});

Register a security service provider which controls authentication and authorization for our application
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        // The login firewall makes it so that the path /user/login doesn't require authentication to access
        'login' => array(
            'pattern' => '^/user/login$',
        ),
        // The user firewall controls access to our application.
        //   It controls the authentication part of our application.
        'user' => array(
            // The following URLs require authentication /user* and /blog/new-post* (where * is anything number
            //   of characters).
            // Note that the pattern is a regular expression.
            //   ^ means the beginning of the string
            //   () is a group of characters
            //   | is an or
            // So, the regex specifies match either /user or /blog/new-post at the beginning of the given string.
            //   The string in this case is a path in our application.  The rest of the string is ignored.
            'pattern' => '(^/user)|(^/blog/new-post)|(^/blog/delete)|(^/blog/new-comment)',
            // Setting 'http' to true will use HTTP based authentication instead of a login form
            //'http' => true,
            // Use a HTML form to login
            //   /user/login displays the form.
            //   /user/login_check checks the submitted user information to see if it is valid.
            'form' => array('login_path' => '/user/login', 'check_path' => '/user/login_check'),
            // The path to logout.
            //   /usr/logout is the path a user must access to be logged out.  The user's session will be invalidated
            //   and then the user will be redirected to /user/login
            'logout' => array('logout_path' => '/user/logout', 'target_url' => '/user/test', 'invalidate_session' => true),
            // Define how users are looked up.
            //   This closure returns a new instance of SilexBlog\UserProvider.  Note that this instance will be used
            //   by Silex for all user operations for this firewall.
            'users' => function () use ($app) {
                return new SilexBlog\UserProvider($app['db']);
            },
        ),
    ),
    // For help debugging, tell the user if the username doesn't exist.
    //   Normally this would be set to true in production.  You don't want attackers to be able to guess usernames.
    'security.hide_user_not_found' => false,
    // Access rules define what ROLE a user must have to access a URL
    //   They provide the authorization part of the application.
    'security.access_rules' => array(
        // In order to access any path that starts with /blog/new-post, the user must have the ROLE_USER role.
        array('^/blog/new-post', 'ROLE_USER'),
        array('^/blog/delete', 'ROLE_ADMIN'),
        array('^/blog/new-comment', 'ROLE_COMMENTER'),
        array('^/user/new', 'ROLE_ADMIN'),
    ),
));

// The login form used by the user firewall.
$app->get('/user/login', function(Request $request) use ($app) {
    // This route is called whenever a user wants to login or a login fails.
    // The twig template will tell the user of any errors.
    return $app['twig']->render('login.twig', array(
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
})
->bind('userLogin');

// Show a list of the roles a current user has.
$app->get('/user/show-roles', function () use ($app) {
    // Get the token for currently logged in user.
    $token = $app['security.token_storage']->getToken();
    // If the token is null, no user is logged in.
    if (!is_null($token)) {
        // Use the token to get the user object for the currently logged in user.
        $user = $token->getUser();
    } else {
        // We shouldn't get here, because the user should have to be logged in to get to this route!
        return "There is no current user.  This shouldn't happen!";
    }

    // Show the user his/her username and his/her list of roles.
    return 'You are logged in as the user ' . $user->getUsername() . " with the following roles: " . implode(', ', $user->getRoles());
})
->bind('userShowRoles');

// A test page to see if a user is logged in ok.
$app->get('/user/test', function () use ($app) {
    return "Test Page - You're in!";
})
->bind('userTest');

// The root route.  Display links to other routes and some other helpful info.
$app->get('/', function () use ($app) {
    return $app->render('links_and_info.twig');
});

// A controller to process the route /blog/
$app->get('/blog/', function () use ($app) {
    // Use the function SilexBlog\PostRepository->findAll() to get all the posts in the database.
    $posts = $app['repository.post']->findAll();
    
    // Set the page title variable.
    $page_title = 'List of All the Blog Posts';

    // Pass the page title and posts to twig to be rendered using the list_posts.twig template.
    return $app->render('list_posts.twig', array('page_title' => $page_title, 'posts' => $posts));
})
->bind('findAllPosts');

// A controller to process the route /blog/id/{id} where id is a post id.
// Example: /blog/4
//   bind('findPost') names this route findPost.
//   assert('id', '\d+') tells Silex to validate the id route variable contains at least 1 positive integer.
$app->get('/blog/id/{id}', function ($id) use ($app) {
    // Use the function SilexBlog\PostRepository->find($id) to get the specified post by its id.
    $post = $app['repository.post']->find($id);

    // Set the page title to the blog post title and author
    $page_title = $post->getTitle() . ' by ' . $post->getAuthor();

    // Pass the page title and post to twig to be rendered using the list_posts.twig template.
    return $app->render('list_posts.twig', array('page_title' => $page_title, 'posts' => array($post)));
})
->bind('findPost')
->assert('id', '\d+');

// A controller to process the route for /blog/author/{author} where author is the post author.
// Will be part of assignment 9.
$app->get('/blog/author/{author_name}', function ($author_name) use ($app) {
    $author_name = $app->escape($author_name);
    $posts = $app['repository.post']->findByAuthor($author_name);
    $page_title = "List of $author_name Blog Posts";
    return $app->render('list_posts.twig', array('page_title' => $page_title, 'posts' => $posts));
})
    ->bind('findPostByAuthor')
    ->assert('author_name', '[\sa-zA-Z0-9]+');

$app->get('/blog/delete/{id}', function ($id) use ($app) {
    $post = $app['repository.post']->find($id);
    $app['repository.post']->delete($post);
    return "Post ID: $id deleted!";
})
    ->bind('deletePost')
    ->assert('id', '\d+');

$app->match('/blog/new-comment/{id}', function ($id, Request $request) use ($app) {
    $data = array('comment' => 'Your comment');
    $form = $app->form($data)
        ->add('comment', TextareaType::class, array('constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 10)))))
        ->getForm();
    $form->handleRequest($request);
    if ($form->isValid()) {
        $data = $form->getData();
        $token = $app['security.token_storage']->getToken();
        if (!is_null($token)) {
            // Use the token to get the user object for the currently logged in user.
            $user = $token->getUser();
        } else {
            // We shouldn't get here, because the user should have to be logged in to get to this route!
            return "There is no current user.  This shouldn't happen!";
        }
        $username = $user->getUsername();
        $db_result = $app['db']->fetchAssoc('SELECT `id` FROM `users` where username = ?', array($username));
        $app['db']->insert('comments', array(
            'user_id' => $db_result['id'],
            'comments' => $app->escape($data['comment']),
            'created_date' => date('Y-m-d H:i:s'),
        ));
        return $app->redirect($app->path('findPost', array('id' => $id)));
    }
    return $app->render('new-post.twig', array('form' => $form->createView()));
})
    ->bind('newComment')
    ->assert('id', '\d+');

$app->match(/**
 * @param Request $request
 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
 */
    '/user/new', function (Request $request) use ($app) {
    $data = array(
        'username' => '',
        'password' => '',
    );
    $form = $app->form($data)
        ->add('username',  TextType::class, array('constraints' => array(
            new Assert\NotBlank(),
            new Assert\Length(array('min' => 5)),
            new Assert\Regex(array('pattern' => '/^[a-zA-Z0-9]+$/', 'message' => 'Your name cannot contain non alphanumeric characters')))))
        ->add('password', TextType::class, array('constraints' => array(new Assert\NotBlank())))
        ->getForm();
    $form->handleRequest($request);
    if ($form->isValid()) {
        $data = $form->getData();
        $username = $app->escape($data['username']);
        $password = $app->escape($data['password']);
        $hashed_pass = $app['security.default_encoder']->encodePassword($password, null);
        $app['db']->insert('users', array(
            'username' => $username,
            'password' => $hashed_pass,
            'enabled' => 1,
        ));
        return $app->redirect($app->path('userLogin'));
    }
    return $app->render('new-post.twig', array('form' => $form->createView()));
})
    ->bind('newUser');


$app->match('/blog/new-post', function (Request $request) use ($app) {
    // Default data for the form.
    $data = array(
        'author' => 'Your name',
        'title' => 'Title of the Post',
        'body' => 'Blog post content',
    );

    // Create a new form builder object and give it the default data.
    // Add 2 fields of type text: one for author and one for title.  Neither can be blank.
    // Add 1 field for body of type text area.  It must be at least 20 characters.
    // Get the finished form from the form builder and store it to the $form variable.
    $form = $app->form($data)
        ->add('author',  TextType::class, array('constraints' => array(new Assert\NotBlank())))
        ->add('title', TextType::class, array('constraints' => array(new Assert\NotBlank())))
        ->add('body', TextareaType::class, array('constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 20)))))
        ->getForm();

    // Use the form to handle the request.
    $form->handleRequest($request);

    // Only create a new post if the form input passes all the given validation rules.
    if ($form->isValid()) {
        // Get the form input.
        $data = $form->getData();

        // Use the PostFactory to create a new Post instance.
        $post = SilexBlog\PostFactory::create($app->escape($data['author']),
                                              $app->escape($data['title']),
                                              $app->escape($data['body']));
        // Use the PostRepository to persist the Post to the database.
        $app['repository.post']->save($post);

        // Redirect the user to the list of all blog posts.
        return $app->redirect($app->path('findAllPosts'));
    }

    // The form data is either invalid or the form is being display for the first time.
    // So, render the form template.
    return $app->render('new-post.twig', array('form' => $form->createView()));
})
->bind('newPost');

// Return the service container used by web/index.php
return $app;
