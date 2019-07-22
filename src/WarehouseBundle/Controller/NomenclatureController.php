<?php

namespace WarehouseBundle\Controller;

use AppBundle\Repository\RepositoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use WarehouseBundle\Service\Import\NomenclatureImport;

class NomenclatureController extends Controller
{
    use RepositoryAwareTrait;
    const PER_PAGE = 20;

    /**
     * @Route("/nomenclature", name="nomenclature_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);

        if (!$this->getUser()->canViewNomenclature()) {
            return $this->redirectToRoute('homepage');
        }

        $products = $this->getNomenclatureRepository()->getAvailableProducts($filters, $currentPage, self::PER_PAGE);
        $groups = $this->getNomenclatureGroupRepository()->findAll();

        $maxRows = $products->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('nomenclature/list.html.twig', [
            'products' => $products,
            'groups' => $groups,
            'filters' => $filters,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows
        ]);
    }

    /**
     * @Route("/nomenclature/import", name="nomenclature_import")
     */
    public function importNomenclatureAction(Request $request)
    {
        $importFile = $request->files->get('import_items_file');

        if (!$this->getUser()->canEditNomenclature()) {
            return $this->redirectToRoute('homepage');
        }

        $filePath = $this->moveFile($importFile);

        $importBuilder = new NomenclatureImport($this->getDoctrine());
        $importBuilder->build($filePath);

        unlink($filePath);

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    protected function moveFile(UploadedFile $file)
    {
        $fileName = $file->getClientOriginalName();

        $filePath = sys_get_temp_dir() . '/' . $fileName;

        $file->move(
            sys_get_temp_dir(),
            $fileName
        );

        return $filePath;
    }
}