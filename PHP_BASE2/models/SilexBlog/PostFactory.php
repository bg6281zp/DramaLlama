<?php

namespace SilexBlog;

require_once __DIR__.'/Post.php';

class PostFactory
{
    public static function create($author, $title, $body) {
        // This function returns a post object for a post just written, so use the current time.
        $current_time = date('Y-m-d H:i:s');

        // The post hasn't been persisted yet, so the id is null as that is assigned by the database.
        return new Post(null, $author, $title, $current_time, $current_time, $body);
    }
}