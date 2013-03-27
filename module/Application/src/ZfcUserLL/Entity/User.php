<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */
 
namespace ZfcUserLL\Entity;

use BjyAuthorize\Provider\Role\ProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ZfcUser\Entity\UserInterface;
use DateTime;

class User implements UserInterface, ProviderInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $displayName;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var int
     */
    protected $state;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $roles;
    
    /**
     * @var string
     */
    protected $ip = '';

    /**
     * @var DateTime
     */
    protected $lastActivityDate;

    /**
     * Initialies the roles variable.
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();        
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = (int) $id;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return void
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get displayName.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set displayName.
     *
     * @param string $displayName
     *
     * @return void
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return void
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get state.
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set state.
     *
     * @param int $state
     *
     * @return void
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * Get role.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoles()
    {
        return $this->roles->getValues();
    }

    /**
     * Add a role to the user.
     *
     * @param Role $role
     *
     * @return void
     */
    public function addRole($role)
    {
        $this->roles[] = $role;
    }
    
    /**
     * Set the IP address of the user (needed for ban functionnality)
     *
     * @param  string $ip
     * @return User
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * Get the IP address of the user (needed for ban functionnality)
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set the last activity date (this is updated at each request)
     *
     * @param  DateTime $lastActivityDate
     * @return User
     */
    public function setLastActivityDate(DateTime $lastActivityDate)
    {
        $this->lastActivityDate = clone $lastActivityDate;
        return $this;
    }

    /**
     * Get the last activity date
     *
     * @return DateTime
     */
    public function getLastActivityDate()
    {
        if ($this->lastActivityDate){
            return clone $this->lastActivityDate;
        }
    }
    
    public function __toString()
    {
        return $this->getUsername();
    }
}
