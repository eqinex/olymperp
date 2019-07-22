<?php
/**
 * Created by PhpStorm.
 * User: mazitovtr
 * Date: 07.05.19
 * Time: 16:28
 */

namespace AppBundle\Command;

use AppBundle\Entity\Team;
use PurchaseBundle\Entity\ManagerStats;
use PurchaseBundle\Entity\PurchaseRequest;
use PurchaseBundle\Entity\PurchaseRequestDiff;
use PurchaseBundle\PurchaseConstants;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GeneratePurchasingStatsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('olymp:purchase:statsgenerate')
            ->setDescription('Command for generating purchasing manager stats')
            ->setHelp('Command will generate purchasing manager stats')
            ->addArgument('statsDate', InputArgument::OPTIONAL, 'Select date for stat regen')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getEntityManager();

        $output->writeln([
            'Selecting managers..',
            '================',
            ''
        ]);

        /** @var Team $purchasingTeam */
        $purchasingTeam = $em->getRepository(Team::class)->findOneBy(['type' => 'purchasing_team']);
        $requestRepo = $em->getRepository(PurchaseRequest::class);
        $requestDiffRepo = $em->getRepository(PurchaseRequestDiff::class);

        $date = $input->getArgument('statsDate');

        $statsDate = !empty($date) ? new \DateTime($date) : new \DateTime();

        if (!$purchasingTeam) {
            throw new \Exception('Purchasing Team not found');
        }

        $output->writeln([
            'Selected ' . count($purchasingTeam->getTeamMembers()) . ' manager(s)..',
            '================',
        ]);

        foreach ($purchasingTeam->getTeamMembers() as $manager) {
            $output->writeln([
                'Calculating stats for ' . $manager->getLastNameWithInitials(),
                '================',
            ]);

            $managerStats = new ManagerStats();
            $assignedRequests = $requestRepo->findBy([
                'status' => PurchaseConstants::STATUS_MANAGER_ASSIGNED,
                'purchasingManager' => $manager
            ]);
            $requestsInProgress = $requestRepo->findBy([
                'status' => [
                    PurchaseConstants::STATUS_MANAGER_STARTED_WORK,
                    PurchaseConstants::STATUS_ON_PRELIMINARY_ESTIMATE,
                    PurchaseConstants::STATUS_NEEDS_PRELIMINARY_ESTIMATE_APPROVE,
                ],
                'purchasingManager' => $manager
            ]);
            $requestsProcessedStats = $requestDiffRepo->getProcessedRequestsStats($manager, $statsDate);
            $finishedRequestsStats = $requestDiffRepo->getFinishedRequestsCount($manager, $statsDate);

            $managerStats
                ->setCreatedAt(new \DateTime())
                ->setStatsDate($statsDate)
                ->setManager($manager)
                ->setAssignedRequests(count($assignedRequests))
                ->setRequestsInprogress(count($requestsInProgress))
                ->setRequestsProcessed($requestsProcessedStats['processedRequests'])
                ->setItemsProcessed($requestsProcessedStats['processedItems'])
                ->setProcessedPricesAmount($requestsProcessedStats['processedMoneyAmount'])
                ->setFinishedRequests($finishedRequestsStats)
            ;

            $output->writeln([
                'assignedRequests: ' . count($assignedRequests),
                'requestsInProgress: ' . count($requestsInProgress),
                'requestsProcessed: ' . $requestsProcessedStats['processedRequests'],
                'processedItems: ' . $requestsProcessedStats['processedItems'],
                'processedMoneyAmount: ' . $requestsProcessedStats['processedMoneyAmount'],
                'finishedRequestsStats: ' . $finishedRequestsStats,
                '================',
            ]);

            $em->persist($managerStats);
        }

        $em->flush();
    }
}