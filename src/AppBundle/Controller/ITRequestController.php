<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ITRequest;
use AppBundle\Entity\ProjectTask;
use AppBundle\Repository\ITRequestRepository;
use AppBundle\Repository\RepositoryAwareTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ITRequestController extends Controller
{
    use RepositoryAwareTrait;
    const PER_PAGE = 20;
    /**
     * IT Requests list.
     *
     * @Route("/it-requests", name="it_requests_list")
     */
    public function listAction(Request $request)
    {
        $user = $this->getUser();
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);

        if ($user->isITRequestManager()) {
            $requests = $this->getITRequestsRepository()->getAvailableRequests($filters, $currentPage, self::PER_PAGE);
        } else {
            $filters['owner'] = $user;
            $requests = $this->getITRequestsRepository()->getAvailableRequests($filters, $currentPage, self::PER_PAGE);
        }
        $maxRows = $requests->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('it_requests/list.html.twig', [
            'requests' => $requests,
            'statuses' => ITRequest::getStatusList(),
            'filters' => $filters,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'perPage' => self::PER_PAGE,
        ]);
    }

    /**
     * New IT Request.
     *
     * @Route("/it-requests/new", name="it_requests_new")
     */
    public function newAction(Request $request)
    {
        $user = $this->getUser();

        $itRequest = $request->get('it_request');

        $newRequest = new ITRequest();
        $newRequest
            ->setOwner($user)
            ->setCreatedAt(new \DateTime())
            ->setTitle($itRequest['title'])
            ->setDescription($itRequest['description'])
            ->setStatus(0)
        ;

        $em = $this->getDoctrine()->getEntityManager();

        $em->persist($newRequest);
        $em->flush();

        return $this->redirectToRoute('it_requests_list');
    }

    /**
     * Edit IT Request.
     *
     * @Route("/it-requests/{id}/edit", name="it_request_edit")
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();

        $itRequestData = $request->get('it_request');
        $itRequestId = $request->get('id');

        $itRequest = $this->getITRequestsRepository()->find($itRequestId);

        if ($user->isITRequestManager() || $user->getId() == $itRequest->getOwner()->getId()) {
            $itRequest
                ->setTitle($itRequestData['title'])
                ->setDescription($itRequestData['description'])
            ;

            $em = $this->getDoctrine()->getEntityManager();

            $em->persist($itRequest);
            $em->flush();
        }

        return $this->redirectToRoute('it_requests_list');
    }

    /**
     * IT Requests change state.
     *
     * @Route("/it-requests/{id}/change-state/{state}", name="it_requests_change_state")
     */
    public function changeStateAction(Request $request)
    {
        $state = $request->get('state');
        $requestId = $request->get('id');

        /** @var ITRequest $ITRequest */
        $ITRequest = $this->getITRequestsRepository()->find($requestId);

        if (array_key_exists($state, $ITRequest->getStatusList())) {
            $ITRequest->setStatus($state);
            if ($state == ITRequest::STATUS_DONE || $state == ITRequest::STATUS_CANCELLED) {
                $ITRequest->setClosedAt(new \DateTime());
            }
            $ITRequest->setAssignee($this->getUser());

            $em = $this->getDoctrine()->getEntityManager();
            $team = $this->getTeamRepository()->findOneBy(['code' => 'ПО']);
            $teamSpace = $this->getTeamSpaceRepository()->findOneBy(['team' => $team]);

            if ($state == ITRequest::STATUS_IN_PROGRESS && empty($ITRequest->getTask())) {

                $task = new ProjectTask();
                $task->setTitle($ITRequest->getTitle());
                $task->setResponsibleUser($this->getUser());
                $task->setDescription($ITRequest->getDescription());
                $task->setReporter($ITRequest->getOwner());
                $task->setType('task');
                $task->setStartAt(new \DateTime(date('d.m.Y 09:00')));
                $task->setEndAt(new \DateTime(date('d.m.Y 21:00')));
                $task->setPriority(1);
                $task->setControllingUser($ITRequest->getOwner());
                $task->setProject($teamSpace);
                $task
                    ->addSubscriber($ITRequest->getAssignee())
                    ->addSubscriber($ITRequest->getOwner())
                    ->addSubscriber($ITRequest->getAssignee()->getTeam()->getLeader());

                $em->persist($task);

                $ITRequest->setTask($task);
            }

            $em->persist($ITRequest);
            $em->flush();
        }

        return $this->redirectToRoute('it_requests_list');
    }

    /**
     * @return ITRequestRepository
     */
    protected function getITRequestsRepository()
    {
        return $this->getDoctrine()->getRepository(ITRequest::class);
    }
}
