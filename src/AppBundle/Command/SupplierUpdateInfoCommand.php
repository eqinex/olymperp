<?php


namespace AppBundle\Command;

use PurchaseBundle\Entity\Supplier;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class SupplierUpdateInfoCommand  extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('olymp:supplier:updateinfo')
            ->setDescription('Command for update supplier')
            ->setHelp('Command will automatically update 25 suppliers that have not been updated for more than two weeks')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getEntityManager();

        $helper = $this->getHelper('question');

        $output->writeln(['====================================', '']);

        $question = new Question('Please enter limit suppliers(by default 25): ', '25');

        $limit = $helper->ask($input, $output, $question);

        $output->writeln(['====================================', '']);
        $output->writeln(['Search supplier', '']);
        $output->writeln(['====================================', '']);

        $suppliers = $em->getRepository(Supplier::class)->getSupplierReadyToUpdate($limit);

        $supplierService = $this->getContainer()->get('service.supplier');

        if (empty($suppliers)) {
            $output->writeln(['Suppliers not found!...', '']);
        } else {
            foreach ($suppliers as $supplier) {
                $output->writeln(['Found supplier "' . $supplier->getTitle() . '"', '']);

                $supplierService->updateSupplierInfo($supplier);
            }
        }
        $output->writeln(['====================================', '']);
    }
}