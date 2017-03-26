<?php

namespace SilexBlog;

// Classes that make up our model.
require_once __DIR__.'/../../models/SilexBlog/Post.php';
require_once __DIR__.'/../../models/SilexBlog/PostFactory.php';
require_once __DIR__.'/../../models/SilexBlog/PostRepository.php';
require_once __DIR__.'/../../models/SilexBlog/UserProvider.php';

// Use the twig trait so that we can call render on $app ($app->render).
class SilexBlogApplication extends \Silex\Application {
    use \Silex\Application\TwigTrait;
    use \Silex\Application\UrlGeneratorTrait;
    use \Silex\Application\FormTrait;
}