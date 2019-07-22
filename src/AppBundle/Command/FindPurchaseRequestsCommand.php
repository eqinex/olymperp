<?php
/**
 * Created by PhpStorm.
 * User: mazitovtr
 * Date: 04.02.19
 * Time: 11:09
 */

namespace AppBundle\Command;

use AppBundle\Entity\User;
use PurchaseBundle\Entity\PurchaseRequest;
use PurchaseBundle\Entity\PurchaseRequestComment;
use PurchaseBundle\Entity\PurchaseRequestDiff;
use PurchaseBundle\PurchaseConstants;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FindPurchaseRequestsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('olymp:request:actualize')
            ->setDescription('Command for find purchase requests')
            ->setHelp('This command find all purchase requests')
            ->addArgument('countRequests', InputArgument::OPTIONAL, 'How many purchase requests?')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Twig\Error\Error
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getEntityManager();
        $translator = $this->getContainer()->get('translator');
        /** @var User $admin */
        $admin = $em->getRepository(User::class)->findOneBy([
            'admin' => 1
        ]);

        $countRequests = $input->getArgument('countRequests');

        $output->writeln([
            'Find requests...',
            '================',
            ''
        ]);

        if ($countRequests) {
            $output->writeln('You selected search by ' . $countRequests . ' purchase requests!');
        } else {
            $output->writeln('You selected search by default count purchase requests!');
        }

        $purchaseRequests = $em->getRepository(PurchaseRequest::class)->getLostPurchaseRequests($countRequests);

        $output->writeln(['================================', '']);

        if ($purchaseRequests) {

            $output->writeln(['Found ' . count($purchaseRequests) . ' purchase requests!...', '']);
            $output->writeln(['================================', '']);

            /** @var PurchaseRequest $purchaseRequest */
            foreach ($purchaseRequests as $purchaseRequest) {
                $output->writeln('Purchase request with id = "' . $purchaseRequest->getId() . '" and code="' . $purchaseRequest->getCode() . '"');

                $purchaseRequest->setStatus(PurchaseConstants::STATUS_NEEDS_FIXING);
                $purchaseRequest->setLeaderApproved(false);
                $purchaseRequest->setProjectLeaderApproved(false);
                $purchaseRequest->setLeader(null);
                $purchaseRequest->setPurchasingLeaderApproved(false);
                $purchaseRequest->setProductionLeaderApproved(false);
                $purchaseRequest->setFinancialLeaderApproved(false);
                $purchaseRequest->setProductionLeader(null);
                $purchaseRequest->setPurchasingLeader(null);
                $purchaseRequest->setFinancialLeader(null);
                $purchaseRequest->setProjectLeader(null);
                $purchaseRequest->setPurchasingManager(null);
                $purchaseRequest->setFinancialManager(null);
                $purchaseRequest->setDeliveryStatus(null);
                $purchaseRequest->setPaymentStatus(null);
                $purchaseRequest->setProductionStatus(null);

                $em->persist($purchaseRequest);

                $comment = $translator->trans('Request was automatic return "need fixing", because there were no updates for a month.');

                $comment = $this->addComment($purchaseRequest, $comment);

                $uof = $em->getUnitOfWork();
                $uof->computeChangeSets();

                $purchaseRequestChanges = $this->logChanges($purchaseRequest, $uof->getEntityChangeSet($purchaseRequest));

                foreach ($purchaseRequest->getRequestRecipients($admin) as $recipient) {
                    $this->sendEmail(
                        '{' . $translator->trans(ucfirst($purchaseRequest->getType())) . '} ' . $purchaseRequest->getCode(),
                        $recipient->getEmail(),
                        $this->getContainer()->get('templating')->render(
                            'emails/purchase/comment.html.twig', [
                            'user' => $admin,
                            'purchaseRequest' => $purchaseRequest,
                            'comment' => $comment
                        ])
                    );
                }

                if (!empty($purchaseRequestChanges)) {
                    /** @var User $recipient */
                    foreach ($purchaseRequest->getRequestRecipients($admin) as $recipient) {
                        $this->sendEmail(
                            '{' . $translator->trans(ucfirst($purchaseRequest->getType())) . '} ' . $purchaseRequest->getCode(),
                            $recipient->getEmail(),
                            $this->getContainer()->get('templating')->render(
                                'emails/purchase/updated.html.twig', [
                                    'user' => $admin,
                                    'purchaseRequest' => $purchaseRequest,
                                    'purchaseRequestChanges' => $purchaseRequestChanges,
                                    'state' => ''
                                ])
                        );
                    }
                }
            }

            $em->flush();
        } else {
            $output->writeln(['Purchase requests not found!...', '']);
        }

        $output->writeln(['=========================================================', '']);
        $output->writeln(['Purchase requests have status changed to "Need fixing"...Done!', '']);
    }

    /**
     * @param PurchaseRequest $purchaseRequest
     * @param $comment
     * @return PurchaseRequestComment
     * @throws \Doctrine\ORM\ORMException
     */
    protected function addComment(PurchaseRequest $purchaseRequest, $comment)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getEntityManager();
        /** @var User $admin */
        $admin = $em->getRepository(User::class)->findOneBy([
            'admin' => 1
        ]);

        $purchaseRequestComment = new PurchaseRequestComment();

        $changes['comment'][] = $purchaseRequestComment->getCommentText();
        $purchaseRequestComment
            ->setOwner($admin)
            ->setPurchaseRequest($purchaseRequest)
            ->setCommentText($comment)
            ->setCreatedAt(new \DateTime())
        ;
        $changes['comment'][] = $purchaseRequestComment->getCommentText();


        $this->logChanges($purchaseRequest, $changes);
        $em->persist($purchaseRequestComment);

        return $purchaseRequestComment;
    }

    /**
     * @param PurchaseRequest $purchaseRequest
     * @param $changeSet
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    protected function logChanges(PurchaseRequest $purchaseRequest, $changeSet)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getEntityManager();
        /** @var User $admin */
        $admin = $em->getRepository(User::class)->findOneBy([
            'admin' => 1
        ]);

        $purchaseRequestDiffs = [];
        foreach ($changeSet as $field => $changes) {
            if ($field == 'timings') {
                continue;
            }
            $oldValue = $this->prepareChangesValue($field, $changes[0]);
            $newValue = $this->prepareChangesValue($field, $changes[1]);
            if ($oldValue != $newValue) {
                $purchaseRequestDiff = new PurchaseRequestDiff();

                $purchaseRequestDiff
                    ->setChangedBy($admin)
                    ->setPurchaseRequest($purchaseRequest)
                    ->setField($field)
                    ->setOldValue($oldValue)
                    ->setNewValue($newValue)
                    ->setUpdatedAt(new \DateTime())
                ;

                $em->persist($purchaseRequestDiff);
                $purchaseRequestDiffs[] = $purchaseRequestDiff;
            }
        }

        return $purchaseRequestDiffs;
    }

    /**
     * @param $field
     * @param $value
     * @return string
     */
    protected function prepareChangesValue($field, $value)
    {
        if ($value instanceof \DateTime) {
            $value = $value->format('d/m/Y H:i');
        } elseif (!$value) {
            $value = 'no';
        } elseif ($value === true) {
            $value = 'yes';
        }
        
        return $value;
    }

    /**
     * @param $title
     * @param $recipient
     * @param $body
     */
    protected function sendEmail($title, $recipient, $body)
    {
        $email = new \Swift_Message('[OLYMP]' . $title);
        $email
            ->setFrom('olymp@npo-at.com')
            ->setTo($recipient)
            ->setBody($body, 'text/html');

        $this->getContainer()->get('mailer')->send($email);
    }
}