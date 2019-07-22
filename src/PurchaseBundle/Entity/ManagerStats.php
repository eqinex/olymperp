<?php

namespace PurchaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ManagerStats
 *
 * @ORM\Table(name="manager_stats")
 * @ORM\Entity(repositoryClass="PurchaseBundle\Repository\ManagerStatsRepository")
 */
class ManagerStats
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="manager_id", referencedColumnName="id")
     */
    private $manager;

    /**
     * @var int
     *
     * @ORM\Column(name="assigned_requests", type="integer")
     */
    private $assignedRequests;

    /**
     * @var int
     *
     * @ORM\Column(name="requests_in_progress", type="integer")
     */
    private $requestsInprogress;

    /**
     * @var int
     *
     * @ORM\Column(name="requests_processed", type="integer")
     */
    private $requestsProcessed;

    /**
     * @var int
     *
     * @ORM\Column(name="items_processed", type="integer")
     */
    private $itemsProcessed;

    /**
     * @var float
     *
     * @ORM\Column(name="processed_prices_amount", type="float")
     */
    private $processedPricesAmount;

    /**
     * @var int
     *
     * @ORM\Column(name="finished_requests", type="integer")
     */
    private $finishedRequests;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="stats_date", type="datetime")
     */
    private $statsDate;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set manager.
     *
     * @param \stdClass $manager
     *
     * @return ManagerStats
     */
    public function setManager($manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Get manager.
     *
     * @return \stdClass
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Set assignedRequests.
     *
     * @param int $assignedRequests
     *
     * @return ManagerStats
     */
    public function setAssignedRequests($assignedRequests)
    {
        $this->assignedRequests = $assignedRequests;

        return $this;
    }

    /**
     * Get assignedRequests.
     *
     * @return int
     */
    public function getAssignedRequests()
    {
        return $this->assignedRequests;
    }

    /**
     * Set requestsInprogress.
     *
     * @param int $requestsInprogress
     *
     * @return ManagerStats
     */
    public function setRequestsInprogress($requestsInprogress)
    {
        $this->requestsInprogress = $requestsInprogress;

        return $this;
    }

    /**
     * Get requestsInprogress.
     *
     * @return int
     */
    public function getRequestsInprogress()
    {
        return $this->requestsInprogress;
    }

    /**
     * Set requestsProcessed.
     *
     * @param int $requestsProcessed
     *
     * @return ManagerStats
     */
    public function setRequestsProcessed($requestsProcessed)
    {
        $this->requestsProcessed = $requestsProcessed;

        return $this;
    }

    /**
     * Get requestsProcessed.
     *
     * @return int
     */
    public function getRequestsProcessed()
    {
        return $this->requestsProcessed;
    }

    /**
     * Set itemsProcessed.
     *
     * @param int $itemsProcessed
     *
     * @return ManagerStats
     */
    public function setItemsProcessed($itemsProcessed)
    {
        $this->itemsProcessed = $itemsProcessed;

        return $this;
    }

    /**
     * Get itemsProcessed.
     *
     * @return int
     */
    public function getItemsProcessed()
    {
        return $this->itemsProcessed;
    }

    /**
     * Set processedPricesAmount.
     *
     * @param float $processedPricesAmount
     *
     * @return ManagerStats
     */
    public function setProcessedPricesAmount($processedPricesAmount)
    {
        $this->processedPricesAmount = $processedPricesAmount;

        return $this;
    }

    /**
     * Get processedPricesAmount.
     *
     * @return float
     */
    public function getProcessedPricesAmount()
    {
        return $this->processedPricesAmount;
    }

    /**
     * Set finishedRequests.
     *
     * @param int $finishedRequests
     *
     * @return ManagerStats
     */
    public function setFinishedRequests($finishedRequests)
    {
        $this->finishedRequests = $finishedRequests;

        return $this;
    }

    /**
     * Get finishedRequests.
     *
     * @return int
     */
    public function getFinishedRequests()
    {
        return $this->finishedRequests;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return ManagerStats
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set statsDay.
     *
     * @param \DateTime $statsDate
     *
     * @return ManagerStats
     */
    public function setStatsDate($statsDate)
    {
        $this->statsDate = $statsDate;

        return $this;
    }

    /**
     * Get statsDay.
     *
     * @return \DateTime
     */
    public function getStatsDate()
    {
        return $this->statsDate;
    }
}
