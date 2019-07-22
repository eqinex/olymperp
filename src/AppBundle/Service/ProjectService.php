<?php

namespace AppBundle\Service;
use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectPassport;
use AppBundle\Entity\User;
use AppBundle\Repository\RepositoryAwareTrait;

/**
 * Created by PhpStorm.
 * User: apermyakov
 * Date: 17.11.17
 * Time: 11:40
 */
class ProjectService
{
    use RepositoryAwareTrait;

    protected $doctrine;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getAvailableProjects(User $user)
    {
        return $this->getProjectRepository()->getAllAvailableProjects($user);
    }

    public function getProjectPassportFile($project, $projectPassport)
    {
        return $this->getFileRepository()->getProjectPassportFile($project, $projectPassport);
    }

    /**
     * @return mixed
     */
    protected function getDoctrine()
    {
        return $this->doctrine;
    }
}