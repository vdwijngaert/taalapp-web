<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * var ArrayCollection
     *
     * ORM\OneToMany(targetEntity="Device", mappedBy="user", cascade={"remove"})
     **
    private $devices;*/

    public function __construct()
    {
        parent::__construct();

        //$this->devices = new ArrayCollection();
    }

    /*public function addDevice(Device $device) {
        $this->devices->add($device);
        $device->setUser($this);
    }

    public function removeDevice(Device $device) {
        $this->devices->removeElement($device);
        $device->setUser(null);
    }*/
}