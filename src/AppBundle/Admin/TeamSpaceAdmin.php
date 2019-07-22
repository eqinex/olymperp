<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Project;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TeamSpaceAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'team-space';
    protected $baseRoutePattern = 'team-space';

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('code')
            ->add('type')
            ->add('team')
            ->add('status')
            ->add('leader')
            ->add('category')
            ->add('startAt')
            ->add('endAt')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('name')
            ->add('code')
            ->add('type')
            ->add('team')
            ->add('status')
            ->add('leader')
            ->add('category')
            ->add('startAt')
            ->add('endAt')
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
            ->tab('Project', array('admin_code' => 'admin.team.space', 'class' => Project::class))
                ->with('Details', ['class' => 'col-md-5'])
                    ->add('name')
                    ->add('code')
                    ->add('type', ChoiceType::class, [
                        'label' => 'Type',
                        'choices' => [
                            'Project' => 'project',
                            'Team' => 'team',
                            'Product' => 'product',
                        ]
                    ])
                    ->add('description')
                    ->add('status')
                    ->add('priority', ChoiceType::class, [
                        'label' => 'Priority',
                        'choices' => [
                            'A+' => Project::PRIORITY_A_PLUS,
                            'A' => Project::PRIORITY_A,
                            'B' => Project::PRIORITY_B,
                            'C' => Project::PRIORITY_C,
                        ]
                    ])
                    ->add('leader')
                    ->add('category')
                ->end()
                ->with('Team', ['class' => 'col-md-7'])
                    ->add('team')
                ->end()
                ->with('Notifications', ['class' => 'col-md-7'])
                    ->add('telegramChatId')
                ->end()
                ->with('Timelines', ['class' => 'col-md-12'])
                    ->add('startAt')
                    ->add('endAt')
                ->end()
            ->end()
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('description')
            ->add('status')
            ->add('leader')
            ->add('category')
            ->add('startAt')
            ->add('endAt')
        ;
    }

    protected function configureRoutes(RouteCollection $routeCollection)
    {
        $routeCollection->remove('delete');
    }
}
