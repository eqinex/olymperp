<?php


namespace PurchaseBundle\Controller;


use AppBundle\Repository\RepositoryAwareTrait;
use PurchaseBundle\Entity\Tenement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class TenementController extends Controller
{
    use RepositoryAwareTrait;

    const PER_PAGE = 20;

    /**
     * List action.
     *
     * @Route("/tenement", name="tenement_list")
     */
    public function listAction(Request $request)
    {
        if (!$this->getUser()->canViewTenement())
        {
            return $this->redirectToRoute('homepage');
        }

        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);
        $orderBy = $request->get('orderBy');
        $order = $request->get('order');


        $tenements = $this->getTenementRepository()->getTenement($currentPage, self::PER_PAGE);
        $users = $this->getUserRepository()->findAll();
        $suppliers = $this->getSupplierRepository()->findAll();

        $maxRows = $tenements->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('tenement/list.html.twig', [
            'suppliers' => $suppliers,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'orderBy' => $orderBy,
            'order' => $order,
            'filters' => $filters,
            'users' => $users,
            'tenements' => $tenements
        ]);
    }

    /**
     * Add tenement.
     *
     * @Route("/tenement/add", name="tenement_add")
     */
    public function addTenement(Request $request)
    {
        if (!$this->getUser()->canEditTenement())
        {
            return $this->redirect($request->headers->get('referer'));
        }

        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $tenementsDetails = $request->get('tenement');

        try {
            if (!empty($tenementsDetails)) {
                $tenement = new Tenement();

                $tenement = $this->buildTenement($tenement, $tenementsDetails);

                $em = $this->getEm();
                $em->persist($tenement);
                $em->flush();
            }
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirectToRoute('tenement_list');
    }

    /**
     * Edit tenement.
     *
     * @Route("/tenement/{id}/edit", name="tenement_edit")
     */
    public function editTenement(Request $request)
    {
        if (!$this->getUser()->canEditTenement())
        {
            return $this->redirect($request->headers->get('referer'));
        }

        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $tenementId = $request->get('id');
        $tenementsDetails = $request->get('tenement');

        try {
            if (!empty($tenementsDetails)) {
                /* @var Tenement $tenement */
                $tenement = $this->getTenementRepository()->find($tenementId);

                $tenement = $this->buildTenement($tenement, $tenementsDetails);

                $em = $this->getEm();
                $em->persist($tenement);
                $em->flush();
            }
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirectToRoute('tenement_list');
    }

    /**
     * @param Tenement $tenement
     * @param $tenementsDetails
     * @return Tenement
     */
    public function buildTenement(Tenement $tenement, $tenementsDetails)
    {
        $supplier = $this->getSupplierRepository()->find($tenementsDetails['supplier']);

        $tenement
            ->setTitle($tenementsDetails['title'])
            ->setRent($tenementsDetails['rent'])
            ->setCommunalPayments($tenementsDetails['communalPayments'])
            ->setHeating($tenementsDetails['heating'])
            ->setTotal($tenementsDetails['total'])
            ->setSquare($tenementsDetails['square'])
            ->setSupplier($supplier)
        ;

        return $tenement;
    }
}