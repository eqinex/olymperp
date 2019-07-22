<?php


namespace PurchaseBundle\Service;

use PurchaseBundle\Entity\Supplier;
use PurchaseBundle\Entity\SupplierDiff;
use AppBundle\Repository\RepositoryAwareTrait;

/**
 * Created by PhpStorm.
 * User: mazitov
 * Date: 14.06.19
 * Time: 15:11
 */
class SupplierService
{
    use RepositoryAwareTrait;

    /**
     * @var $doctrine
     */
    protected $doctrine;

    /**
     * SupplierService constructor.
     * @param $doctrine
     */
    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param Supplier $supplier
     * @return bool|Supplier
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateSupplierInfo(Supplier $supplier)
    {
        $client = new \GuzzleHttp\Client(
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Token 389a7a1b4e620686322cdca86561dcaffab7ae57',
                ]
            ]);


        if ($supplier->getItn()) {
            $url = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party';
            $query = $supplier->getItn();
        } else {
            $url = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/party';
            $query = $supplier->getTitle();
        }

        $request = $client->request(
            'POST',
            $url,
            [
                'json' => ['query' => $query, "branch_type" => "MAIN"]
            ]
        );

        $resp = \GuzzleHttp\json_decode($request->getBody());

        $suggest = current($resp->suggestions);

        if (!empty($suggest->value)) {

            $supplier
                ->setTitle($suggest->value)
                ->setFullTitle($suggest->data->name->full_with_opf)
                ->setItn($suggest->data->inn)
                ->setDirector(!empty($suggest->data->management->name) ? $suggest->data->management->name : '')
                ->setOgrn($suggest->data->ogrn)
                ->setOkpo($suggest->data->okpo)
                ->setOkved($suggest->data->okved)
                ->setLegalAddress($suggest->data->address->unrestricted_value);
            if (!empty($suggest->data->kpp)) {
                $supplier->setKpp($suggest->data->kpp);
            }
        }
        $supplier->setUpdatedAt(new \DateTime());

        $em = $this->getEm();
        $em->persist($supplier);

        $uof = $em->getUnitOfWork();
        $uof->computeChangeSets();

        $this->logChanges($supplier, $uof->getEntityChangeSet($supplier));
        $em->flush();
    }

    /**
     * @param $supplier
     * @param $changeSet
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    protected function logChanges($supplier, $changeSet)
    {
        $em = $this->getEm();
        $supplierDiffs = [];
        foreach ($changeSet as $field => $changes) {
            if ($field == 'updatedAt') {
                continue;
            }
            $oldValue = $this->prepareChangesValue($field, $changes[0]);
            $newValue = $this->prepareChangesValue($field, $changes[1]);
            if ($oldValue != $newValue && $oldValue) {
                $supplierDiff = new SupplierDiff();

                $supplierDiff
                    ->setSupplier($supplier)
                    ->setField($field)
                    ->setOldValue($oldValue)
                    ->setNewValue($newValue)
                    ->setUpdatedAt(new \DateTime());

                $em->persist($supplierDiff);
                $supplierDiffs[] = $supplierDiff;
            }
        }

        return $supplierDiffs;
    }

    /**
     * @param $field
     * @param $value
     * @return int|string
     */
    protected function prepareChangesValue($field, $value)
    {
        if ($value instanceof \DateTime) {
            $value = $value->format('d/m/Y H:i');
        } elseif (!$value) {
            $value = 'No';
        } elseif ($value === true) {
            $value = 'Yes';
        }

        return $value;
    }

    /**
     * @return mixed
     */
    protected function getDoctrine()
    {
        return $this->doctrine;
    }
}