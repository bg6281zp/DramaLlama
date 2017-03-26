<?php

namespace SilexBlog;

class Post
{
    private $id, $author, $title, $created_date, $modified_date, $body;
    // $persisted is true when the data is saved and up-to-date in the database.
    private $persisted = false;

    // Basic constructor.  Pass null for $id when creating a new post that has not been persisted yet.
    public function __construct($id, $author, $title, $created_date, $modified_date, $body) {
        $this->id = $id;
        $this->author = $author;
        $this->title = $title;
        $this->created_date = $created_date;
        $this->modified_date = $modified_date;
        $this->body = $body;
    }
    
    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getCreatedDate()
    {
        return $this->created_date;
    }

    /**
     * @param mixed $created_date
     */
    public function setCreatedDate($created_date)
    {
        $this->created_date = $created_date;
    }

    /**
     * @return mixed
     */
    public function getModifiedDate()
    {
        return $this->modified_date;
    }

    /**
     * @param mixed $modified_date
     */
    public function setModifiedDate($modified_date)
    {
        $this->modified_date = $modified_date;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @return mixed
     */

    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getPersisted()
    {
        return $this->persisted;
    }

    /**
     * @param mixed $persisted
     */
    public function setPersisted($persisted)
    {
        $this->persisted = $persisted;
    }
}