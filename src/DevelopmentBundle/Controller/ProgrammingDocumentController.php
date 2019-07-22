<?php

namespace DevelopmentBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Exception\MaxFileSizeException;
use AppBundle\Exception\WrongFileFormatException;
use AppBundle\Repository\RepositoryAwareTrait;
use DevelopmentBundle\Entity\ProgrammingDocument;
use DevelopmentBundle\Entity\ProgrammingDocumentFile;
use DevelopmentBundle\Entity\ProgrammingDocumentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProgrammingDocumentController extends Controller
{
    use RepositoryAwareTrait;
    const PER_PAGE = 20;

    /**
     * Programming document list.
     *
     * @Route("/development/programming-document", name="programming_document_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);
        $projects = $this->getProjectRepository()->findAll();
        $orderBy = $request->get('orderBy');
        $order = $request->get('order');

        $programmingDocuments = $this->getProgrammingDocumentRepository()->getProgrammingDocumentCatalogs($filters, $orderBy, $order, $currentPage, self::PER_PAGE);
        $maxRows = $programmingDocuments->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);
        $types = $this->getProgrammingDocumentTypeRepository()->findAll();

        return $this->render('development/programming_document/list.html.twig', [
            'programmingDocuments' => $programmingDocuments,
            'projects' => $projects,
            'types' => $types,
            'codes' => ProgrammingDocument::getCodeList(),
            'filters' => $filters,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'orderBy' => $orderBy,
            'order' => $order
        ]);
    }

    /**
     * Add programming document.
     *
     * @Route("/development/programming-document/add", name="programming_document_add")
     */
    public function addAction(Request $request)
    {
        $programmingDocumentDetails = $request->get('programming_document');
        $programmingDocument = new ProgrammingDocument();

        $this->buildProgrammingDocument($programmingDocument, $programmingDocumentDetails);

        return $this->redirectToRoute('programming_document_list');
    }

    /**
     * Edit programming document.
     *
     * @Route("/development/programming-document/{id}/edit", name="programming_document_edit")
     */
    public function editAction(Request $request)
    {
        $programmingDocumentDetails = $request->get('programming_document');
        $programmingDocumentId = $request->get('id');

        $programmingDocument = $this->getProgrammingDocumentRepository()->find($programmingDocumentId);
        /** @var ProgrammingDocument $programmingDocument */
        $this->buildProgrammingDocument($programmingDocument, $programmingDocumentDetails);

        return $this->redirect($request->headers->get('referer'));
    }
    
    /**
     * @param ProgrammingDocument $programmingDocument
     * @param $programmingDocumentDetails
     * @return ProgrammingDocument
     */
    protected function buildProgrammingDocument(ProgrammingDocument $programmingDocument, $programmingDocumentDetails)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $owner */
        $owner = $this->getUserRepository()->find($programmingDocumentDetails['owner']);
        /** @var Project $project */
        $project = $this->getProjectRepository()->find($programmingDocumentDetails['project']);
        /** @var ProgrammingDocumentType $type */
        $type = $this->getProgrammingDocumentTypeRepository()->find($programmingDocumentDetails['type']);

        $programmingDocument
            ->setType($type)
            ->setCreatedAt(new \DateTime($programmingDocumentDetails['createdAt']))
            ->setNumberOfPages($programmingDocumentDetails['numberOfPages'])
            ->setCode($programmingDocumentDetails['code'])
            ->setEditionNumber($programmingDocumentDetails['editionNumber'])
            ->setRegisterNumber($programmingDocumentDetails['registerNumber'])
            ->setDocumentNumber($programmingDocumentDetails['documentNumber'])
            ->setInventoryNumber($programmingDocumentDetails['inventoryNumber'])
            ->setOwner($owner)
            ->setProject($project)
            ->setNotice($programmingDocumentDetails['notice'])
        ;

        $registerNumber = sprintf("%05d", $programmingDocumentDetails['registerNumber']);
        $documentNumber = $type->getCode() ?  sprintf('%02d', $programmingDocument->getDocumentNumber()) : '';
        $editionNumber = sprintf('%02d', $programmingDocumentDetails['editionNumber']);
        $designation = $this->get('translator')->trans($programmingDocumentDetails['code']) . '.' . $registerNumber . '-' . $editionNumber . ($type->getCode() ? ' ' . $type->getCode() : '') . ' ' . $documentNumber;

        $programmingDocument->setDesignation($designation);

        $em->persist($programmingDocument);
        $em->flush();

        return $programmingDocument;
    }

    /**
     * Upload file api.
     *
     * @Route("/development/programming-document/upload", name="programming_document_upload_file")
     * @throws \Exception
     */
    public function uploadFileAction(Request $request)
    {
        $programmingDocumentId = $request->get('programmingDocumentId');
        $fileId = $request->get('fileId');
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        /** @var ProgrammingDocument $programmingDocument */
        $programmingDocument = $this->getProgrammingDocumentRepository()->find($programmingDocumentId);
        $programmingDocumentFile = $request->files->get('programming_document_file');
        try {
            if ($programmingDocumentFile instanceof UploadedFile) {
                $this->validateFile($programmingDocumentFile);
                $file = $this->getProgrammingDocumentFileRepository()->find($fileId);
                if (empty($file)) {
                    $file = new ProgrammingDocumentFile();
                }

                $format = !(empty($programmingDocumentFile->guessExtension()))
                    ? $programmingDocumentFile->guessExtension()
                    : $programmingDocumentFile->getClientOriginalExtension();
                $file
                    ->setFileName($programmingDocumentFile->getClientOriginalName())
                    ->setFormat($format)
                    ->setOwner($this->getUser())
                    ->setFileSize($programmingDocumentFile->getSize())
                    ->setProgrammingDocument($programmingDocument)
                    ->setUploadedAt(new \DateTime());

                $programmingDocument->setFile($file);

                $this->moveProgrammingDocumentFile($programmingDocumentFile, $file, $programmingDocumentId);

                $em = $this->getEm();
                $em->persist($file);
                $em->persist($programmingDocument);
                $em->flush();
            }
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirect($request->headers->get('referer') . '#attachmentstab');
    }

    /**
     * @param UploadedFile $file
     * @param ProgrammingDocumentFile $programmingDocumentFile
     * @param $programmingDocumentId
     */
    protected function moveProgrammingDocumentFile(UploadedFile $file, ProgrammingDocumentFile $programmingDocumentFile, $programmingDocumentId)
    {
        // Generate a unique name for the file before saving it
        $dirName = uniqid();
        $fileName = $file->getClientOriginalName();
        $programmingDocumentFile->setStoredFileDir($dirName);

        $filePath = $this->getParameter('programming_document_files_root_dir') . '/' . $programmingDocumentId . '/' .
            $dirName;

        $file->move(
            $filePath,
            $fileName
        );
    }

    /**
     * Download file file url.
     *
     * @Route("/development/programming-document/{id}/download/{fileId}", name="programming_document_download_file")
     */
    public function downloadFileAction(Request $request)
    {
        $fileId = $request->get('fileId');
        $programmingDocumentId = $request->get('id');
        /** @var ProgrammingDocument $programmingDocument */
        $programmingDocument = $this->getProgrammingDocumentRepository()->find($programmingDocumentId);
        /** @var User $user */
        $user = $this->getUser();

        if ($user->canEditProgrammingDocument() or $user->getId() == $programmingDocument->getOwner()->getId()) {
            /** @var ProgrammingDocumentFile $programmingDocumentFile */
            $programmingDocumentFile = $this->getProgrammingDocumentFileRepository()->find($fileId);
        }

        $headers = [
            'Content-Type' => 'application/' . $programmingDocumentFile->getFormat(),
            'Content-Disposition' => 'inline; filename="' . $programmingDocumentFile->getFileName() . '"'
        ];

        $programmingDocumentDir = $this->getParameter('programming_document_files_root_dir') . '/' . $programmingDocumentId . '/' .
            $programmingDocumentFile->getStoredFileDir();

        return new Response(file_get_contents($programmingDocumentDir . '/' . $programmingDocumentFile->getFileName()), 200, $headers);
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
        $allowedFormats = ['doc','docx'];

        if (!in_array(substr(strrchr($file->getClientOriginalName(), '.'), 1), $allowedFormats)) {
            throw new WrongFileFormatException($this->get('translator'), $file->getClientOriginalName(), implode($allowedFormats, ', '));
        }
    }
}