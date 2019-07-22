<?php

namespace DocumentBundle\Controller;

use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use AppBundle\Exception\MaxFileSizeException;
use AppBundle\Repository\RepositoryAwareTrait;
use DocumentBundle\Entity\TechnicalMap;
use DocumentBundle\Entity\TechnicalMapComment;
use DocumentBundle\Entity\TechnicalMapDiff;
use DocumentBundle\Entity\TechnicalMapFile;
use DocumentBundle\Entity\TechnicalMapSignatory;
use DocumentBundle\Entity\TechnicalMapSolutions;
use DocumentBundle\Service\Export\TechnicalMapBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TechnicalMapController extends Controller
{
    use RepositoryAwareTrait;

    const PER_PAGE = 20;

    /**
     * @Route("/technical-maps", name="technical_maps_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);

        $technicalMaps = $this->getTechnicalMapRepository()->getAvailableTechnicalMaps($filters, $currentPage, self::PER_PAGE);

        $projects = $this->getProjectRepository()->findAll();
        $users = $this->getUserRepository()->getUsersGroupedByTeams();

        $maxRows = $technicalMaps->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('technical_maps/list.html.twig', [
            'technicalMaps' => $technicalMaps,
            'projects' => $projects,
            'users' => $users,
            'filters' => $filters,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows
        ]);
    }

    /**
     * @Route("/technical-maps/needs-approve", name="technical_maps_need_approve_list")
     */
    public function technicalMapsNeedApproveAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);

        $user = $this->getUser();
        $technicalMaps = $this->getTechnicalMapRepository()->getNeedsApproveTechnicalMaps($user, $filters, $currentPage, self::PER_PAGE);

        $projects = $this->getProjectRepository()->findAll();
        $users = $this->getUserRepository()->getUsersGroupedByTeams();

        $maxRows = $technicalMaps->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('technical_maps/list.html.twig', [
            'technicalMaps' => $technicalMaps,
            'projects' => $projects,
            'users' => $users,
            'filters' => $filters,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows
        ]);
    }

    /**
     * @Route("/technical-maps/{id}/details", name="technical_maps_details")
     */
    public function detailsAction(Request $request)
    {
        $technicalMapId = $request->get('id');
        /** @var TechnicalMap $technicalMap */
        $technicalMap = $this->getTechnicalMapRepository()->find($technicalMapId);

        $solutions = $this->getTechnicalMapSolutionsRepository()->findBy([
            'technicalMap' => $technicalMap,
            'deleted' => null
        ]);
        $technicalMapComments = $this->getTechnicalMapCommentRepository()->findBy([
            'technicalMap' => $technicalMap
        ]);

        $technicalMapChanges = $this->getTechnicalMapDiffRepository()->getTechnicalMapChanges($technicalMap);

        $users = $this->getUserRepository()->getUsersGroupedByTeams();

        $fileParams = [
            'technicalMap' => $technicalMap,
            'deleted' => 0
        ];
        $technicalMapFiles = $this->getTechnicalMapFileRepository()->findBy($fileParams);

        return $this->render('technical_maps/details.html.twig', [
            'technicalMap' => $technicalMap,
            'solutions' => $solutions,
            'technicalMapComments' => $technicalMapComments,
            'technicalMapChanges' => $technicalMapChanges,
            'technicalMapFiles' => $technicalMapFiles,
            'users' => $users
        ]);
    }

    /**
     * @Route("/technical-maps/new", name="add_technical_map")
     */
    public function addTechnicalMapAction(Request $request)
    {
        $technicalMapDetails = $request->get('technicalMap');

        /** @var TechnicalMap $technicalMap */
        $technicalMap = new TechnicalMap();

        $technicalMap = $this->buildTechnicalMap($technicalMap, $technicalMapDetails);

        $em = $this->getEm();
        $em->refresh($technicalMap);

        /** @var Team $team */
        $team = $this->getUser()->getTeam();

        $code = $technicalMap->getId() . '.' . $team->getCode();

        if ($technicalMap->getProject()->getCode() && $technicalMap->getProject()->getCode() != $team->getCode()) {
            $code = $code . '.' . $technicalMap->getProject()->getCode();
        }

        $technicalMap->setCode($code);
        $em->persist($technicalMap);
        $em->flush();

        return $this->redirectToRoute('technical_maps_details', ['id' => $technicalMap->getId()]);
    }

    /**
     * @Route("/technical-maps/{id}/new", name="edit_technical_map")
     */
    public function editTechnicalMapAction (Request $request)
    {
        $technicalMapId = $request->get('id');
        /** @var TechnicalMap $technicalMap */
        $technicalMap = $this->getTechnicalMapRepository()->find($technicalMapId);
        $technicalMapDetails = $request->get('technicalMap');
        $user = $this->getUser();

        if (!$technicalMap->checkGrants($user)) {
            return $this->redirect($request->headers->get('referer'));
        }

        $technicalMap = $this->buildTechnicalMap($technicalMap, $technicalMapDetails);

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param TechnicalMap $technicalMap
     * @param $technicalMapDetails
     * @return TechnicalMap
     */
    protected function buildTechnicalMap(TechnicalMap $technicalMap, $technicalMapDetails)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $project = $this->getProjectRepository()->find($technicalMapDetails['project']);

        if (!$technicalMap->getId()) {
            $technicalMap
                ->setOwner($user)
                ->setCreatedAt(new \DateTime())
            ;
        }

        $technicalMap
            ->setProject($project)
            ->setTask($technicalMapDetails['task'])
            ->setGoal($technicalMapDetails['goal'])
            ->setCriterionTitle1($technicalMapDetails['criterion1'])
            ->setCriterionTitle2($technicalMapDetails['criterion2'])
            ->setCriterionTitle3($technicalMapDetails['criterion3'])
            ->setCriterionTitle4($technicalMapDetails['criterion4'])
            ->setMaxPoints1($technicalMapDetails['maxPoints1'])
            ->setMaxPoints2($technicalMapDetails['maxPoints2'])
            ->setMaxPoints3($technicalMapDetails['maxPoints3'])
            ->setMaxPoints4($technicalMapDetails['maxPoints4'])
        ;

        $technicalMap->addSubscriber($user);

        $em->persist($technicalMap);

        if ($technicalMap->getId()) {
            $uof = $em->getUnitOfWork();
            $uof->computeChangeSets();

            $technicalMapChanges = $this->logChanges($technicalMap, $uof->getEntityChangeSet($technicalMap));

            if (count($technicalMapChanges)) {
                $recipients = $this->getTechnicalMapRecipients($technicalMap);
                $template = 'emails/technical_map/updated.html.twig';
                $params = [
                    'technicalMap' => $technicalMap,
                    'technicalMapChanges' => $technicalMapChanges
                ];

                foreach ($recipients as $recipient) {
                    if ($recipient instanceof User) {
                        $this->sendEmail($technicalMap->getCode(), $recipient->getEmail(), $this->renderView($template, $params));
                    }
                }
            }
        }

        $em->flush();

        return $technicalMap;
    }

    /**
     * @Route("/technical-maps/{id}/solution/new", name="add_technical_map_solution")
     */
    public function addSolutionAction(Request $request)
    {
        $solutionDetails = $request->get('solution');
        $technicalMapId = $request->get('id');

        /** @var TechnicalMap $technicalMap */
        $technicalMap = $this->getTechnicalMapRepository()->find($technicalMapId);

        $user = $this->getUser();

        if (!$technicalMap->checkGrants($user)) {
            return $this->redirect($request->headers->get('referer'));
        }

        /** @var TechnicalMapSolutions $solution */
        $solution = new TechnicalMapSolutions();

        $solution = $this->buildSolution($solution, $technicalMap, $solutionDetails);

        $em = $this->getEm();
        $em->persist($solution);
        $em->flush();

        $recipients = $this->getTechnicalMapRecipients($technicalMap);
        $template = 'emails/technical_map/add_solution.html.twig';
        $params = [
            'technicalMap' => $technicalMap
        ];

        foreach ($recipients as $recipient) {
            if ($recipient instanceof User) {
                $this->sendEmail($technicalMap->getCode(), $recipient->getEmail(), $this->renderView($template, $params));
            }
        }

        return $this->redirectToRoute('technical_maps_details', ['id' => $technicalMap->getId()]);
    }

    /**
     * @Route("/technical-maps/{id}/solution/{solutionId}/edit", name="edit_technical_map_solution")
     */
    public function editSolutionAction (Request $request)
    {
        $solutionDetails = $request->get('solution');
        $technicalMapId = $request->get('id');
        $solutionId = $request->get('solutionId');

        /** @var TechnicalMapSolutions $solution */
        $solution = $this->getTechnicalMapSolutionsRepository()->find($solutionId);

        /** @var TechnicalMap $technicalMap */
        $technicalMap = $this->getTechnicalMapRepository()->find($technicalMapId);

        $user = $this->getUser();

        if (!$technicalMap->checkGrants($user)) {
            return $this->redirect($request->headers->get('referer'));
        }

        $solution = $this->buildSolution($solution, $technicalMap, $solutionDetails);

        $em = $this->getEm();
        $em->persist($solution);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/technical-maps/{id}/solution/{solutionId}/remove", name="remove_technical_map_solution")
     */
    public function removeSolutionAction(Request $request)
    {
        $solutionId = $request->get('solutionId');
        $technicalMapId = $request->get('id');
        /** @var TechnicalMap $technicalMap */
        $technicalMap = $this->getTechnicalMapRepository()->find($technicalMapId);
        /** @var TechnicalMapSolutions $solution */
        $solution = $this->getTechnicalMapSolutionsRepository()->find($solutionId);
        $user = $this->getUser();

        if (!$technicalMap->checkGrants($user)) {
            return $this->redirect($request->headers->get('referer'));
        }

        $solution->setDeleted(1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($solution);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param TechnicalMapSolutions $solution
     * @param TechnicalMap $technicalMap
     * @param $solutionDetails
     * @return TechnicalMapSolutions
     */
    protected function buildSolution(TechnicalMapSolutions $solution, TechnicalMap $technicalMap, $solutionDetails)
    {
        if (!$solution->getId()) {
            $solution
                ->setTechnicalMap($technicalMap);
        }

        if (isset($solutionDetails['criterion3'])) {
            $solution
                ->setCriterion3($solutionDetails['criterion3'])
                ->setPoints3($solutionDetails['points3'])
            ;
        }

        if (isset($solutionDetails['criterion4'])) {
            $solution
                ->setCriterion4($solutionDetails['criterion4'])
                ->setPoints4($solutionDetails['points4'])
            ;
        }

        $solution
            ->setName($solutionDetails['name'])
            ->setCriterion1($solutionDetails['criterion1'])
            ->setCriterion2($solutionDetails['criterion2'])
            ->setPoints1($solutionDetails['points1'])
            ->setPoints2($solutionDetails['points2'])
        ;

        return $solution;
    }

    /**
     * @param int $solutionId
     * @param array $solutions
     * @return true
     */
    protected function setSolutionSelected($solutionId, $solutions)
    {
        $em = $this->getEm();

        /** @var TechnicalMapSolutions $solution */
        foreach ($solutions as $solution) {
            if ($solution->getId() == $solutionId) {
                $solution->setSelected(true);
            } else {
                $solution->setSelected(false);
            }
            $em->persist($solution);
        }

        $em->flush();

        return true;
    }

    /**
     * @param $solutionId
     * @param $solutions
     * @param $justification
     * @return \Doctrine\ORM\EntityManager
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function setSolutionJustification($solutionId, $solutions, $justification)
    {
        $em = $this->getEm();

        /** @var TechnicalMapSolutions $solution */
        foreach ($solutions as $solution) {
            if ($solution->getId() == $solutionId) {
                $solution->setJustification($justification);
            }

            $em->persist($solution);
        }

        $em->flush();

        return $em;
    }

    /**
     * @Route("/technical-maps/{id}/comment", name="technical_map_add_comment")
     */
    public function commentAction(Request $request)
    {
        $comment = $request->get('comment');
        $technicalMapId = $request->get('id');

        /** @var TechnicalMap $technicalMap */
        $technicalMap = $this->getTechnicalMapRepository()->find($technicalMapId);

        $technicalMapComment = $this->addComment($technicalMap, $comment);

        $technicalMap->addSubscriber($this->getUser());

        $em = $this->getEm();
        $em->flush();

        $recipients = $this->getTechnicalMapRecipients($technicalMap);
        $template = 'emails/technical_map/comment.html.twig';
        $params = [
            'technicalMap' => $technicalMap,
            'technicalMapComment' => $technicalMapComment
        ];

        foreach ($recipients as $recipient) {
            if ($recipient instanceof User) {
                $this->sendEmail($technicalMap->getCode(), $recipient->getEmail(), $this->renderView($template, $params));
            }
        }

        return $this->redirectToRoute('technical_maps_details', ['id' => $technicalMap->getId()]);
    }

    /**
     * @param TechnicalMap $technicalMap
     * @param $comment
     * @return TechnicalMapComment
     */
    protected function addComment(TechnicalMap $technicalMap, $comment)
    {
        $technicalMapComment = new TechnicalMapComment();
        $technicalMapComment->setCreatedAt(new \DateTime());
        $changes = ['comment' => []];

        if (!empty($comment['id'])) {
            $technicalMapComment = $this->getTechnicalMapCommentRepository()->findOneBy([
                'id' => $comment['id'],
                'owner' => $this->getUser()->getId()
            ]) ?: $technicalMapComment;
        }

        $changes['comment'][] = $technicalMapComment->getCommentText();
        $technicalMapComment
            ->setOwner($this->getUser())
            ->setTechnicalMap($technicalMap)
            ->setCommentText($comment['text'])
        ;
        $changes['comment'][] = $technicalMapComment->getCommentText();

        if (!empty($comment['reply-id'])) {
            $parentComment = $this->getTechnicalMapCommentRepository()->find($comment['reply-id']);
            $technicalMapComment->setParentComment($parentComment);
        }

        $this->logChanges($technicalMap, $changes);
        $em = $this->getDoctrine()->getManager();
        $em->persist($technicalMapComment);

        return $technicalMapComment;
    }

    /**
     * @Route("/technical-maps/{id}/change-state/{state}", name="technical_map_change_state")
     */
    public function changeStateAction(Request $request)
    {
        $state = $request->get('state');
        $technicalMapId = $request->get('id');
        /** @var TechnicalMap $technicalMap */
        $technicalMap = $this->getTechnicalMapRepository()->find($technicalMapId);

        $user = $this->getUser();

        if (!$technicalMap->checkGrants($user) && !$technicalMap->getSignatory($user)) {
            return $this->redirect($request->headers->get('referer'));
        }

        $technicalMapDetails = $request->get('technicalMap');
        $signatories = $request->get('signatories');
        $solutionId = $request->get('solutions');
        $solutions = $this->getTechnicalMapSolutionsRepository()->findBy(['technicalMap' => $technicalMapId]);
        $em = $this->getDoctrine()->getManager();

        if (array_key_exists($state, $technicalMap->getStatusList())) {
            if ($state == $technicalMap::TECHNICAL_MAP_STATUS_NEEDS_APPROVE) {
                if (!$technicalMap->isOwner($this->getUser())) {
                    return $this->redirect($request->headers->get('referer'));
                }
                $technicalMap = $this->updateSignatories($signatories, $technicalMap);

                if (!empty($technicalMapDetails['comment']['text'])) {
                    $this->addComment($technicalMap, $technicalMapDetails['comment']);
                    $this->setSolutionJustification($solutionId, $solutions, $technicalMapDetails['comment']['text']);
                }

                if (!empty($solutionId)) {
                   $this->setSolutionSelected($solutionId, $solutions);
                }
            }
            if ($state == $technicalMap::TECHNICAL_MAP_STATUS_NEEDS_FIXING) {
                if (!$technicalMap->canReturnFixing($this->getUser())) {
                    return $this->redirect($request->headers->get('referer'));
                }

                foreach ($technicalMap->getSignatories() as $signatory) {
                    $signatory->setApproved(false);
                    $em->persist($signatory);
                }

                if (!empty($technicalMapDetails['comment']['text'])) {
                    $this->addComment($technicalMap, $technicalMapDetails['comment']);
                }
            }

            $technicalMap->setStatus($state);

            $em->persist($technicalMap);

            $uof = $em->getUnitOfWork();
            $uof->computeChangeSets();

            $technicalMapChanges = $this->logChanges($technicalMap, $uof->getEntityChangeSet($technicalMap));

            $em->flush();

            if (count($technicalMapChanges)) {
                $recipients = $this->getTechnicalMapRecipients($technicalMap);
                $template = 'emails/technical_map/updated.html.twig';
                $params = [
                    'technicalMap' => $technicalMap,
                    'technicalMapChanges' => $technicalMapChanges
                ];

                foreach ($recipients as $recipient) {
                    if ($recipient instanceof User) {
                        $this->sendEmail($technicalMap->getCode(), $recipient->getEmail(), $this->renderView($template, $params));
                    }
                }
            }
        }
        return $this->redirectToRoute('technical_maps_details', ['id' => $technicalMap->getId()]);
    }


    /**
     * @param $signatories
     * @param TechnicalMap $technicalMap
     * @return TechnicalMap
     * @throws \Doctrine\ORM\ORMException
     */
    protected function updateSignatories($signatories, TechnicalMap $technicalMap)
    {
        $em = $this->getEm();

        $deletedSignatories = $this->getTechnicalMapSignatoryRepository()->findDeletedSignatories($technicalMap->getId(), $signatories);

        foreach ($deletedSignatories as $deletedSignatory) {
            $em->remove($deletedSignatory);
        }

        foreach ($signatories as $userId) {
            $signatory = $this->getTechnicalMapSignatoryRepository()->findOneBy(['technicalMap' => $technicalMap, 'signatory' => $userId]);

            if (empty($signatory)) {
                $signatoryAdd = $this->getUserRepository()->find($userId);
                $technicalMapSignatory = new TechnicalMapSignatory();
                $technicalMapSignatory
                    ->setSignatory($signatoryAdd)
                    ->setTechnicalMap($technicalMap);
                $technicalMap->addSubscriber($signatoryAdd);

                $em->persist($technicalMapSignatory);
            }
        }

        return $technicalMap;
    }

    /**
     * @Route("/technical-maps/{id}/signatory/{signatoryId}/remove", name="technical_map_remove_signatory")
     */
    public function removeTechnicalMapSignatoryAction(Request $request)
    {
        $technicalMapId = $request->get('id');
        $signatoryId = $request->get('signatoryId');

        /** @var TechnicalMap $technicalMap */
        $technicalMap = $this->getTechnicalMapRepository()->find($technicalMapId);

        if (!$technicalMap->canRemoveSignatories($this->getUser())) {
            return $this->redirect($request->headers->get('referer'));
        }

        $signatory = $this->getTechnicalMapSignatoryRepository()->find($signatoryId);

        $em = $this->getDoctrine()->getManager();
        $em->remove($signatory);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/technical-maps/{id}/change-approval/{approval}", name="change_approval_technical_map")
     */
    public function changeApprovalAction(Request $request)
    {
        $approval = $request->get('approval');
        $technicalMapId = $request->get('id');
        /** @var TechnicalMap $technicalMap */
        $technicalMap = $this->getTechnicalMapRepository()->find($technicalMapId);
        $user = $this->getUser();

        /** @var TechnicalMapSignatory $technicalMapSignatories */
        $technicalMapSignatories = $this->getTechnicalMapSignatoryRepository()->findOneBy([
            'technicalMap' => $technicalMap,
            'signatory' => $user
        ]);

        if ($approval == 'true') {
            $change['approved'] = [$technicalMapSignatories->isApproved(), true];
            $technicalMapSignatories->setApproved(true);

            $fullyApproved = true;
            foreach ($technicalMap->getSignatories() as $signatory) {
                if (!$signatory->isApproved()) {
                    $fullyApproved = false;
                    break;
                }
            }

            if ($fullyApproved) {
                $technicalMap->setStatus(TechnicalMap::TECHNICAL_MAP_STATUS_APPROVED);
            }
        } else {
            $change['approved'] = [$technicalMapSignatories->isApproved(), false];
            $technicalMapSignatories->setApproved(false);
        }

        $em = $this->getEm();
        $em->persist($technicalMapSignatories);

        $uof = $em->getUnitOfWork();
        $uof->computeChangeSets();
        $technicalMapChanges = $this->logChanges($technicalMap, $uof->getEntityChangeSet($technicalMap));

        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Upload file api.
     *
     * @Route("/technical-maps/{id}/upload", name="technical_map_upload_file")
     */
    public function uploadFileAction(Request $request)
    {
        $technicalMapId = $request->get('id');
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        /** @var TechnicalMap $technicalMap */
        $technicalMap = $this->getTechnicalMapRepository()->find($technicalMapId);

        $technicalMapFiles = $request->files->get('files');

        foreach($technicalMapFiles as $technicalMapFile) {
            try {
                $this->validateFile($technicalMapFile);

                if ($technicalMapFile instanceof UploadedFile) {
                    $file = new TechnicalMapFile();
                    $format = !(empty($technicalMapFile->guessExtension()))
                        ? $technicalMapFile->guessExtension()
                        : $technicalMapFile->getClientOriginalExtension();

                    $file
                        ->setFileName($technicalMapFile->getClientOriginalName())
                        ->setFormat($format)
                        ->setOwner($this->getUser())
                        ->setFileSize($technicalMapFile->getSize())
                        ->setTechnicalMap($technicalMap)
                        ->setUploadedAt(new \DateTime())
                    ;

                    $this->moveFile($technicalMapFile, $file, $technicalMapId);

                    $em = $this->getEm();
                    $em->persist($file);
                    $em->flush();

                    $recipients = $this->getTechnicalMapRecipients($technicalMap);
                    $template = 'emails/technical_map/add_file.html.twig';
                    $params = [
                        'technicalMap' => $technicalMap,
                        'technicalMapFile' => $file
                    ];

                    foreach ($recipients as $recipient) {
                        if ($recipient instanceof User) {
                            $this->sendEmail($technicalMap->getCode(), $recipient->getEmail(), $this->renderView($template, $params));
                        }
                    }
                }
            } catch (\Exception $exception) {
                $flashbag->add('danger', $exception->getMessage());
            }
        }

        return $this->redirect($request->headers->get('referer') . '#attachmentstab');
    }

    /**
     * @param UploadedFile $file
     * @param TechnicalMapFile $technicalMapFile
     * @param $technicalMapId
     */
    protected function moveFile(UploadedFile $file, TechnicalMapFile $technicalMapFile, $technicalMapId)
    {
        // Generate a unique name for the file before saving it
        $dirName = uniqid();
        $fileName = $file->getClientOriginalName();
        $storedFileName = $fileName;
        $technicalMapFile->setStoredFileName($storedFileName);
        $technicalMapFile->setStoredFileDir($dirName);

        // Move the file to the directory where brochures are stored

        $filePath = $this->getParameter('technical_map_files_root_dir') . '/' . $technicalMapId . '/' .
            $dirName;

        $file->move(
            $filePath,
            $fileName
        );

        if (in_array($technicalMapFile->getFormat(), ['jpg', 'jpeg', 'png'])) {
            $thumbName = $fileName .'_100x100.' . $technicalMapFile->getFormat();
            $technicalMapFile->setStoredPreviewFileName($thumbName);
            $thumb = new \Imagick($this->getParameter('technical_map_files_root_dir') . '/' . $technicalMapId . '/' . $dirName  . '/' .  $storedFileName);
            $thumb->setImageGravity(\Imagick::GRAVITY_CENTER);
            $thumb->resizeImage(200, 200, \Imagick::FILTER_LANCZOS, 1, 0);
            $thumb->cropImage(100,100, 25, 25);
            $thumb->writeImage($this->getParameter('technical_map_files_root_dir') . '/' . $technicalMapId . '/' . $dirName  . '/' . $thumbName);
        }
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
     * @Route("/technical-maps/{id}/file/{fileId}/delete", name="technical_map_delete_file")
     */
    public function deleteFileAction(Request $request)
    {
        $fileId = $request->get('fileId');

        $file = $this->getTechnicalMapFileRepository()->find($fileId);

        /** @var TechnicalMapFile $file */
        if ($file->canDeleteFile($this->getUser())) {
            $file->setDeleted(true);

            $this->getEm()->persist($file);
            $this->getEm()->flush();
        }

        return $this->redirect($request->headers->get('referer') . '#attachmentstab');
    }

    /**
     * Download file url.
     *
     * @Route("/technical-maps/{fileId}/download/{preview}", name="technical_map_download_file", defaults={"preview": 0})
     */
    public function downloadFileAction(Request $request)
    {
        $fileId = $request->get('fileId');
        $preview = $request->get('preview');

        /** @var TechnicalMapFile $technicalMapFile */
        $technicalMapFile = $this->getTechnicalMapFileRepository()->find($fileId);

        $fileName = $preview ? $technicalMapFile->getStoredPreviewFileName() : $technicalMapFile->getStoredFileName();

        $headers = [
            'Content-Type' => 'application/' . $technicalMapFile->getFormat(),
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ];

        $dir = $this->getParameter('technical_map_files_root_dir') . '/' .
            $technicalMapFile->getTechnicalMap()->getId() . '/'
        ;

        $dir .= $technicalMapFile->getStoredFileDir() ? $technicalMapFile->getStoredFileDir() . '/' : '';

        return new Response(file_get_contents($dir . $fileName), 200, $headers);
    }

    /**
     * @Route("/technical-maps/{id}/export-technical-map", name="export_technical_map")
     */
    public function exportTechnicalMapAction(Request $request)
    {
        $technicalMapId = $request->get('id');
        /** @var TechnicalMap $technicalMap */
        $technicalMap = $this->getTechnicalMapRepository()->find($technicalMapId);
        $recommendedSolution = $this->getTechnicalMapSolutionsRepository()->findOneBy(['technicalMap' => $technicalMap, 'selected' => true]);

        $exportBuilder = new TechnicalMapBuilder($this->get('translator'));
        $phpWordObject = $exportBuilder->build($technicalMap, $recommendedSolution);

        $filename = $technicalMap->getCode() . '_КВУР/КВТР' . '.docx';
        $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $filename);

        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWordObject, 'Word2007');

        $tmp = tempnam('', 'technical_map');

        $writer->save($tmp);

        $headers = [
            'Content-Type' => 'application/docx',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ];

        $response = new Response(file_get_contents($tmp), 200, $headers);

        unlink($tmp);

        return $response;
    }

    /**
     * @Route("/technical-maps/{id}/subscribe", name="technical_map_subscribe")
     */
    public function subscribeDocumentAction(Request $request)
    {
        $technicalMapId = $request->get('id');
        /** @var TechnicalMap $technicalMap */
        $technicalMap = $this->getTechnicalMapRepository()->find($technicalMapId);

        $technicalMap->addSubscriber($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($technicalMap);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/technical-maps/{id}/unsubscribe", name="technical_map_unsubscribe")
     */
    public function unsubscribeDocumentAction(Request $request)
    {
        $technicalMapId = $request->get('id');
        /** @var TechnicalMap $technicalMap */
        $technicalMap = $this->getTechnicalMapRepository()->find($technicalMapId);
        $owner = $technicalMap->getOwner()->getId();

        if ($owner != $this->getUser()->getId()) {
            $technicalMap->removeSubscriber($this->getUser());
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($technicalMap);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param $technicalMap
     * @param $changeSet
     * @return array
     */
    protected function logChanges($technicalMap, $changeSet)
    {
        $em = $this->getDoctrine()->getManager();
        $technicalMapDiffs = [];
        foreach ($changeSet as $field => $changes) {
            if ($field == 'updatedAt') {
                continue;
            }
            $oldValue = $this->prepareChangesValue($field, $changes[0]);
            $newValue = $this->prepareChangesValue($field, $changes[1]);
            if ($oldValue != $newValue && $oldValue) {
                $technicalMapDiff = new TechnicalMapDiff();

                $technicalMapDiff
                    ->setChangedBy($this->getUser())
                    ->setTechnicalMap($technicalMap)
                    ->setField($field)
                    ->setOldValue($oldValue)
                    ->setNewValue($newValue)
                    ->setUpdatedAt(new \DateTime())
                ;

                $em->persist($technicalMapDiff);
                $technicalMapDiffs[] = $technicalMapDiff;
            }
        }

        return $technicalMapDiffs;
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

        switch ($field) {
            case 'status':
                $value = TechnicalMap::getStatusList()[$value];
                break;
        }

        return $value;
    }

    /**
     * @param TechnicalMap $technicalMap
     * @return array
     */
    protected function getTechnicalMapRecipients(TechnicalMap $technicalMap)
    {
        $recipients = [];

        foreach ($technicalMap->getSubscribers() as $subscriberId) {
            $subscriber = $this->getUserRepository()->find($subscriberId);
            if ($subscriber->getId() == $this->getUser()->getId()) {
                continue;
            }

            $recipients[] = $subscriber;
        }

        return $recipients;
    }

    protected function sendEmail($title, $recipient, $body)
    {
        $email = new \Swift_Message('[OLYMP] ' . $title);
        $email
            ->setFrom('olymp@npo-at.com')
            ->setTo($recipient)
            ->setBody($body, 'text/html');

        $this->get('mailer')->send($email);
    }
}