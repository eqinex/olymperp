<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Vacancy;
use AppBundle\Repository\RepositoryAwareTrait;
use AppBundle\Repository\ApplicantRepository;
use AppBundle\Repository\ProjectRoleRepository;
use AppBundle\Entity\Applicant;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class VacancyController extends Controller
{
    use RepositoryAwareTrait;

    /**
     * @Route("hr/vacancy", name="vacancies_list")
     */
    public function listAction(Request $request)
    {
        $user = $this->getUser();
        if ($user->canViewVacancy()) {
            $vacancies = $this->getVacancyRepository()->findAll();
        } else if ($user->getSubmissionTeam()) {
            $vacancies = $this->getVacancyRepository()->findBy(['owner' => $user->getId()]);
        } else {
            return $this->redirectToRoute('homepage');
        }
        $employeeRoles = $this->getProjectRoleRepository()->findAll();
        $teams = $this->getTeamRepository()->findAll();

        return $this->render('vacancy/list.html.twig', [
            'vacancies' => $vacancies,
            'employeeRoles' => $employeeRoles,
            'teams' => $teams
        ]);
    }

    /**
     * Edit request form.
     *
     * @Route("hr/vacancy/add", name="vacancies_add")
     */
    public function addAction(Request $request)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $vacancyDetails = $request->get('vacancy');
        $user = $this->getUser();

        try {
            if (!empty($vacancyDetails)) {
                $vacancy = new Vacancy();

                $vacancy = $this->buildVacancy($vacancy, $vacancyDetails);
                $vacancy->setOwner($this->getUser());

                $this->getEm()->persist($vacancy);
                $this->getEm()->flush();
            }
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Edit request form.
     *
     * @Route("hr/vacancy/edit", name="vacancies_edit")
     */
    public function editAction(Request $request)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $vacancyId = $request->get('id');
        $vacancyDetails = $request->get('vacancy');
        $user = $this->getUser();

        /** @var Vacancy $vacancy */
        $vacancy = $this->getVacancyRepository()->find($vacancyId);

        try {
            if (!empty($vacancyDetails) && ($user->canEditVacancy() || $user->getSubmissionTeam())) {
                $vacancy = $this->buildVacancy($vacancy, $vacancyDetails);

                $em = $this->getEm();
                $em->persist($vacancy);
                $em->flush();
            }
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirect($request->headers->get('referer'));
    }


    /**
     * @param Vacancy $vacancy
     * @param $vacancyDetails
     * @return Vacancy
     */
    protected function buildVacancy(Vacancy $vacancy, $vacancyDetails)
    {
        $employeeRole = $this->getProjectRoleRepository()->find($vacancyDetails['employeeRole']);
        $team = $this->getTeamRepository()->find($vacancyDetails['team']);

        $vacancy
            ->setEmployeeRole($employeeRole)
            ->setTeam($team)
            ->setDescription($vacancyDetails['description'])
        ;

        return $vacancy;
    }
}