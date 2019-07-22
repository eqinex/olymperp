<?php
/**
 * Created by PhpStorm.
 * User: mazitovtr
 * Date: 07.05.19
 * Time: 16:28
 */

namespace AppBundle\Command;

use PurchaseBundle\Entity\Supplier;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ramsey\Uuid\Uuid;

class SupplierCodeGenerateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('olymp:supplier:codegenerate')
            ->setDescription('Command for generating supplier codes for 1C')
            ->setHelp('Command will regenerate empty 1C unique codes for suppliers')
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

        $suppliers = $em->getRepository(Supplier::class)->findBy(['oneSUniqueCode' => null]);

        $output->writeln([
            'Find suppliers..',
            '================',
            ''
        ]);

        if (!$suppliers) {
            $output->writeln('Suppliers not found!...');
        } else {

            $output->writeln(['Found ' . count($suppliers) . ' suppliers!...', '']);
            $output->writeln(['================================', '']);

            /** @var Supplier $supplier */
            foreach ($suppliers as $supplier) {
                do {
                    $code = Uuid::uuid4()->toString();
                    $supplierDuplicate = $em->getRepository(Supplier::class)->findOneBy(['oneSUniqueCode' => $code]);
                    if (!$supplierDuplicate) {
                        $supplier->setOneSUniqueCode($code);
                        $check = true;
                    } else {
                        $check = false;
                    }

                } while ($check == false);

                $supplier->setOneSUniqueCode($code);
                $output->writeln('Supplier with id = "' . $supplier->getId() . '" generate code="' . $code . '"');

                $em->persist($supplier);
                $em->flush();
            }

            $output->writeln(['=========================================================', '']);
            $output->writeln(['All suppliers have a code...Done!', '']);
        }
    }
}