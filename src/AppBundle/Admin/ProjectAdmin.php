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

class ProjectAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'project';
    protected $baseRoutePattern = 'project';

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
            ->tab('Project')
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
                    ->add('goal')
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
                    ->add('purchasingManager')
                    ->add('category')
                    ->add('supplier')
                ->end()
                ->with('Notifications', ['class' => 'col-md-7'])
                    ->add('telegramChatId')
                    ->add('telegramChatUrl')
                ->end()
                ->with('Timelines', ['class' => 'col-md-12'])
                    ->add('startAt', 'date', ['years' => range(2011, 2030)])
                    ->add('endAt', 'date', ['years' => range(2011, 2030)])
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
            ->add('goal')
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

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $query->andWhere('o.type = :type')
            ->setParameter('type', 'project');

        return $query;
    }
}
