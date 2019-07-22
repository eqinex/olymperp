<?php

namespace AppBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use PurchaseBundle\Entity\PurchaseRequest;
use PurchaseBundle\PurchaseConstants;

/**
 * Product
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProjectRepository")
 *
 */
class Product extends Project
{
    
}

