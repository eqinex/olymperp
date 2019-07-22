<?php

namespace AppBundle\Service;
use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectTask;
use AppBundle\Entity\TaskFile;
use AppBundle\Entity\Team;
use AppBundle\Repository\RepositoryAwareTrait;

/**
 * Created by PhpStorm.
 * User: apermyakov
 * Date: 17.11.17
 * Time: 11:40
 */
class TaskService
{
    use RepositoryAwareTrait;

    protected $doctrine;

    protected $epicColors = ['success', 'brand-cerulean',
        'curious-blue', 'eminence', 'endaveour', 'violet-eggplant'];

    protected $epicLabels = [];

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param Project $project
     * @return array
     */
    public function getAvailableEpics(Project $project)
    {
        return $this->getProjectTaskRepository()->findBy([
            'type' => ProjectTask::TYPE_EPIC,
            'status' => [
                ProjectTask::STATUS_NEW,
                ProjectTask::STATUS_IN_PROGRESS,
                ProjectTask::STATUS_NEED_APPROVE,
                ProjectTask::STATUS_READY_TO_WORK,
                ProjectTask::STATUS_ON_HOLD
            ],
            'project' => $project]);
    }

    /**
     * @param ProjectTask $epic
     * @return array
     */
    public function getEpicLabel(ProjectTask $epic)
    {
        if (!isset($this->epicLabels[$epic->getId()])) {
            $selectedColor = rand(0, count($this->epicColors) - 1);
            $this->epicLabels[$epic->getId()] = $this->epicColors[$selectedColor];
            unset($this->epicColors[$selectedColor]);
        }

        return $this->epicLabels[$epic->getId()];
    }

    /**
     * @param TaskFile $taskFile
     * @return mixed
     */
    public function getCountDownloadsFile(TaskFile $taskFile)
    {
        return $this->getTaskFileDownloadManager()->getDownloadsFile($taskFile);
    }

    /**
     * @return mixed
     */
    protected function getDoctrine()
    {
        return $this->doctrine;
    }
}