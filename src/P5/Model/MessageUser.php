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
 * @ORM\Entity(repositoryClass="P5\Repository\MessageRepository")
 * @ORM\Table(name="`message_user`")
 */
class MessageUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="P5\Model\Message", inversedBy="receivedUsers")
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity="P5\Model\User", inversedBy="receivedMessages")
     */
    private $toUser;

    /**
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getToUser()
    {
        return $this->toUser;
    }

    /**
     * @param mixed $toUser
     */
    public function setToUser($toUser)
    {
        $this->toUser = $toUser;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
}