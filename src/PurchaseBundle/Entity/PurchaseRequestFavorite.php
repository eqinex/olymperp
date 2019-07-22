<?php


namespace PurchaseBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * PurchaseRequestFavorite
 *
 * @ORM\Table(name="purchase_request_favorite")
 * @ORM\Entity(repositoryClass="PurchaseBundle\Repository\PurchaseRequestFavoriteRepository")
 */
class PurchaseRequestFavorite
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
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseBundle\Entity\PurchaseRequest")
     * @ORM\JoinColumn(name="purchase_request_id", referencedColumnName="id")
     */
    private $purchaseRequest;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPurchaseRequest()
    {
        return $this->purchaseRequest;
    }

    /**
     * @param PurchaseRequest $purchaseRequest
     *
     * @return $this
     */
    public function setPurchaseRequest($purchaseRequest)
    {
        $this->purchaseRequest = $purchaseRequest;

        return $this;
    }
}