<?php

namespace InfrastructureBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Repository\RepositoryAwareTrait;
use InfrastructureBundle\Entity\ComputerDiff;
use InfrastructureBundle\Entity\ComputerPart;
use InfrastructureBundle\Exception\ComputerPartTiedException;
use InfrastructureBundle\Service\Export\InfrastructureBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ComputerPartController extends Controller
{
    use RepositoryAwareTrait;
    const PER_PAGE = 20;

    /**
     * @Route("/infrastructure/computer-parts", name="computer_parts_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->canViewInfrastructure()) {
            return $this->redirectToRoute('homepage');
        }

        $parts = $this->getComputerPartRepository()->getComputerParts($filters, $currentPage, self::PER_PAGE);

        $maxRows = $parts->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);


        return $this->render('/infrastructure/computer_parts/list.html.twig', [
            'parts' => $parts,
            'types' => ComputerPart::getTypesList(),
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'filters' => $filters,
            'perPage' => self::PER_PAGE,
        ]);
    }

    /**
     * @Route("infrastructure/computer-parts/{id}/details", name="computer_part_details")
     */
    public function detailsAction(Request $request)
    {
        $partId = $request->get('id');
        /** @var ComputerPart $part */
        $part = $this->getComputerPartRepository()->find($partId);
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->canViewInfrastructure()) {
            return $this->redirect($request->headers->get('referer'));
        }

        $type = 'computerPart';
        $computerPartChanges = $this->getComputerDiffRepository()->getComputerChanges($type, $partId);

        return $this->render('infrastructure/computer_parts/details.html.twig', [
            'part' => $part,
            'types' => ComputerPart::getTypesList(),
            'computerPartChanges' => $computerPartChanges
        ]);
    }

    /**
     * @Route("/infrastructure/computer-parts/add", name="computer_parts_add")
     */
    public function addComputerPartAction(Request $request)
    {
        $computerPartDetails = $request->get('computerPart');

        /** @var User $user */
        $user = $this->getUser();

        if (!$user->canViewInfrastructure()) {
            return $this->redirect($request->headers->get('referer'));
        }

        if (!empty($computerPartDetails)) {
            $computerPart = new ComputerPart();

            $computerPart = $this->buildComputerPart($computerPart, $computerPartDetails);

            $em = $this->getEm();
            $em->persist($computerPart);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
        }
    }

    /**
     * @Route("infrastructure/computer-parts/{id}/edit", name="computer_parts_edit")
     */
    public function editComputerPartAction(Request $request)
    {
        $computerPartDetails = $request->get('computerPart');
        $computerPartId = $request->get('id');
        /** @var ComputerPart $computerPart */
        $computerPart = $this->getComputerPartRepository()->find($computerPartId);

        /** @var User $user */
        $user = $this->getUser();

        if (!$user->canViewInfrastructure()) {
            return $this->redirect($request->headers->get('referer'));
        }

        $computerPart = $this->buildComputerPart($computerPart, $computerPartDetails);

        $em = $this->getEm();
        $em->persist($computerPart);
        $uof = $em->getUnitOfWork();
        $uof->computeChangeSets();

        $this->logChanges($computerPart, $uof->getEntityChangeSet($computerPart));

        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Remove computer part
     *
     * @Route("/infrastructure/computer-part/{id}/delete", name="delete_computer_part")
     */
    public function deleteComputerPartAction(Request $request)
    {
        $computerPartId = $request->get('id');
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        try {
            /** @var ComputerPart $computerPart */
            $computerPart = $this->getComputerPartRepository()->find($computerPartId);
            $this->validateDeleteComputerPart($computerPart);

            $computerPart->setDeleted(true);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
        } catch (ComputerPartTiedException $exception) {
            $flashbag->add('danger', $exception->getMessage());
        }

        return $this->redirectToRoute('computer_parts_list');
    }

    /**
     * @param ComputerPart $computerPart
     * @param $computerPartDetails
     * @return ComputerPart
     */
    protected function buildComputerPart(ComputerPart $computerPart, $computerPartDetails)
    {

        $computerPart
            ->setType($computerPartDetails['type'])
            ->setName($computerPartDetails['name'])
            ->setDescription($computerPartDetails['description'])
            ->setInventoryNumber($computerPartDetails['inventoryNumber'])
            ->setSerialNumber($computerPartDetails['serialNumber'])
        ;

        return $computerPart;
    }

    /**
     * @param $computerPart
     * @param $changeSet
     * @return array
     */
    protected function logChanges($computerPart, $changeSet)
    {
        $em = $this->getDoctrine()->getManager();
        $computerPartDiffs = [];
        foreach ($changeSet as $field => $changes) {
            if ($field == 'updatedAt') {
                continue;
            }
            $oldValue = $changes[0];
            $newValue = $changes[1];
            if ($oldValue != $newValue && $oldValue) {
                $computerPartDiff = new ComputerDiff();

                $computerPartDiff
                    ->setChangedBy($this->getUser())
                    ->setComputerPart($computerPart)
                    ->setField($field)
                    ->setOldValue($oldValue)
                    ->setNewValue($newValue)
                    ->setUpdatedAt(new \DateTime())
                ;

                $em->persist($computerPartDiff);
                $computerPartDiffs = $computerPartDiff;
            }
        }

        return $computerPartDiffs;
    }

    /**
     * @param $computerPart
     * @throws ComputerPartTiedException
     */
    protected function validateDeleteComputerPart($computerPart)
    {
        /** @var ComputerPart $computerPart */
        $computerTied = $this->getComputerRepository()->findComputersPartTied($computerPart);

        if (!empty($computerTied)) {
            throw new ComputerPartTiedException($this->get('translator'), $computerPart->getName(), $computerPart->getType(), implode(",", $computerTied));
        }
    }
}