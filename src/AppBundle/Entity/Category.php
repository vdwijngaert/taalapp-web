<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CategoryRepository")
 */
class Category implements \JsonSerializable
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
     * @Assert\NotBlank(message="Naam kan niet leeg zijn.")
     */
    private $name;

    /**
     * @var Icon
     *
     * @ORM\ManyToOne(targetEntity="Icon")
     */
    private $icon;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent", cascade={"remove"})
     **/
    private $children;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Question", mappedBy="category", cascade={"remove"})
     **/
    private $questions;

    /**
     * @var Category
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     **/
    private $parent;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @Gedmo\Mapping\Annotation\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @Gedmo\Mapping\Annotation\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", options={"default"=1})
     */
    private $status;


    function __construct()
    {
        $this->status = 1;
        $this->children  = new ArrayCollection();
        $this->questions = new ArrayCollection();
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
     *
     * @return Category
     */
    public function setName( $name )
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
     * Set icon
     *
     * @param Icon $icon
     *
     * @return Category
     */
    public function setIcon( Icon $icon )
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return Icon
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed $children
     */
    public function setChildren( $children )
    {
        $this->children = $children;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent( $parent )
    {
        $this->parent = $parent;
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
    public function setUser( $user )
    {
        $this->user = $user;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated( $created )
    {
        $this->created = $created;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated( $updated )
    {
        $this->updated = $updated;
    }

    /**
     * @return ArrayCollection
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * @param ArrayCollection $questions
     */
    public function setQuestions( $questions )
    {
        $this->questions = $questions;
    }

    /**
     * @param Question $question
     */
    public function addQuestion( Question $question )
    {
        $this->questions->add( $question );
        $question->setCategory( $this );
    }

    /**
     * @param Question $question
     */
    public function removeQuestion( Question $question )
    {
        $this->questions->removeElement( $question );
        $question->setCategory( null );
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus( $status )
    {
        $this->status = $status;
    }

    public function isDeleted() {
        return $this->status == 0;
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
            'user' => $this->getUser()->getId(),
            'parent' => $this->getParent()->getId(),
            'name' => $this->getName(),
            'icon' => $this->getIcon()->getIcon(),
            'updated' => $this->getUpdated()
        );
    }
}