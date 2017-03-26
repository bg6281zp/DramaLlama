<?php

namespace SilexBlog;

use \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

require_once __DIR__.'/Post.php';

// For posts that don't exist, use a custom exception based on the 404 exception Silex/Syfony uses.
class PostNotFoundException extends \Symfony\Component\HttpKernel\Exception\NotFoundHttpException {}

class PostRepository
{
    private $app;

    public function __construct(\Silex\Application $app) {
        $this->app = $app;
    }

    // Look up a post by its id in the database and return a new Post object.
    public function find($id) {
        $db_result = $this->app['db']->fetchAssoc('SELECT * FROM `posts` where id = ?', array((int) $id));
        // If no post is found for the given id, the result will be false.
        if ($db_result === false) {
            // Throw a custom 404 not found exception.
            throw new PostNotFoundException('A post with id=' . ((int) $id) . ' was not found!');
        }
        // Create a post instance given the data fetched from the database.
        $post_object = new Post($db_result['id'], $db_result['author'], $db_result['title'],
                                $db_result['created_date'], $db_result['modified_date'], $db_result['body']);
        // The data came from the database, so it is already persistent.
        $post_object->setPersisted(true);

        return $post_object;
    }

    // Grab all posts from the database and return an array of Post objects.
    public function findAll() {
        $db_results = $this->app['db']->fetchAll('SELECT * FROM `posts`');
        if ($db_results === false) {
            throw new PostNotFoundException('No posts were found in the database!');
        }

        // $db_results will be an array containing the rows returned by the query.
        // Those rows need to be converted to Post instances and appended to the $posts array.
        $posts = array();
        foreach($db_results as $db_result) {
            $post_object = new Post($db_result['id'], $db_result['author'], $db_result['title'],
                                    $db_result['created_date'], $db_result['modified_date'], $db_result['body']);
            $post_object->setPersisted(true);
            $posts[] = $post_object;
        }

        return $posts;
    }

    // Persist a Post instance.
    public function save(Post $post_object) {
        // If the Post id is null, then the instance is new.
        if(is_null($post_object->getId())) {
            // Use an INSERT statement to insert the Post instance into the posts table.
            $this->app['db']->insert('posts', array(
                'author' => $post_object->getAuthor(),
                'title' => $post_object->getTitle(),
                'body' => $post_object->getBody(),
                'created_date' => $post_object->getCreatedDate(),
                'modified_date' => $post_object->getModifiedDate(),
            ));
            // Grab the last inserted id and assign it the the Post instance that was just inserted.
            $post_object->setId($this->app['db']->lastInsertId());
            // The post object has been persisted.
            $post_object->setPersisted(true);
        } else {
            // update post
        }
    }

    public function findByAuthor($author_name) {
        $db_results = $this->app['db']->fetchAll('SELECT * FROM `posts` where author = ?', array((string) $author_name));
        // If no post is found for the given id, the result will be false.
        if (empty($db_results)) {
            // Throw a custom 404 not found exception.
            throw new PostNotFoundException('A post with author=' . ((string) $author_name) . ' was not found!');
        }
        $posts = array();
        foreach($db_results as $db_result) {
            $post_object = new Post($db_result['id'], $db_result['author'], $db_result['title'],
                $db_result['created_date'], $db_result['modified_date'], $db_result['body']);
            $post_object->setPersisted(true);
            $posts[] = $post_object;
        }

        return $posts;
    }

    public function delete(Post $post_object) {
            $this->app['db']->delete('posts', array('id' => $post_object->getId()));
            // Grab the last inserted id and assign it the the Post instance that was just inserted.
    }
}