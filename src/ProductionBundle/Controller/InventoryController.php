<?php

namespace ProductionBundle\Controller;

use AppBundle\Repository\RepositoryAwareTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class InventoryController extends Controller
{
    use RepositoryAwareTrait;

    const PER_PAGE = 50;

    /**
     * @Route("/production/inventory", name="inventory_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);
        $perPage = self::PER_PAGE;
        $user = $this->getUser();

        if (!$user->canViewAllInventoryItems()) {
            return $this->redirectToRoute('homepage');
        }

        $items = $this->getRequestItemRepository()->getAvailableItems($filters, $currentPage, $perPage);

        $maxRows = $items->count();
        $maxPages = ceil($maxRows / $perPage);

        $suppliers = $this->getSupplierRepository()->findAll();

        return $this->render('production/inventory/list.html.twig',[
            'items' => $items,
            'suppliers' => $suppliers,
            'filters' => $filters,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'perPage' => $perPage,
        ]);
    }
}