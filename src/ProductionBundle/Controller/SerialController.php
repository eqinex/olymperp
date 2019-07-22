<?php
/**
 * Created by PhpStorm.
 * User: mazitovtr
 * Date: 07.02.19
 * Time: 11:03
 */

namespace ProductionBundle\Controller;

use AppBundle\Repository\RepositoryAwareTrait;
use ProductionBundle\Entity\Serial;
use ProductionBundle\Entity\SerialCategory;
use ProductionBundle\Entity\SerialItems;
use ProductionBundle\Entity\Ware;
use PurchaseBundle\Entity\PurchaseRequest;
use PurchaseBundle\Entity\RequestItem;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SerialController extends Controller
{
    use RepositoryAwareTrait;
    const PER_PAGE = 20;

    /**
     * @Route("/production/serials", name="production_serials_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);

        $user = $this->getUser();

        if (!$user->canViewSerialProduction()) {
            return $this->redirectToRoute('homepage');
        }

        $serials = $this->getSerialRepository()->getAvailableSerials($filters, $currentPage, self::PER_PAGE);
        $categories = $this->getSerialCategoryRepository()->findAll();
        $wares = $this->getWareRepository()->getSerialProducts($serials);
        $waresForFilter = $this->getWareRepository()->findAll();
        $projects = $this->getProjectRepository()->findAll();

        $maxRows = $serials->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('production/serials/list.html.twig', [
            'serials' => $serials,
            'categories' => $categories,
            'wares' => $wares,
            'waresForFilter' => $waresForFilter,
            'projects' => $projects,
            'filters' => $filters,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows
        ]);
    }

    /**
     * @Route("/production/serials/{id}/details")
     */
    public function detailsAction(Request $request)
    {
        $user = $this->getUser();

        if (!$user->canViewSerialProduction()) {
            return $this->redirect($request->headers->get('referer'));
        }

        $serialId = $request->get('id');
        /** @var Serial $serial */
        $serial = $this->getSerialRepository()->find($serialId);
        /** @var Ware $ware */
        $ware = $serial->getWare();

        $serials = $this->getSerialRepository()->findAll();
        $purchaseRequests = $this->getPurchaseRequestRepository()->getAvailableSerialPurchaseRequests($ware);
        $categories = $this->getSerialCategoryRepository()->findAll();
        $wares = $this->getWareRepository()->getSerialProducts($serials, $ware);

        $requestsIds = [];

        /** @var PurchaseRequest $purchaseRequest */
        foreach ($purchaseRequests as $purchaseRequest) {
            foreach ($purchaseRequest->getItems() as $item) {
                $requestsIds[$item->getPurchaseRequest()->getId()][] = $item->getId();
            }
        }

        return $this->render('production/serials/details.html.twig', [
            'serial' => $serial,
            'purchaseRequests' => $purchaseRequests,
            'requestsIds' => $requestsIds,
            'categories' => $categories,
            'wares' => $wares
        ]);
    }

    /**
     * @Route("/production/serials/add", name="serial_add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addSerialAction(Request $request)
    {
        $user = $this->getUser();

        if (!$user->canViewSerialProduction()) {
            return $this->redirect($request->headers->get('referer'));
        }

        $serialDetails = $request->get('serial');

        $serial = new Serial();
        $this->buildSerial($serial, $serialDetails);

        return $this->redirectToRoute('production_serials_list');
    }

    /**
     * @Route("/production/serials/{id}/edit", name="serial_edit")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function editSerialAction(Request $request)
    {
        $user = $this->getUser();

        if (!$user->canViewSerialProduction()) {
            return $this->redirect($request->headers->get('referer'));
        }

        $serialDetails = $request->get('serial');
        $serialId = $request->get('id');

        /** @var Serial $serial */
        $serial = $this->getSerialRepository()->find($serialId);

        $this->buildSerial($serial, $serialDetails);

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param Serial $serial
     * @param $serialDetails
     * @return Serial
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function buildSerial(Serial $serial, $serialDetails)
    {
        $em = $this->getEm();

        /** @var Ware $ware */
        $ware = $this->getWareRepository()->find($serialDetails['ware']);
        /** @var SerialCategory $category */
        $category = $this->getSerialCategoryRepository()->find($serialDetails['category']);

        if (!$serial->getId()) {
            $serial->setCreatedAt(new \DateTime());
        }

        $serial
            ->setCategory($category)
            ->setWare($ware)
        ;

        $em->persist($serial);
        $em->flush();

        return $serial;
    }

    /**
     * @Route("/production/serials/{id}/add-serial-items", name="add_serial_items")
     */
    public function addItemsAction(Request $request)
    {
        $user = $this->getUser();

        if (!$user->canViewSerialProduction()) {
            return $this->redirect($request->headers->get('referer'));
        }

        $serialId = $request->get('id');
        /** @var Serial $serial */
        $serial = $this->getSerialRepository()->find($serialId);
        $items = $request->get('items',[]);
        $purchaseRequests = $request->get('requests',[]);
        $em = $this->getDoctrine()->getManager();

        foreach ($serial->getSerialItems() as $item) {
            $serial->removeSerialItem($item);
        }

        $em->persist($serial);
        $em->flush();

        foreach ($items as $itemId) {
            /** @var RequestItem $item */
            $item = $this->getRequestItemRepository()->find($itemId);
            $serial->addSerialItem($item);
        }

        foreach ($purchaseRequests as $purchaseRequestId) {
            /** @var PurchaseRequest $purchaseRequest */
            $purchaseRequest = $this->getPurchaseRequestRepository()->find($purchaseRequestId);
            foreach ($purchaseRequest->getItems() as $item) {
                $serial->addSerialItem($item);
            }
        }

        $em->persist($serial);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }
}