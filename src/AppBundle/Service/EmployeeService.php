<?php

namespace AppBundle\Service;

use AppBundle\Repository\RepositoryAwareTrait;
use AppBundle\Entity\User;

/**
 * Created by PhpStorm.
 * User: apermyakov
 * Date: 17.11.17
 * Time: 11:40
 */
class EmployeeService
{
    use RepositoryAwareTrait;

    protected $doctrine;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @return array
     */
    public function getUsersGroupedByTeams()
    {
        return $this->getUserRepository()->getUsersGroupedByTeams();
    }

    /**
     * @return mixed
     */
    public function getTodayEmployeesBirthdates()
    {
        return $this->getUserRepository()->getTodayEmployeesBirthdates();
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function getTodayEmployeeBirthdate($user)
    {
        return $this->getUserRepository()->getTodayEmployeeBirthdate($user);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isOnline($user)
    {
        $isOnline = false;

        if ($user->getLastOnline() != null) {
            $dateDiffSeconds = $this->getLastOnlineDiff($user->getLastOnline());
            $isOnline = $dateDiffSeconds <= 900 ? true : false;
        }

        return $isOnline;
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function getOnlineStatus($user)
    {
        $status = ['status' => 'offline', 'colorBadge' => 'danger'];

        if ($user->getLastOnline() != null) {

            $dateDiffSeconds = $this->getLastOnlineDiff($user->getLastOnline());

            if ($dateDiffSeconds < 300) {
                $status = ['status' => 'online', 'colorBadge' => 'success'];
            } elseif ($dateDiffSeconds >= 300 && $dateDiffSeconds <= 900) {
                $status = ['status' => 'not_here', 'colorBadge' => 'warning'];
            }
        }

        return $status;
    }

    /**
     * @param $lastOnline
     * @return mixed
     */
    protected function getLastOnlineDiff($lastOnline)
    {
        $nowAt = new \DateTime();
        $dateDiff = date_diff($lastOnline, $nowAt);
        $dateDiffSeconds = date_create('@0')->add($dateDiff)->getTimestamp();

        return $dateDiffSeconds;
    }

    /**
     * @return mixed
     */
    protected function getDoctrine()
    {
        return $this->doctrine;
    }
}