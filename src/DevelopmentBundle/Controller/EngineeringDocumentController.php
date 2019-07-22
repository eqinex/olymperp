<?php

namespace DevelopmentBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Exception\MaxFileSizeException;
use DevelopmentBundle\Entity\EngineeringDocumentClassifier;
use DevelopmentBundle\Exception\NonUniqueEngineeringDocumentException;
use AppBundle\Exception\WrongFileFormatException;
use AppBundle\Repository\RepositoryAwareTrait;
use DevelopmentBundle\Entity\EngineeringDocument;
use DevelopmentBundle\Entity\EngineeringDocumentFile;
use DevelopmentBundle\Service\Import\EngineeringDocumentClassifierImport;
use DevelopmentBundle\Service\Import\EngineeringDocumentImport;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

class EngineeringDocumentController extends Controller
{
    use RepositoryAwareTrait;
    const PER_PAGE = 20;

    /**
     * Engineering document list.
     *
     * @Route("/development/engineering-document", name="engineering_document_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);
        $projects = $this->getProjectRepository()->findAll();
        $orderBy = $request->get('orderBy');
        $order = $request->get('order');

        $engineeringDocuments = $this->getEngineeringDocumentRepository()->getEngineeringDocumentCatalogs($filters, $orderBy, $order, $currentPage, self::PER_PAGE);
        $classifierClasses = $this->getEngineeringDocumentClassifierRepository()->findBy(['subgroup' => null]);

        $maxRows = $engineeringDocuments->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('development/engineering_document/list.html.twig', [
            'engineeringDocuments' => $engineeringDocuments,
            'classifierClasses' => $classifierClasses,
            'projects' => $projects,
            'filters' => $filters,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'orderBy' => $orderBy,
            'order' => $order
        ]);
    }

    /**
     * Add engineering document.
     *
     * @Route("/development/engineering-document/add", name="engineering_document_add")
     */
    public function addAction(Request $request)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $engineeringDocumentDetails = $request->get('engineering_document');
        try {
            $engineeringDocument = new EngineeringDocument();
            $this->validateEngineeringDocument($engineeringDocumentDetails, $engineeringDocument);
            $engineeringDocument = $this->buildEngineeringDocument($engineeringDocument, $engineeringDocumentDetails);

            $this->getEm()->persist($engineeringDocument);

            $this->getEm()->flush();
        } catch (NonUniqueEngineeringDocumentException $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirectToRoute('engineering_document_list');
    }

    /**
     * Edit engineering document.
     *
     * @Route("/development/engineering-document/edit", name="engineering_document_edit")
     */
    public function editAction(Request $request)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $engineeringDocumentDetails = $request->get('engineering_document');
        $engineeringDocumentId = $request->get('engineeringDocumentId');
        try {
            /** @var EngineeringDocument $engineeringDocument */
            $engineeringDocument = $this->getEngineeringDocumentRepository()->find($engineeringDocumentId);
            $this->validateEngineeringDocument($engineeringDocumentDetails, $engineeringDocument);
            $engineeringDocument = $this->buildEngineeringDocument($engineeringDocument, $engineeringDocumentDetails);

            $this->getEm()->persist($engineeringDocument);

            $this->getEm()->flush();
        } catch (NonUniqueEngineeringDocumentException $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Change engineering document
     *
     * @Route("/development/engineering-document/add-executions", name="add_engineering_document_executions")
     */
    public function addExecutionsAction(Request $request){
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $engineeringDocumentDetails = $request->get('engineering_document');

        try {
            foreach ($engineeringDocumentDetails['items'] as $engineeringDocumentData){
                $engineeringDocument = new EngineeringDocument();

                $engineeringDocumentData['code'] = $engineeringDocumentDetails['code'];
                $engineeringDocumentData['classifierCode'] = $engineeringDocumentDetails['classifierCode'];
                $engineeringDocumentData['indexNumber'] = $engineeringDocumentDetails['indexNumber'];
                $engineeringDocumentData['typeOfDocument'] = $engineeringDocumentDetails['typeOfDocument'];
                $engineeringDocumentData['decryptionCode'] = $engineeringDocumentDetails['decryptionCode'];
                $engineeringDocumentData['project'] = $engineeringDocumentDetails['project'];

                $this->validateEngineeringDocument($engineeringDocumentData, $engineeringDocument);

                $engineeringDocument = $this->buildEngineeringDocument($engineeringDocument, $engineeringDocumentData);

                $this->getEm()->persist($engineeringDocument);
            }
            $this->getEm()->flush();

        } catch (NonUniqueEngineeringDocumentException $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param EngineeringDocument $engineeringDocument
     * @param $engineeringDocumentDetails
     * @return EngineeringDocument
     */
    protected function buildEngineeringDocument(EngineeringDocument $engineeringDocument, $engineeringDocumentDetails)
    {
        $documentExecution = $engineeringDocumentDetails['documentExecution'] != null ? '-' . $engineeringDocumentDetails['documentExecution'] : '';
        $designation = $engineeringDocumentDetails['code'] . '.' . $engineeringDocumentDetails['classifierCode'] . '.' . $engineeringDocumentDetails['indexNumber'] . $documentExecution . ' ' . $engineeringDocumentDetails['typeOfDocument'];

        $owner = $this->getUserRepository()->find($engineeringDocumentDetails['owner']);
        $project = $this->getProjectRepository()->find($engineeringDocumentDetails['project']);

        $engineeringDocument
            ->setInventoryNumber($engineeringDocumentDetails['inventoryNumber'])
            ->setCreatedAt(new \DateTime($engineeringDocumentDetails['createdAt']))
            ->setDesignation($designation)
            ->setTypeOfDocument($engineeringDocumentDetails['typeOfDocument'])
            ->setNumberOfPages($engineeringDocumentDetails['numberOfPages'])
            ->setFormat($engineeringDocumentDetails['format'])
            ->setTitle($engineeringDocumentDetails['title'])
            ->setCode($engineeringDocumentDetails['code'])
            ->setClassifierCode($engineeringDocumentDetails['classifierCode'])
            ->setIndexNumber($engineeringDocumentDetails['indexNumber'])
            ->setDocumentExecution($engineeringDocumentDetails['documentExecution'])
            ->setDecryptionCode($engineeringDocumentDetails['decryptionCode'])
            ->setOwner($owner)
            ->setProject($project)
            ->setNotice($engineeringDocumentDetails['notice']);

        return $engineeringDocument;
    }

    /**
     * Get classifier ajax.
     *
     * @Route("/development/engineering-document/classifier", name="engineering_document_classifier")
     */
    public function getClassSubgroups(Request $request)
    {
        $engineeringDocumentDetails = $request->get('engineering_document');

        $filters = [
            'class' => !empty($engineeringDocumentDetails['class']) ? $engineeringDocumentDetails['class'] : substr($engineeringDocumentDetails['value'], 0,2),
            'subgroup' => !empty($engineeringDocumentDetails['value']) ? substr($engineeringDocumentDetails['value'], 2, 4) : ''
        ];

        $classifier = $this->getEngineeringDocumentClassifierRepository()->getSelectClassifier($filters);

        $subclasses = [];

        foreach ($classifier as $subclass) {
            $subclasses[] = [
                'class' => $subclass->getClass(),
                'subgroup' => $subclass->getSubgroup() ? $subclass->getSubgroup() : '',
                'description' => $subclass->getDescription()
            ];
        }

        array_shift($subclasses);

        return new JsonResponse($subclasses);
    }

    /**
     * Import request items info action.
     *
     * @Route("/development/engineering-document/import", name="engineering_document_import")
     */
    public function importEngineeringDocumentAction(Request $request)
    {
        $importFile = $request->files->get('import_items_file');
        $filePath = $this->moveFile($importFile);

        $importBuilder = new EngineeringDocumentImport($this->getDoctrine());
        $importBuilder->build($filePath);

        unlink($filePath);

        return $this->redirect($request->headers->get('referer'));
    }


    /**
     * @param UploadedFile $file
     * @return string
     * @throws \Exception
     */
    protected function moveFile(UploadedFile $file)
    {
        // Generate a unique name for the file before saving it
        $fileName = $file->getClientOriginalName();

        // Move the file to the directory where brochures are stored

        $filePath = sys_get_temp_dir() . '/' . $fileName;

        $file->move(
            sys_get_temp_dir(),
            $fileName
        );

        return $filePath;
    }

    /**
     * Upload file api.
     *
     * @Route("/development/engineering-document/upload", name="engineering_document_upload_file")
     * @throws \Exception
     */
    public function uploadFileAction(Request $request)
    {
        $engineeringDocumentId = $request->get('engineeringDocumentId');
        $fileId = $request->get('fileId');
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        /** @var EngineeringDocument $engineeringDocument */
        $engineeringDocument = $this->getEngineeringDocumentRepository()->find($engineeringDocumentId);
        $engineeringDocumentFile = $request->files->get('engineering_document_file');
        try {
            if ($engineeringDocumentFile instanceof UploadedFile) {
                $this->validateFile($engineeringDocumentFile);
                $file = $this->getEngineeringDocumentFileRepository()->find($fileId);
                if (empty($file)) {
                    $file = new EngineeringDocumentFile();
                }

                $format = !(empty($engineeringDocumentFile->guessExtension()))
                    ? $engineeringDocumentFile->guessExtension()
                    : $engineeringDocumentFile->getClientOriginalExtension();
                $file
                    ->setFileName($engineeringDocumentFile->getClientOriginalName())
                    ->setFormat($format)
                    ->setOwner($this->getUser())
                    ->setFileSize($engineeringDocumentFile->getSize())
                    ->setEngineeringDocument($engineeringDocument)
                    ->setUploadedAt(new \DateTime());

                $engineeringDocument->setFile($file);

                $this->moveEngineeringDocumentFile($engineeringDocumentFile, $file, $engineeringDocumentId);

                $em = $this->getEm();
                $em->persist($file);
                $em->persist($engineeringDocument);
                $em->flush();
            }
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirect($request->headers->get('referer') . '#attachmentstab');
    }

    /**
     * @param UploadedFile $file
     * @param EngineeringDocumentFile $engineeringDocumentFile
     * @param $engineeringDocumentId
     */
    protected function moveEngineeringDocumentFile(UploadedFile $file, EngineeringDocumentFile $engineeringDocumentFile, $engineeringDocumentId)
    {
        // Generate a unique name for the file before saving it
        $dirName = uniqid();
        $fileName = $file->getClientOriginalName();
        $engineeringDocumentFile->setStoredFileDir($dirName);

        // Move the file to the directory where brochures are stored

        $filePath = $this->getParameter('engineering_document_files_root_dir') . '/' . $engineeringDocumentId . '/' .
            $dirName;

        $file->move(
            $filePath,
            $fileName
        );
    }

    /**
     * Download file file url.
     *
     * @Route("/development/engineering-document/{engineeringDocumentId}/download/{fileId}", name="engineering_document_download_file")
     */
    public function downloadFileAction(Request $request)
    {
        $fileId = $request->get('fileId');
        $engineeringDocumentId = $request->get('engineeringDocumentId');
        /** @var EngineeringDocument $engineeringDocument */
        $engineeringDocument = $this->getEngineeringDocumentRepository()->find($engineeringDocumentId);
        /** @var User $user */
        $user = $this->getUser();

        if ($user->canEditEngineeringDocument() or $user->getId() == $engineeringDocument->getOwner()->getId()) {
            /** @var EngineeringDocumentFile $engineeringDocumentFile */
            $engineeringDocumentFile = $this->getEngineeringDocumentFileRepository()->find($fileId);
        }

        $headers = [
            'Content-Type' => 'application/' . $engineeringDocumentFile->getFormat(),
            'Content-Disposition' => 'inline; filename="' . $engineeringDocumentFile->getFileName() . '"'
        ];

        $engineeringDocumentDir = $this->getParameter('engineering_document_files_root_dir') . '/' . $engineeringDocumentId . '/' .
            $engineeringDocumentFile->getStoredFileDir();

        return new Response(file_get_contents($engineeringDocumentDir . '/' . $engineeringDocumentFile->getFileName()), 200, $headers);
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
        $allowedFormats = ['pdf'];
        $fileFormat = substr($file->getClientOriginalName(), strlen($file->getClientOriginalName()) - 3, strlen($file->getClientOriginalName()));
        if (!in_array(strtolower($fileFormat), $allowedFormats)) {
            throw new WrongFileFormatException($this->get('translator'), $file->getClientOriginalName(), implode($allowedFormats, ', '));
        }
    }

    /**
     * @param $engineeringDocumentDetails
     * @param EngineeringDocument $engineeringDocument
     * @throws NonUniqueEngineeringDocumentException
     */
    protected function validateEngineeringDocument($engineeringDocumentDetails, EngineeringDocument $engineeringDocument)
    {
        $documentExecution = $engineeringDocumentDetails['documentExecution'] != null ? '-' . $engineeringDocumentDetails['documentExecution'] : '';
        $designation = $engineeringDocumentDetails['code'] . '.' . $engineeringDocumentDetails['classifierCode'] . '.' . $engineeringDocumentDetails['indexNumber'] . $documentExecution . ' ' . $engineeringDocumentDetails['typeOfDocument'];

        $engineeringDocumentExists = $this->getEngineeringDocumentRepository()->findOneBy([
            'code' => $engineeringDocumentDetails['code'],
            'classifierCode' => $engineeringDocumentDetails['classifierCode'],
            'indexNumber' => $engineeringDocumentDetails['indexNumber'],
            'documentExecution' => $engineeringDocumentDetails['documentExecution']
        ]);

        if (!$engineeringDocumentExists) {
            $engineeringDocumentExists = $this->getEngineeringDocumentRepository()->findOneBy([
                'code' => $engineeringDocumentDetails['code'],
                'classifierCode' => $engineeringDocumentDetails['classifierCode'],
                'indexNumber' => $engineeringDocumentDetails['indexNumber'],
                'documentExecution' => null,
            ]);
        }

        if ($engineeringDocumentExists and $engineeringDocumentExists->getId() != $engineeringDocument->getId()) {
            throw new NonUniqueEngineeringDocumentException($this->get('translator'), $designation);
        }
    }

    /**
     * Engineering document classifier list.
     *
     * @Route("/development/engineering-document-classifier", name="engineering_document_classifier_list")
     */
    public function classifierListAction(Request $request)
    {
        $filters = $request->get('filters');
        $currentPage = $request->get('page', 1);

        if (!$this->getUser()->canViewEngineeringDocumentClassifier()) {
            return $this->redirectToRoute('homepage');
        }

        $classifierEngineeringDocuments = $this->getEngineeringDocumentClassifierRepository()->getEngineeringDocumentClassifier($filters, $currentPage, 50);
        $maxRows = $classifierEngineeringDocuments->count();
        $maxPages = ceil($maxRows / 50);

        return $this->render('development/engineering_document/classifier_list.html.twig', [
            'classifierEngineeringDocuments' => $classifierEngineeringDocuments,
            'filters' => $filters,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
        ]);
    }

    /**
     * @Route("/engineering-document-classifier/import", name="engineering_document_classifier_import")
     */
    public function importEngineeringDocumentClassifierAction(Request $request)
    {
        $importFile = $request->files->get('import_items_file');

        if (!$this->getUser()->canEditEngineeringDocumentClassifier()) {
            return $this->redirectToRoute('homepage');
        }

        $filePath = $this->moveFile($importFile);

        $importBuilder = new EngineeringDocumentClassifierImport($this->getDoctrine());
        $importBuilder->build($filePath);

        unlink($filePath);

        return $this->redirect($request->headers->get('referer'));
    }


}