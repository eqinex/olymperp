<?php
namespace AppBundle\Admin;

use AppBundle\Entity\User;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sonata\UserBundle\Admin\Model\UserAdmin as SonataUserAdmin;

class UserAdmin extends SonataUserAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        parent::configureFormFields($formMapper);

        $formMapper
            ->tab('User')
                ->with('Profile')
                    ->add('badgeColor', null, [
                        'required' => false,
                        'label' => 'badgeColor'
                    ])
                    ->add('middlename', null, [
                        'required' => false,
                        'label' => 'middlename'
                    ])
                    ->add('employeeRole', null, [
                        'required' => false,
                        'label' => 'Employee Role'
                    ])
                    ->add('employmentDate', 'date', [
                        'required' => false,
                        'label' => 'Employment date'
                    ])
                    ->add('team', null, [
                        'required' => false,
                        'label' => 'Team'
                    ])
                    ->add('employeeStatus', ChoiceType::class, [
                        'label' => 'Employee Status',
                        'choices' => User::getEmployeeStatusChoices()
                    ])
                    ->add('submissionTeam')
                    ->add('room', null, [
                        'required' => false,
                        'label' => 'Office room number'
                    ])
                ->end()
                ->with('Social')
                    ->add('telegramChatId', null, [
                        'required' => false,
                        'label' => 'Telegram Chat ID'
                    ])
                    ->add('telegramUsername', null, [
                        'required' => false,
                        'label' => 'Telegram Username'
                    ])
                    ->add('theme', ChoiceType::class, [
                        'label' => 'Olymp theme',
                        'choices' => [
                            'Dark' => 'dark',
                            'Light' => 'light',
                        ]
                    ])
            ->end()
                ->with('Privileges', ['class' => 'col-md-6'])
                ->add('admin', null, [
                    'required' => false,
                    'label' => 'Admin'
                ])
                ->add('halfTime', null, [
                    'required' => false,
                    'label' => 'Half Time'
                ])
                ->add('closeOwnTasks', null, [
                    'required' => false,
                    'label' => 'Close own tasks'
            ])
            ->end()
        ;

    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->add('fullname');
        parent::configureListFields($listMapper);

    }
}