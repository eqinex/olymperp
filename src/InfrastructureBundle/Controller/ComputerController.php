<?php

namespace InfrastructureBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Repository\RepositoryAwareTrait;
use InfrastructureBundle\Entity\Computer;
use InfrastructureBundle\Entity\ComputerDiff;
use InfrastructureBundle\Entity\ComputerPart;
use InfrastructureBundle\Entity\ComputerParts;
use InfrastructureBundle\Exception\ServerTiedException;
use InfrastructureBundle\Service\Export\InfrastructureBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ComputerController extends Controller
{
    use RepositoryAwareTrait;
    const PER_PAGE = 20;

    /**
     * @Route("/infrastructure/computers", name="computers_list")
     */
    public function listAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);
        $orderBy = $request->get('orderBy');
        $order = $request->get('order');

        /** @var User $user */
        $user = $this->getUser();

        if (!$user->canViewInfrastructure()) {
            return $this->redirectToRoute('homepage');
        }

        $types = Computer::getTypesList();
        $computers = $this->getComputerRepository()->getComputers(
            $types,
            $filters,
            $orderBy,
            $order,
            $currentPage,
            self::PER_PAGE);
        $users = $this->getUserRepository()->getUsersGroupedByTeams();
        $rooms = $this->getUserRepository()->getRooms();

        $operationSystems = $this->getComputerPartRepository()->findBy(['type' => ComputerPart::TYPE_OPERATION_SYSTEM, 'deleted' => 0]);
        $processors = $this->getComputerPartRepository()->findBy(['type' => ComputerPart::TYPE_PROCESSOR, 'deleted' => 0]);
        $videoCards = $this->getComputerPartRepository()->findBy(['type' => ComputerPart::TYPE_VIDEO_CARD, 'deleted' => 0]);
        $rams = $this->getComputerPartRepository()->findBy(['type' => ComputerPart::TYPE_RAM, 'deleted' => 0]);
        $mouses = $this->getComputerPartRepository()->findBy(['type' => ComputerPart::TYPE_MOUSE, 'deleted' => 0]);
        $motherboards = $this->getComputerPartRepository()->findBy(['type' => ComputerPart::TYPE_MOTHERBOARD, 'deleted' => 0]);
        $monitors = $this->getComputerPartRepository()->findBy(['type' => ComputerPart::TYPE_MONITOR, 'deleted' => 0]);
        $keyboards = $this->getComputerPartRepository()->findBy(['type' => ComputerPart::TYPE_KEYBOARD, 'deleted' => 0]);
        $hdds = $this->getComputerPartRepository()->findBy(['type' => ComputerPart::TYPE_HDD, 'deleted' => 0]);

        $maxRows = $computers->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);


        return $this->render('infrastructure/computers/list.html.twig', [
            'computers' => $computers,
            'types' => $types,
            'ipTypes' => Computer::getIpTypesList(),
            'users' => $users,
            'operationSystems' => $operationSystems,
            'processors' => $processors,
            'videoCards' => $videoCards,
            'rams' => $rams,
            'mouses' => $mouses,
            'motherboards' => $motherboards,
            'monitors' => $monitors,
            'keyboards' => $keyboards,
            'hdds' => $hdds,
            'rooms' => $rooms,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'perPage' => self::PER_PAGE,
            'filters' => $filters,
            'orderBy' => $orderBy,
            'order' => $order
        ]);
    }

    /**
     * @Route("infrastructure/computers/{id}/details", name="computer_details")
     */
    public function detailsAction(Request $request)
    {
        $computerId = $request->get('id');
        /** @var Computer $computer */
        $computer = $this->getComputerRepository()->find($computerId);
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->canViewInfrastructure()) {
            return $this->redirect($request->headers->get('referer'));
        }

        $users = $this->getUserRepository()->getUsersGroupedByTeams();

        $operationSystems = $this->getComputerPartRepository()->findBy(['type' => ComputerPart::TYPE_OPERATION_SYSTEM, 'deleted' => 0]);
        $processors = $this->getComputerPartRepository()->findBy(['type' => ComputerPart::TYPE_PROCESSOR, 'deleted' => 0]);
        $videoCards = $this->getComputerPartRepository()->findBy(['type' => ComputerPart::TYPE_VIDEO_CARD, 'deleted' => 0]);
        $rams = $this->getComputerPartRepository()->findBy(['type' => ComputerPart::TYPE_RAM, 'deleted' => 0]);
        $mouses = $this->getComputerPartRepository()->findBy(['type' => ComputerPart::TYPE_MOUSE, 'deleted' => 0]);
        $motherboards = $this->getComputerPartRepository()->findBy(['type' => ComputerPart::TYPE_MOTHERBOARD, 'deleted' => 0]);
        $monitors = $this->getComputerPartRepository()->findBy(['type' => ComputerPart::TYPE_MONITOR, 'deleted' => 0]);
        $keyboards = $this->getComputerPartRepository()->findBy(['type' => ComputerPart::TYPE_KEYBOARD, 'deleted' => 0]);
        $hdds = $this->getComputerPartRepository()->findBy(['type' => ComputerPart::TYPE_HDD, 'deleted' => 0]);

        $type = 'computer';

        $computerChanges = $this->getComputerDiffRepository()->getComputerChanges($type, $computerId);

        return $this->render('infrastructure/computers/details.html.twig', [
            'computer' => $computer,
            'types' => Computer::getTypesList(),
            'ipTypes' => Computer::getIpTypesList(),
            'partsTypes' => ComputerPart::getTypesList(),
            'users' => $users,
            'operationSystems' => $operationSystems,
            'processors' => $processors,
            'videoCards' => $videoCards,
            'rams' => $rams,
            'mouses' => $mouses,
            'motherboards' => $motherboards,
            'monitors' => $monitors,
            'keyboards' => $keyboards,
            'hdds' => $hdds,
            'computerChanges' => $computerChanges
        ]);
    }

    /**
     * @Route("/infrastructure/servers", name="servers_list")
     */
    public function serversListAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);
        $orderBy = $request->get('orderBy');
        $order = $request->get('order');
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->canViewInfrastructure()) {
            return $this->redirectToRoute('homepage');
        }

        $serverTypes = Computer::getServerTypesList();
        $servers = $this->getComputerRepository()->getComputers($serverTypes, $filters, $orderBy, $order, $currentPage, self::PER_PAGE);
        $operationSystems = $this->getComputerPartRepository()->findBy(['type' => ComputerPart::TYPE_OPERATION_SYSTEM, 'deleted' => 0]);
        $hosts = $this->getComputerRepository()->findBy(['type' => Computer::SERVER_TYPE_HOST, 'deleted' => 0]);

        $maxRows = $servers->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('/infrastructure/servers/list.html.twig', [
            'servers' => $servers,
            'operationSystems' => $operationSystems,
            'serverTypes' => $serverTypes,
            'ipTypes' => Computer::getIpTypesList(),
            'hosts' => $hosts,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'filters' => $filters,
            'perPage' => self::PER_PAGE,
            'orderBy' => $orderBy,
            'order' => $order
        ]);
    }

    /**
     * @Route("infrastructure/servers/{id}/details", name="server_details")
     */
    public function serverDetailsAction(Request $request)
    {
        $serverId = $request->get('id');
        /** @var Computer $server */
        $server = $this->getComputerRepository()->find($serverId);
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->canViewInfrastructure()) {
            return $this->redirect($request->headers->get('referer'));
        }
        $operationSystems = $this->getComputerPartRepository()->findBy(['type' => ComputerPart::TYPE_OPERATION_SYSTEM, 'deleted' => 0]);
        $hosts = $this->getComputerRepository()->findBy(['type' => Computer::SERVER_TYPE_HOST, 'deleted' => 0]);

        $host = $this->getComputerRepository()->findOneBy([
            'ipAddress' => $server->getHost(),
            'type' => Computer::SERVER_TYPE_HOST,
            'deleted' => 0
        ]);
        $type = 'computer';
        $serverChanges = $this->getComputerDiffRepository()->getComputerChanges($type, $serverId);

        $guestServers = $this->getComputerRepository()->findBy(['host' => $server->getIpAddress(), 'type' => Computer::SERVER_TYPE_GUEST, 'deleted' => 0]);

        return $this->render('infrastructure/servers/details.html.twig', [
            'server' => $server,
            'guestServers' => $guestServers,
            'host' => $host,
            'ipTypes' => Computer::getIpTypesList(),
            'serverTypes' => Computer::getServerTypesList(),
            'hosts' => $hosts,
            'operationSystems' => $operationSystems,
            'serverChanges' => $serverChanges
        ]);
    }

    /**
     * @Route("/infrastructure/printers", name="printers_list")
     */
    public function printersListAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);
        $orderBy = $request->get('orderBy');
        $order = $request->get('order');
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->canViewInfrastructure()) {
            return $this->redirectToRoute('homepage');
        }

        $type = 'printer';
        $printers = $this->getComputerRepository()->getComputers($type, $filters, $orderBy, $order, $currentPage, self::PER_PAGE);

        $maxRows = $printers->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('/infrastructure/printers/list.html.twig', [
            'printers' => $printers,
            'ipTypes' => Computer::getIpTypesList(),
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'filters' => $filters,
            'perPage' => self::PER_PAGE,
            'orderBy' => $orderBy,
            'order' => $order
        ]);
    }

    /**
     * @Route("infrastructure/printers/{id}/details", name="printer_details")
     */
    public function printerDetailsAction(Request $request)
    {
        $printerId = $request->get('id');
        /** @var Computer $printer */
        $printer = $this->getComputerRepository()->find($printerId);
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->canViewInfrastructure()) {
            return $this->redirect($request->headers->get('referer'));
        }
        $type = 'computer';
        $printerChanges = $this->getComputerDiffRepository()->getComputerChanges($type, $printerId);

        return $this->render('infrastructure/printers/details.html.twig', [
            'printer' => $printer,
            'ipTypes' => Computer::getIpTypesList(),
            'printerChanges' => $printerChanges
        ]);
    }

    /**
     * @Route("/infrastructure/commutators", name="commutators_list")
     */
    public function commutatorsListAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);
        $orderBy = $request->get('orderBy');
        $order = $request->get('order');
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->canViewInfrastructure()) {
            return $this->redirectToRoute('homepage');
        }

        $type = 'commutator';
        $commutators = $this->getComputerRepository()->getComputers($type, $filters, $orderBy, $order, $currentPage, self::PER_PAGE);

        $maxRows = $commutators->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('/infrastructure/commutators/list.html.twig', [
            'commutators' => $commutators,
            'ipTypes' => Computer::getIpTypesList(),
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'filters' => $filters,
            'perPage' => self::PER_PAGE,
            'orderBy' => $orderBy,
            'order' => $order
        ]);
    }

    /**
     * @Route("infrastructure/commutators/{id}/details", name="commutator_details")
     */
    public function commutatorDetailsAction(Request $request)
    {
        $commutatorId = $request->get('id');
        /** @var Computer $printer */
        $commutator = $this->getComputerRepository()->find($commutatorId);
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->canViewInfrastructure()) {
            return $this->redirect($request->headers->get('referer'));
        }

        $type = 'computer';
        $commutatorChanges = $this->getComputerDiffRepository()->getComputerChanges($type, $commutatorId);

        return $this->render('infrastructure/commutators/details.html.twig', [
            'commutator' => $commutator,
            'ipTypes' => Computer::getIpTypesList(),
            'commutatorChanges' => $commutatorChanges
        ]);
    }

    /**
     * @Route("/infrastructure/computers/add", name="computers_add")
     */
    public function addComputerAction(Request $request)
    {
        $computerDetails = $request->get('computer');
        $computerPartDetails = $request->get('computerPart');

        /** @var User $user */
        $user = $this->getUser();

        if (!$user->canViewInfrastructure()) {
            return $this->redirect($request->headers->get('referer'));
        }

        if (!empty($computerDetails)) {
            $computer = new Computer();

            $computer = $this->buildComputer($computer, $computerDetails, $computerPartDetails);

            $em = $this->getEm();
            $em->persist($computer);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
        }
    }

    /**
     * Remove computer
     *
     * @Route("/infrastructure/computers/{id}/delete", name="delete_computer")
     */
    public function deleteComputerAction(Request $request)
    {
        $computerId = $request->get('id');
        $listPath = $request->get('listPath');

        /** @var Computer $computer */
        $computer = $this->getComputerRepository()->find($computerId);

        if ($computer->getType() == Computer::SERVER_TYPE_HOST) {
            $flashbag = $this->get('session')->getFlashBag();
            $flashbag->clear();

            try {
                $this->validateDeleteServer($computer);
                $computer->setDeleted(true);
                $em = $this->getDoctrine()->getManager();
                $em->flush();
            } catch (ServerTiedException $exception) {
                $flashbag->add('danger', $exception->getMessage());
            }
        } else {
            $computer->setDeleted(true);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
        }

        return $this->redirectToRoute($listPath);
    }

    /**
     * @Route("/infrastructure/computers/{id}/edit", name="computers_edit")
     */
    public function editComputerAction(Request $request)
    {
        $computerDetails = $request->get('computer');
        $computerId = $request->get('id');
        /** @var Computer $computer */
        $computer = $this->getComputerRepository()->find($computerId);
        $computerPartDetails = $request->get('computerPart');

        /** @var User $user */
        $user = $this->getUser();

        if (!$user->canViewInfrastructure()) {
            return $this->redirect($request->headers->get('referer'));
        }

        /** @var Computer $computer */
        $computer = $this->buildComputer($computer, $computerDetails, $computerPartDetails);

        $em = $this->getEm();
        $em->persist($computer);
        $uof = $em->getUnitOfWork();
        $uof->computeChangeSets();

        $this->logChanges($computer, $uof->getEntityChangeSet($computer));

        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param Computer $computer
     * @param $computerDetails
     * @param $computerPartDetails
     * @return Computer
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function buildComputer(Computer $computer, $computerDetails, $computerPartDetails)
    {
        $computer
            ->setType($computerDetails['type'])
            ->setIpType($computerDetails['ipType'])
            ->setInventoryNumber($computerDetails['inventoryNumber']);

        if (in_array($computerDetails['type'], Computer::getTypesList())) {

            $employee = $this->getUserRepository()->find($computerDetails['employee']);

            if (empty($computerPartDetails['operationSystem'])) {
                $operationSystem = $this->getComputerPartRepository()->find($computerDetails['operationSystem']);
            } else {
                $operationSystem = $this->addComputerPart('operation_system', $computerPartDetails['operationSystem']);
            }
            if (empty($computerPartDetails['processor'])) {
                $processor = $this->getComputerPartRepository()->find($computerDetails['processor']);
            } else {
                $processor = $this->addComputerPart('processor', $computerPartDetails['processor']);
            }
            if (empty($computerPartDetails['ram'])) {
                $ram = $this->getComputerPartRepository()->find($computerDetails['ram']);
            } else {
                $ram = $this->addComputerPart('ram', $computerPartDetails['ram']);
            }
            if (empty($computerPartDetails['motherboard'])) {
                $motherboard = $this->getComputerPartRepository()->find($computerDetails['motherboard']);
            } else {
                $motherboard = $this->addComputerPart('motherboard', $computerPartDetails['motherboard']);
            }
            if (empty($computerPartDetails['videoCard'])) {
                $videoCard = $this->getComputerPartRepository()->find($computerDetails['videoCard']);
            } else {
                $videoCard = $this->addComputerPart('video_card', $computerPartDetails['videoCard']);
            }
            if (empty($computerPartDetails['firstHdd'])) {
                $firstHdd = $this->getComputerPartRepository()->find($computerDetails['firstHdd']);
            } else {
                $firstHdd = $this->addComputerPart('hdd', $computerPartDetails['firstHdd']);
            }
            if (empty($computerPartDetails['secondHdd'])) {
                $secondHdd = $this->getComputerPartRepository()->find($computerDetails['secondHdd']);
            } else {
                $secondHdd = $this->addComputerPart('hdd', $computerPartDetails['secondHdd']);
            }
            if (!empty($computerPartDetails['monitor'])) {
                $computerPart = new ComputerPart();
                $computerPart
                    ->setType('monitor')
                    ->setName($computerPartDetails['monitor'])
                ;
                $this->getEm()->persist($computerPart);
                $this->getEm()->flush();

                $monitor = $this->getComputerPartRepository()->find($computerPart);

                if ($monitor) {
                    $computerParts = new ComputerParts();
                    $computerParts
                        ->setComputer($computer)
                        ->setPart($monitor);

                    $this->getEm()->persist($computerParts);
                }
            } else {

                /** @var ComputerParts $computerPart */
                $computerParts = $this->getComputerPartsRepository()->findBy([
                    'computer' => $computer,
                ]);

                if ($computerParts) {
                    foreach ($computerParts as $computerPart) {
                        $this->getEm()->remove($computerPart);
                    }
                }
                
                if (!empty($computerDetails['monitors'])) {
                    foreach ($computerDetails['monitors'] as $part) {
                        $computerPart = new ComputerParts();
                        /** @var ComputerPart $part */
                        $part = $this->getComputerPartRepository()->find($part);

                        $computerPart
                            ->setComputer($computer)
                            ->setPart($part);

                        $this->getEm()->persist($computerPart);
                    }
                }
            }

            if (empty($computerPartDetails['keyboard'])) {
                $keyboard = $this->getComputerPartRepository()->find($computerDetails['keyboard']);
            } else {
                $keyboard = $this->addComputerPart('keyboard', $computerPartDetails['keyboard']);
            }
            if (empty($computerPartDetails['mouse'])) {
                $mouse = $this->getComputerPartRepository()->find($computerDetails['mouse']);
            } else {
                $mouse = $this->addComputerPart('mouse', $computerPartDetails['mouse']);
            }

            $computer
                ->setName($computerDetails['name'])
                ->setIpAddressComputer($computerDetails['ipAddress'])
                ->setMacAddressComputer($computerDetails['macAddress'])
                ->setEmployee($employee)
                ->setKeyInSystem($computerDetails['keyInSystem'])
                ->setKeyOnSticker($computerDetails['keyOnSticker'])
                ->setDomain($computerDetails['domain'])
                ->setLegal(!empty($computerDetails['legal']) ? true : false)
                ->setOperationSystem($operationSystem)
                ->setProcessor($processor)
                ->setRam($ram)
                ->setMotherboard($motherboard)
                ->setVideoCard($videoCard)
                ->setHddFirst($firstHdd)
                ->setHddSecond($secondHdd)
                ->setKeyboard($keyboard)
                ->setMouse($mouse);

            if ($computerDetails['type'] != Computer::TYPE_DESKTOP_COMPUTER) {
                $computer->setModel($computerDetails['model']);
            }
        }

        if (in_array($computerDetails['type'], Computer::getServerTypesList())) {
            $operationSystem = $this->getComputerPartRepository()->find($computerDetails['operationSystem']);

            $computer
                ->setOperationSystem($operationSystem)
                ->setIpAddress($computerDetails['ipAddress'])
                ->setMacAddress($computerDetails['macAddress'])
                ->setInstalledService($computerDetails['installedService'])
                ->setSerialNumber($computerDetails['serialNumber'])
                ->setDomain($computerDetails['domain'])
                ->setLegal(!empty($computerDetails['legal']) ? true : false)
                ->setRoom($computerDetails['room'])
            ;

            if ($computerDetails['type'] == Computer::SERVER_TYPE_GUEST) {
                $computer->setHost($computerDetails['host']);
            }
        }

        if ($computerDetails['type'] == 'printer' or $computerDetails['type'] == 'commutator') {
            $computer
                ->setIpAddress($computerDetails['ipAddress'])
                ->setMacAddress($computerDetails['macAddress'])
                ->setModel($computerDetails['model'])
                ->setRoom($computerDetails['room'])
            ;

        }

        if ($computerDetails['type'] == 'printer') {
            $computer
                ->setCartridgeType($computerDetails['cartridgeType'])
                ->setQuantity($computerDetails['quantity'])
            ;
        }

        return $computer;
    }

    /**
     * @param $type
     * @param $name
     * @return ComputerPart
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function addComputerPart($type, $name)
    {
        $computerPart = new ComputerPart();
        $computerPart
            ->setType($type)
            ->setName($name);

        $em = $this->getEm();
        $em->persist($computerPart);
        $em->flush();

        return $computerPart;
    }

    /**
     * @Route("/infrastructure/servers/add", name="server_add")
     */
    public function addServerAction(Request $request)
    {
        $serverDetails = $request->get('server');

        /** @var User $user */
        $user = $this->getUser();

        if (!$user->canViewInfrastructure()) {
            return $this->redirect($request->headers->get('referer'));
        }

        if (!empty($serverDetails)) {
            $server = new Computer();

            $server = $this->buildComputer($server, $serverDetails, '');

            $em = $this->getEm();
            $em->persist($server);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
        }
    }

    /**
     * @Route("/infrastructure/servers/{id}/edit", name="server_edit")
     */
    public function editServerAction(Request $request)
    {
        $serverDetails = $request->get('server');
        $serverId = $request->get('id');
        /** @var Computer $server */
        $server = $this->getComputerRepository()->find($serverId);

        /** @var User $user */
        $user = $this->getUser();

        if (!$user->canViewInfrastructure()) {
            return $this->redirect($request->headers->get('referer'));
        }

        $server = $this->buildComputer($server, $serverDetails, '');

        $em = $this->getEm();
        $em->persist($server);
        $uof = $em->getUnitOfWork();
        $uof->computeChangeSets();

        $this->logChanges($server, $uof->getEntityChangeSet($server));
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/infrastructure/printers/add", name="printer_add")
     */
    public function addPrinterAction(Request $request)
    {
        $printerDetails = $request->get('printer');

        /** @var User $user */
        $user = $this->getUser();

        if (!$user->canViewInfrastructure()) {
            return $this->redirect($request->headers->get('referer'));
        }

        if (!empty($printerDetails)) {
            $printer = new Computer();

            $printer = $this->buildComputer($printer, $printerDetails, '');

            $em = $this->getEm();
            $em->persist($printer);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
        }
    }

    /**
     * @Route("/infrastructure/printers/{id}/edit", name="printer_edit")
     */
    public function editPrinterAction(Request $request)
    {
        $printerDetails = $request->get('printer');
        $printerId = $request->get('id');
        /** @var Computer $printer */
        $printer = $this->getComputerRepository()->find($printerId);

        /** @var User $user */
        $user = $this->getUser();

        if (!$user->canViewInfrastructure()) {
            return $this->redirect($request->headers->get('referer'));
        }

        $printer = $this->buildComputer($printer, $printerDetails, '');

        $em = $this->getEm();
        $em->persist($printer);
        $uof = $em->getUnitOfWork();
        $uof->computeChangeSets();

        $this->logChanges($printer, $uof->getEntityChangeSet($printer));
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/infrastructure/commutators/add", name="commutator_add")
     */
    public function addCommutatorAction(Request $request)
    {
        $commutatorDetails = $request->get('commutator');

        /** @var User $user */
        $user = $this->getUser();

        if (!$user->canViewInfrastructure()) {
            return $this->redirect($request->headers->get('referer'));
        }

        if (!empty($commutatorDetails)) {
            $commutator = new Computer();

            $commutator = $this->buildComputer($commutator, $commutatorDetails, '');

            $em = $this->getEm();
            $em->persist($commutator);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
        }
    }

    /**
     * @Route("/infrastructure/commutators/{id}/edit", name="commutator_edit")
     */
    public function editCommutatorAction(Request $request)
    {
        $commutatorDetails = $request->get('commutator');
        $commutatorId = $request->get('id');
        /** @var Computer $commutator */
        $commutator = $this->getComputerRepository()->find($commutatorId);

        /** @var User $user */
        $user = $this->getUser();

        if (!$user->canViewInfrastructure()) {
            return $this->redirect($request->headers->get('referer'));
        }

        $commutator = $this->buildComputer($commutator, $commutatorDetails, '');

        $em = $this->getEm();
        $em->persist($commutator);
        $uof = $em->getUnitOfWork();
        $uof->computeChangeSets();

        $this->logChanges($commutator, $uof->getEntityChangeSet($commutator));
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param $computer
     * @param $changeSet
     * @return array
     */
    protected function logChanges($computer, $changeSet)
    {
        $em = $this->getDoctrine()->getManager();
        $computerDiffs = [];
        foreach ($changeSet as $field => $changes) {
            if ($field == 'updatedAt') {
                continue;
            }
            $oldValue = $this->prepareChangesValue($field, $changes[0]);
            $newValue = $this->prepareChangesValue($field, $changes[1]);
            if ($oldValue != $newValue && $oldValue) {
                $computerDiff = new ComputerDiff();

                $computerDiff
                    ->setChangedBy($this->getUser())
                    ->setComputer($computer)
                    ->setField($field)
                    ->setOldValue($oldValue)
                    ->setNewValue($newValue)
                    ->setUpdatedAt(new \DateTime())
                ;

                $em->persist($computerDiff);
                $computerDiffs = $computerDiff;
            }
        }

        return $computerDiffs;
    }

    /**
     * @param $field
     * @param $value
     * @return int|string
     */
    protected function prepareChangesValue($field, $value)
    {
        if ($value === true && $field == 'legal') {
            $value = 'Yes';
        } elseif ($value === false && $field == 'legal') {
            $value = 'No';
        } elseif ($value instanceof ComputerPart) {
            /** @var ComputerPart $value */
            $value = $value->getName();
        }

        return $value;
    }

    /**
     * @param $server
     * @throws ServerTiedException
     */
    protected function validateDeleteServer($server)
    {
        /** @var ComputerPart $computerPart */
        $serverTied = $this->getComputerRepository()->findServerTied($server);

        if (!empty($serverTied)) {
            throw new ServerTiedException($this->get('translator'), $server->getIpAddress(), implode(", ", $serverTied));
        }
    }

    /**
     * @Route("/infrastructure/{exportType}/export-infrastructure", name="export_infrastructure")
     */
    public function exportInfrastructureAction(Request $request)
    {
        $filters = $request->get('filters', []);
        $order = $request->get('order', 'asc');
        $orderBy = $request->get('orderBy', 'priority');
        $exportType = $request->get('exportType');

        if ($exportType == 'computers') {
            $type = Computer::getTypesList();
        } elseif ($exportType == 'servers') {
            $type = Computer::getServerTypesList();
        } elseif ($exportType == 'printers') {
            $type = 'printer';
        } else {
            $type = 'commutator';
        }

        if ($exportType == 'computer-parts') {
            $computers = $this->getComputerPartRepository()->getAvailableComputerParts($filters);
        } else {
            $computers = $this->getComputerRepository()->getAvailableComputers($type, $filters, $orderBy, $order);
        }

        $exportBuilder = new InfrastructureBuilder($this->get('phpexcel'), $this->get('translator'));

        $phpExcelObject = $exportBuilder->build($computers, $exportType);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding header
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'infrastructure_' . $exportType .'.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

}