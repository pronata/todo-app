<?php

namespace pronata\Entity;

/**
 * @Entity
 * @Table(name="todo_list")
 */
class TodoList
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     */
    protected $id;

    /**
     * Many TodoLists have One User
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @Column(type="string")
     */
    protected $name;

    /**
     * @Column(name="created_at", type="datetimetz")
     */
    protected $created;

    /**
     * @Column(name="modified_at", type="datetimetz")
     */
    protected $modified;

    public function __construct()
    {
        date_default_timezone_set('UTC');
        if (is_null($this->getCreated())) {
            $this->setCreated(new \DateTime("now"));
        }
        $this->setModified(new \DateTime("now"));
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     */
    private function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @param mixed $modified
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
    }
}