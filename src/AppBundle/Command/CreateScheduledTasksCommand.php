<?php
/**
 * Created by PhpStorm.
 * User: shemyakindv
 * Date: 15.02.19
 * Time: 12:57
 */

namespace AppBundle\Command;

use AppBundle\Entity\DayOff;
use AppBundle\Entity\ProductionCalendar;
use AppBundle\Entity\ProjectTask;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateScheduledTasksCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('olymp:scheduler:create')
            ->setDescription('Command to create scheduled tasks')
            ->setHelp('This command creates a scheduled task for today')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getEntityManager();

        $scheduledTasks = $em->getRepository(ProjectTask::class)->getAvailableScheduledTasks();

        $todayAt = new \DateTime(date('d.m.Y'));

        $isWorkDay = $em->getRepository(ProductionCalendar::class)->isWorkDay();

        if ($isWorkDay) {
            /** @var ProjectTask $scheduledTask */
            foreach ($scheduledTasks as $scheduledTask) {
                $isDayOff = $em->getRepository(DayOff::class)->isDayOff($scheduledTask->getResponsibleUser());
                if (!$isDayOff) {
                    if ($scheduledTask->getScheduledPeriod() == ProjectTask::SCHEDULER_TYPE_SINGLY &&
                        $scheduledTask->getStartAt()->format('d.m.Y') == $todayAt->format('d.m.Y')) {
                        $scheduledTask->setStatus(ProjectTask::STATUS_DONE);
                        $task = $this->buildSchedulerTask($scheduledTask);
                        $em->persist($task);
                    } elseif ($scheduledTask->getScheduledPeriod() == ProjectTask::SCHEDULER_TYPE_DAILY) {
                        $task = $this->buildSchedulerTask($scheduledTask);
                        $em->persist($task);
                    } elseif ($scheduledTask->getScheduledPeriod() == ProjectTask::SCHEDULER_TYPE_WEEKLY &&
                        !empty($scheduledTask->getDaysWeek()) &&
                        in_array($todayAt->format('N'), $scheduledTask->getDaysWeek())) {
                        $task = $this->buildSchedulerTask($scheduledTask);
                        $em->persist($task);
                    } elseif ($scheduledTask->getScheduledPeriod() == ProjectTask::SCHEDULER_TYPE_MONTHLY &&
                        $todayAt->format('m.Y') == $scheduledTask->getStartAt()->format('m.Y')) {
                        $task = $this->buildSchedulerTask($scheduledTask);
                        $em->persist($task);
                    } elseif ($scheduledTask->getScheduledPeriod() == ProjectTask::SCHEDULER_TYPE_YEARLY &&
                        $todayAt->format('d.m') == $scheduledTask->getStartAt()->format('d.m') &&
                        $todayAt->format('d.m.Y') >= $scheduledTask->getStartAt()->format('d.m.Y')) {
                        $task = $this->buildSchedulerTask($scheduledTask);
                        $em->persist($task);
                    }
                }
            }
            $em->flush();
        }
    }

    /**
     * @param ProjectTask $scheduledTask
     * @return ProjectTask
     */
    protected function buildSchedulerTask(ProjectTask $scheduledTask) {

        /** @var ProjectTask $task */
        $task = new ProjectTask();

        $task
            ->setTitle($scheduledTask->getTitle())
            ->setDescription($scheduledTask->getDescription() ? $scheduledTask->getDescription() : '')
            ->setType($scheduledTask->getType())
            ->setStartAt(new \DateTime(date('d.m.Y 09:00')))
            ->setEndAt(new \DateTime(date('d.m.Y 21:00')))
            ->setPriority($scheduledTask->getPriority())
            ->setProject($scheduledTask->getProject())
            ->setOriginalEstimate($scheduledTask->getOriginalEstimate())
            ->setReporter($scheduledTask->getReporter())
            ->setControllingUser($scheduledTask->getControllingUser())
            ->setResponsibleUser($scheduledTask->getResponsibleUser());

        return $task;
    }
}