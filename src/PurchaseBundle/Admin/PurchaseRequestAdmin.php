<?php

namespace PurchaseBundle\Admin;

use PurchaseBundle\PurchaseConstants;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sonata\AdminBundle\Show\ShowMapper;

class PurchaseRequestAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('code')
            ->add('description')
            ->add('preferredShipmentDate')
            ->add('leaderApproved')
            ->add('projectLeaderApproved')
            ->add('productionLeaderApproved')
            ->add('financialLeaderApproved')
            ->add('purchasingLeaderApproved')
            ->add('status')
            ->add('priority')
            ->add('type')
            ->add('paymentStatus')
            ->add('deliveryStatus')
            ->add('productionStatus')
            ->add('createdAt')
            ->add('relevanceDate')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('code')
            ->add('status')
            ->add('priority')
            ->add('type')
            ->add('paymentStatus')
            ->add('deliveryStatus')
            ->add('productionStatus')
            ->add('createdAt')
            ->add('relevanceDate')
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
            ->add('project')
            ->add('owner')
            ->add('leader')
            ->add('status', ChoiceType::class, [
                'label' => 'status',
                'choices' => [
                    PurchaseConstants::STATUS_NEW => PurchaseConstants::STATUS_NEW,
                    PurchaseConstants::STATUS_NEEDS_LEADER_APPROVAL => PurchaseConstants::STATUS_NEEDS_LEADER_APPROVAL,
                    PurchaseConstants::STATUS_NEEDS_PROJECT_LEADER_APPROVE =>  PurchaseConstants::STATUS_NEEDS_PROJECT_LEADER_APPROVE,
                    PurchaseConstants::STATUS_NEEDS_PRODUCTION_LEADER_APPROVAL => PurchaseConstants::STATUS_NEEDS_PRODUCTION_LEADER_APPROVAL,
                    PurchaseConstants::STATUS_NEEDS_PRELIMINARY_ESTIMATE_APPROVE => PurchaseConstants::STATUS_NEEDS_PRELIMINARY_ESTIMATE_APPROVE,
                    PurchaseConstants::STATUS_ON_PRELIMINARY_ESTIMATE => PurchaseConstants::STATUS_ON_PRELIMINARY_ESTIMATE,
                    PurchaseConstants::STATUS_NEEDS_PURCHASING_MANAGER => PurchaseConstants::STATUS_NEEDS_PURCHASING_MANAGER,
                    PurchaseConstants::STATUS_NEEDS_FIXING => PurchaseConstants::STATUS_NEEDS_FIXING,
                    PurchaseConstants::STATUS_MANAGER_ASSIGNED => PurchaseConstants::STATUS_MANAGER_ASSIGNED,
                    PurchaseConstants::STATUS_MANAGER_STARTED_WORK => PurchaseConstants::STATUS_MANAGER_STARTED_WORK,
                    PurchaseConstants::STATUS_MANAGER_FINISHED_WORK => PurchaseConstants::STATUS_MANAGER_FINISHED_WORK,
                    PurchaseConstants::STATUS_REJECTED => PurchaseConstants::STATUS_REJECTED,
                    PurchaseConstants::STATUS_DONE => PurchaseConstants::STATUS_DONE,
                ]
            ])
            ->add('projectLeader')
            ->add('productionLeader')
            ->add('financialLeader')
            ->add('purchasingLeader')
            ->add('purchasingManager')
            ->add('deliveryStatus', ChoiceType::class, [
                'required' => false ,'label' => 'deliveryStatus',
                'choices' => [
                    PurchaseConstants::DELIVERY_STATUS_AWAITING_DELIVERY  => PurchaseConstants::DELIVERY_STATUS_AWAITING_DELIVERY,
                    PurchaseConstants::DELIVERY_STATUS_IN_DELIVERY  => PurchaseConstants::DELIVERY_STATUS_IN_DELIVERY,
                    PurchaseConstants::DELIVERY_STATUS_DELIVERED  => PurchaseConstants::DELIVERY_STATUS_DELIVERED,
                ]
            ])
            ->add('paymentStatus', ChoiceType::class, [
                'required' => false , 'label' => 'paymentStatus',
                'choices' => [
                    PurchaseConstants::PAYMENT_STATUS_NEEDS_PAYMENT => PurchaseConstants::PAYMENT_STATUS_NEEDS_PAYMENT,
                    PurchaseConstants::PAYMENT_STATUS_PAYMENT_PROCESSING => PurchaseConstants::PAYMENT_STATUS_PAYMENT_PROCESSING,
                    PurchaseConstants::PAYMENT_STATUS_PAID => PurchaseConstants::PAYMENT_STATUS_PAID,
                ]
            ])
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('code')
            ->add('description')
            ->add('preferredShipmentDate')
            ->add('leaderApproved')
            ->add('projectLeaderApproved')
            ->add('productionLeaderApproved')
            ->add('financialLeaderApproved')
            ->add('purchasingLeaderApproved')
            ->add('status')
            ->add('priority')
            ->add('type')
            ->add('paymentStatus')
            ->add('deliveryStatus')
            ->add('productionStatus')
            ->add('createdAt')
            ->add('relevanceDate')
        ;
    }
}
