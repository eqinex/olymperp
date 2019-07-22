<?php

namespace PurchaseBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class SupplierAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('title')
            ->add('fullTitle')
            ->add('legalAddress')
            ->add('actualAddress')
            ->add('postalAddress')
            ->add('email')
            ->add('site')
            ->add('phone')
            ->add('fax')
            ->add('ogrn')
            ->add('itn')
            ->add('kpp')
            ->add('okpo')
            ->add('okved')
            ->add('okfs')
            ->add('okopf')
            ->add('okato')
            ->add('director')
            ->add('accountant')
            ->add('basis')
            ->add('createdAt')
            ->add('registeredAt')
            ->add('checkingAccount')
            ->add('bankShortName')
            ->add('bankFullName')
            ->add('correspondentAccount')
            ->add('bic')
            ->add('bankMailingAddress')
            ->add('bankLegalAddress')
            ->add('bankActualAddress')
            ->add('bankItn')
            ->add('bankKpp')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('title')
            ->add('fullTitle')
            ->add('legalAddress')
            ->add('actualAddress')
            ->add('postalAddress')
            ->add('email')
            ->add('site')
            ->add('phone')
            ->add('fax')
            ->add('ogrn')
            ->add('itn')
            ->add('kpp')
            ->add('okpo')
            ->add('okved')
            ->add('okfs')
            ->add('okopf')
            ->add('okato')
            ->add('director')
            ->add('accountant')
            ->add('basis')
            ->add('createdAt')
            ->add('registeredAt')
            ->add('checkingAccount')
            ->add('bankShortName')
            ->add('bankFullName')
            ->add('correspondentAccount')
            ->add('bic')
            ->add('bankMailingAddress')
            ->add('bankLegalAddress')
            ->add('bankActualAddress')
            ->add('bankItn')
            ->add('bankKpp')
            ->add('_action', null, array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                ),
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title')
            ->add('fullTitle')
            ->add('itn')
            ->add('createdAt')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('title')
            ->add('fullTitle')
            ->add('legalAddress')
            ->add('actualAddress')
            ->add('postalAddress')
            ->add('email')
            ->add('site')
            ->add('phone')
            ->add('fax')
            ->add('itn')
            ->add('ogrn')
            ->add('kpp')
            ->add('okpo')
            ->add('okved')
            ->add('okfs')
            ->add('okopf')
            ->add('okato')
            ->add('director')
            ->add('accountant')
            ->add('basis')
            ->add('createdAt')
            ->add('registeredAt')
            ->add('checkingAccount')
            ->add('bankShortName')
            ->add('bankFullName')
            ->add('correspondentAccount')
            ->add('bic')
            ->add('bankMailingAddress')
            ->add('bankLegalAddress')
            ->add('bankActualAddress')
            ->add('bankItn')
            ->add('bankKpp')
        ;
    }
}
