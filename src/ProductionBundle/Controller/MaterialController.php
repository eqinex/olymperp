<?php

namespace ProductionBundle\Controller;

use AppBundle\Repository\RepositoryAwareTrait;
use ProductionBundle\Entity\Material;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class MaterialController extends Controller
{
    use RepositoryAwareTrait;
    const PER_PAGE = 20;

    /**
     * Finds and displays all production materials.
     *
     * @Route("/production/materials", name="production_materials_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);

        $materials = $this->getMaterialRepository()->getMaterials($filters, $currentPage, self::PER_PAGE);

        $maxRows = $materials->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('production/materials/list.html.twig', [
            'materials' => $materials->getIterator(),
            'filters' => $filters,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows
        ]);
    }

    /**
     * Add materials form.
     *
     * @Route("/production/materials/add", name="material_add")
     */
    public function addMaterialsAction(Request $request)
    {
        $materialDetails = $request->get('material');

        $materials = new Material();
        $materials = $this->buildMaterial($materials, $materialDetails);

        $this->getEm()->persist($materials);

        $this->getEm()->flush();

        return $this->redirectToRoute('production_materials_list');
    }

    /**
     * Edit materials form.
     *
     * @Route("/production/materials/{materialId}/edit", name="material_edit")
     */
    public function editMaterialsAction(Request $request)
    {
        $materialDetails = $request->get('material');
        $materialId = $request->get('materialId');

        $materials = $this->getMaterialRepository()->find($materialId);
        /** @var Material $materials */
        $materials = $this->buildMaterial($materials, $materialDetails);

        $this->getEm()->persist($materials);

        $this->getEm()->flush();

        return $this->redirectToRoute('production_materials_list');
    }


    /**
     * @param Material $materials
     * @param $materialDetails
     * @return Material
     */
    protected function buildMaterial(Material $materials, $materialDetails)
    {
        $unit = 'pcs';

        $materials
            ->setName($materialDetails['name'])
            ->setQuantity($materialDetails['quantity'])
            ->setUnit($unit)
            ->setComment($materialDetails['comment']);

        return $materials;
    }
}