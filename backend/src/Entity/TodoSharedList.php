<?php

namespace pronata\Entity;

/**
 * @Entity
 * @Table(name="todo_shared_list")
 */
class TodoSharedList
{
    /**
     * Many TodoSharedLists have One TodoList
     * @Id
     * @ManyToOne(targetEntity="TodoList")
     * @JoinColumn(name="todo_list_id", referencedColumnName="id")
     */
    protected $todoList;

    /**
     * @Id
     * Many TodoSharedLists have One SharedWithUsers
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $sharedWithUser;

    /**
     * @Column(name="can_write", type="boolean")
     */
    protected $canWrite;

    /**
     * @Column(name="created_at", type="datetimetz")
     */
    protected $created;
    /**
     * @Column(name="modified_at", type="datetimetz")
     */
    protected $modified;

    public function __construct($todoList, $sharedWithUser)
    {
        $this->todoList = $todoList;
        $this->sharedWithUser = $sharedWithUser;
        date_default_timezone_set('UTC');
        if (is_null($this->getCreated())) {
            $this->setCreated(new \DateTime("now"));
        }
        $this->setModified(new \DateTime("now"));
    }

    /**
     * @return mixed
     */
    public function getTodoList()
    {
        return $this->todoList;
    }

    /**
     * @param mixed $todoList
     */
    public function setTodoList($todoList)
    {
        $this->todoList = $todoList;
    }

    /**
     * @param mixed $sharedFromUser
     */
    public function setSharedFromUser($sharedFromUser)
    {
        $this->sharedFromUser = $sharedFromUser;
    }

    /**
     * @return mixed
     */
    public function getSharedWithUser()
    {
        return $this->sharedWithUser;
    }

    /**
     * @param mixed $sharedWithUser
     */
    public function setSharedWithUser($sharedWithUser)
    {
        $this->sharedWithUser = $sharedWithUser;
    }

    /**
     * @return mixed
     */
    public function getCanWrite()
    {
        return $this->canWrite;
    }

    /**
     * @param mixed $canWrite
     */
    public function setCanWrite($canWrite)
    {
        $this->canWrite = $canWrite;
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
    public function setCreated($created)
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