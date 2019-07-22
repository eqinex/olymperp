<?php

namespace ProductionBundle\Controller;

use AppBundle\Repository\RepositoryAwareTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller
{
    use RepositoryAwareTrait;
    const PER_PAGE = 20;

    /**
     * @Route("/products", name="products_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);

        $products = $this->getWareRepository()->getProducts($filters, $currentPage, self::PER_PAGE);
        $projects = $this->getProjectRepository()->findAll();

        $maxRows = $products->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('production/products/list.html.twig', [
            'products' => $products,
            'projects' => $projects,
            'filters' => $filters,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows
        ]);
    }

    /**
     * @return array
     */
    protected function getProjectLeaders()
    {
        $users = $this->getUserRepository()->findAll();
        $leaders = [];

        foreach ($users as $user) {
            if ($user->isProjectLeader()) {
                $leaders[] = $user;
            }
        }

        return $users;
    }
}
