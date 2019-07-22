<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Achievement;
use AppBundle\Entity\UserAchievement;
use AppBundle\Repository\RepositoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class AchievementController extends Controller
{
    use RepositoryAwareTrait;
    const PER_PAGE = 25;

    /**
     * @Route("/achievements", name="achievements_list")
     */
    public function listAction(Request $request)
    {
        $achievementsBasic = $this->getAchievementRepository()->findBy(['basic' => true],['title' => 'ASC']);
        $achievementsProject = $this->getAchievementRepository()->findBy(['basic' => false],['title' => 'ASC']);

        return $this->render('achievements/list.html.twig', [
            'achievementsBasic' => $achievementsBasic,
            'achievementsProject' => $achievementsProject,
        ]);
    }

    /**
     * @Route("/achievements/{id}/details", name="achievements_details")
     */
    public function detailsAction(Request $request)
    {
        $achievementId = $request->get('id');
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);
        /** @var Achievement $achievement */
        $achievement = $this->getAchievementRepository()->find($achievementId);

        $userAchievements = $this->getUserAchievementRepository()->getUserAchievements($achievementId, $filters, $currentPage, self::PER_PAGE);
        $users = $this->getUserRepository()->getUsersGroupedByTeams();

        $maxRows = $userAchievements->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('achievements/details.html.twig', [
            'userAchievements' => $userAchievements,
            'achievement' => $achievement,
            'users' => $users,
            'filters' => $filters,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'perPage' => self::PER_PAGE,
        ]);
    }

    /**
     * Add achievements user.
     *
     * @Route("/achievements/{id}/details/add", name="achievements_user_add")
     */
    public function addAchievementsUserAction(Request $request)
    {
        $achievementId = $request->get('id');
        $userAchievementsDetails = $request->get('achievementUser');
        $user = $this->getUserRepository()->find($userAchievementsDetails['user']);
        $achievement = $this->getAchievementRepository()->find($achievementId);

        $userAchievements = new UserAchievement();
        $userAchievements
            ->setOwner($user)
            ->setAchievement($achievement)
            ->setCreatedAt(new \DateTime())
        ;

        $this->getEm()->persist($userAchievements);

        $this->getEm()->flush();

        return $this->redirectToRoute('achievements_details', ['id' => $achievementId]);
    }
}