<?php

namespace PurchaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RequestTimings
 *
 * @ORM\Table(name="request_timings")
 * @ORM\Entity(repositoryClass="PurchaseBundle\Repository\RequestTimingsRepository")
 */
class RequestTimings
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", name="created_at", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", name="returned_to_fixing_at", nullable=true)
     */
    private $returnedToFixingAt;

    /**
     * @ORM\Column(type="datetime", name="moved_to_leader_approve_at", nullable=true)
     */
    private $movedToLeaderApproveAt;

    /**
     * @ORM\Column(type="datetime", name="leader_approved_at", nullable=true)
     */
    private $leaderApprovedAt;

    /**
     * @ORM\Column(type="datetime", name="production_leader_approved_at", nullable=true)
     */
    private $productionLeaderApprovedAt;

    /**
     * @ORM\Column(type="datetime", name="purchasing_leader_approved_at", nullable=true)
     */
    private $purchasingLeaderApprovedAt;

    /**
     * @ORM\Column(type="datetime", name="manager_stated_work_at", nullable=true)
     */
    private $managerStartedWorkAt;

    /**
     * @ORM\Column(type="datetime", name="manager_attached_invoice_at", nullable=true)
     */
    private $managerAttachedInvoiceAt;

    /**
     * @ORM\Column(type="datetime", name="manager_finished_work_at", nullable=true)
     */
    private $managerFinishedWorkAt;

    /**
     * @ORM\Column(type="datetime", name="manager_marked_as_delivered_at", nullable=true)
     */
    private $managerMarkedAsDeliveredAt;

    /**
     * @ORM\Column(type="datetime", name="financial_leader_approved_at", nullable=true)
     */
    private $financialLeaderApprovedAt;

    /**
     * @ORM\Column(type="datetime", name="financial_manager_marked_as_paid_at", nullable=true)
     */
    private $financialManagerMarkedAsPaidAt;

    /**
     * @ORM\Column(type="datetime", name="request_marked_as_done_at", nullable=true)
     */
    private $requestMarkedAsDoneAt;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     * @return RequestTimings
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReturnedToFixingAt()
    {
        return $this->returnedToFixingAt;
    }

    /**
     * @param mixed $returnedToFixingAt
     * @return RequestTimings
     */
    public function setReturnedToFixingAt($returnedToFixingAt)
    {
        $this->returnedToFixingAt = $returnedToFixingAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMovedToLeaderApproveAt()
    {
        return $this->movedToLeaderApproveAt;
    }

    /**
     * @param mixed $movedToLeaderApproveAt
     * @return RequestTimings
     */
    public function setMovedToLeaderApproveAt($movedToLeaderApproveAt)
    {
        $this->movedToLeaderApproveAt = $movedToLeaderApproveAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLeaderApprovedAt()
    {
        return $this->leaderApprovedAt;
    }

    /**
     * @param mixed $leaderApprovedAt
     * @return RequestTimings
     */
    public function setLeaderApprovedAt($leaderApprovedAt)
    {
        $this->leaderApprovedAt = $leaderApprovedAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProductionLeaderApprovedAt()
    {
        return $this->productionLeaderApprovedAt;
    }

    /**
     * @param mixed $productionLeaderApprovedAt
     * @return RequestTimings
     */
    public function setProductionLeaderApprovedAt($productionLeaderApprovedAt)
    {
        $this->productionLeaderApprovedAt = $productionLeaderApprovedAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPurchasingLeaderApprovedAt()
    {
        return $this->purchasingLeaderApprovedAt;
    }

    /**
     * @param mixed $purchasingLeaderApprovedAt
     * @return RequestTimings
     */
    public function setPurchasingLeaderApprovedAt($purchasingLeaderApprovedAt)
    {
        $this->purchasingLeaderApprovedAt = $purchasingLeaderApprovedAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getManagerStartedWorkAt()
    {
        return $this->managerStartedWorkAt;
    }

    /**
     * @param mixed $managerStartedWorkAt
     * @return RequestTimings
     */
    public function setManagerStartedWorkAt($managerStartedWorkAt)
    {
        $this->managerStartedWorkAt = $managerStartedWorkAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getManagerAttachedInvoiceAt()
    {
        return $this->managerAttachedInvoiceAt;
    }

    /**
     * @param mixed $managerAttachedInvoiceAt
     * @return RequestTimings
     */
    public function setManagerAttachedInvoiceAt($managerAttachedInvoiceAt)
    {
        $this->managerAttachedInvoiceAt = $managerAttachedInvoiceAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getManagerFinishedWorkAt()
    {
        return $this->managerFinishedWorkAt;
    }

    /**
     * @param mixed $managerFinishedWorkAt
     * @return RequestTimings
     */
    public function setManagerFinishedWorkAt($managerFinishedWorkAt)
    {
        $this->managerFinishedWorkAt = $managerFinishedWorkAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getManagerMarkedAsDeliveredAt()
    {
        return $this->managerMarkedAsDeliveredAt;
    }

    /**
     * @param mixed $managerMarkedAsDeliveredAt
     * @return RequestTimings
     */
    public function setManagerMarkedAsDeliveredAt($managerMarkedAsDeliveredAt)
    {
        $this->managerMarkedAsDeliveredAt = $managerMarkedAsDeliveredAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFinancialLeaderApprovedAt()
    {
        return $this->financialLeaderApprovedAt;
    }

    /**
     * @param mixed $financialLeaderApprovedAt
     * @return RequestTimings
     */
    public function setFinancialLeaderApprovedAt($financialLeaderApprovedAt)
    {
        $this->financialLeaderApprovedAt = $financialLeaderApprovedAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFinancialManagerMarkedAsPaidAt()
    {
        return $this->financialManagerMarkedAsPaidAt;
    }

    /**
     * @param mixed $financialManagerMarkedAsPaidAt
     * @return RequestTimings
     */
    public function setFinancialManagerMarkedAsPaidAt($financialManagerMarkedAsPaidAt)
    {
        $this->financialManagerMarkedAsPaidAt = $financialManagerMarkedAsPaidAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRequestMarkedAsDoneAt()
    {
        return $this->requestMarkedAsDoneAt;
    }

    /**
     * @param mixed $requestMarkedAsDoneAt
     * @return RequestTimings
     */
    public function setRequestMarkedAsDoneAt($requestMarkedAsDoneAt)
    {
        $this->requestMarkedAsDoneAt = $requestMarkedAsDoneAt;
        return $this;
    }

    /**
     * Reset all timings
     */
    public function resetTimings()
    {
        $this
            ->setReturnedToFixingAt(new \DateTime())
            ->setFinancialLeaderApprovedAt(null)
            ->setFinancialManagerMarkedAsPaidAt(null)
            ->setLeaderApprovedAt(null)
            ->setManagerAttachedInvoiceAt(null)
            ->setManagerFinishedWorkAt(null)
            ->setManagerMarkedAsDeliveredAt(null)
            ->setManagerStartedWorkAt(null)
            ->setMovedToLeaderApproveAt(null)
            ->setProductionLeaderApprovedAt(null)
            ->setPurchasingLeaderApprovedAt(null)
            ->setRequestMarkedAsDoneAt(null);
    }
}

