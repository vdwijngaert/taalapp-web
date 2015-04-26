<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Device
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\DeviceRepository")
 */
class Device implements \JsonSerializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255)
     */
    private $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastActive", type="datetime", nullable=true)
     */
    private $lastActive;

    /**
     * @var \DateTime
     *
     * @Gedmo\Mapping\Annotation\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

    public function __construct() {
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Device
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return Device
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set lastActive
     *
     * @param \DateTime $lastActive
     * @return Device
     */
    public function setLastActive($lastActive)
    {
        $this->lastActive = $lastActive;

        return $this;
    }

    /**
     * Get lastActive
     *
     * @return \DateTime 
     */
    public function getLastActive()
    {
        return $this->lastActive;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Device
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser( $user )
    {
        $this->user = $user;
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        return array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'token' => $this->getToken(),
        );
    }
}
