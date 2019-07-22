<?php

namespace AppBundle\Service;

use AppBundle\Entity\Notification;
use AppBundle\Entity\User;
use AppBundle\Repository\RepositoryAwareTrait;

/**
 * Created by PhpStorm.
 * User: kuhtevindv
 * Date: 05.02.19
 * Time: 14:20
 */
class NotificationService
{
    use RepositoryAwareTrait;

    /**
     * @var $doctrine
     */
    protected $doctrine;

    /**
     * @var $renderer
     */
    private $renderer;

    /**
     * @var $mailer
     */
    private $mailer;

    public function __construct($doctrine, $renderer, $mailer)
    {
        $this->doctrine = $doctrine;
        $this->renderer = $renderer;
        $this->mailer = $mailer;
    }

    /**
     * @param User[] $owners
     * @param string $title
     * @param string $type
     * @param User $sender
     * @param array $params
     * @return true
     * @throws \Exception
     */
    public function sendNotifications($owners, $title, $type, $sender, $params)
    {

        $em = $this->getDoctrine()->getEntityManager();

        $templates=[
            'new_task' => '/task/new.html.twig',
            'task_update' => '/task/updated.html.twig',
            'new_comment' => '/task/comment.html.twig',
            'new_attachment' => '/task/add_file.html.twig'
        ];

        foreach ($owners as $owner) {
            $notification = new Notification();

            $notificationTemplate = 'notifications'.$templates[$type];
            $template = 'emails'.$templates[$type];

            $notification
                ->setCreatedAt(new \DateTime())
                ->setTitle($title)
                ->setDescription($this->renderer->render($notificationTemplate, $params))
                ->setType($type)
                ->setSender($sender)
                ->setOwner($owner)
            ;
            $em->persist($notification);

            $this->sendEmail($title, $owner, $template, $params);
        }

        $em->flush();

        return true;
    }

    protected function sendEmail($title, $recipient, $template, $params)
    {
        if ($recipient instanceof User) {
            $email = new \Swift_Message('[OLYMP] ' . $title);
            $email
                ->setFrom('olymp@npo-at.com')
                ->setTo($recipient->getEmail())
                ->setBody($this->renderer->render($template, $params), 'text/html');

            $this->mailer->send($email);
        }
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function getNotificationsCounter(User $user){
        return $this->getNotificationRepository()->getNotificationsCounter($user);
    }

    /**
     * @return mixed
     */
    protected function getDoctrine()
    {
        return $this->doctrine;
    }
}