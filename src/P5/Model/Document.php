<?php
/**
 * Created by PhpStorm.
 * User: thaiht
 * Date: 10/1/15
 * Time: 4:12 PM
 */

namespace P5\Model;

use Doctrine\ORM\Mapping as ORM;
use P5\Model\User;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="P5\Repository\DocumentRepository")
 * @ORM\Table(name="`document`")
 */
class Document
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="documents")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Folder", inversedBy="documents")
     */
    private $folder;

    /**
     * @ORM\Column(name="filename", type="string", length=255, nullable = false)
     */
    private $filename;

    /**
     * @ORM\Column(name="upload_date", type="datetime")
     */
    private $uploadDate;

    /**
     * @ORM\Column(name="last_modified", type="datetime")
     */
    private $lastModified;

    /**
     * @ORM\ManyToMany(targetEntity="P5\Model\User")
     * @ORM\JoinTable(
     *      joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     */
    protected $sharing_users;

    public function __construct()
    {
        $this->sharing_users = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param mixed $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return mixed
     */
    public function getUploadDate()
    {
        return $this->uploadDate;
    }

    /**
     * @param mixed $uploadDate
     */
    public function setUploadDate($uploadDate)
    {
        $this->uploadDate = $uploadDate;
    }

    /**
     * @return mixed
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * @param mixed $folder
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;
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
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * @param mixed $lastModified
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;
    }

    /**
     * @return mixed
     */
    public function getSharingUsers() {
        return $this->sharing_users;
    }

    /**
     * @param mixed $user
     */
    public function hasSharingUsers(User $user) {
        return $this->getSharingUsers()->contains($user);
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->id = $id;
    }
}