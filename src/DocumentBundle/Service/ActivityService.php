<?php

namespace DocumentBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Repository\RepositoryAwareTrait;

class ActivityService
{
    use RepositoryAwareTrait;

    protected $doctrine;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param User $currentUser
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getActivitiesResponsible(User $currentUser)
    {
        return $this->getActivityRepository()->getActivitiesResponsible($currentUser);
    }

    /**
     * @return mixed
     */
    protected function getDoctrine()
    {
        return $this->doctrine;
    }
}