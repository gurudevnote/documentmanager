<?php
namespace P5\Model;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use P5\Model\Document;

/**
 * @ORM\Entity
 * @ORM\Table(name="`user`")
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
     * @ORM\Column(name="address", type="string", length=255, nullable = true)
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity="P5\Model\Document", mappedBy="user")
     */
    private $documents;

    /**
     * @ORM\OneToMany(targetEntity="P5\Model\Folder", mappedBy="user")
     */
    private $folders;

    /**
     * @ORM\ManyToMany(targetEntity="P5\Model\Document", mappedBy="sharingUsers")
     * @ORM\JoinTable(
     *      joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     */
    protected $sharingDocuments;

    public function __construct()
    {
        parent::__construct();
        $this->documents = new ArrayCollection();
        $this->sharingDocuments = new ArrayCollection();
        $this->folders = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address) {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getSharingDocuments() {
        return $this->sharingDocuments;
    }

    /**
     * @param mixed $sharingDocuments
     */
    public function hasSharingDocuments(Document $sharingDocuments) {
        $this->getSharingDocuments()->contains($sharingDocuments);
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
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @param mixed $documents
     */
    public function setDocuments($documents)
    {
        $this->documents = $documents;
    }

    /**
     * @return mixed
     */
    public function getFolders()
    {
        return $this->folders;
    }

    /**
     * @param mixed $folders
     */
    public function setFolders($folders)
    {
        $this->folders = $folders;
    }

}