<?php

namespace DocumentBundle\Controller;

use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use AppBundle\Exception\MaxFileSizeException;
use AppBundle\Repository\RepositoryAwareTrait;
use AppBundle\Utils\StringUtils;
use DocumentBundle\Entity\Document;
use DocumentBundle\Entity\DocumentComment;
use DocumentBundle\Entity\DocumentDiff;
use DocumentBundle\Entity\DocumentFile;
use DocumentBundle\Entity\DocumentRevision;
use DocumentBundle\Entity\DocumentSignatory;
use DocumentBundle\Service\Export\DocumentApprovalSheetBuilder;
use DocumentBundle\Service\Export\DocumentBuilder;
use DocumentBundle\Service\Export\DocumentCardBuilder;
use DocumentBundle\Service\Export\RegistryDocumentsBuilder;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class DocumentController extends Controller
{
    use RepositoryAwareTrait;

    const PER_PAGE = 20;

    /**
     * Finds and displays all documents.
     *
     * @Route("/documents", name="documents_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);
        $orderBy = $request->get('orderBy');
        $order = $request->get('order');
        $user = $this->getUser();

        $teamMembers = $this->getUserRepository()->getUsersGroupedByTeams();

        if (!$user->canViewAllDocuments()) {
            $filters['user'] = $user;
        }

        $documents = $this->getDocumentRepository()->getAvailableDocuments($filters, $user, $orderBy, $order, $currentPage, self::PER_PAGE);

        $documentTemplates = $this->getDocumentTemplateRepository()->findBy(['basic' => true]);
        $suppliers = $this->getSupplierRepository()->findAll();
        $documentCategories = $this->getDocumentCategoryRepository()->findAll();
        $users = $this->getUserRepository()->getUsersGroupedByTeams();

        $projects = $this->getProjectRepository()->getAvailableProjects($user);

        if (empty($filters['createdAt'])) {
            $maxRows = $documents->count();
            $maxPages = ceil($maxRows / self::PER_PAGE);
        }

        $document = new Document();

        return $this->render('documents/list.html.twig', [
            'documents' => empty($filters['createdAt']) ? $documents->getIterator() : $documents,
            'document' => $document,
            'users' => $users,
            'documentTemplates' => $documentTemplates,
            'documentCategories' => $documentCategories,
            'suppliers' => $suppliers,
            'statuses' => Document::getStatusList(),
            'filters' => $filters,
            'currentPage' => $currentPage,
            'maxPages' => !empty($maxPages) ? $maxPages : null,
            'maxRows' => !empty($maxRows) ? $maxRows : null ,
            'orderBy' => $orderBy,
            'order' => $order,
            'projects' => $projects,
            'teamMembers' => $teamMembers,

        ]);
    }

    /**
     * @Route("/documents/needs-approve", name="documents_need_approve_list")
     */
    public function documentsNeedApproveAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);
        $orderBy = $request->get('orderBy');
        $order = $request->get('order');
        $user = $this->getUser();

        $teamMembers = $this->getUserRepository()->getUsersGroupedByTeams();

        $filters['user'] = $this->getUser();

        $projects = $this->getProjectRepository()->getAvailableProjects($user);
        $documents = $this->getDocumentRepository()->getNeedsApproveDocuments($filters, $orderBy, $order, $currentPage, self::PER_PAGE);
        $documentTemplates = $this->getDocumentTemplateRepository()->findAll();
        $suppliers = $this->getSupplierRepository()->findAll();

        $maxRows = $documents->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('documents/list.html.twig', [
            'documents' => $documents->getIterator(),
            'statuses' => Document::getStatusList(),
            'suppliers' => $suppliers,
            'documentTemplates' => $documentTemplates,
            'filters' => $filters,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'orderBy' => $orderBy,
            'order' => $order,
            'projects' => $projects,
            'teamMembers' => $teamMembers,
        ]);
    }

    /**
     * New Document.
     *
     * @Route("/documents/new", name="add_document")
     */
    public function addDocumentAction(Request $request)
    {
        $documentDetails = $request->get('document');

        $document = new Document();

        $document = $this->buildDocument($document, $documentDetails);

        $team = $this->getUser()->getTeam();
        /** @var Team $team */
        if (!$team) {
            $team = $this->getTeamRepository()->findOneBy(['code' => 'АТ']);
        }

        $em = $this->getEm();

        $document->setCode(uniqid('FN'));
        $em->persist($document);
        $em->flush();
        $em->refresh($document);

        $code = $document->getId() . '.' .
            'AT' . '.' .
            date('y') . '.' .
            $document->getDocumentTemplate()->getCode() . '.' .
            $team->getCode()
        ;

        if ($document->getProject()->getCode() && $document->getProject()->getCode() != $team->getCode()) {
            $code = $code . '.' . $document->getProject()->getCode();
        }

        $document->setCode($code);
        $em->persist($document);
        $em->flush();

        return $this->redirectToRoute('documents_list');
    }

    /**
     * @Route("/documents/{id}/edit", name="edit_document")
     */
    public function editDocumentAction (Request $request)
    {
        $documentId = $request->get('id');
        $document = $this->getDocumentRepository()->find($documentId);
        $documentDetails = $request->get('document');
        $project = $this->getProjectRepository()->find($documentDetails['project']);

        $document = $this->buildDocument($document, $documentDetails);

        if ($document->isNew()  || $document->isNeedsFixing()) {
            $team = $this->getUser()->getTeam();
            /** @var Team $team */
            if (!$team) {
                $team = $this->getTeamRepository()->findOneBy(['code' => 'АТ']);
            }

            $countDocumentTemplateSupplementary = '';

            if ($document->getDocumentTemplateSupplementary() != null) {
                $countDocumentTemplateSupplementary = $this->getDocumentRepository()->getDocumentTemplateSupplementaryCounter($document);
            }

            $document->regenerateCode($team, $project, $countDocumentTemplateSupplementary);

        }

        $em = $this->getEm();
        $em->persist($document);

        $uof = $em->getUnitOfWork();
        $uof->computeChangeSets();

        $this->logChanges($document, $uof->getEntityChangeSet($document));
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/documents/{id}/add-debtreceivable", name="add_debreceivable")
     */
    public function addDebtreceivableAction (Request $request)
    {
        $documentId = $request->get('id');
        /** @var Document $document */
        $document = $this->getDocumentRepository()->find($documentId);
        $documentDetails = $request->get('document');

        $document->setDebtReceivable(new \DateTime($documentDetails['debtReceivable']));

        $em = $this->getEm();
        $em->persist($document);

        $uof = $em->getUnitOfWork();
        $uof->computeChangeSets();

        $this->logChanges($document, $uof->getEntityChangeSet($document));
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/documents/{id}/add-agreement", name="add-agreement")
     */
    public function addAgreementAction(Request $request)
    {
        $documentId = $request->get('id');
        $parentDocument = $this->getDocumentRepository()->find($documentId);
        $documentDetails = $request->get('document');

        $document = new Document();

        $parentDocument->getParentDocument() ? $document->setParentDocument($parentDocument->getParentDocument()) : $document->setParentDocument($parentDocument);
        $document = $this->buildDocument($document, $documentDetails);

        $team = $this->getUser()->getTeam();
        /** @var Team $team */
        if (!$team) {
            $team = $this->getTeamRepository()->findOneBy(['code' => 'АТ']);
        }

        $em = $this->getEm();

        $document->setCode(uniqid('FN'));
        $em->persist($document);
        $em->flush();
        $em->refresh($document);

        $code = $document->getId() . '.' .
            'AT' . '.' .
            date('y') . '.' .
            $document->getDocumentTemplate()->getCode() . '.' .
            $team->getCode()
        ;

        if ($document->getProject()->getCode() && $document->getProject()->getCode() != $team->getCode()) {
            $code = $code . '.' . $document->getProject()->getCode();
        }

        if ($document->getDocumentTemplateSupplementary() != null) {
            $countDocumentTemplateSupplementary = $this->getDocumentRepository()->getDocumentTemplateSupplementaryCounter($document);
            $code = $code . '.' . $document->getDocumentTemplateSupplementary()->getCode() . $countDocumentTemplateSupplementary;
        }

        $document->setCode($code);
        $em->persist($document);
        $em->flush();

        return $this->redirectToRoute('document_details', ['id' => $document->getId()]);
    }

    /**
     * Finds and displays a Document entity.
     *
     * @Route("/documents/{id}/details", name="document_details")
     */
    public function detailsAction(Request $request)
    {
        $documentId = $request->get('id');
        /** @var Document $document */
        $document = $this->getDocumentRepository()->find($documentId);

        if (!$document->checkGrants($this->getUser())) {
            return $this->redirect($request->headers->get('referer'));
        }

        $documentTemplates = $this->getDocumentTemplateRepository()->findBy(['basic' => true]);
        $supplementaryDocumentTemplates = $this->getDocumentTemplateRepository()->findBy(['basic' => false]);
        $suppliers = $this->getSupplierRepository()->findAll();
        $documentCategories = $this->getDocumentCategoryRepository()->findAll();
        $users = $this->getUserRepository()->getUsersGroupedByTeams();
        $supplementaryAgreements = $this->getDocumentRepository()->findBy([
            'parentDocument' => $document
        ]);

        $documentChanges = $this->getDocumentDiffRepository()->getDocumentChanges($document);

        $documentComments = $this->getDocumentCommentRepository()->findBy([
            'document' => $document
        ]);
        $fileParams = [
            'document' => $document,
            'deleted' => 0
        ];
        $documentFiles = $this->getDocumentFileRepository()->findBy($fileParams);

        return $this->render('documents/details.html.twig', [
            'document' => $document,
            'documentComments' => $documentComments,
            'users' => $users,
            'documentTemplates' => $documentTemplates,
            'supplementaryDocumentTemplates' => $supplementaryDocumentTemplates,
            'documentCategories' => $documentCategories,
            'suppliers' => $suppliers,
            'documentChanges' => $documentChanges,
            'documentFiles' => $documentFiles,
            'supplementaryAgreements' => $supplementaryAgreements,
        ]);
    }

    /**
     * @Route("/documents/{id}/export-document-card", name="export_document_card")
     */
    public function exportDocumentCardAction(Request $request)
    {
        $documentId = $request->get('id');
        $document = $this->getDocumentRepository()->find($documentId);
        $supplementaryAgreements = $this->getDocumentRepository()->findBy([
            'parentDocument' => $document
        ]);

        $exportBuilder = new DocumentCardBuilder($this->get('translator'));
        $phpWordObject = $exportBuilder->build($document, $supplementaryAgreements);

        $filename = 'Карточка договора ' . $document->getCode() . '.docx';
        $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $filename);

        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWordObject, 'Word2007');

        $tmp = tempnam('', 'document');

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
     * @Route("/documents/{id}/export-document", name="export_document")
     */
    public function exportDocumentAction(Request $request)
    {
        $documentId = $request->get('id');
        /** @var Document $document */
        $document = $this->getDocumentRepository()->find($documentId);
        $user = $this->getUser();

        if (!$document->canExportDocument($user)) {
            return $this->redirect($request->headers->get('referer'));
        }

        /** @var DocumentRevision $lastRevision */
        $lastRevision = $this->getDocumentRevisionRepository()->findOneBy(['document' => $documentId], ['version' => 'DESC']);

        if (!$lastRevision) {
            return $this->redirect($request->headers->get('referer'));
        }

        $exportBuilder = new DocumentBuilder($this->get('translator'));
        $phpWordObject = $exportBuilder->build($document, $lastRevision);

        $filename = $document->getCode() . ' Версия ' . $lastRevision->getVersion() . '.docx';
        $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $filename);

        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWordObject, 'Word2007');

        $tmp = tempnam('', 'document');

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
     * @Route("/documents/{id}/export-document-approval-sheet", name="export_document_approval_sheet")
     */
    public function exportApprovalSheetAction(Request $request)
    {
        $documentId = $request->get('id');
        /** @var Document $document */
        $document = $this->getDocumentRepository()->find($documentId);

        if (!$document->isApproved() && !$document->isApproved()) {
            return $this->redirect($request->headers->get('referer'));
        }

        $exportBuilder = new DocumentApprovalSheetBuilder($this->get('translator'));
        $phpWordObject = $exportBuilder->build($document);

        $filename = 'Лист согласования ' . $document->getCode() . '.docx';
        $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $filename);

        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWordObject, 'Word2007');

        $tmp = tempnam('', 'approval-sheet');

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
     * @param Document $document
     * @param $documentDetails
     * @return Document
     * @throws \Doctrine\ORM\ORMException
     */
    protected function buildDocument(Document $document, $documentDetails)
    {
        $user = $this->getUser();

        $project = $this->getProjectRepository()->find($documentDetails['project']);
        $template = $this->getDocumentTemplateRepository()->find($documentDetails['template']);
        $supplier = $this->getSupplierRepository()->find($documentDetails['supplier']);
        $category = $this->getDocumentCategoryRepository()->find($documentDetails['category']);
        $curator = $this->getUserRepository()->find($documentDetails['curator']);

        if (!$document->getId()) {
            $document
                ->setOwner($user)
                ->setCreatedAt(new \DateTime())
            ;
            $document = $this->generateDocumentOneSUniqueCode($document);
        }

        $document
            ->setProject($project)
            ->setCategory($category)
            ->setDocumentTemplate($template)
            ->setSupplier($supplier)
            ->setType($documentDetails['type'])
            ->setPeriod($documentDetails['period'])
            ->setAmount($documentDetails['amount'])
            ->setVat($documentDetails['vat'])
            ->setCurator($curator)
            ->setUpdatedAt(new \DateTime())
            ->setStartAt(new \DateTime($documentDetails['startAt']))
            ->setEndAt(new \DateTime($documentDetails['endAt']))
            ->setUnLimited(!empty($documentDetails['unlimited']) ? true : false)
            ->setSubject($documentDetails['subject'])
            ->setContractExtension(!empty($documentDetails['extension']) ? true : false)
            ->setMeasureOfResponsibility($documentDetails['measure'])
            ->setSecurity($documentDetails['security'])
            ->setSupplierContractCode($documentDetails['supplierContractCode'])
            ->setAct($documentDetails['act'])
            ->setComment($documentDetails['comment'])
        ;

        $document
            ->addSubscriber($this->getUser())
            ->addSubscriber($document->getCurator())
            ->addSubscriber($document->getProject()->getLeader())
        ;

        if (!empty($documentDetails['supplementaryTemplate'])) {
            $supplementaryTemplate = $this->getDocumentTemplateRepository()->find($documentDetails['supplementaryTemplate']);
            $document->setDocumentTemplateSupplementary($supplementaryTemplate);
        }

        return $document;
    }

    /**
     * Edit document content
     *
     * @Route("/documents/{id}/edit-content", name="document_edit_content")
     */
    public function editContentAction(Request $request)
    {
        $documentId = $request->get('id');
        $content = $request->get('content');

        /** @var Document $document */
        $document = $this->getDocumentRepository()->find($documentId);

        $revision = new DocumentRevision();
        $changes['version'][] = $document->getLastRevision() ? $document->getLastRevision()->getVersion() : false;

        $search = ['<o:p>', '</o:p>', '<br>'];
        $replace = ['', '', '<br/>'];
        $content = str_replace($search, $replace, $content);

        $pattern = ['/style=\"[^\"]+\"/i'];
        $replacement = [''];

        $content = preg_replace($pattern, $replacement, $content);

        $revision
            ->setOwner($this->getUser())
            ->setContent($content)
            ->setVersion($document->getLastRevision() ? $document->getLastRevision()->getVersion() + 1 : 1)
            ->setDocument($document)
            ->setPreviousRevision($document->getLastRevision())
        ;
        $changes['version'][] = $document->getLastRevision() ? $document->getLastRevision()->getVersion() + 1 : 1;
        $document->setLastRevision($revision);

        $this->logChanges($document, $changes);
        $this->getEm()->persist($document);
        $this->getEm()->flush();

        $recipients = $this->getDocumentRecipients($document);
        $template = 'emails/documents/revisions.html.twig';
        $params = [
            'document' => $document,
            'revision' => $revision
        ];

        foreach ($recipients as $recipient) {
            if ($recipient instanceof User) {
                $this->sendEmail('{' . $this->get('translator')->trans('Contract') . '} ' . $document->getCode(), $recipient->getEmail(), $this->renderView($template, $params));
            }
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Add comment to Document.
     *
     * @Route("/documents/{id}/comment", name="document_add_comment")
     */
    public function commentAction(Request $request)
    {
        $comment = $request->get('comment');
        $documentId = $request->get('id');

        /** @var Document $document */
        $document = $this->getDocumentRepository()->find($documentId);

        $documentComment = $this->addComment($document, $comment);

        $document->addSubscriber($this->getUser());

        $em = $this->getEm();
        $em->flush();

        $recipients = $this->getDocumentRecipients($document);
        $template = 'emails/documents/comment.html.twig';
        $params = [
            'document' => $document,
            'documentComment' => $documentComment
        ];

        foreach ($recipients as $recipient) {
            if ($recipient instanceof User) {
                $this->sendEmail('{' . $this->get('translator')->trans('Contract') . '} ' . $document->getCode(), $recipient->getEmail(), $this->renderView($template, $params));
            }
        }

        return $this->redirectToRoute('document_details', ['id' => $document->getId()]);
    }

    /**
     * @param Document $document
     * @param $comment
     * @return DocumentComment
     */
    protected function addComment(Document $document, $comment)
    {
        $documentComment = new DocumentComment();
        $documentComment->setCreatedAt(new \DateTime());
        $changes = ['comment' => []];

        if (!empty($comment['id'])) {
            $documentComment = $this->getDocumentCommentRepository()->findOneBy([
                'id' => $comment['id'],
                'owner' => $this->getUser()->getId()
            ]) ?: $documentComment;
        }

        $changes['comment'][] = $documentComment->getCommentText();
        $documentComment
            ->setOwner($this->getUser())
            ->setDocument($document)
            ->setCommentText(StringUtils::parseLinks($comment['text']))
        ;
        $changes['comment'][] = $documentComment->getCommentText();

        if (!empty($comment['reply-id'])) {
            $parentComment = $this->getDocumentCommentRepository()->find($comment['reply-id']);
            $documentComment->setParentComment($parentComment);
        }

        $this->logChanges($document, $changes);
        $em = $this->getDoctrine()->getManager();
        $em->persist($documentComment);

        return $documentComment;
    }


    /**
     * Documents subscribe
     *
     * @Route("/documents/{id}/subscribe", name="document_subscribe")
     */
    public function subscribeDocumentAction(Request $request)
    {
        $documentId = $request->get('id');
        $document = $this->getDocumentRepository()->find($documentId);

        $document->addSubscriber($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($document);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Documents unsubscribe
     *
     * @Route("/documents/{id}/unsubscribe", name="document_unsubscribe")
     */
    public function unsubscribeDocumentAction(Request $request)
    {
        $documentId = $request->get('id');
        $document = $this->getDocumentRepository()->find($documentId);
        $owner = $document->getOwner()->getId();
        $curator = $document->getCurator()->getId();
        $projectLeader = $document->getProject()->getLeader()->getId();

        if ($owner != $this->getUser()->getId() && $curator != $this->getUser()->getId() && $projectLeader != $this->getUser()->getId()) {
            $document->removeSubscriber($this->getUser());
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($document);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Remove document signatory
     *
     * @Route("/documents/{id}/signatory/{signatoryId}/remove", name="document_remove_signatory")
     */
    public function removeDocumentSignatoryAction(Request $request)
    {
        $documentId = $request->get('id');
        $signatoryId = $request->get('signatoryId');

        /** @var Document $document */
        $document = $this->getDocumentRepository()->find($documentId);

        if (!$document->canRemoveSignatories($this->getUser())) {
            return $this->redirect($request->headers->get('referer'));
        }

        $signatory = $this->getDocumentSignatoryRepository()->find($signatoryId);

        $em = $this->getDoctrine()->getManager();
        $em->remove($signatory);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/documents/{id}/version/{versionId}", name="version_changes")
     */
    public function revisionDocumentAction(Request $request)
    {
        $documentId = $request->get('id');
        $document = $this->getDocumentRepository()->find($documentId);
        $versionId = $request->get('versionId');

        $versions = $this->getDocumentRevisionRepository()->findOneBy([
            'document' => $document,
            'version' => $versionId
        ]);

        return $this->render('documents/partial/versions_changes.html.twig', [
            'document' => $document,
            'versions' => $versions,
        ]);
    }

    /**
     * @Route("/documents/{id}/change-state/{state}", name="document_change_state")
     */
    public function changeStateAction(Request $request)
    {
        $state = $request->get('state');
        $documentId = $request->get('id');
        /** @var Document $document */
        $document = $this->getDocumentRepository()->find($documentId);
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $signatories = $request->get('signatories');
        $documentDetails = $request->get('document');

        if (!empty($documentDetails['comment']['text'])) {
            $this->addComment($document, $documentDetails['comment']);
        }

        if (array_key_exists($state, $document->getStatusList())) {

            if ($state == DOCUMENT::DOCUMENT_STATUS_NEEDS_APPROVE) {
                if (!$document->isOwner($user)) {
                    return $this->redirect($request->headers->get('referer'));
                }

                /** @var Document $document */
                $document = $this->updateSignatories($signatories, $document, $documentId);
                $em->flush();

                $recipients = $this->getDocumentSignatoryRecipients($document->getSignatories());
                $template = 'emails/documents/signatory.html.twig';
                $params = [
                    'document' => $document,
                ];

                foreach ($recipients as $recipient) {
                    if ($recipient instanceof User) {
                        $this->sendEmail('{' . $this->get('translator')->trans('Contract') . '} ' . $document->getCode(), $recipient->getEmail(), $this->renderView($template, $params));
                    }
                }
            }

            if ($state == Document::DOCUMENT_STATUS_CANCELLED) {

                if (!$document->isOwner($user)) {
                    return $this->redirect($request->headers->get('referer'));
                }

                $recipients = $this->getDocumentRecipients($document);
                $template = 'emails/documents/document_cancel.html.twig';
                $params = [
                    'document' => $document,
                ];

                foreach ($recipients as $recipient) {
                    if ($recipient instanceof User) {
                        $this->sendEmail('{' . $this->get('translator')->trans('Contract') . '} ' . $document->getCode(), $recipient->getEmail(), $this->renderView($template, $params));
                    }
                }
            }

            if ($state == Document::DOCUMENT_STATUS_NEEDS_FIXING) {

                if (!$document->canReturnFixing($user)) {
                    return $this->redirect($request->headers->get('referer'));
                }

                foreach ($document->getSignatories() as $signatory) {
                    $signatory->setApproved(false);
                    $em->persist($signatory);
                }
            }

            if ($state == Document::DOCUMENT_STATUS_REGISTERED) {
                if (!$document->checkGrants($user)) {
                    return $this->redirect($request->headers->get('referer'));
                }

                $recipients = $this->getDocumentSignatoryRecipients($document->getSignatories());
                $template = 'emails/documents/document_registered.html.twig';
                $params = [
                    'document' => $document,
                ];

                foreach ($recipients as $recipient) {
                    if ($recipient instanceof User) {
                        $this->sendEmail('{' . $this->get('translator')->trans('Contract') . '} ' . $document->getCode(), $recipient->getEmail(), $this->renderView($template, $params));
                    }
                }
            }

            $document->setStatus($state);
            $em->persist($document);
            $em->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Document Signatory.
     *
     * @Route("/documents/{id}/approve-contract", name="approve_contract")
     */
    public function approveContractAction(Request $request)
    {
        $documentId = $request->get('id');
        $signatoryRemarks = $request->get('remarks');
        /** @var Document $document */
        $document = $this->getDocumentRepository()->find($documentId);
        $user = $this->getUser();

        /** @var DocumentSignatory $documentSignatories */
        $documentSignatories = $this->getDocumentSignatoryRepository()->findOneBy([
            'document' => $document,
            'signatory' => $user
        ]);

        $change['approved'] = [$documentSignatories->isApproved(), true];
        $documentSignatories
            ->setApproved(true)
            ->setApprovedAt(new \DateTime())
            ->setRemarks($signatoryRemarks['comment']['text']);
        $fullyApproved = true;
        foreach ($document->getSignatories() as $signatory) {
            if (!$signatory->isApproved()) {
                $fullyApproved = false;
                break;
            }
        }

        if ($fullyApproved) {
            $document->setStatus(Document::DOCUMENT_STATUS_APPROVED);

            $recipients = $this->getDocumentRecipients($document);
            $template = 'emails/documents/document_approved.html.twig';
            $params = [
                'document' => $document,
                'documentSignatories' => $documentSignatories
            ];

            foreach ($recipients as $recipient) {
                if ($recipient instanceof User) {
                    $this->sendEmail('{' . $this->get('translator')->trans('Contract') . '} ' . $document->getCode(), $recipient->getEmail(), $this->renderView($template, $params));
                }
            }
        }
        $this->logChanges($document, $change);
        $em = $this->getEm();
        $em->persist($documentSignatories);
        $em->flush();

        $recipients = $this->getDocumentRecipients($document);
        $template = 'emails/documents/signatory_change_approved.html.twig';
        $params = [
            'document' => $document,
            'documentSignatories' => $documentSignatories
        ];

        foreach ($recipients as $recipient) {
            if ($recipient instanceof User) {
                $this->sendEmail('{' . $this->get('translator')->trans('Contract') . '} ' . $document->getCode(), $recipient->getEmail(), $this->renderView($template, $params));
            }
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Document Signatory.
     *
     * @Route("/documents/{id}/disapprove-contract", name="disapprove_contract")
     */
    public function disapproveContractAction(Request $request)
    {
        $documentId = $request->get('id');
        $signatoryRemarks = $request->get('remarks');
        $document = $this->getDocumentRepository()->find($documentId);
        $user = $this->getUser();

        /** @var DocumentSignatory $documentSignatories */
        $documentSignatories = $this->getDocumentSignatoryRepository()->findOneBy([
            'document' => $document,
            'signatory' => $user
        ]);
        $change['approved'] = [$documentSignatories->isApproved(), false];

        $documentSignatories
            ->setApproved(false)
            ->setApprovedAt(null)
            ->setRemarks($signatoryRemarks['comment']['text']);

        $this->logChanges($document, $change);
        $em = $this->getEm();
        $em->persist($documentSignatories);
        $em->flush();

        $recipients = $this->getDocumentRecipients($document);
        $template = 'emails/documents/signatory_change_approved.html.twig';
        $params = [
            'document' => $document,
            'documentSignatories' => $documentSignatories
        ];

        foreach ($recipients as $recipient) {
            if ($recipient instanceof User) {
                $this->sendEmail('{' . $this->get('translator')->trans('Contract') . '} ' . $document->getCode(), $recipient->getEmail(), $this->renderView($template, $params));
            }
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Document Signatory.
     *
     * @Route("/documents/{id}/edit-remarks", name="edit_remarks")
     */
    public function editRemarks(Request $request){
        $documentId = $request->get('id');
        $signatoryRemarks = $request->get('remarks');

        $search = ['<o:p>', '</o:p>', '<br>'];
        $replace = ['', '', '<br/>'];
        $signatoryRemarks = str_replace($search, $replace, $signatoryRemarks);

        $pattern = ['/style=\"[^\"]+\"/i'];
        $replacement = [''];

        $signatoryRemarks = preg_replace($pattern, $replacement, $signatoryRemarks);

        /** @var Document $document */
        $document = $this->getDocumentRepository()->find($documentId);
        $user = $this->getUser();

        /** @var DocumentSignatory $documentSignatories */
        $documentSignatories = $this->getDocumentSignatoryRepository()->findOneBy([
            'document' => $document,
            'signatory' => $user
        ]);

        $documentSignatories
            ->setRemarks(htmlspecialchars_decode(trim(strip_tags($signatoryRemarks))));

        $em = $this->getEm();
        $em->persist($documentSignatories);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Document Signatory.
     *
     * @Route("/documents/{id}/add-signatories", name="document_add_signatories")
     */
    public function addSignatoriesAction(Request $request)
    {
        $documentId = $request->get('id');
        /** @var Document $document */
        $document = $this->getDocumentRepository()->find($documentId);
        $signatories = $request->get('signatories');

        if (!$document->isOwner($this->getUser())) {
            return $this->redirect($request->headers->get('referer'));
        }

        $em = $this->getEm();

        $document = $this->updateSignatories($signatories, $document, $documentId);

        if ($document->getStatus() == Document::DOCUMENT_STATUS_APPROVED) {
            $document->setStatus(Document::DOCUMENT_STATUS_NEEDS_APPROVE);
        }

        $em->flush();

        $recipients = $this->getDocumentSignatoryRecipients($document->getSignatories());
        $template = 'emails/documents/signatory.html.twig';
        $params = [
            'document' => $document,
        ];

        foreach ($recipients as $recipient) {
            if ($recipient instanceof User) {
                $this->sendEmail('{' . $this->get('translator')->trans('Contract') . '} ' . $document->getCode(), $recipient->getEmail(), $this->renderView($template, $params));
            }
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param $signatories
     * @param Document $document
     * @param $documentId
     * @return Document
     * @throws \Exception
     */
    protected function updateSignatories($signatories, Document $document, $documentId)
    {
        $em = $this->getEm();

        $deletedSignatories = $this->getDocumentSignatoryRepository()->findDeletedSignatories($documentId, $signatories);

        foreach ($deletedSignatories as $deletedSignatory) {
            $em->remove($deletedSignatory);
        }

        foreach ($signatories as $userId) {
            $signatory = $this->getDocumentSignatoryRepository()->findOneBy(['document' => $documentId, 'signatory' => $userId]);

            if (empty($signatory)) {
                $signatoryAdd = $this->getUserRepository()->find($userId);
                $documentSignatory = new DocumentSignatory();
                $documentSignatory
                    ->setSignatory($signatoryAdd)
                    ->setDocument($document);
                $document->addSubscriber($signatoryAdd);

                $em->persist($documentSignatory);
            }
        }

        return $document;
    }

    /**
     * Upload file api.
     *
     * @Route("/documents/{id}/upload", name="document_upload_file")
     */
    public function uploadFileAction(Request $request)
    {
        $documentId = $request->get('id');
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        /** @var Document $document */
        $document = $this->getDocumentRepository()->find($documentId);

        $documentFiles = $request->files->get('files');

        foreach($documentFiles as $documentFile) {
            try {
                $this->validateFile($documentFile);

                if ($documentFile instanceof UploadedFile) {
                    $file = new DocumentFile();
                    $format = !(empty($documentFile->guessExtension()))
                        ? $documentFile->guessExtension()
                        : $documentFile->getClientOriginalExtension();

                    $file
                        ->setFileName($documentFile->getClientOriginalName())
                        ->setFormat($format)
                        ->setOwner($this->getUser())
                        ->setFileSize($documentFile->getSize())
                        ->setDocument($document)
                        ->setUploadedAt(new \DateTime())
                    ;

                    $this->moveFile($documentFile, $file, $documentId);

                    $em = $this->getEm();
                    $em->persist($file);
                    $em->flush();
                }
            } catch (\Exception $exception) {
                $flashbag->add('danger', $exception->getMessage());
            }
        }

        return $this->redirect($request->headers->get('referer') . '#attachmentstab');
    }

    /**
     * Delete file action.
     *
     * @Route("/documents/{id}/file/{fileId}/delete", name="document_delete_file")
     */
    public function deleteFileAction(Request $request)
    {
        $fileId = $request->get('fileId');

        $file = $this->getDocumentFileRepository()->find($fileId);

        /** @var DocumentFile $file */
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
     * @Route("/documents/{fileId}/download/{preview}", name="document_download_file", defaults={"preview": 0})
     */
    public function downloadFileAction(Request $request)
    {
        $fileId = $request->get('fileId');
        $preview = $request->get('preview');

        /** @var DocumentFile $documentFile */
        $documentFile = $this->getDocumentFileRepository()->find($fileId);

        $fileName = $preview ? $documentFile->getStoredPreviewFileName() : $documentFile->getStoredFileName();

        $headers = [
            'Content-Type' => 'application/' . $documentFile->getFormat(),
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ];

        $purchaseDir = $this->getParameter('document_files_root_dir') . '/' .
            $documentFile->getDocument()->getId() . '/'
        ;

        $purchaseDir .= $documentFile->getStoredFileDir() ? $documentFile->getStoredFileDir() . '/' : '';

        return new Response(file_get_contents($purchaseDir . $fileName), 200, $headers);
    }

    /**
     * @param $document
     * @param $changeSet
     * @return array
     */
    protected function logChanges($document, $changeSet)
    {
        $em = $this->getDoctrine()->getManager();
        $documentDiffs = [];
        foreach ($changeSet as $field => $changes) {
            if ($field == 'updatedAt') {
                continue;
            }
            $oldValue = $this->prepareChangesValue($field, $changes[0]);
            $newValue = $this->prepareChangesValue($field, $changes[1]);
            if ($oldValue != $newValue && $oldValue) {
                $documentDiff = new DocumentDiff();

                $documentDiff
                    ->setChangedBy($this->getUser())
                    ->setDocument($document)
                    ->setField($field)
                    ->setOldValue($oldValue)
                    ->setNewValue($newValue)
                    ->setUpdatedAt(new \DateTime())
                ;

                $em->persist($documentDiff);
                $documentDiffs[] = $documentDiff;
            }
        }

        return $documentDiffs;
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
        } elseif ($field == 'vat' && $value == 0) {
            $value = 'Without VAT';
        } elseif (!$value) {
            $value = 'No';
        } elseif ($value === true) {
            $value = 'Yes';
        }

        return $value;
    }

    /**
     * @param DocumentSignatory[] $documentSignatories
     * @return array
     */
    protected function getDocumentSignatoryRecipients($documentSignatories)
    {
        $recipients = [];

        foreach ($documentSignatories as $signatory) {
            if ($signatory->getSignatory()->getId() == $this->getUser()->getId()) {
                continue;
            }

            $recipients[] = $signatory->getSignatory();
        }

        return $recipients;
    }

    /**
     * @param Document $document
     * @return array
     */
    protected function getDocumentRecipients(Document $document)
    {
        $recipients = [];

        foreach ($document->getSubscribers() as $subscriber) {
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

    /**
     * @param UploadedFile $file
     * @param DocumentFile $documentFile
     * @param $documentId
     * @return string
     * @throws \Exception
     */
    protected function moveFile(UploadedFile $file, DocumentFile $documentFile, $documentId)
    {
        // Generate a unique name for the file before saving it
        $dirName = uniqid();
        $fileName = $file->getClientOriginalName();
        $storedFileName = $fileName;
        $documentFile->setStoredFileName($storedFileName);
        $documentFile->setStoredFileDir($dirName);

        // Move the file to the directory where brochures are stored

        $filePath = $this->getParameter('document_files_root_dir') . '/' . $documentId . '/' .
            $dirName;

        $file->move(
            $filePath,
            $fileName
        );

        if (in_array($documentFile->getFormat(), ['jpg', 'jpeg', 'png'])) {
            $thumbName = $fileName .'_100x100.' . $documentFile->getFormat();
            $documentFile->setStoredPreviewFileName($thumbName);
            $thumb = new \Imagick($this->getParameter('document_files_root_dir') . '/' . $documentId . '/' . $dirName  . '/' .  $storedFileName);
            $thumb->setImageGravity(\Imagick::GRAVITY_CENTER);
            $thumb->resizeImage(200, 200, \Imagick::FILTER_LANCZOS, 1, 0);
            $thumb->cropImage(100,100, 25, 25);
            $thumb->writeImage($this->getParameter('document_files_root_dir') . '/' . $documentId . '/' . $dirName  . '/' . $thumbName);
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
     * @Route("/documents/export-registry-documents", name="export_registry_documents")
     */
    public function exportRegistryDocumentsAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $orderBy = $request->get('orderBy');
        $order = $request->get('order');
        $user = $this->getUser();

        if (!$user->canViewAllDocuments()) {
            $filters['user'] = $user;
        }

        $documents = $this->getDocumentRepository()->getDocuments($filters, $user, $orderBy, $order);

        $exportBuilder = new RegistryDocumentsBuilder($this->get('phpexcel'), $this->get('translator'), $user);

        $phpExcelObject = $exportBuilder->build($documents);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding header
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'registry_documents.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @param Document $document
     * @return Document
     * @throws \Doctrine\ORM\ORMException
     */
    protected function generateDocumentOneSUniqueCode(Document $document)
    {
        do {
            $code = Uuid::uuid4()->toString();
            $documentDuplicate = $this->getDocumentRepository()->findOneBy(['oneSUniqueCode' => $code]);
            if (!$documentDuplicate) {
                $document->setOneSUniqueCode($code);
                $check = true;
            } else {
                $check = false;
            }

        } while ($check == false);

        $this->getEm()->persist($document);

        return $document;
    }
}