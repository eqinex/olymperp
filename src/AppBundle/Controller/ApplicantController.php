<?php


namespace AppBundle\Controller;

use AppBundle\Entity\ApplicantDiff;
use AppBundle\Entity\ApplicantFile;
use AppBundle\Entity\ApplicantFileDownloadManager;
use AppBundle\Entity\ApplicantStatus;
use AppBundle\Entity\ProjectRole;
use AppBundle\Repository\RepositoryAwareTrait;
use AppBundle\Entity\Applicant;
use AppBundle\Entity\ApplicantComment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use AppBundle\Utils\StringUtils;

class ApplicantController extends Controller
{
    use RepositoryAwareTrait;

    /**
     * @Route("hr/applicant", name="applicant_list")
     */
    public function listAction(Request $request)
    {
        if (!$this->getUser()->canViewApplicant()) {
            return $this->redirectToRoute('homepage');
        }
        $filters = $request->get('filters', []);

        $currentPage = $request->get('page', 1);
        $perPage = $request->get('perPage', 25);
        $applicants = $this->getApplicantRepository()->getAllApplicants($filters, $currentPage, $perPage);
        $employeeRoles = $this->getProjectRoleRepository()->findAll();
        $statusList = $this->getApplicantStatusRepository()->findAll();

        $maxRows = $applicants->count();
        $maxPages = ceil($maxRows / $perPage);

        return $this->render('applicant/list.html.twig', [
            'applicants' => $applicants,
            'filters' => $filters,
            'employeeRoles' => $employeeRoles,
            'statusList' => $statusList,
            'maxRows' => $maxRows,
            'maxPages' => $maxPages,
            'currentPage' => $currentPage,
            'perPage' => $perPage
        ]);
    }

    /**
     * @Route("hr/applicant/{applicantId}/details", name="applicant_details")
     */
    public function detailsAction(Request $request)
    {
        if ($this->getUser()->canViewApplicant()) {
            $applicantId = $request->get('applicantId');

            /** @var Applicant $applicant */
            $applicant = $this->getApplicantRepository()->find($applicantId);

            $applicantChanges = $this->getApplicantDiffRepository()->getApplicantChanges($applicant);
            $applicantComments = $this->getApplicantCommentRepository()->findBy(['applicant' => $applicant], ['id' => 'ASC']);
            $applicantFiles = $this->getApplicantFileRepository()->findBy(['applicant' => $applicantId, 'deleted' => null]);
            $employeeRoles = $this->getProjectRoleRepository()->findAll();
            $statusList = $this->getApplicantStatusRepository()->findAll();

            return $this->render('applicant/details.html.twig', [
                'applicant' => $applicant,
                'applicantChanges' => $applicantChanges,
                'applicantComments' => $applicantComments,
                'applicantFiles' => $applicantFiles,
                'employeeRoles' => $employeeRoles,
                'statusList' => $statusList
            ]);
        }
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Add applicant form.
     *
     * @Route("hr/applicant/add", name="applicant_add")
     */
    public function addAction(Request $request)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $applicantDetails = $request->get('applicant');

        try {
            if (!empty($applicantDetails) && $this->getUser()->canEditApplicant()) {
                $applicant = new Applicant();

                $applicant = $this->buildApplicant($applicant, $applicantDetails);

                $this->getEm()->persist($applicant);
                $this->getEm()->flush();
            }
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Edit applicant form.
     *
     * @Route("hr/applicant/edit", name="applicant_edit")
     * @throws \Exception
     */
    public function editAction(Request $request)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $applicantId = $request->get('id');
        $applicantDetails = $request->get('applicant');

        try {
            if ($this->getUser()->canEditApplicant()) {
                if (!empty($applicantDetails)) {
                    /** @var Applicant $applicant */
                    $applicant = $this->getApplicantRepository()->find($applicantId);

                    $applicant = $this->buildApplicant($applicant, $applicantDetails);

                    $em = $this->getEm();
                    $uof = $em->getUnitOfWork();
                    $uof->computeChangeSets();

                    $this->logChanges($applicant, $uof->getEntityChangeSet($applicant));

                    $em->persist($applicant);
                    $em->flush();
                }
            } else {
                throw new \Exception('You do not have permission to edit applicants');
            }
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param Applicant $applicant
     * @param $applicantDetails
     * @param $applicantStatusId
     * @return Applicant
     */
    protected function buildApplicant(Applicant $applicant, $applicantDetails)
    {
        /** @var ProjectRole $employeeRole */
        $employeeRole = $this->getProjectRoleRepository()->find($applicantDetails['employeeRole']);
        /** @var ApplicantStatus $applicantStatus */
        $applicantStatus = $this->getApplicantStatusRepository()->find($applicantDetails['status']);

        $applicant
            ->setFirstName($applicantDetails['firstname'])
            ->setLastName($applicantDetails['lastname'])
            ->setMiddleName($applicantDetails['middlename'])
            ->setEmail($applicantDetails['email'])
            ->setPhone($applicantDetails['phone'])
            ->setEmployeeRole($employeeRole)
            ->setStatus($applicantStatus)
            ->setNotice($applicantDetails['notice'])
            ->setUpdatedAt(new \DateTime())
        ;

        if (empty($applicant->getCreatedAt())) {
            $applicant->setCreatedAt(new \DateTime());
        }

        return $applicant;
    }

    /**
     * @param $applicant
     * @param $changeSet
     * @return array
     */
    protected function logChanges($applicant, $changeSet)
    {
        $em = $this->getDoctrine()->getManager();
        $applicantDiffs = [];
        foreach ($changeSet as $field => $changes) {
            if ($field == 'createdAt') {
                continue;
            }
            $oldValue = $this->prepareChangesValue($field, $changes[0]);
            $newValue = $this->prepareChangesValue($field, $changes[1]);

            if ($oldValue != $newValue) {
                $applicantDiff = new ApplicantDiff();

                $applicantDiff
                    ->setChangedBy($this->getUser())
                    ->setApplicant($applicant)
                    ->setField($field)
                    ->setOldValue($oldValue)
                    ->setNewValue($newValue)
                    ->setUpdatedAt(new \DateTime())
                ;

                $em->persist($applicantDiff);
                $applicantDiffs[] = $applicantDiff;
            }
        }

        return $applicantDiffs;
    }

    /**
     * @param $field
     * @param $value
     * @return int|string
     */
    protected function prepareChangesValue($field, $value)
    {
        if ($value instanceof \DateTime) {
            $value = $value->format('d/m/Y H:i');
        } elseif (!$value) {
            $value = 'No';
        } elseif ($value === true) {
            $value = 'Yes';
        }

        return $value;
    }

    /**
     * Add comment to Applicant.
     *
     * @Route("hr/applicant/{applicantId}/comment", name="applicant_add_comment")
     */
    public function commentAction(Request $request)
    {
        $comment = $request->get('comment');
        $applicantId = $request->get('applicantId');

        /** @var Applicant $applicant */
        $applicant = $this->getApplicantRepository()->find($applicantId);

        $this->addComment($applicant, $comment);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('applicant_details', ['applicantId' => $applicant->getId()]);
    }

    /**
     * @param Applicant $applicant
     * @param $comment
     * @return ApplicantComment
     */
    protected function addComment(Applicant $applicant, $comment)
    {
        $applicantComment = new ApplicantComment();
        $applicantComment->setCreatedAt(new \DateTime());
        $changes = ['comment' => []];

        if (!empty($comment['id'])) {
            $applicantComment = $this->getApplicantCommentRepository()->findOneBy([
                'id' => $comment['id'],
                'owner' => $this->getUser()->getId()
            ]) ?: $applicantComment;
        }

        $changes['comment'][] = $applicantComment->getCommentText();
        $applicantComment
            ->setOwner($this->getUser())
            ->setApplicant($applicant)
            ->setCommentText(StringUtils::parseLinks($comment['text']))
        ;
        $changes['comment'][] = $applicantComment->getCommentText();

        if (!empty($comment['reply-id'])) {
            $parentComment = $this->getApplicantCommentRepository()->find($comment['reply-id']);
            $applicantComment->setParentComment($parentComment);
        }

        $this->logChanges($applicant, $changes);
        $em = $this->getDoctrine()->getManager();
        $em->persist($applicantComment);

        return $applicantComment;
    }

    /**
     * Upload file api.
     *
     * @Route("hr/applicant/{applicantId}/upload-file", name="applicant_upload_file")
     */
    public function uploadFileAction(Request $request)
    {
        $applicantId = $request->get('applicantId');
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        /** @var Applicant $applicant */
        $applicant = $this->getApplicantRepository()->find($applicantId);

        $applicantFiles = $request->files->get('files');

        foreach($applicantFiles as $applicantFile) {
            try {
                $this->validateFile($applicantFile);
                $this->processFile($applicant, $applicantFile);
            } catch (\Exception $exception) {
                $flashbag->add('danger', $exception->getMessage());
            }
        }

        return $this->redirect($request->headers->get('referer') . '#attachmentstab');
    }

    /**
     * @param Applicant $applicant
     * @param UploadedFile $file
     * @throws \Exception
     */
    protected function processFile(Applicant $applicant, $file)
    {

        if ($file instanceof UploadedFile) {
            $applicantFile = new ApplicantFile();
            $format = !(empty($file->guessExtension()))
                ? $file->guessExtension()
                : $file->getClientOriginalExtension();

            $applicantFile
                ->setFileName($file->getClientOriginalName())
                ->setFormat($format)
                ->setOwner($this->getUser())
                ->setFileSize($file->getSize())
                ->setApplicant($applicant)
                ->setUploadedAt(new \DateTime())
            ;

            $this->moveFile($file, $applicantFile, $applicant->getId());

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($applicantFile);
            $em->flush();
        }
    }

    /**
     * @param UploadedFile $file
     * @param ApplicantFile $applicantFile
     * @param int $applicantId
     * @return string
     * @throws \Exception
     */
    protected function moveFile(UploadedFile $file, ApplicantFile $applicantFile, $applicantId)
    {
        // Generate a unique name for the file before saving it
        $dirName = uniqid();
        $fileName = $file->getClientOriginalName();
        $storedFileName = $fileName;
        $applicantFile->setStoredFileName($storedFileName);
        $applicantFile->setStoredFileDir($dirName);

        $basePath = $this->getParameter('applicant_files_root_dir') . '/' . $applicantId . '/' . $dirName;
        // Move the file to the directory where brochures are stored
        $file->move(
            $basePath,
            $storedFileName
        );

        if (in_array($applicantFile->getFormat(), ['jpg', 'jpeg', 'png'])) {
            $thumbName = $fileName .'_100x100.' . $applicantFile->getFormat();
            $applicantFile->setStoredPreviewFileName($thumbName);
            $thumb = new \Imagick($basePath . '/' . $storedFileName);
            $thumb->setImageGravity(\Imagick::GRAVITY_CENTER);
            $thumb->resizeImage(200, 200, \Imagick::FILTER_LANCZOS, 1, 0);
            $thumb->cropImage(100, 100, 25, 25);
            $thumb->writeImage($basePath . '/' . $thumbName);
        }
    }

    /**
     * Download file file url.
     *
     * @Route("hr/applicant/{applicantId}/download/{fileId}/{preview}", name="applicant_download_file", defaults={"preview": 0})
     *
     */
    public function downloadFileAction(Request $request)
    {
        $fileId = $request->get('fileId');
        $preview = $request->get('preview');

        /** @var ApplicantFile $applicantFile */
        $applicantFile = $this->getApplicantFileRepository()->find($fileId);

        if (!$applicantFile->hasAccess($this->getUser())) {
            return $this->redirectToRoute('applicant_list');
        }

        $applicantFileDownloadManager = new ApplicantFileDownloadManager();
        $applicantFileDownloadManager
            ->setApplicantFile($applicantFile)
            ->setUser($this->getUser())
            ->setDownloadDate(new \DateTime(date('d.m.Y H:i')))
        ;
        $this->getEm()->persist($applicantFileDownloadManager);
        $this->getEm()->flush();

        $fileName = $preview ? $applicantFile->getStoredPreviewFileName() : $applicantFile->getStoredFileName();

        $headers = [
            'Content-Type' => 'application/' . $applicantFile->getFormat(),
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ];

        $applicantDir = $this->getParameter('applicant_files_root_dir') . '/' .
            $applicantFile->getApplicant()->getId() . '/';

        $applicantDir .= $applicantFile->getStoredFileDir() ? $applicantFile->getStoredFileDir() . '/' : '';

        return new Response(file_get_contents($applicantDir . $fileName), 200, $headers);
    }

    /**
     * @param UploadedFile $file
     * @throws \Exception
     */
    protected function validateFile(UploadedFile $file)
    {
        if ($file->getSize() > 102400000) {
            throw new MaxFileSizeException($this->get('translator'), $file->getClientOriginalName());
        }
    }

    /**
     * Delete file action.
     *
     * @Route("hr/applicant/{applicantId}/remove/{fileId}/", name="applicant_remove_file")
     */
    public function removeFileAction(Request $request)
    {
        $fileId = $request->get('fileId');
        $applicantFile = $this->getApplicantFileRepository()->find($fileId);

        /** @var ApplicantFile $applicantFile */
        if ($applicantFile->canDeleteFile($this->getUser())) {
            $applicantFile->setDeleted(true);

            $this->getEm()->persist($applicantFile);
            $this->getEm()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }
}