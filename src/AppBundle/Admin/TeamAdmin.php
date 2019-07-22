<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Team;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TeamAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('title')
            ->add('department')
            ->add('code')
            ->add('leader')
            ->add('deputyLeader')
            ->add('teamMembers')
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
            ->add('department')
            ->add('code')
            ->add('leader')
            ->add('deputyLeader')
            ->add('teamMembers')
            ->add('childTeams')
            ->add('parentTeam')
            ->add('type')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title')
            ->add('department')
            ->add('code')
            ->add('leader')
            ->add('deputyLeader')
            ->add('purchasesTeam')
            ->add('financialTeam')
            ->add('productionTeam')
            ->add('needsTaskApprove')
            ->add('needsResultApprove')
            ->add('needsTeamLeaderNotification')
            ->add('teamMembers')
            ->add('telegramChatId')
            ->add('includeSubmissionTeams')
            ->add('childTeams')
            ->add('parentTeam')
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => Team::getTypes()
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
            ->add('title')
            ->add('department')
            ->add('code')
            ->add('leader')
            ->add('deputyLeader')
            ->add('teamMembers')
            ->add('type')
        ;
    }
}
