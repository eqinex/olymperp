<?php

namespace PurchaseBundle\Controller;

use AppBundle\Utils\StringUtils;
use Symfony\Component\Translation\TranslatorInterface;
use PurchaseBundle\Service\Export\RentBuilder;
use AppBundle\Repository\RepositoryAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use PHPExcel_IOFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use AppBundle\Entity\User;
use PurchaseBundle\Entity\Rent;

class RentController extends Controller
{
    use RepositoryAwareTrait;

    const PER_PAGE = 20;

    /**
     * List action.
     *
     * @Route("/rent", name="rent_list")
     */
    public function listAction(Request $request)
    {
        if (!$this->getUser()->canViewRent())
        {
            return $this->redirectToRoute('homepage');
        }

        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);
        $orderBy = $request->get('orderBy');
        $order = $request->get('order');


        $rents = $this->getRentRepository()->getRent($currentPage, self::PER_PAGE);
        $users = $this->getUserRepository()->findAll();
        $tenements = $this->getTenementRepository()->findAll();
        $ten = $this->getTenementRepository()->findBy(['title' => '123']);

        $maxRows = $rents->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('rent/list.html.twig', [
            'tens' => $ten,
            'tenements' => $tenements,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'orderBy' => $orderBy,
            'order' => $order,
            'filters' => $filters,
            'users' => $users,
            'rents' => $rents,
            'paymentMethods' => Rent::getPaymentMethodList(),
            'months' => Rent::getMonthList()
        ]);
    }

    /**
     * Add rent.
     *
     * @Route("/rent/add", name="rent_add")
     */
    public function addRent(Request $request)
    {
        if (!$this->getUser()->canAddRent())
        {
            return $this->redirect($request->headers->get('referer'));
        }

        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $rentsDetails = $request->get('rent');

        try {
            if (!empty($rentsDetails)) {
                $rent = new Rent();

                $rent = $this->buildRent($rent, $rentsDetails);

                $em = $this->getEm();
                $em->persist($rent);
                $em->flush();
            }
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirectToRoute('rent_list');
    }

    /**
     * @param Rent $rent
     * @param $rentsDetails
     * @return Rent
     * @throws \Exception
     */
    public function buildRent(Rent $rent, $rentsDetails)
    {
        $date = new \DateTime(date('d.m.Y',strtotime(01 . '.' . $rentsDetails['month'] . '.' . $rentsDetails['year'])));

        if (is_object($rentsDetails['tenement'][0]))
        {
            $tenement = $rentsDetails['tenement'][0];
        } else {
            $tenement = $this->getTenementRepository()->find($rentsDetails['tenement']);
        }

        $rent
            ->setTenement($tenement)
            ->setRent($rentsDetails['rent'])
            ->setCommunalPayments($rentsDetails['communalPayments'])
            ->setHeating($rentsDetails['heating'])
            ->setTotal($rentsDetails['total'])
            ->setSquare($rentsDetails['square'])
            ->setMethod($rentsDetails['method'])
            ->setDate($date)
        ;

        if (!$rent->getEmployee()) {
            $rent->setEmployee($this->getUser());
        }
        return $rent;
    }

    /**
     * Import items action.
     *
     * @Route("/rent/import-rent", name="import_rent")
     */
    public function importRentAction(Request $request)
    {
        if (!$this->getUser()->canImportRent())
        {
            return $this->redirect($request->headers->get('referer'));
        }

        $importFile = $request->files->get('import_rent_file');

        $filePath = $this->moveFile($importFile, 'import');

        $inputFileType = PHPExcel_IOFactory::identify($filePath);

        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($filePath);

        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $translator = $this->get('translator');

        $dateRow = current($sheet->rangeToArray('A' . 2 , NULL, TRUE, FALSE));

        preg_match_all('!\d+!', $dateRow[0], $year);

        foreach (Rent::getMonthList() as $month => $numMonth)
        {
            if (mb_stripos($dateRow[0],$translator->trans($month)))
            {
                break;
            }
        }

        //  Loop through each row of the worksheet in turn
        for ($row = 5; $row <= $highestRow - 4; $row++)
        {

            //  Read a row of data into an array
            $rows = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

            $rent = current($rows);

            preg_match('#\((.*?)\)#', $rent[1], $tenement);

            $rentData = [
                'tenement' =>  $this->getTenementRepository()->findBy(['title' => $tenement[1]]),
                'rent' => $rent[2],
                'heating' => $rent[3],
                'communalPayments' => $rent[4],
                'total' => $rent[5],
                'square' => $rent[6],
                'method' => $rent[7],
                'month' => $month,
                'year' => $year[0][0]
            ];

            $rent = $this->buildRent(new Rent(), $rentData);
            $this->getEm()->persist($rent);
        }

        $this->getEm()->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param UploadedFile $file
     * @param $part
     * @return string
     * @throws \Exception
     */
    protected function moveFile(UploadedFile $file, $part)
    {
        // Generate a unique name for the file before saving it
        $fileName = $file->getClientOriginalName();

        if ($file->getSize() > 102400000) {
            throw new \Exception("Максимальный размер файла 100MB");
        }

        // Move the file to the directory where brochures are stored

        $filePath = $this->getParameter('rent_files_root_dir') . '/' . $part . '/' . $fileName;

        $file->move(
            $this->getParameter('rent_files_root_dir') . '/' . $part,
            $fileName
        );

        return $filePath;
    }
    
    /**
     * Export rent.
     *
     * @Route("/rent/export-rent", name="export_rent")
     */
    public function exportRentAction(Request $request)
    {
        $date = $request->get('date');

        /** @var Rent $rent */
        $rent = $this->getRentRepository()->findBy(['date' => new \DateTime(01 . '.' . $date['month'] . '.' . $date['year'])]);

        $exportBuilder = new RentBuilder($this->get('phpexcel'), $this->get('translator'));

        $user = $this->getUserRepository()->find($this->getUser());

        $phpExcelObject = $exportBuilder->build($rent, $date, $this->get('translator'), $user);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding header
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            StringUtils::transliterate($date['month'] . $date['year']) . '.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}