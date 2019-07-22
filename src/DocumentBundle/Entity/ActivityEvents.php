<?php

namespace DocumentBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ActivityEvents
 *
 * @ORM\Table(name="activity_events")
 * @ORM\Entity(repositoryClass="DocumentBundle\Repository\ActivityEventsRepository")
 */
class ActivityEvents
{
    const TYPE_ADDITIONAL = 'additional';
    const TYPE_SUCCESS = 'success';

    const ACTIVITY_EVENTS_STATUS_NOT_PERFORMED = 1;
    const ACTIVITY_EVENTS_STATUS_DONE = 2;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name = '';

    /**
     * @ORM\ManyToOne(targetEntity="DocumentBundle\Entity\Activity")
     * @ORM\JoinColumn(name="activity_id", referencedColumnName="id")
     */
    private $activity;

    /**
     * @ORM\ManyToOne(targetEntity="DocumentBundle\Entity\ActivityEvents")
     * @ORM\JoinColumn(name="success_event_id", referencedColumnName="id", nullable=true)
     */
    private $successEvent;

    /**
     * @ORM\OneToMany(targetEntity="DocumentBundle\Entity\ActivityEvents", mappedBy="successEvent", cascade="all")
     */
    private $additionalEvents;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status = 1;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_at", type="datetime", nullable=true)
     */
    private $endAt;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="responsible_user_id", referencedColumnName="id")
     */
    private $responsibleUser;

    public function __construct()
    {
        $this->type = self::TYPE_SUCCESS;
        $this->additionalEvents = new ArrayCollection();
        $this->status = self::ACTIVITY_EVENTS_STATUS_NOT_PERFORMED;
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
     * Set name
     *
     * @param string $name
     *
     * @return ActivityEvents
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * @param mixed $activity
     */
    public function setActivity($activity)
    {
        $this->activity = $activity;
    }

    /**
     * @return mixed
     */
    public function getSuccessEvent()
    {
        return $this->successEvent;
    }

    /**
     * @param ActivityEvents $successEvent
     * @return ActivityEvents
     */
    public function setSuccessEvent($successEvent)
    {
        $this->successEvent = $successEvent;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getAdditionalEvents()
    {
        return $this->additionalEvents;
    }

    /**
     * @param ActivityEvents[] $additionalEvents
     */
    public function setAdditionalEvents($additionalEvents)
    {
        $this->additionalEvents = $additionalEvents;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return ActivityEvents
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return \DateTime
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * @param \DateTime $endAt
     * @return ActivityEvents
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;
        return $this;
    }

    /**
     * Set responsibleUser
     *
     * @param User $responsibleUser
     *
     * @return $this
     */
    public function setResponsibleUser($responsibleUser)
    {
        $this->responsibleUser = $responsibleUser;

        return $this;
    }

    /**
     * Get responsibleUser
     *
     * @return User
     */
    public function getResponsibleUser()
    {
        return $this->responsibleUser;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isResponsibleUser(User $user)
    {
        return $this->responsibleUser->getId() == $user->getId();
    }

    /**
     * @return array
     */
    public static function getTypesList()
    {
        return [
            self::TYPE_SUCCESS => self::TYPE_SUCCESS,
            self::TYPE_ADDITIONAL => self::TYPE_ADDITIONAL,
        ];
    }

    /**
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::ACTIVITY_EVENTS_STATUS_NOT_PERFORMED => 'activity.result_not_performed',
            self::ACTIVITY_EVENTS_STATUS_DONE => 'activity.result_done'
        ];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isSuccessEvent()
    {
        return $this->type == self::TYPE_SUCCESS;
    }

    /**
     * @return bool
     */
    public function isAdditionalEvent()
    {
        return $this->type == self::TYPE_ADDITIONAL;
    }

    /**
     * @return bool
     */
    public function isNotPerformed()
    {
        return $this->status == self::ACTIVITY_EVENTS_STATUS_NOT_PERFORMED;
    }

    /**
     * @return bool
     */
    public function isDone()
    {
        return $this->status == self::ACTIVITY_EVENTS_STATUS_DONE;
    }
}