<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Notification;
use AppBundle\Repository\RepositoryAwareTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class NotificationsController extends Controller
{
    use RepositoryAwareTrait;

    const PER_PAGE = 20;

    /**
     * Displays all notifications.
     *
     *
     * @Route("/notifications", name="notification_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $readNotifications = $request->get('readNotifications', []);
        $currentPage = $request->get('page', 1);
        $perPage = $request->get('perPage', 20);
        $types = Notification::getTypesList();
        $notifications = $this->getNotificationRepository()->getUserNotifications(
            $this->getUser(),
            $filters,
            $currentPage,
            self::PER_PAGE);
        $senders = $this->getUserRepository()->getUsersGroupedByTeams();
        if (!empty($readNotifications)){
            foreach ($readNotifications as $id) {
                $readItem = $this->getNotificationRepository()->find($id);
                $readItem
                    ->setReadAt(new \DateTime());
                $this->getEm()->persist($readItem);
            }
            $this->getEm()->flush();
        }

        $maxRows = $notifications->count();
        $maxPages = ceil($maxRows / $perPage);
        return $this->render('notifications/list.html.twig', [
            'notifications' => $notifications,
            'filters' => $filters,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'types' => $types,
            'senders' => $senders,
            'perPage' => $perPage
        ]);
    }
}
