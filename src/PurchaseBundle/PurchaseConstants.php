<?php
namespace PurchaseBundle;

class PurchaseConstants
{
    const STATUS_NEW = 'new';
    const STATUS_NEEDS_PROJECT_LEADER_APPROVE = 'needs_project_leader_approve';
    const STATUS_NEEDS_LEADER_APPROVAL = 'needs_leader_approval';
    const STATUS_NEEDS_PRODUCTION_LEADER_APPROVAL = 'needs_production_leader_approval';
    const STATUS_NEEDS_PURCHASING_MANAGER = 'needs_purchasing_manager';
    const STATUS_NEEDS_FIXING = 'needs_fixing';
    const STATUS_MANAGER_ASSIGNED = 'manager_assigned';
    const STATUS_MANAGER_STARTED_WORK = 'manager_started_work';
    const STATUS_MANAGER_FINISHED_WORK = 'manager_finished_work';
    const STATUS_REJECTED = 'rejected';
    const STATUS_DONE = 'done';
    const STATUS_ON_PRELIMINARY_ESTIMATE = 'on_preliminary_estimate';
    const STATUS_NEEDS_PRELIMINARY_ESTIMATE_APPROVE = 'needs_preliminary_estimate_approve';

    const PAYMENT_STATUS_NEEDS_PAYMENT = 'needs_payment';
    const PAYMENT_STATUS_PAYMENT_PROCESSING = 'payment_processing';
    const PAYMENT_STATUS_PAID = 'paid';

    const DELIVERY_STATUS_AWAITING_DELIVERY = 'awaiting_delivery';
    const DELIVERY_STATUS_IN_DELIVERY = 'in_delivery';
    const DELIVERY_STATUS_DELIVERED = 'delivered';

    const PRODUCTION_STATUS_IN_PRODUCTION = 'in_production';
    const PRODUCTION_STATUS_PRODUCED = 'produced';

    const TYPE_PURCHASE = 'purchase';
    const TYPE_PRODUCTION = 'production';
    const TYPE_MOVEMENT = 'movement';

    /**
     * @return array
     */
    public static function getStatesList()
    {
        return [
            self::STATUS_NEW,
            self::STATUS_NEEDS_LEADER_APPROVAL,
            self::STATUS_NEEDS_PROJECT_LEADER_APPROVE,
            self::STATUS_NEEDS_PRODUCTION_LEADER_APPROVAL,
            self::STATUS_NEEDS_PURCHASING_MANAGER,
            self::STATUS_NEEDS_FIXING,
            self::STATUS_MANAGER_ASSIGNED,
            self::STATUS_MANAGER_STARTED_WORK,
            self::STATUS_MANAGER_FINISHED_WORK,
            self::STATUS_REJECTED,
            self::STATUS_DONE,
            self::STATUS_ON_PRELIMINARY_ESTIMATE,
            self::STATUS_NEEDS_PRELIMINARY_ESTIMATE_APPROVE
        ];
    }

    /**
     * @return array
     */
    public static function getPrioritiesList()
    {
        return [
            4 => 'А+',
            3 => 'A',
            2 => 'B',
            1 => 'C'
        ];
    }

    /**
     * @return array
     */
    public static function getProjectPrioritiesMapping()
    {
        return [
            4 => 1,
            3 => 2,
            2 => 3,
            1 => 4
        ];
    }

    /**
     * @return array
     */
    public static function getPaymentStatesList()
    {
        return [
            self::PAYMENT_STATUS_NEEDS_PAYMENT,
            self::PAYMENT_STATUS_PAYMENT_PROCESSING,
            self::PAYMENT_STATUS_PAID,
        ];
    }

    /**
     * @return array
     */
    public static function getDeliveryStatesList()
    {
        return [
            self::DELIVERY_STATUS_AWAITING_DELIVERY,
            self::DELIVERY_STATUS_IN_DELIVERY,
            self::DELIVERY_STATUS_DELIVERED,
        ];
    }

    public static function getTypesChoices()
    {
        return [
            self::TYPE_PRODUCTION => ucfirst(self::TYPE_PRODUCTION),
            self::TYPE_PURCHASE => ucfirst(self::TYPE_PURCHASE),
            self::TYPE_MOVEMENT => ucfirst(self::TYPE_MOVEMENT),
        ];
    }
}