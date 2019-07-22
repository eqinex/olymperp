<?php

namespace PurchaseBundle\Service;
use AppBundle\Entity\User;
use AppBundle\Repository\RepositoryAwareTrait;
use PurchaseBundle\Entity\PurchaseRequest;

/**
 * Created by PhpStorm.
 * User: apermyakov
 * Date: 17.11.17
 * Time: 11:40
 */
class RequestService
{
    use RepositoryAwareTrait;

    protected $doctrine;
    protected $translator;

    public function __construct($doctrine, $translator)
    {
        $this->doctrine = $doctrine;
        $this->translator = $translator;
    }

    /**
     * @param User $currentUser
     * @param $type
     * @return mixed
     */
    public function getRequestsCounter($type, User $currentUser)
    {
        return $this->getPurchaseRequestRepository()->getRequestsCounter($type, $currentUser);
    }

    /**
     * @param $user
     * @param $purchaseRequest
     * @return mixed
     */
    public function getPurchaseRequestFavorite($user, $purchaseRequest)
    {
        return $this->getPurchaseRequestFavoriteRepository()->findOneBy(['user' => $user, 'purchaseRequest' => $purchaseRequest]);
    }

    /**
     * @param PurchaseRequest $purchaseRequest
     * @return array
     */
    public function getValidatePurchaseRequestItems(PurchaseRequest $purchaseRequest)
    {
        $translator = $this->getTranslator();
        $validateItems = [];
        $i = 0;
        foreach ($purchaseRequest->getItems() as $item) {
            $itemId = $item->getId();
            $validateItems[$itemId]['num'] = '#' . ++$i;
            $validateItems[$itemId]['text'] = '';
            if ($item->getProductionStatus() != 'in_production') {
                if (!($item->getSupplier())) {
                    $validateItems[$itemId]['text'] .= $translator->trans('item.supplier');
                }

                if (!($item->getInvoice())) {
                    $validateItems[$itemId]['text'] .= $validateItems[$itemId]['text'] ? ', ' . $translator->trans('item.invoice_nr') : $translator->trans('item.invoice_nr');
                }

                if (!($item->getPrice())) {
                    $validateItems[$itemId]['text'] .= $validateItems[$itemId]['text'] ? ', ' . $translator->trans('item.price') : $translator->trans('item.price');
                }
            }
        }
        return $validateItems;
    }

    /**
     * @return mixed
     */
    protected function getDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * @return mixed
     */
    protected function getTranslator()
    {
        return $this->translator;
    }
}