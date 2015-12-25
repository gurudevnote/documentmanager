<?php

/**
 * Created by PhpStorm.
 * User: thaiht
 * Date: 10/8/15
 * Time: 3:28 PM.
 */
namespace P5NotificationBundle\DependencyInjection;

use Doctrine\ORM\EntityManager;
use P5\Model\Message;
use P5\Model\MessageUser;
use P5\Model\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Doctrine\Common\Collections\ArrayCollection;

class MessageCenter
{
    private $em;
    private $token;
    public function __construct(EntityManager $em, TokenStorage $token)
    {
        $this->em = $em;
        $this->token = $token;
    }

    public function pushMessage($from, $content, $type, $parameters = array(), $to = array())
    {
        $message = new Message();
        $message->setUser($from);
        $message->setContent($content);
        $message->setType($type);

        if (count($to) === 0 || !is_array($to)) {
            $userRepository = $this->em->getRepository('P5:User');
            $to = $userRepository->findAll();
        }
        $toUsers = new ArrayCollection();
        foreach ($to as $u) {
            if ($u->getEmail() === $from->getEmail()) {
                continue;
            }
            $messageUser = new MessageUser();
            $messageUser->setToUser($u);
            $messageUser->setMessage($message);
            $messageUser->setStatus(false);
            $this->em->persist($messageUser);
            $toUsers->add($messageUser);
        }
        $message->setParameters($parameters);
        $message->setReceivedUsers($toUsers);
        $message->setSentTime(new \DateTime());
        $this->em->persist($message);
        $this->em->flush();

        return $message;
    }

    public function getNotificationNumber()
    {
        //return count($this->getNotifications());
        $mRepository = $this->em->getRepository('P5:MessageUser');
        $query = $mRepository->createQueryBuilder('mu');
        $query->select('count(mu)')->where('mu.status = :status')->andWhere('mu.toUser = :user');
        $query->setParameters(array('status' => false, 'user' => $this->token->getToken()->getUser()));

        return $query->getQuery()->getSingleScalarResult();
    }

    public function getNotifications()
    {
        $mRepository = $this->em->getRepository('P5:MessageUser');
        $messages = $mRepository->findBy(array('toUser' => $this->token->getToken()->getUser(), 'status' => false));

        return $messages;
    }
}
