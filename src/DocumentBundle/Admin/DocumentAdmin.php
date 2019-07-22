<?php

namespace DocumentBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class DocumentAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('status')
            ->add('code')
            ->add('type')
            ->add('unlimited')
            ->add('contractExtension')
            ->add('period')
            ->add('amount')
            ->add('vat')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('startAt')
            ->add('endAt')
            ->add('subject')
            ->add('supplierContractCode')
            ->add('measureOfResponsibility')
            ->add('security')
            ->add('debtReceivable')
            ->add('act')
            ->add('comment')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('status')
            ->add('code')
            ->add('type')
            ->add('unlimited')
            ->add('contractExtension')
            ->add('period')
            ->add('amount')
            ->add('vat')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('startAt')
            ->add('endAt')
            ->add('subject')
            ->add('supplierContractCode')
            ->add('measureOfResponsibility')
            ->add('security')
            ->add('debtReceivable')
            ->add('act')
            ->add('comment')
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
            ->add('id')
            ->add('status')
            ->add('code')
            ->add('type')
            ->add('unlimited')
            ->add('contractExtension')
            ->add('period')
            ->add('amount')
            ->add('vat')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('startAt')
            ->add('endAt')
            ->add('subject')
            ->add('supplierContractCode')
            ->add('measureOfResponsibility')
            ->add('security')
            ->add('debtReceivable')
            ->add('act')
            ->add('comment')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('status')
            ->add('code')
            ->add('type')
            ->add('unlimited')
            ->add('contractExtension')
            ->add('period')
            ->add('amount')
            ->add('vat')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('startAt')
            ->add('endAt')
            ->add('subject')
            ->add('supplierContractCode')
            ->add('measureOfResponsibility')
            ->add('security')
            ->add('debtReceivable')
            ->add('act')
            ->add('comment')
        ;
    }
}
