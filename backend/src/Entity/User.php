<?php

namespace pronata\Entity;

/**
 * @Entity
 * @Table(name="user")
 */
class User
{
    /**
     * @Id @GeneratedValue @Column(type="integer")
     */
    protected $id;

    /**
     * @Column(type="string", unique=true)
     */
    protected $username;

    /**
     * @Column(type="string")
     */
    protected $password;

    /**
     * @Column(name="sign_key", type="string")
     * For generating jwt token
     */
    protected $signKey;

    /**
     * @Column(type="boolean")
     */
    protected $confirmed;

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
        $this->setConfirmed(true);
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
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $options = [
            'cost' => 12
        ];
        $this->password = password_hash($password, PASSWORD_BCRYPT, $options);

    }

    public function isValidPassword($passwordToCheck)
    {
        return password_verify($passwordToCheck, $this->getPassword());
    }

    /**
     * @return mixed
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * @param mixed $confirmed
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;
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

    /**
     * @return mixed
     */
    public function getSignKey()
    {
        return $this->signKey;
    }

    /**
     * @param mixed $signKey
     */
    public function setSignKey($signKey)
    {
        $this->signKey = $signKey;
    }


}