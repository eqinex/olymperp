<?php

namespace PurchaseBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectTask;
use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use AppBundle\Exception\MaxFileSizeException;
use AppBundle\Repository\RepositoryAwareTrait;
use AppBundle\Utils\StringUtils;
use PurchaseBundle\Entity\Invoice;
use PurchaseBundle\Entity\InvoiceRegistry;
use PurchaseBundle\Entity\PurchaseRequest;
use PurchaseBundle\Entity\PurchaseRequestComment;
use PurchaseBundle\Entity\PurchaseRequestDiff;
use PurchaseBundle\Entity\PurchaseRequestFavorite;
use PurchaseBundle\Entity\RequestFile;
use PurchaseBundle\Entity\RequestItem;
use PurchaseBundle\Entity\RequestMovement;
use PurchaseBundle\Entity\RequestTimings;
use ProductionBundle\Entity\Ware;
use PurchaseBundle\Entity\Supplier;
use PurchaseBundle\Entity\Warehouse;
use PurchaseBundle\PurchaseConstants;
use PurchaseBundle\Service\Export\GanttChartRequestsExportBuilder;
use PurchaseBundle\Service\Export\ProjectRequestsExportBuilder;
use PurchaseBundle\Service\Export\RequestsExportBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class RequestController extends Controller
{
    use RepositoryAwareTrait;

    const PER_PAGE = 50;

    /**
     * @Route("/project/{id}/purchases/board", name="project_requests_board")
     */
    public function projectRequestBoardAction(Request $request)
    {
        $projectId = $request->get('id');
        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        $prePurchaseRequests = $this->getPurchaseRequestRepository()->findBy([
            'project' => $projectId,
            'status' => [
                PurchaseConstants::STATUS_NEW,
                PurchaseConstants::STATUS_NEEDS_FIXING,
                PurchaseConstants::STATUS_NEEDS_LEADER_APPROVAL,
                PurchaseConstants::STATUS_NEEDS_PRODUCTION_LEADER_APPROVAL,
                PurchaseConstants::STATUS_NEEDS_PROJECT_LEADER_APPROVE
            ]
        ]);

        $queuedRequests = $this->getPurchaseRequestRepository()->findBy([
            'project' => $projectId,
            'status' => [
                PurchaseConstants::STATUS_NEEDS_PURCHASING_MANAGER,
                PurchaseConstants::STATUS_MANAGER_ASSIGNED,
            ]
        ]);

        $checkingPricesRequests = $this->getPurchaseRequestRepository()->findBy([
            'project' => $projectId,
            'status' => [
                PurchaseConstants::STATUS_ON_PRELIMINARY_ESTIMATE,
                PurchaseConstants::STATUS_NEEDS_PRELIMINARY_ESTIMATE_APPROVE,
                PurchaseConstants::STATUS_MANAGER_STARTED_WORK,
            ]
        ]);

        $signingContractRequests = $this->getPurchaseRequestRepository()->findBy([
            'project' => $projectId,
            'status' => [
                PurchaseConstants::STATUS_MANAGER_FINISHED_WORK
            ],
            'deliveryStatus' => [
                PurchaseConstants::DELIVERY_STATUS_AWAITING_DELIVERY
            ],
            'paymentStatus' => [
                PurchaseConstants::PAYMENT_STATUS_NEEDS_PAYMENT,
            ],
            'invoicePayment' => [PurchaseRequest::PAYMENT_ACCOUNT_UFK, PurchaseRequest::PAYMENT_ACCOUNT_SPECIAL],
            'expensesType' => [PurchaseRequest::EXPENSES_TYPE_MATERIALS, PurchaseRequest::EXPENSES_TYPE_OTHER_DIRECT]
        ]);

        $additionalCostRequests = $this->getPurchaseRequestRepository()->findBy([
            'project' => $projectId,
            'status' => [
                PurchaseConstants::STATUS_MANAGER_FINISHED_WORK
            ],
            'deliveryStatus' => [
                PurchaseConstants::DELIVERY_STATUS_AWAITING_DELIVERY
            ],
            'paymentStatus' => [
                PurchaseConstants::PAYMENT_STATUS_NEEDS_PAYMENT,
            ],
            'invoicePayment' => [PurchaseRequest::PAYMENT_ACCOUNT_UFK, PurchaseRequest::PAYMENT_ACCOUNT_SPECIAL],
            'expensesType' => [PurchaseRequest::EXPENSES_TYPE_ADDITIONAL]
        ]);

        $inDeliveryRequests = $this->getPurchaseRequestRepository()->findBy([
            'project' => $projectId,
            'status' => [
                PurchaseConstants::STATUS_MANAGER_FINISHED_WORK
            ],
            'deliveryStatus' => [
                PurchaseConstants::DELIVERY_STATUS_IN_DELIVERY
            ],
        ]);

        $needsPaymentRequests = $this->getPurchaseRequestRepository()->findBy([
            'project' => $projectId,
            'status' => [
                PurchaseConstants::STATUS_MANAGER_FINISHED_WORK
            ],
            'deliveryStatus' => [
                PurchaseConstants::DELIVERY_STATUS_DELIVERED
            ],
        ]);

        $doneRequests = $this->getPurchaseRequestRepository()->findBy([
            'project' => $projectId,
            'status' => [
                PurchaseConstants::STATUS_DONE
            ],
        ]);

        $materialsNeedsPaymentRequests = $this->getPurchaseRequestRepository()->findBy([
            'project' => $projectId,
            'paymentStatus' => [
                PurchaseConstants::PAYMENT_STATUS_NEEDS_PAYMENT
            ],
            'expensesType' => [
                PurchaseRequest::EXPENSES_TYPE_MATERIALS
            ]
        ]);

        $materialsPaidRequests = $this->getPurchaseRequestRepository()->findBy([
            'project' => $projectId,
            'paymentStatus' => [
                PurchaseConstants::PAYMENT_STATUS_PAID
            ],
            'expensesType' => [
                PurchaseRequest::EXPENSES_TYPE_MATERIALS
            ]
        ]);

        $additionalNeedsPaymentRequests = $this->getPurchaseRequestRepository()->findBy([
            'project' => $projectId,
            'paymentStatus' => [
                PurchaseConstants::PAYMENT_STATUS_NEEDS_PAYMENT
            ],
            'expensesType' => [
                PurchaseRequest::EXPENSES_TYPE_ADDITIONAL
            ]
        ]);

        $additionalPaidRequests = $this->getPurchaseRequestRepository()->findBy([
            'project' => $projectId,
            'paymentStatus' => [
                PurchaseConstants::PAYMENT_STATUS_PAID
            ],
            'expensesType' => [
                PurchaseRequest::EXPENSES_TYPE_ADDITIONAL
            ]
        ]);

        $otherDirectNeedsPaymentRequests = $this->getPurchaseRequestRepository()->findBy([
            'project' => $projectId,
            'paymentStatus' => [
                PurchaseConstants::PAYMENT_STATUS_NEEDS_PAYMENT
            ],
            'expensesType' => [
                PurchaseRequest::EXPENSES_TYPE_OTHER_DIRECT
            ]
        ]);

        $otherDirectPaidRequests = $this->getPurchaseRequestRepository()->findBy([
            'project' => $projectId,
            'paymentStatus' => [
                PurchaseConstants::PAYMENT_STATUS_PAID
            ],
            'expensesType' => [
                PurchaseRequest::EXPENSES_TYPE_OTHER_DIRECT
            ]
        ]);

        $notCategorizedNeedsPaymentRequests = $this->getPurchaseRequestRepository()->findBy([
            'project' => $projectId,
            'paymentStatus' => [
                PurchaseConstants::PAYMENT_STATUS_NEEDS_PAYMENT
            ],
            'expensesType' => [
                PurchaseRequest::EXPENSES_TYPE_NOT_CATEGORIZED
            ]
        ]);

        $notCategorizedPaidRequests = $this->getPurchaseRequestRepository()->findBy([
            'project' => $projectId,
            'paymentStatus' => [
                PurchaseConstants::PAYMENT_STATUS_PAID
            ],
            'expensesType' => [
                PurchaseRequest::EXPENSES_TYPE_NOT_CATEGORIZED
            ]
        ]);

        $purchasingTeam = $this->getTeamRepository()->findOneBy(['purchasesTeam' => true]);

        return $this->render('purchase/project_requests_board.html.twig', [
            'project' => $project,
            'purchasingTeam' => $purchasingTeam,
            'prePurchaseRequests' => $prePurchaseRequests,
            'queuedRequests' => $queuedRequests,
            'checkingPricesRequests' => $checkingPricesRequests,
            'signingContractRequests' => $signingContractRequests,
            'additionalCostRequests' => $additionalCostRequests,
            'inDeliveryRequests' => $inDeliveryRequests,
            'needsPaymentRequests' => $needsPaymentRequests,
            'doneRequests' => $doneRequests,
            'materialsNeedsPaymentRequests' => $materialsNeedsPaymentRequests,
            'materialsPaidRequests' => $materialsPaidRequests,
            'additionalNeedsPaymentRequests' => $additionalNeedsPaymentRequests,
            'additionalPaidRequests' => $additionalPaidRequests,
            'otherDirectNeedsPaymentRequests' => $otherDirectNeedsPaymentRequests,
            'otherDirectPaidRequests' => $otherDirectPaidRequests,
            'notCategorizedNeedsPaymentRequests' => $notCategorizedNeedsPaymentRequests,
            'notCategorizedPaidRequests' => $notCategorizedPaidRequests
        ]);
    }

    /**
     * @Route("/project/{id}/purchases", name="project_requests_list")
     */
    public function listAction(Request $request)
    {
        $projectId = $request->get('id');
        $filters = $request->get('filters', []);
        $currentPage = $request->get('page', 1);
        $orderBy = $request->get('orderBy');
        $order = $request->get('order');

        $filters['project'] = $projectId;

        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);
        $categories = $this->getPurchaseRequestCategoriesRepository()->findAll();
        $units = $this->getUnitRepository()->findAll();
        $wares = $project->getWares();
        $warehouses = $this->getWarehouseRepository()->findAll();

        $purchaseRequests = $this->getPurchaseRequestRepository()->getPurchaseRequests(
            'all-requests',
            $this->getUser(),
            $filters,
            $orderBy,
            $order,
            $currentPage,
            self::PER_PAGE
        );

        $maxRows = $purchaseRequests->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        $purchaseRequest = new PurchaseRequest();
        $team = $this->getUser()->getTeam();
        /** @var Team $team */
        if (!$team) {
            $team = $this->getTeamRepository()->findOneBy(['code' => 'АТ']);
        }

        $requestCode = $team->getCode() . '-' . ($team->getLastPurchaseId() + 1) . '-' . date('ymd');
        $purchaseRequest->setCode($requestCode);

        $suppliesCategoriesGrouped = $this->getSuppliesCategoryRepository()->getCategoriesGroupedByParent();

        return $this->render('purchase/project_purchases.html.twig', [
            'project' => $project,
            'purchaseRequests' => $purchaseRequests,
            'categories' => $categories,
            'suppliesCategoriesGrouped' => $suppliesCategoriesGrouped,
            'units' => $units,
            'purchaseRequest' => $purchaseRequest,
            'states' => PurchaseConstants::getStatesList(),
            'wares' => $wares,
            'filters' => $filters,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'warehouses' => $warehouses
        ]);
    }

    /**
     * @Route("/project/{id}/purchases/export", name="export_request_list")
     */
    public function exportProjectListAction(Request $request)
    {
        $projectId = $request->get('id');
        $filters = $request->get('filters', []);
        $filters['project'] = $projectId;

        if (!$this->getUser()->canViewFinancialInfo()) {
            return $this->redirect($request->headers->get('referer'));
        }

        $purchaseRequests = $this->getPurchaseRequestRepository()->getRequestsListForExport($filters);
        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        $exportBuilder = new ProjectRequestsExportBuilder($this->get('phpexcel'));

        $phpExcelObject = $exportBuilder->build($purchaseRequests);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding header
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            StringUtils::transliterate($project->getName()) . '.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Route("/project/{id}/purchases/export-gantt-chart", name="export_gantt_chart_request_list")
     */
    public function exportGanttChartRequestListAction(Request $request)
    {
        $projectId = $request->get('id');
        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);
        $filters['project'] = $project->getId();

        if (!$this->getUser()->canViewFinancialInfo()) {
            return $this->redirect($request->headers->get('referer'));
        }

        $wares = $this->getWareRepository()->getWaresForGanttChart($project);

        $purchaseRequests = $this->getPurchaseRequestRepository()->findBy([
            'project' => $project->getId(),
            'ware' => null
        ]);

        $exportBuilder = new GanttChartRequestsExportBuilder($this->get('phpexcel'));

        $phpExcelObject = $exportBuilder->build($wares, $purchaseRequests, $project);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding header
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'Gantt ' . StringUtils::transliterate($project->getName()) . '.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Route("/request/export", name="export_requests_list")
     */
    public function exportListAction(Request $request)
    {
        $filters = $request->get('filters', []);

        if (!$this->getUser()->canViewFinancialInfo()) {
            return $this->redirectToRoute('homepage');
        }

        $purchaseRequests = $this->getPurchaseRequestRepository()->getRequestsListForExport($filters);

        $exportBuilder = new RequestsExportBuilder($this->get('phpexcel'), $this->get('translator'));

        $phpExcelObject = $exportBuilder->build($purchaseRequests);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding header
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'request_report.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Route("/purchases/{type}", name="requests_list")
     */
    public function purchasesListAction(Request $request)
    {
        $needApproveReqs = $this->getPurchaseRequestRepository()->getRequestsCounter('need-approve', $this->getUser());
        $productionLeaderReqs = $this->getPurchaseRequestRepository()->getRequestsCounter('production', $this->getUser());

        $type = $request->get('type');
        $filters = $request->get('filters', []);
        $orderBy = $request->get('orderBy');
        $order = $request->get('order');

        if (!((($type == 'all-requests' || $type == 'need-manager') && $this->getUser()->isPurchasingLeader()) ||
            ($type == 'need-approve' && ($this->getUser()->isPurchaseRequestApprovingLeader() || $needApproveReqs)) ||
            ($type == 'manager-assigned' && ($this->getUser()->isPurchasingManager() || $this->getUser()->isPurchasingLeader())) ||
            ($type == 'needs-payment' && ($this->getUser()->isFinancialManager() || $this->getUser()->isFinancialLeader())) ||
            ($type == 'production' && ($this->getUser()->isProductionLeader() || $productionLeaderReqs)) ||
            ($type == 'production_tools_list' && ($this->getUser()->isProductionLeader() || $this->getUser()->canViewToolWorkLog())) ||
            $type == 'my-requests' || $type == 'favorite')) {
            return $this->redirectToRoute('homepage');
        }

        $currentUser = $this->getUser();
        $currentPage = $request->get('page', 1);

        if ($type == 'favorite') {
            $purchaseRequests = $this->getPurchaseRequestFavoriteRepository()->getPurchaseRequestFavorite(
                $currentUser,
                $order,
                $orderBy,
                $filters,
                $currentPage,
                self::PER_PAGE);
        } else {
            $purchaseRequests = $this->getPurchaseRequestRepository()->getPurchaseRequests(
                $type,
                $currentUser,
                $filters,
                $orderBy,
                $order,
                $currentPage,
                self::PER_PAGE
            );
        }
        $purchasingTeam = $this->getTeamRepository()->findOneBy(['purchasesTeam' => true]);

        $maxRows = $purchaseRequests->count();
        $maxPages = ceil($maxRows / self::PER_PAGE);

        return $this->render('purchase/purchases_list.html.twig', [
            'purchaseRequests' => $purchaseRequests,
            'purchasingTeam' => $purchasingTeam,
            'projects' => $this->getProjectRepository()->getProjectsWithPurchases(),
            'states' => PurchaseConstants::getStatesList(),
            'priorities' => PurchaseConstants::getPrioritiesList(),
            'paymentStates' => PurchaseConstants::getPaymentStatesList(),
            'deliveryStates' => PurchaseConstants::getDeliveryStatesList(),
            'types' => PurchaseConstants::getTypesChoices(),
            'suppliers' => $this->getUser()->canViewFinancialInfo() ? $this->getSupplierRepository()->findAll() : [],
            'paymentList' => PurchaseRequest::getPaymentList(),
            'expensesList' => PurchaseRequest::getExpensesList(),
            'filters' => $filters,
            'currentPage' => $currentPage,
            'maxPages' => $maxPages,
            'maxRows' => $maxRows,
            'perPage' => self::PER_PAGE,
            'orderBy' => $orderBy,
            'order' => $order
        ]);
    }

    /**
     * Add request form.
     *
     * @Route("/project/{id}/purchases/add", name="request_add")
     */
    public function addAction(Request $request)
    {
        $projectId = $request->get('id');
        $requestDetails = $request->get('request');
        $movementDetails = $request->get('movement');
        $productionType = $request->get('productionType');
        $warehouses = $request->get('warehouse');
        $warehouse = $this->getWarehouseRepository()->find($warehouses);
        /** @var Project $project */
        $project = $this->getProjectRepository()->find($projectId);

        if (!$project->checkGrants($this->getUser())) {
            return $this->redirectToRoute('homepage');
        }

        $purchaseRequest = new PurchaseRequest();

        if (!empty($requestDetails)) {
            $this->buildPurchaseRequest($purchaseRequest, $project, $requestDetails, $movementDetails, $productionType, $warehouse);

            $team = $this->getUser()->getTeam();
            /** @var Team $team */
            if (!$team) {
                $team = $this->getTeamRepository()->findOneBy(['code' => 'АТ']);
            }

            $em = $this->getEm();
            $team->setLastPurchaseId($team->getLastPurchaseId() + 1);
            $em->persist($team);
            $em->flush();
        }

        return $this->redirectToRoute('request_details', [
            'id' => $project->getId(),
            'requestId' => $purchaseRequest->getId(),
        ]);
    }

    /**
     * Edit request form.
     *
     * @Route("/project/{id}/purchases/{requestId}/edit", name="request_edit")
     */
    public function editAction(Request $request)
    {
        $requestId = $request->get('requestId');
        $requestDetails = $request->get('request');
        $productionType = $request->get('productionType');

        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->getPurchaseRequestRepository()->find($requestId);
        /** @var Ware $ware */
        $ware = $this->getWareRepository()->find($requestDetails['ware']);
        if (!empty($purchaseRequest) && $purchaseRequest->canEditItems($this->getUser())) {


            $purchaseRequest
                ->setType($requestDetails['type'])
                ->setDescription(isset($requestDetails['description']) ? $requestDetails['description'] : '')
                ->setWare($ware)
            ;

            if (!empty($requestDetails['supplies-category'])) {
                $suppliesCategory = $this->getSuppliesCategoryRepository()->find($requestDetails['supplies-category']);
                $purchaseRequest->setSuppliesCategory($suppliesCategory);
            }

            $movementDetails = $request->get('movement');
            if (!empty($movementDetails)) {
                $requestMovement = $purchaseRequest->getMovements()->first() ?: new RequestMovement();
                $this->buildRequestMovement($purchaseRequest, $requestMovement, $movementDetails);
            }

            if (!empty($productionType)) {
                $purchaseRequest->setTypeOfProduction($productionType);
            }

            $this->getEm()->persist($purchaseRequest);
            $this->getEm()->flush();
        }

        return $this->redirectToRoute('request_details', [
            'id' => $purchaseRequest->getProject()->getId(),
            'requestId' => $purchaseRequest->getId(),
        ]);
    }

    /**
     * Make purchase request favorite.
     *
     * @Route("/project/{id}/purchases/{requestId}/make-favorite", name="purchase_request_make_favorite")
     */
    public function makePurchaseRequestFavorite(Request $request)
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->getPurchaseRequestRepository()->find($request->get('requestId'));

        /** @var PurchaseRequestFavorite $favorite*/
        $favorite = new PurchaseRequestFavorite();

        try {
            if (!empty($purchaseRequest)) {
                $favorite
                    ->setUser($this->getUser())
                    ->setPurchaseRequest($purchaseRequest)
                ;

                $em = $this->getEm();
                $em->persist($favorite);
                $em->flush();
            }
        } catch (\Exception $exception) {
            $favorite->add('danger', $exception->getMessage());
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Remave purchase request favorite.
     *
     * @Route("/project/{id}/purchases/{requestId}/remove-favorite/{favoriteId}", name="purchase_request_remove_favorite")
     */
    public function removePurchaseRequestFavorite(Request $request)
    {
        /** @var PurchaseRequestFavorite $favorite*/
        $favorite = $this->getPurchaseRequestFavoriteRepository()->find($request->get('favoriteId'));

        try {
            if (!empty($favorite)) {
                $em = $this->getEm();
                $em->remove($favorite);
                $em->flush();
            }
        } catch (\Exception $exception) {
            $favorite->add('danger', $exception->getMessage());
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Change owner form.
     *
     * @Route("/project/{id}/purchases/{requestId}/changeOwner", name="change_request_owner")
     */
    public function changeOwnerAction(Request $request)
    {
        $requestId = $request->get('requestId');
        $ownerId = $request->get('owner');
        $owner = $this->getUserRepository()->find($ownerId);
        $team = $owner->getTeam();

        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->getPurchaseRequestRepository()->find($requestId);

        if (!empty($ownerId) && ($purchaseRequest->canChangeRequestOwner($this->getUser()))) {
            $purchaseRequest->setOwner($owner);
        }

        $this->getEm()->persist($purchaseRequest);
        $this->getEm()->flush();

        return $this->redirectToRoute('request_details', [
            'id' => $purchaseRequest->getProject()->getId(),
            'requestId' => $purchaseRequest->getId(),
        ]);
    }

    /**
     * Change request priority
     *
     * @Route("/project/{id}/purchases/{requestId}/set-priority/{priority}", name="request_set_priority")
     */
    public function setPriorityAction(Request $request)
    {
        $requestId = $request->get('requestId');
        $priority = $request->get('priority');

        if ($this->getUser()->hasFullRequestPrivileges() &&
            array_key_exists($priority, PurchaseConstants::getPrioritiesList())) {
            /** @var PurchaseRequest $purchaseRequest */
            $purchaseRequest = $this->getPurchaseRequestRepository()->find($requestId);
            $purchaseRequest->setPriority($priority);

            $this->getEm()->persist($purchaseRequest);

            $uof = $this->getEm()->getUnitOfWork();
            $uof->computeChangeSets();

            $purchaseRequestChanges = $this->logChanges($purchaseRequest, $uof->getEntityChangeSet($purchaseRequest));
            $translator = $this->get('translator');

            if (!empty($purchaseRequestChanges)) {
                    $this->sendEmail(
                        '{' . $translator->trans(ucfirst($purchaseRequest->getType())) . '} ' . $purchaseRequest->getCode(),
                        $purchaseRequest->getRequestRecipients($this->getUser()),
                        $this->renderView(
                            'emails/purchase/updated.html.twig', [
                            'purchaseRequest' => $purchaseRequest,
                            'purchaseRequestChanges' => $purchaseRequestChanges,
                            'state' => ''
                        ])
                    );
            }

            $this->getEm()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/project/{id}/purchases/{requestId}/details", name="request_details")
     */
    public function detailsAction(Request $request)
    {
        $projectId = $request->get('id');
        $requestId = $request->get('requestId');

        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->getPurchaseRequestRepository()->find($requestId);
        $team = $this->getUser()->getTeam();
        $user = $this->getUser();
        $owner = $purchaseRequest->getOwner();
        $project = $this->getProjectRepository()->find($projectId);
        $purchasingTeam = $this->getTeamRepository()->findOneBy(['purchasesTeam' => true]);
        $financialTeam = $this->getTeamRepository()->findOneBy(['financialTeam' => true]);
        $registryList = $this->getInvoiceRegistryRepository()->getActualRegistry();

        $categories = $this->getSuppliesCategoryRepository()->getCategoriesGroupedByParent();
        $units = $this->getUnitRepository()->findAll();
        $wares = $project->getWares();

        $teamMembers = [];
        if ($user->canChangeRequestOwner()) {
            $teamMembers = $this->getUserRepository()->getUsersGroupedByTeams();
        } elseif ($owner->getTeam()->isTeamLeader($user)) {
            $teamMembers[$owner->getTeam()->getTitle()] = $owner->getTeam()->getTeamMembers();
        }

        $approvingLeaders = [];
        if ($purchaseRequest->canRequestApprove($user) ||
            $purchaseRequest->canLeaderApprove($user)) {
            $approvingLeaders = $this->getUserRepository()->getPurchaseRequestApprovingLeaders();
        }

        $productionSpecialists = [];
        if ($purchaseRequest->canProductionLeaderApprove($user)) {
            /** @var Team $productionTeam */
            $productionTeam = $this->getTeamRepository()->findOneBy(['productionTeam' => true]);
            $productionLeader = $productionTeam->getLeader();

            $productionSpecialists = $this->getUserRepository()->getPurchaseProductionSpecialists($productionLeader);
        }

        $fileParams = [
            'purchaseRequest' => $requestId,
            'type' => RequestFile::FILE_TYPE_DEFAULT,
            'deleted' => 0
        ];
        $requestFiles = $this->getRequestFileRepository()->findBy($fileParams);

        if ($this->getUser()->canViewFinancialInfo()) {
            $fileParams['type'] = RequestFile::FILE_TYPE_FINANCIAL;
            $financialFiles = $this->getRequestFileRepository()->findBy($fileParams);
        } else {
            $financialFiles = [];
        }

        $requestChanges = $this->getPurchaseRequestDiffRepository()->getPurchaseRequestChanges($purchaseRequest);
        $comments = $this->getPurchaseRequestCommentRepository()->findBy(['purchaseRequest' => $requestId]);
        $suppliesCategoriesGrouped = $this->getSuppliesCategoryRepository()->getCategoriesGroupedByParent();
        $warehouses = $this->getWarehouseRepository()->findAll();
        $suppliers = $this->getSupplierRepository()->findAll();
        $countries = $this->getCountryRepository()->findAll();
        $deliveries = $this->getPurchaseRequestDeliveryRepository()->findBy([
            'purchaseRequest' => $purchaseRequest->getId()
        ]);

        $recommendedSuppliers = $this->getSupplierRepository()->findSuppliersByCategory(
            $purchaseRequest->getSuppliesCategory()
        );

        return $this->render('purchase/details.html.twig', [
            'purchaseRequest' => $purchaseRequest,
            'team' => $team,
            'project' => $project,
            'purchasingTeam' => $purchasingTeam,
            'suppliesCategoriesGrouped' => $suppliesCategoriesGrouped,
            'financialTeam' => $financialTeam,
            'purchaseRequestChanges' => $requestChanges,
            'requestFiles' => $requestFiles,
            'financialFiles' => $financialFiles,
            'comments' => $comments,
            'categories' => $categories,
            'units' => $units,
            'approvingLeaders' => $approvingLeaders,
            'productionSpecialists' => $productionSpecialists,
            'wares' => $wares,
            'warehouses' => $warehouses,
            'recommendedSuppliers' => $recommendedSuppliers,
            'teamMembers' => $teamMembers,
            'owner' => $owner,
            'suppliers' => $suppliers,
            'countries' => $countries,
            'deliveries' => $deliveries,
            'registryList' => $registryList
        ]);
    }

    /**
     * Request subscribe
     *
     * @Route("/project/{id}/purchases/{requestId}/subscribe", name="request_subscribe")
     */
    public function subscribeRequestAction(Request $request)
    {
        $requestId = $request->get('requestId');
        $req = $this->getPurchaseRequestRepository()->find($requestId);

        $req->addSubscriber($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($req);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Request unsubscribe
     *
     * @Route("/project/{id}/purchases/{requestId}/unsubscribe", name="request_unsubscribe")
     */
    public function unsubscribeRequestAction(Request $request)
    {
        $requestId = $request->get('requestId');
        $req = $this->getPurchaseRequestRepository()->find($requestId);

        if ($req->getOwner()->getId() != $this->getUser()->getId()) {
            $req->removeSubscriber($this->getUser());
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($req);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param PurchaseRequest $purchaseRequest
     * @param Project $project
     * @param $purchaseRequestDetails
     * @param $movementDetails
     * @param $productionType
     * @param $warehouse
     * @return ProjectTask
     */
    protected function buildPurchaseRequest(
        PurchaseRequest $purchaseRequest,
        Project $project,
        $purchaseRequestDetails,
        $movementDetails,
        $productionType,
        $warehouse
    ) {
        /** @var Ware $ware */
        $ware = $this->getWareRepository()->find($purchaseRequestDetails['ware']);
        $em = $this->getEm();
        $purchaseRequest
            ->setProject($project)
            ->setCode($purchaseRequestDetails['code'])
            ->setDescription(isset($purchaseRequestDetails['description']) ? $purchaseRequestDetails['description'] : '')
            ->setType($purchaseRequestDetails['type'])
            ->setCreatedAt(new \DateTime())
            ->setWare($ware)
        ;

        if (!empty($purchaseRequestDetails['supplies-category'])) {
            $suppliesCategory = $this->getSuppliesCategoryRepository()->find($purchaseRequestDetails['supplies-category']);
            $purchaseRequest->setSuppliesCategory($suppliesCategory);
        }

        if ($productionType) {
            $purchaseRequest->setTypeOfProduction($productionType);
        }

        if ($warehouse) {
            $purchaseRequest->setWarehouse($warehouse);
        }

        if (!$purchaseRequest->getPriority()) {
            $priority = $project->getPriority() ?
                PurchaseConstants::getProjectPrioritiesMapping()[$project->getPriority()] :
                1
            ;

            $purchaseRequest
                ->setPriority($priority);
        }

        if (!$purchaseRequest->getOwner()) {
            $purchaseRequest->setOwner($this->getUser());
            $purchaseRequest->addSubscriber($project->getLeader());
        }

        if (!empty($movementDetails)) {
            $requestMovement = new RequestMovement();
            $this->buildRequestMovement($purchaseRequest, $requestMovement, $movementDetails);
        }

        $em->persist($purchaseRequest);
        $em->flush();

        return $purchaseRequest;
    }

    /**
     * @param PurchaseRequest $purchaseRequest
     * @param RequestMovement $requestMovement
     * @param $requestMovementDetails
     *
     * @return RequestMovement
     */
    protected function buildRequestMovement(PurchaseRequest $purchaseRequest, RequestMovement $requestMovement, $requestMovementDetails)
    {
        $em = $this->getEm();
        $requestMovement
            ->setDescription($requestMovementDetails['details'])
            ->setDestination($requestMovementDetails['destination'])
            ->setSource($requestMovementDetails['source'])
            ->setStartDate(new \DateTime($requestMovementDetails['startDate']))
            ->setEndDate(new \DateTime($requestMovementDetails['endDate']))
            ->setRequest($purchaseRequest)
            ->setSendResponsible($requestMovementDetails['sendResponsible'])
            ->setReceiveResponsible($requestMovementDetails['receiveResponsible'])
            ->setNeedPrr(!empty($requestMovementDetails['needPrr']) ? true : false)
            ->setNeedCargoDescription(!empty($requestMovementDetails['needCargoDescription']) ? true : false)
            ->setNeedCargoInsurance(!empty($requestMovementDetails['needCargoInsurance']) ? true : false)
            ->setNeedAdditionalCargo(!empty($requestMovementDetails['needAdditionalCargo']) ? true : false)
        ;

        $em->persist($requestMovement);

        return $requestMovement;
    }

    /**
     * @Route("/project/{id}/purchases/{requestId}/{state}", name="project_purchase_change_state")
     */
    public function changeStateAction(Request $request)
    {
        $translator = $this->get('translator');
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $requestId = $request->get('requestId');
        $state = $request->get('state');
        $purchaseRequestData = $request->get('request');

        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->getPurchaseRequestRepository()->find($requestId);

        if (empty($purchaseRequest->getTimings())) {
            $purchaseRequest->setTimings(new RequestTimings());
        }

        // Hack for multi-purchasing-teams
        if ($state == 'need-purchasing-manager' && $purchaseRequest->getProject()->getPurchasingManager()) {
            $state = 'assign-manager';
            $purchaseRequestData['manager'] = $purchaseRequest->getProject()->getPurchasingManager()->getId();

            $purchaseRequest->setLeaderApproved(true);
        }

        if ($state == 'need-approve') {
            $leader = $this->getUserRepository()->find($purchaseRequestData['leader']);
            $purchaseRequest->setLeader($leader);
            $purchaseRequest->getTimings()->setMovedToLeaderApproveAt(new \DateTime());

            $newStatus = PurchaseConstants::STATUS_NEEDS_LEADER_APPROVAL;

            $purchaseRequest->setRelevanceDate(new \DateTime());
        } elseif ($state == 'need-project-leader-approve') {
            $purchaseRequest->setLeaderApproved(true);
            $purchaseRequest->setProjectLeader($purchaseRequest->getProject()->getLeader());

            $newStatus = PurchaseConstants::STATUS_NEEDS_PROJECT_LEADER_APPROVE;
        } elseif ($state == 'need-production-leader-approve') {
            if ($purchaseRequest->getStatus() != PurchaseConstants::STATUS_NEEDS_PRODUCTION_LEADER_APPROVAL) {
                $purchaseRequest->setProjectLeaderApproved(true);
                /** @var Team $productionTeam */
                $productionTeam = $this->getTeamRepository()->findOneBy(['productionTeam' => true]);

                $purchaseRequest->setProductionLeader($productionTeam->getLeader());

                $purchaseRequest
                    ->setInvoicePayment($purchaseRequestData['invoicePayment'])
                    ->setAcceptanceType($purchaseRequestData['acceptanceType'])
                    ->setExpensesType($purchaseRequestData['expensesType'])
                    ->setNumberOfProducts($purchaseRequestData['numberOfProducts'])
                ;
            } else {
                $productionSpecialist = $this->getUserRepository()->find($purchaseRequestData['productionSpecialist']);
                $purchaseRequest->setProductionLeader($productionSpecialist);
            }

            $newStatus = PurchaseConstants::STATUS_NEEDS_PRODUCTION_LEADER_APPROVAL;
        } elseif ($state == 'need-purchasing-manager') {
            $purchaseRequest->setProjectLeaderApproved(true);

            if ($purchaseRequest->getStatus() != PurchaseConstants::STATUS_NEEDS_PRODUCTION_LEADER_APPROVAL) {
                $purchaseRequest
                    ->setInvoicePayment($purchaseRequestData['invoicePayment'])
                    ->setAcceptanceType($purchaseRequestData['acceptanceType'])
                    ->setExpensesType($purchaseRequestData['expensesType'])
                    ->setNumberOfProducts($purchaseRequestData['numberOfProducts'])
                ;
            }

            $newStatus = PurchaseConstants::STATUS_NEEDS_PURCHASING_MANAGER;

            if ($purchaseRequest->getType() == PurchaseConstants::TYPE_PRODUCTION) {
                $purchaseRequest->setProductionLeaderApproved(true);
                $inProduction = false;
                $needsPurchasing = false;
                foreach ($purchaseRequest->getItems() as $item) {
                    if ($item->getProductionStatus() == RequestItem::PRODUCTION_STATUS_IN_PRODUCTION) {
                        $inProduction = true;
                    } else {
                        $item->setProductionStatus(null);
                        $needsPurchasing = true;
                    }
                }

                if ($inProduction) {
                    $purchaseRequest->setProductionStatus(PurchaseConstants::PRODUCTION_STATUS_IN_PRODUCTION);
                }

            } else {
                $purchaseRequest->setLeaderApproved(true);
                $needsPurchasing = true;
            }
            if ($needsPurchasing) {
                /** @var Team $purchasingTeam */
                $purchasingTeam = $this->getTeamRepository()->findOneBy(['purchasesTeam' => true]);
                $purchaseRequest->setPurchasingLeader($purchasingTeam->getLeader());

                if ($purchasingTeam->getTelegramChatId()) {
                    $this->sendTelegramNotification(
                        $purchasingTeam->getTelegramChatId(),
                        $this->renderView('telegram/request/needs_manager.html.twig', ['request' => $purchaseRequest])
                    );
                }
            } else {
                $newStatus = PurchaseConstants::STATUS_MANAGER_FINISHED_WORK;
            }
        } elseif ($state == 'assign-manager') {
            $manager = $this->getUserRepository()->find($purchaseRequestData['manager']);

            $purchaseRequest->setProjectLeaderApproved(true);
            $purchaseRequest->setProductionLeaderApproved(true);
            $purchaseRequest->setPurchasingLeaderApproved(true);
            $purchaseRequest->setPurchasingManager($manager);

            if ($purchaseRequest->getStatus() != PurchaseConstants::STATUS_NEEDS_PRODUCTION_LEADER_APPROVAL &&
                $purchaseRequest->getStatus() != PurchaseConstants::STATUS_NEEDS_PURCHASING_MANAGER &&
                $purchaseRequest->getStatus() != PurchaseConstants::STATUS_MANAGER_ASSIGNED) {
                $purchaseRequest
                    ->setInvoicePayment($purchaseRequestData['invoicePayment'])
                    ->setAcceptanceType($purchaseRequestData['acceptanceType'])
                    ->setExpensesType($purchaseRequestData['expensesType'])
                    ->setNumberOfProducts($purchaseRequestData['numberOfProducts'])
                ;
            }

            $newStatus = PurchaseConstants::STATUS_MANAGER_ASSIGNED;

        } elseif ($state == 'preliminary-estimate') {
            $newStatus = PurchaseConstants::STATUS_ON_PRELIMINARY_ESTIMATE;
        } elseif ($state == 'need-preliminary-estimate-approve') {
            $newStatus = PurchaseConstants::STATUS_NEEDS_PRELIMINARY_ESTIMATE_APPROVE;
        } elseif ($state == 'start-work') {
            $newStatus = PurchaseConstants::STATUS_MANAGER_STARTED_WORK;

            $purchaseRequest->getTimings()->setManagerStartedWorkAt(new \DateTime());
        } elseif ($state == 'need-fixing') {

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
            $newStatus = PurchaseConstants::STATUS_NEEDS_FIXING;

            $purchaseRequest->getTimings()->resetTimings();
        } elseif ($state == 'fix-prices') {
            $purchaseRequest->setFinancialLeader(null);
            $purchaseRequest->setDeliveryStatus(null);
            $purchaseRequest->setPaymentStatus(null);

            $newStatus = PurchaseConstants::STATUS_MANAGER_STARTED_WORK;
        } elseif ($state == 'fixing-prices-items') {
            $newStatus = PurchaseConstants::STATUS_ON_PRELIMINARY_ESTIMATE;
        } elseif ($state == 'reject' && $purchaseRequest->canReject($this->getUser())) {
            $newStatus = PurchaseConstants::STATUS_REJECTED;

        } elseif ($state == 'finish-work' && $purchaseRequest->canManagerFinishWork($this->getUser())) {
            $requestService = $this->get('service.request');
            $validateItems = $requestService->getValidatePurchaseRequestItems($purchaseRequest);
            if (empty($validateItems)) {
                $newStatus = PurchaseConstants::STATUS_MANAGER_FINISHED_WORK;
                /** @var Team $financialTeam */
                $financialTeam = $this->getTeamRepository()->findOneBy(['financialTeam' => true]);
                $purchaseRequest->setFinancialLeader($financialTeam->getLeader());
                $purchaseRequest->setPaymentStatus(PurchaseConstants::PAYMENT_STATUS_NEEDS_PAYMENT);
                $purchaseRequest->setDeliveryStatus(PurchaseConstants::DELIVERY_STATUS_AWAITING_DELIVERY);
                $purchaseRequest->getTimings()->setManagerFinishedWorkAt(new \DateTime());
            } else {
                foreach ($validateItems as $validateItem) {
                    if ($validateItem['text']) {
                        $flashbag->add('danger', sprintf($translator->trans('In item %s you must fill in "%s"'), $validateItem['num'], $validateItem['text']));
                    }
                }
            }
        } elseif ($state == 'in-delivery' && $purchaseRequest->canStartDelivery($this->getUser())) {
            $purchaseRequest->setDeliveryStatus(PurchaseConstants::DELIVERY_STATUS_IN_DELIVERY);
        } elseif ($state == 'delivered' && $purchaseRequest->canFinishDelivery($this->getUser())) {
            $purchaseRequest->setDeliveryStatus(PurchaseConstants::DELIVERY_STATUS_DELIVERED);

            $items= $this->getRequestItemRepository()->findBy(['purchaseRequest' => $requestId]);

            foreach ($items as $item) {
                $item->setStockStatus(RequestItem::STOCK_STATUS_ON_STOCK);
                $item->setOnStockAt(new \DateTime());
                $itemTitle = $item->getTitle() . ' (' . $item->getSku() .
                    ') - ' . $item->getQuantity() . $item->getUnit();
                $this->logChanges($item->getPurchaseRequest(), ['item' => [$itemTitle, 'On stock']]);
            }
            $this->getEm()->flush();

            $this->sendEmail(
                '{' . $translator->trans(ucfirst($purchaseRequest->getType())) . '} ' . $purchaseRequest->getCode(),
                $purchaseRequest->getRequestRecipients($this->getUser()),
                $this->renderView(
                    'emails/purchase/delivered.html.twig', [
                    'purchaseRequest' => $purchaseRequest,
                    'state' => $state
                ])
            );

            $purchaseRequest->getTimings()->setManagerMarkedAsDeliveredAt(new \DateTime());

            if ($purchaseRequest->getPaymentStatus() == PurchaseConstants::PAYMENT_STATUS_PAID) {
                $purchaseRequest->setStatus(PurchaseConstants::STATUS_DONE);
                $purchaseRequest->getTimings()->setRequestMarkedAsDoneAt(new \DateTime());
            }

        } elseif ($state == 'move-to-payment' && $purchaseRequest->canMoveToPayment($this->getUser())) {
            $financialManager = $this->getUserRepository()->find($purchaseRequestData['financialManager']);
            $purchaseRequest->setPaymentStatus(PurchaseConstants::PAYMENT_STATUS_PAYMENT_PROCESSING);
            $purchaseRequest->setFinancialLeaderApproved(true);
            $purchaseRequest->setFinancialManager($financialManager);
            $purchaseRequest->getTimings()->setFinancialLeaderApprovedAt(new \DateTime());

        } elseif ($state == 'paid' && $purchaseRequest->canMarkAsPaid($this->getUser())) {
            $purchaseRequest->setPaymentStatus(PurchaseConstants::PAYMENT_STATUS_PAID);
            $purchaseRequest->setPaymentDate(new \DateTime());
            $purchaseRequest->getTimings()->setFinancialManagerMarkedAsPaidAt(new \DateTime());

            if ($purchaseRequest->getDeliveryStatus() == PurchaseConstants::DELIVERY_STATUS_DELIVERED) {
                $purchaseRequest->setStatus(PurchaseConstants::STATUS_DONE);

                $purchaseRequest->getTimings()->setRequestMarkedAsDoneAt(new \DateTime());
            }

        } elseif ($state == 'produced' && $purchaseRequest->canProductionLeaderMarkAsProduced($this->getUser())) {
            if ($purchaseRequest->canTransition($purchaseRequest->getStatus(), PurchaseConstants::STATUS_DONE) &&
                !$purchaseRequest->getPurchasingManager()
            ) {

                $purchaseRequest->setStatus(PurchaseConstants::STATUS_DONE);
                $purchaseRequest->getTimings()->setRequestMarkedAsDoneAt(new \DateTime());
            }

            $purchaseRequest->setProductionStatus(PurchaseConstants::PRODUCTION_STATUS_PRODUCED);
        }

        if (!empty($newStatus) && (
            $purchaseRequest->canTransition($purchaseRequest->getStatus(), $newStatus) ||
            $this->getUser()->hasFullRequestPrivileges()
        )) {
            $purchaseRequest->setStatus($newStatus);
        }

        $this->getEm()->persist($purchaseRequest);

        if ($purchaseRequest->getId()) {
            $uof = $this->getEm()->getUnitOfWork();
            $uof->computeChangeSets();

            $purchaseRequestChanges = $this->logChanges($purchaseRequest, $uof->getEntityChangeSet($purchaseRequest));
        }

        if (!empty($purchaseRequestData['comment']['text'])) {
            $this->addComment($purchaseRequest, $purchaseRequestData['comment']);
        }

        $translator = $this->get('translator');

        if (!empty($purchaseRequestChanges)) {
            $this->sendEmail(
                '{' . $translator->trans(ucfirst($purchaseRequest->getType())) . '} ' . $purchaseRequest->getCode(),
                $purchaseRequest->getRequestRecipients($this->getUser()),
                $this->renderView(
                    'emails/purchase/updated.html.twig', [
                    'purchaseRequest' => $purchaseRequest,
                    'purchaseRequestChanges' => $purchaseRequestChanges,
                    'state' => $state
                ])
            );
        }

        $this->getEm()->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Upload file api.
     *
     * @Route("/purchases/{purchaseId}/upload", name="purchase_request_upload_file")
     */
    public function uploadFileAction(Request $request)
    {
        $purchaseId = $request->get('purchaseId');
        $payment = $request->get('payment');
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->getPurchaseRequestRepository()->find($purchaseId);
        if (!empty($payment)) {
            $invoice = $this->getInvoiceRepository()->find($payment['invoice']);
        }

        $purchaseFiles = $request->files->get('files');
        $fileType = $request->get('type', 'default');

        foreach ($purchaseFiles as $purchaseFile) {
            try {
                $this->validateFile($purchaseFile);
                if ($purchaseFile instanceof UploadedFile) {
                    $file = new RequestFile();
                    $format = !(empty($purchaseFile->guessExtension()))
                        ? $purchaseFile->guessExtension()
                        : $purchaseFile->getClientOriginalExtension();

                    $file
                        ->setFileName($purchaseFile->getClientOriginalName())
                        ->setFormat($format)
                        ->setOwner($this->getUser())
                        ->setFileSize($purchaseFile->getSize())
                        ->setPurchaseRequest($purchaseRequest)
                        ->setUploadedAt(new \DateTime())
                        ->setType($fileType);

                    $this->moveFile($purchaseFile, $file, $purchaseRequest->getProject()->getId(), $purchaseId);

                    $em = $this->getEm();

                    if (!empty($invoice)) {
                        $invoice->setInvoiceFile($file);
                        $invoice->setStatus(Invoice::STATUS_READY_TO_PAY);
                        $em->persist($file);
                    }

                    $em->persist($file);
                    $em->flush();
                }
            } catch (\Exception $exception) {
                $flashbag->add('danger', $exception->getMessage());
            }
        }
        return $this->redirect($request->headers->get('referer') . '#attachmentstab');
    }

    /**
     * Delete file action.
     *
     * @Route("/purchases/{purchaseId}/file/{fileId}/delete", name="request_delete_file")
     */
    public function deleteFileAction(Request $request)
    {
        $fileId = $request->get('fileId');

        $file = $this->getRequestFileRepository()->find($fileId);

        /** @var RequestFile $file */
        if ($file->canDeleteFile($this->getUser())) {
            $file->setDeleted(true);

            $this->getEm()->persist($file);
            $this->getEm()->flush();
        }

        return $this->redirect($request->headers->get('referer') . '#attachmentstab');
    }

    /**
     * Download file url.
     *
     * @Route("/purchases/{fileId}/download/{preview}", name="purchase_request_download_file", defaults={"preview": 0})
     */
    public function downloadFileAction(Request $request)
    {
        $fileId = $request->get('fileId');
        $preview = $request->get('preview');

        /** @var RequestFile $purchaseRequestFile */
        $purchaseRequestFile = $this->getRequestFileRepository()->find($fileId);

        if (!$purchaseRequestFile->canViewFile($this->getUser())) {
            return $this->redirect($request->headers->get('referer'));
        }
        $fileName = $preview ? $purchaseRequestFile->getStoredPreviewFileName() : $purchaseRequestFile->getStoredFileName();

        $headers = [
            'Content-Type' => 'application/' . $purchaseRequestFile->getFormat(),
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ];

        $purchaseDir = $this->getParameter('purchase_files_root_dir') . '/' .
            $purchaseRequestFile->getPurchaseRequest()->getProject()->getId() . '/' .
            $purchaseRequestFile->getPurchaseRequest()->getId() . '/'
        ;

        $purchaseDir .= $purchaseRequestFile->getStoredFileDir() ? $purchaseRequestFile->getStoredFileDir() . '/' : '';

        return new Response(file_get_contents($purchaseDir . $fileName), 200, $headers);
    }

    /**
     * Add comment to Project task.
     *
     * @Route("/purchase/{id}/comment", name="purchase_request_add_comment")
     */
    public function commentAction(Request $request)
    {
        $commentText = $request->get('comment');
        $purchaseId = $request->get('id');

        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->getPurchaseRequestRepository()->find($purchaseId);

        $comment = $this->addComment($purchaseRequest, $commentText);
        $em = $this->getEm();
        $em->flush();

        $translator = $this->get('translator');

        $this->sendEmail(
            '{' . $translator->trans(ucfirst($purchaseRequest->getType())) . '} ' . $purchaseRequest->getCode(),
            $purchaseRequest->getRequestRecipients($this->getUser()),
            $this->renderView(
                'emails/purchase/comment.html.twig', [
                'purchaseRequest' => $purchaseRequest,
                'comment' => $comment
            ])
        );

        return $this->redirect($request->headers->get('referer') . '#attachmentstab');
    }

    /**
     * Generate request state report.
     *
     * @Route("/request/report/states", name="request_report_states")
     */
    public function reportStatesAction(Request $request)
    {
        $purchasingTeam = $this->getTeamRepository()->findOneBy(['purchasesTeam' => true]);

        $purchasingReport = $this->getPurchaseRequestRepository()->getPurchasingStatesReport();
        $ongoingReport = $this->getPurchaseRequestRepository()->getOngoingStatesReport();
        $financialReport = $this->getPurchaseRequestRepository()->getFinancialStatesReport();
        $productionReport = $this->getPurchaseRequestRepository()->getProductionStatesReport();

        if ($purchasingTeam->getTelegramChatId()) {
            $this->sendTelegramNotification(
                $purchasingTeam->getTelegramChatId(),
                $this->renderView('telegram/request/report/states.html.twig', [
                    'purchasingReport' => $purchasingReport,
                    'ongoingReport' => $ongoingReport,
                    'financialReport' => $financialReport,
                    'productionReport' => $productionReport
                ])
            );
        }

        return $this->render('telegram/request/report/states.html.twig', [
            'purchasingReport' => $purchasingReport,
            'ongoingReport' => $ongoingReport,
            'financialReport' => $financialReport,
            'productionReport' => $productionReport
        ]);
    }

    /**
     * Generate request state report.
     *
     * @Route("/request/report/changes", name="request_report_changes")
     */
    public function reportChangesAction(Request $request)
    {
        $purchasingTeam = $this->getTeamRepository()->findOneBy(['purchasesTeam' => true]);

        $purchasingReport = $this->getPurchaseRequestDiffRepository()->getRequestStatesChangesReport();

//        $ongoingReport = $this->getPurchaseRequestRepository()->getOngoingStatesReport();
        $financialReport = $this->getPurchaseRequestDiffRepository()->getRequestFinancStatesChangesReport();
//        $productionReport = $this->getPurchaseRequestRepository()->getProductionStatesReport();

        if ($purchasingTeam->getTelegramChatId()) {
            $this->sendTelegramNotification(
                $purchasingTeam->getTelegramChatId(),
                $this->renderView('telegram/request/report/states_changes.html.twig', [
                    'purchasingReport' => $purchasingReport,
//                    'ongoingReport' => $ongoingReport,
                    'financialReport' => $financialReport,
//                    'productionReport' => $productionReport
                ])
            );
        }

        return $this->render('telegram/request/report/states_changes.html.twig', [
            'purchasingReport' => $purchasingReport,
            'financialReport' => $financialReport,
        ]);
    }

    /**
     * Generate request state report.
     *
     * @Route("/request/report/money", name="request_report_money")
     */
    public function reportMoneyAction(Request $request)
    {
        $moneyReport = $this->getRequestItemRepository()->getRequestMoneyReport();

        $this->sendTelegramNotification(
            '3932700',
            $this->renderView('telegram/request/report/money.html.twig', [
                'moneyReport' => $moneyReport
            ])
        );

        return $this->redirectToRoute('requests_list', ['type' => 'my-requests']);
    }

    /**
     * @Route("/purchases/{requestId}/send-to-supply-area", name="purchase_request_send_to_supply_area")
     */
    public function sendToSupplyAreaAction(Request $request)
    {
        $requestId = $request->get('requestId');

        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->getPurchaseRequestRepository()->find($requestId);

        /** @var User $user */
        $user = $this->getUser();
        if (!$user->canSendPurchaseToSupplyArea()) {
            return $this->redirect($request->headers->get('referer'));
        }

        $client = new \GuzzleHttp\Client(
            [
                'base_uri' => $this->getParameter('supply_area_api_url'),
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ]
        );

        $url = '/api/purchases/add';
        $query = [
            'olympRequestId' => $purchaseRequest->getId(),
            'code' => $purchaseRequest->getCode(),
            'type' => $purchaseRequest->getType(),
            'items' => []
        ];

        $recommendedSuppliers = $this->getSupplierRepository()->findSuppliersByCategory(
            $purchaseRequest->getSuppliesCategory()
        );

        /** @var RequestItem $item */
        foreach ($purchaseRequest->getItems() as $item) {
            /** @var Supplier $supplier */
            foreach ($recommendedSuppliers as $supplier) {
                $query['items'][] = [
                    'supplierItn' => $supplier->getItn(),
                    'title' => $item->getTitle(),
                    'sku' => $item->getSku(),
                    'quantity' => $item->getQuantity(),
                    'olympItemId' => $item->getId()
                ];
            }
        }

        $client->request(
            'POST',
            $url,
            [
                'json' => $query
            ]
        );

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param PurchaseRequest $purchaseRequest
     * @param $comment
     * @return PurchaseRequestComment
     */
    protected function addComment(PurchaseRequest $purchaseRequest, $comment)
    {
        $purchaseRequestComment = new PurchaseRequestComment();

        $changes = ['comment' => []];

        if (!empty($comment['id'])) {
            $purchaseRequestComment = $this->getPurchaseRequestCommentRepository()->findOneBy([
                'id' => $comment['id'],
                'owner' => $this->getUser()->getId()
            ]) ?: $purchaseRequestComment;
        }

        $changes['comment'][] = $purchaseRequestComment->getCommentText();
        $purchaseRequestComment
            ->setOwner($this->getUser())
            ->setPurchaseRequest($purchaseRequest)
            ->setCommentText(StringUtils::parseLinks($comment['text']))
            ->setCreatedAt(new \DateTime())
        ;
        $changes['comment'][] = $purchaseRequestComment->getCommentText();

        if (!empty($comment['reply-id'])) {
            $parentComment = $this->getPurchaseRequestCommentRepository()->find($comment['reply-id']);
            $purchaseRequestComment->setParentComment($parentComment);
        }

        $this->logChanges($purchaseRequest, $changes);
        $em = $this->getEm();
        $em->persist($purchaseRequestComment);

        return $purchaseRequestComment;
    }

    /**
     * @param PurchaseRequest $purchaseRequest
     * @param $changeSet
     * @return array
     */
    protected function logChanges(PurchaseRequest $purchaseRequest, $changeSet)
    {
        $em = $this->getEm();
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
                    ->setChangedBy($this->getUser())
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
     * @return int|string
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

        if ($field == 'priority' && $value) {
            $value = PurchaseRequest::getPriorityList()[$value];
        }

        return $value;
    }

    protected function sendEmail($title, $recipients, $body)
    {
        foreach ($recipients as $recipient) {
            $email = new \Swift_Message('[OLYMP]' . $title);

            $email
                ->setFrom('olymp@npo-at.com')
                ->setTo($recipient->getEmail())
                ->setBody($body, 'text/html');

            $this->get('mailer')->send($email);
        }
    }

    /**
     * @param UploadedFile $file
     * @param RequestFile $requestFile
     * @param $projectId
     * @param $purchaseRequestId
     * @return string
     * @throws \Exception
     */
    protected function moveFile(UploadedFile $file, RequestFile $requestFile, $projectId, $purchaseRequestId)
    {
        // Generate a unique name for the file before saving it
        $dirName = uniqid();
        $fileName = $file->getClientOriginalName();
        $storedFileName = $fileName;
        $requestFile->setStoredFileName($storedFileName);
        $requestFile->setStoredFileDir($dirName);

        // Move the file to the directory where brochures are stored

        $filePath = $this->getParameter('purchase_files_root_dir') . '/' . $projectId . '/' . $purchaseRequestId . '/' .
            $dirName;

        $file->move(
            $filePath,
            $fileName
        );

        if (in_array($requestFile->getFormat(), ['jpg', 'jpeg', 'png'])) {
            $thumbName = $fileName .'_100x100.' . $requestFile->getFormat();
            $requestFile->setStoredPreviewFileName($thumbName);
            $thumb = new \Imagick($this->getParameter('purchase_files_root_dir') . '/' . $projectId . '/' . $purchaseRequestId . '/' . $dirName  . '/' .  $storedFileName);
            $thumb->setImageGravity(\Imagick::GRAVITY_CENTER);
            $thumb->resizeImage(200, 200, \Imagick::FILTER_LANCZOS, 1, 0);
            $thumb->cropImage(100,100, 25, 25);
            $thumb->writeImage($this->getParameter('purchase_files_root_dir') . '/' . $projectId . '/' . $purchaseRequestId . '/' . $dirName  . '/' . $thumbName);
        }
    }

    /**
     * @param $chatId
     * @param $message
     */
    protected function sendTelegramNotification($chatId, $message)
    {
        $params = [
            'text' => $message,
            'chat_id' => $chatId,
            'parse_mode' => 'Markdown'
        ];

        $this->get('old_sound_rabbit_mq.notification_create_producer')->publish(json_encode($params));
    }

    /**
     * @param UploadedFile $file
     * @throws \Exception
     */
    protected function validateFile(UploadedFile $file)
    {
        if ($file->getSize() > 102400000) {
            throw new MaxFileSizeException($this->get('translator'), $file->getClientOriginalName());
        }
    }
}
