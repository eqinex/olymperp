<?php

namespace DocumentBundle\Controller;

use AppBundle\Repository\RepositoryAwareTrait;
use AppBundle\Utils\StringUtils;
use DocumentBundle\Entity\Activity;
use DocumentBundle\Entity\ActivityDiff;
use DocumentBundle\Entity\ActivityEvents;
use DocumentBundle\Service\Export\ActivityBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ActivityController extends Controller
{
    use RepositoryAwareTrait;

    const PER_PAGE = 20;

    /**
     * @Route("/activities", name="activities_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);
        $user = $this->getUser();
        $responsibleUserActivities = $this->getActivityRepository()->getActivitiesResponsible($user);

        if (!$user->canViewActivity() && !$responsibleUserActivities) {
            return $this->redirectToRoute('homepage');
        }
        
        if (!$user->canViewAllActivity()) {
            $filters['user'] = $user;
        }

        $projects = $this->getProjectRepository()->findAll();
        $responsibleUsers = $this->getActivityRepository()->findAll();
        $users = $this->getUserRepository()->getUsersGroupedByTeams();

        $currentProjectsActivities = $this->getActivityRepository()->getAvailableActivities(Activity::CURRENT_PROJECTS, $filters);
        $preContractualProjects = $this->getActivityRepository()->getAvailableActivities(Activity::PRE_CONTRACTUAL_PROJECTS, $filters);
        $otherActivities = $this->getActivityRepository()->getAvailableActivities(Activity::OTHER_ACTIVITIES, $filters);
        $deferredProjectsActivities = $this->getActivityRepository()->getAvailableActivities(Activity::DEFERRED_PROJECTS, $filters);

        return $this->render('activities/list.html.twig', [
            'projects' => $projects,
            'responsibleUsers' => $responsibleUsers,
            'users' => $users,
            'currentProjectsActivities' => $currentProjectsActivities,
            'preContractualProjects' => $preContractualProjects,
            'otherActivities' => $otherActivities,
            'deferredProjectsActivities' => $deferredProjectsActivities,
            'categories' => Activity::getCategoriesList(),
            'results' => Activity::getResultsList(),
            'filters' => $filters,
            'currentPage' => $currentPage
        ]);
    }

    /**
     * @Route("/activities/{id}/details", name="activities_details")
     */
    public function detailsAction(Request $request)
    {
        if (!$this->getUser()->canViewActivity()) {
            return $this->redirectToRoute('homepage');
        }
        $activityId = $request->get('id');
        /** @var Activity $activityId */
        $activity = $this->getActivityRepository()->find($activityId);

        $users = $this->getUserRepository()->getUsersGroupedByTeams();

        $successEvents = $this->getActivityEventsRepository()->findBy([
            'activity' => $activity,
            'type' => ActivityEvents::TYPE_SUCCESS
        ]);
        $activityChanges = $this->getActivityDiffRepository()->getActivityChanges($activityId);

        return $this->render('activities/details.html.twig', [
            'activity' => $activity,
            'categories' => Activity::getCategoriesList(),
            'types' => ActivityEvents::getTypesList(),
            'users' => $users,
            'activityChanges' => $activityChanges,
            'successEvents' => $successEvents
        ]);
    }

    /**
     * @Route("/activities/new", name="add_activity")
     */
    public function addActivityAction(Request $request)
    {
        $activityDetails = $request->get('activity');

        $activity = new Activity();

        $activity = $this->buildActivity($activity, $activityDetails);

        $em = $this->getEm();
        $em->persist($activity);
        $em->flush();

        return $this->redirectToRoute('activities_details', ['id' => $activity->getId()]);
    }

    /**
     * @Route("/activities/{id}/edit", name="edit_activity")
     */
    public function editActivityAction (Request $request)
    {
        $activityId = $request->get('id');
        $activity = $this->getActivityRepository()->find($activityId);
        $activityDetails = $request->get('activity');

        /** @var Activity $activity */
        $activity = $this->buildActivity($activity, $activityDetails);

        $em = $this->getEm();
        $em->persist($activity);

        $uof = $em->getUnitOfWork();
        $uof->computeChangeSets();

        $this->logChanges($activity, $uof->getEntityChangeSet($activity));
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param Activity $activity
     * @param $activityDetails
     * @return Activity
     */
    protected function buildActivity(Activity $activity, $activityDetails)
    {
        $user = $this->getUser();
        $responsibleUser = $this->getUserRepository()->find($activityDetails['responsibleUser']);
        $project = $this->getProjectRepository()->find($activityDetails['project']);

        if (!$activity->getId()) {
            $activity
                ->setOwner($user)
                ->setCreatedAt(new \DateTime())
            ;
        }

        $activity
            ->setProject($project)
            ->setResponsibleUser($responsibleUser)
            ->setActivity($activityDetails['activity'])
            ->setCategory($activityDetails['category'])
            ->setProfitability($activityDetails['profitability'])
            ->setPlan($activityDetails['plan'])
            ->setFact($activityDetails['fact'])
            ->setReceived($activityDetails['received'])
            ->setEndAt(new \DateTime($activityDetails['endAt']))
        ;

        if (isset($activityDetails['highRisk'])) {
            $activity->setHighRisk($activityDetails['highRisk']);
        }

        return $activity;
    }

    /**
     * @Route("/activities/{id}/change-result/{result}", name="activity_change_result")
     */
    public function changeResultAction (Request $request)
    {
        $result = $request->get('result');
        $activityId = $request->get('id');
        /** @var Activity $activity */
        $activity = $this->getActivityRepository()->find($activityId);
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        if (!$activity->checkGrants($user)) {
            return $this->redirectToRoute('activities_list');
        }

        if (array_key_exists($result, $activity->getResultsList())) {
            $activity->setResult($result);

            $em->persist($activity);

            $uof = $em->getUnitOfWork();
            $uof->computeChangeSets();

            $this->logChanges($activity, $uof->getEntityChangeSet($activity));
            $em->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/activities/{id}/details/new-event", name="add_activity_event")
     */
    public function addActivityEventAction(Request $request)
    {
        $activityId = $request->get('id');
        /** @var Activity $activity */
        $activity = $this->getActivityRepository()->find($activityId);

        $activityEventDetails = $request->get('activityEvent');

        $activityEvent = new ActivityEvents();

        $activity = $this->buildActivityEvent($activity, $activityEvent, $activityEventDetails);

        $em = $this->getEm();
        $em->persist($activity);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/activities/{id}/details/{eventId}/edit-event", name="edit_activity_event")
     */
    public function editActivityEventAction (Request $request)
    {
        $activityId = $request->get('id');
        /** @var Activity $activity */
        $activity = $this->getActivityRepository()->find($activityId);

        $activityEventId = $request->get('eventId');
        /** @var ActivityEvents $activityEvent */
        $activityEvent = $this->getActivityEventsRepository()->find($activityEventId);

        $activityEventDetails = $request->get('activityEvent');

        $activityEvent = $this->buildActivityEvent($activity, $activityEvent, $activityEventDetails);

        $em = $this->getEm();
        $em->persist($activityEvent);

        $uof = $em->getUnitOfWork();
        $uof->computeChangeSets();

        $this->logChanges($activity, $uof->getEntityChangeSet($activityEvent));
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param Activity $activity
     * @param ActivityEvents $activityEvent
     * @param $activityEventDetails
     * @return ActivityEvents
     */
    protected function buildActivityEvent(Activity $activity, ActivityEvents $activityEvent, $activityEventDetails)
    {
        $responsibleUser = $this->getUserRepository()->find($activityEventDetails['responsibleUser']);

        if (!$activityEvent->getId()) {
            $activityEvent
                ->setActivity($activity)
            ;
        }

        $activityEvent
            ->setName($activityEventDetails['name'])
            ->setType($activityEventDetails['type'])
            ->setResponsibleUser($responsibleUser)
            ->setEndAt(new \DateTime($activityEventDetails['endAt']))
        ;

        if (!empty($activityEventDetails['event'])) {
            $successEvent = $this->getActivityEventsRepository()->find($activityEventDetails['event']);
            $activityEvent->setSuccessEvent($successEvent);
        } elseif (!isset($activityEventDetails['event'])) {
            $activityEvent->setSuccessEvent(null);
        }

        return $activityEvent;
    }

    /**
     * @Route("/activities/{id}/details/{eventId}/change-state/{state}", name="activity_event_change_state")
     */
    public function changeStateEventAction (Request $request)
    {
        $activityEventId = $request->get('eventId');
        $state = $request->get('state');

        /** @var ActivityEvents $activityEvent */
        $activityEvent = $this->getActivityEventsRepository()->find($activityEventId);

        $em = $this->getDoctrine()->getManager();

        if (array_key_exists($state, $activityEvent->getStatusList())) {
            $activityEvent->setStatus($state);

            $em->persist($activityEvent);
            $em->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/activities/export-activities", name="export_activities")
     */
    public function exportActivityAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $user = $this->getUser();

        if (!$user->canViewAllActivity()) {
            $filters['user'] = $user;
        }

        $currentProjectsActivities = $this->getActivityRepository()->getAvailableActivities(Activity::CURRENT_PROJECTS, $filters);
        $preContractualProjects = $this->getActivityRepository()->getAvailableActivities(Activity::PRE_CONTRACTUAL_PROJECTS, $filters);
        $otherActivities = $this->getActivityRepository()->getAvailableActivities(Activity::OTHER_ACTIVITIES, $filters);
        $deferredProjectsActivities = $this->getActivityRepository()->getAvailableActivities(Activity::DEFERRED_PROJECTS,$filters);

        $exportBuilder = new ActivityBuilder($this->get('phpexcel'), $this->get('translator'));

        $phpExcelObject = $exportBuilder->build($currentProjectsActivities, $preContractualProjects, $otherActivities, $deferredProjectsActivities);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding header
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            StringUtils::transliterate('activity_map') . '.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @param $activity
     * @param $changeSet
     * @return array
     */
    protected function logChanges($activity, $changeSet)
    {
        $em = $this->getDoctrine()->getManager();
        $activityDiffs = [];
        foreach ($changeSet as $field => $changes) {
            $oldValue = $this->prepareChangesValue($field, $changes[0]);
            $newValue = $this->prepareChangesValue($field, $changes[1]);
            if ($oldValue != $newValue && $oldValue) {
                $activityDiff = new ActivityDiff();

                $activityDiff
                    ->setChangedBy($this->getUser())
                    ->setActivity($activity)
                    ->setField($field)
                    ->setOldValue($oldValue)
                    ->setNewValue($newValue)
                    ->setUpdatedAt(new \DateTime())
                ;

                $em->persist($activityDiff);
                $activityDiffs[] = $activityDiff;
            }
        }

        return $activityDiffs;
    }

    /**
     * @param $field
     * @param $value
     * @return int|string
     */
    protected function prepareChangesValue($field, $value)
    {
        if (empty($value) && in_array($field, ['result'])) {
            $value = 0;
        }

        if ($value instanceof \DateTime) {
            $value = $value->format('d/m/Y H:i');
        }

        switch ($field) {
            case 'result':
                $value = Activity::getResultsList()[$value];
                break;
        }

        return $value;
    }
}