<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectStatus
 *
 * @ORM\Table(name="protocol_members")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProtocolMembersRepository")
 */
class ProtocolMembers
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ProjectTask")
     * @ORM\JoinColumn(name="protocol_id", referencedColumnName="id")
     */
    private $protocol;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     */
    private $member;

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
     * Set protocol
     *
     * @param \stdClass $protocol
     *
     * @return ProtocolMembers
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * Get protocol
     *
     * @return \stdClass
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Set member
     *
     * @param \stdClass $member
     *
     * @return ProtocolMembers
     */
    public function setMember($member)
    {
        $this->member = $member;

        return $this;
    }

    /**
     * Get member
     *
     * @return \stdClass
     */
    public function getMember()
    {
        return $this->member;
    }
}
