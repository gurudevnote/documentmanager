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
 * @ORM\Table(name="`message`")
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="messages")
     */
    private $user;

    /**
     * @ORM\Column(name="type", type="string", length=255, nullable = false)
     */
    private $type;

    /**
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @ORM\Column(name="sent_time", type="datetime")
     */
    private $sentTime;

    /**
     * @ORM\OneToMany(targetEntity="P5\Model\MessageUser", mappedBy="message")
     */
    private $receivedUsers;

    public function __construct()
    {
        $this->receivedUsers = new ArrayCollection();
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getSentTime()
    {
        return $this->sentTime;
    }

    /**
     * @param mixed $sentTime
     */
    public function setSentTime($sentTime)
    {
        $this->sentTime = $sentTime;
    }

    /**
     * @return mixed
     */
    public function getReceivedUsers()
    {
        return $this->receivedUsers;
    }

    /**
     * @param mixed $receivedUsers
     */
    public function setReceivedUsers($receivedUsers)
    {
        $this->receivedUsers = $receivedUsers;
    }
}