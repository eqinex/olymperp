<?php
/**
 * Created by PhpStorm.
 * User: mazitovtr
 * Date: 11.06.19
 * Time: 14:07
 */

namespace AppBundle\Command;

use DocumentBundle\Entity\Document;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DocumentCodeGenerateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('olymp:document:codegenerate')
            ->setDescription('Command for generating document codes for 1C')
            ->setHelp('Command will regenerate empty 1C unique codes for documents')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getEntityManager();

        $documents = $em->getRepository(Document::class)->findBy(['oneSUniqueCode' => null]);

        $output->writeln([
            'Find documents..',
            '================',
            ''
        ]);

        if (!$documents) {
            $output->writeln('Documents not found!...');
        } else {

            $output->writeln(['Found ' . count($documents) . ' documents!...', '']);
            $output->writeln(['================================', '']);

            /** @var Document $document */
            foreach ($documents as $document) {
                do {
                    $code = Uuid::uuid4()->toString();
                    $documentDuplicate = $em->getRepository(Document::class)->findOneBy(['oneSUniqueCode' => $code]);
                    if (!$documentDuplicate) {
                        $document->setOneSUniqueCode($code);
                        $check = true;
                    } else {
                        $check = false;
                    }

                } while ($check == false);

                $document->setOneSUniqueCode($code);
                $output->writeln('Document with id = "' . $document->getId() . '" generate code="' . $code . '"');

                $em->persist($document);
                $em->flush();
            }

            $output->writeln(['=========================================================', '']);
            $output->writeln(['All documents have a code...Done!', '']);
        }
    }
}