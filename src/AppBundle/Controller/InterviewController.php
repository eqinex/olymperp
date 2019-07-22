<?php


namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Vacancy;
use AppBundle\Entity\Applicant;
use AppBundle\Entity\Interview;
use AppBundle\Repository\RepositoryAwareTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class InterviewController extends Controller
{
    use RepositoryAwareTrait;

    /**
     * @Route("hr/interview", name="interviews_list")
     */
    public function listAction(Request $request)
    {
        if (!$this->getUser()->canViewInterview($this->getUser())) {
            return $this->redirectToRoute('homepage');
        }
        $interviews = $this->getInterviewRepository()->getAvailableInterviews();
        $applicants = $this->getApplicantRepository()->findAll();
        $vacancies = $this->getVacancyRepository()->findAll();

        return $this->render('interviews/list.html.twig', [
            'interviews' => $interviews,
            'applicants' => $applicants,
            'vacancies' => $vacancies
        ]);
    }

    /**
     * Add interview form
     *
     * @Route("hr/interview/add", name="interviews_add")
     */
    public function addAction(Request $request)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $interviewDetails = $request->get('interview');
        $user = $this->getUser();

        try {
            if (!empty($interviewDetails) && $user->canEditInterview()) {
                $interview = new Interview();

                $interview = $this->buildInterview($interview, $interviewDetails);
                $interview->setCreatedAt(new \DateTime());

                $this->getEm()->persist($interview);
                $this->getEm()->flush();
            }
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Edit interview form.
     *
     * @Route("hr/interview/edit", name="interview_edit")
     */
    public function editAction(Request $request)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $interviewId = $request->get('id');
        $interviewDetails = $request->get('interview');

        /** @var Interview $interview */
        $interview = $this->getVacancyRepository()->find($interviewId);

        try {
            if (!empty($interviewDetails) && $this->getUser()->canEditInterview()) {
                $interview = $this->buildInterview($interview, $interviewDetails);

                $em = $this->getEm();
                $em->persist($interview);
                $em->flush();
            }
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Interview change state.
     *
     * @Route("hr/interview/change-state/{interviewId}/{state}", name="interview_change_state")
     */
    public function changeStateAction(Request $request)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $state = $request->get('state');
        $interviewDetails = $request->get('interview');
        $interviewId = $request->get('interviewId');

        /** @var Interview $interview */
        $interview = $this->getInterviewRepository()->find($interviewId);

        try {
            $interview->setStatus($state);
            if (!empty($interviewDetails)) {
                if ($state == 1) {
                    $interview->setStartAt(new \DateTime($interviewDetails['startAt']));

                    $owner = $interview->getVacancy()->getOwner();
                    $template = 'emails/interviews/updated.html.twig';
                    $params = [
                        'interview' => $interview
                    ];

                    if ($owner instanceof User) {
                        $this->sendEmail(' Потдверждение собеседования ', $owner->getEmail(), $this->renderView($template, $params));
                    }
                }
            }
            $em = $this->getEm();
            $em->persist($interview);
            $em->flush();
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirect($request->headers->get('referer'));
    }

    protected function sendEmail($title, $ownerEmail, $body)
    {
        $email = new \Swift_Message('[OLYMP] ' . $title);
        $email
            ->setFrom('olymp@npo-at.com')
            ->setTo($ownerEmail)
            ->setBody($body, 'text/html');

        $this->get('mailer')->send($email);
    }

    /**
     * @param Interview $interview
     * @param $interviewDetails
     * @return Interview
     */
    protected function buildInterview(Interview $interview, $interviewDetails)
    {
        /** @var Applicant $applicant */
        $applicant = $this->getApplicantRepository()->find($interviewDetails['applicant']);
        /** @var Vacancy $vacancy */
        $vacancy = $this->getVacancyRepository()->find($interviewDetails['vacancy']);
        $interview
            ->setApplicant($applicant)
            ->setVacancy($vacancy)
            ->setNotice($interviewDetails['notice'])
        ;

        return $interview;
    }
}