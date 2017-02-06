<?php

namespace pronata\Entity;

/**
 * @Entity
 * @Table(name="todo_list_item", uniqueConstraints={@UniqueConstraint(name="unique_id", columns={"id", "todo_list_id"})})
 */
class TodoListItem
{
    /**
     * @Id @GeneratedValue @Column(type="integer", nullable=false)
     */
    protected $id;

    /**
     * Many TodoListItems have One TodoList
     * @ManyToOne(targetEntity="pronata\Entity\TodoList")
     * @JoinColumn(name="todo_list_id", referencedColumnName="id")
     */
    protected $todoList;

    /**
     * @Column(type="string")
     */
    protected $title;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $link;

    /**
     * @Column(type="boolean")
     */
    protected $completed;

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
        $this->completed = false;
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
    public function getTodoList()
    {
        return $this->todoList;
    }

    /**
     * @param mixed $todoList
     */
    public function setTodoList(TodoList $todoList)
    {
        $this->todoList = $todoList;
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
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param mixed $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @return mixed
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * @param mixed $completed
     */
    public function setCompleted($completed)
    {
        $this->completed = (bool)$completed;
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