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
     * @ORM\OneToMany(targetEntity="Document", mappedBy="user")
     */
    private $documents;

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
}