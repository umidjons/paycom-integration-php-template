<?php
namespace Paycom;

/**
 * Class Transaction
 *
 * Example MySQL table might look like to the following:
 *
 * CREATE TABLE `transactions` (
 *   `id` INT(11) NOT NULL AUTO_INCREMENT,
 *   `paycom_transaction_id` VARCHAR(25) NOT NULL COLLATE 'utf8_unicode_ci',
 *   `paycom_time` VARCHAR(13) NOT NULL COLLATE 'utf8_unicode_ci',
 *   `paycom_time_datetime` DATETIME NOT NULL,
 *   `create_time` DATETIME NOT NULL,
 *   `perform_time` DATETIME NULL DEFAULT NULL,
 *   `cancel_time` DATETIME NULL DEFAULT NULL,
 *   `amount` INT(11) NOT NULL,
 *   `state` TINYINT(2) NOT NULL,
 *   `reason` TINYINT(2) NULL DEFAULT NULL,
 *   `receivers` VARCHAR(500) NULL DEFAULT NULL COMMENT 'JSON array of receivers' COLLATE 'utf8_unicode_ci',
 *   `order_id` INT(11) NOT NULL,
 *
 *   PRIMARY KEY (`id`)
 * )
 *   COLLATE='utf8_unicode_ci'
 *   ENGINE=InnoDB
 *   AUTO_INCREMENT=1;
 *
 */
class Transaction
{
    /** Transaction expiration time in milliseconds. 43 200 000 ms = 12 hours. */
    const TIMEOUT = 43200000;

    const STATE_CREATED = 1;
    const STATE_COMPLETED = 2;
    const STATE_CANCELLED = -1;
    const STATE_CANCELLED_AFTER_COMPLETE = -2;

    const REASON_RECEIVERS_NOT_FOUND = 1;
    const REASON_PROCESSING_EXECUTION_FAILED = 2;
    const REASON_EXECUTION_FAILED = 3;
    const REASON_CANCELLED_BY_TIMEOUT = 4;
    const REASON_FUND_RETURNED = 5;
    const REASON_UNKNOWN = 10;

    /** @var string Paycom transaction id. */
    public $paycom_transaction_id;

    /** @var int Paycom transaction time as is without change. */
    public $paycom_time;

    /** @var string Paycom transaction time as date and time string. */
    public $paycom_time_datetime;

    /** @var int Transaction id in the merchant's system. */
    public $id;

    /** @var string Transaction create date and time in the merchant's system. */
    public $create_time;

    /** @var string Transaction perform date and time in the merchant's system. */
    public $perform_time;

    /** @var string Transaction cancel date and time in the merchant's system. */
    public $cancel_time;

    /** @var int Transaction state. */
    public $state;

    /** @var int Transaction cancelling reason. */
    public $reason;

    /** @var int Amount value in coins, this is service or product price. */
    public $amount;

    /** @var string Pay receivers. Null - owner is the only receiver. */
    public $receivers;

    // additional fields:
    // - to identify order or product, for example, code of the order
    // - to identify client, for example, account id or phone number

    /** @var string Code to identify the order or service for pay. */
    public $order_id;

    private $db_conn;

    public function __construct(\PDO $db_conn)
    {
        $this->db_conn = $db_conn;
    }

    /**
     * Saves current transaction instance in a data store.
     * @return void
     */
    public function save()
    {
        // todo: Implement creating/updating transaction into data store
        // todo: Populate $id property with newly created transaction id
    }

    /**
     * Cancels transaction with the specified reason.
     * @param int $reason cancelling reason.
     * @return void
     */
    public function cancel($reason)
    {
        // todo: Implement transaction cancelling on data store

        // todo: Populate $cancel_time with value
        $this->cancel_time = Format::timestamp2datetime(Format::timestamp());

        // todo: Change $state to cancelled (-1 or -2) according to the current state
        // Scenario: CreateTransaction -> CancelTransaction
        $this->state = self::STATE_CANCELLED;
        // Scenario: CreateTransaction -> PerformTransaction -> CancelTransaction
        if ($this->state == self::STATE_COMPLETED) {
            $this->state = self::STATE_CANCELLED_AFTER_COMPLETE;
        }

        // set reason
        $this->reason = $reason;

        // todo: Update transaction on data store
    }

    /**
     * Determines whether current transaction is expired or not.
     * @return bool true - if current instance of the transaction is expired, false - otherwise.
     */
    public function isExpired()
    {
        // todo: Implement transaction expiration check
        // for example, if transaction is active and passed TIMEOUT milliseconds after its creation, then it is expired
        return $this->state == self::STATE_CREATED && Format::datetime2timestamp($this->create_time) - time() > self::TIMEOUT;
    }

    /**
     * Find transaction by given parameters.
     * @param mixed $params parameters
     * @return Transaction|Transaction[]
     */
    public function find($params)
    {
        $is_success = false;
        $db_stmt = null;

        // todo: Implement searching transaction by id, populate current instance with data and return it
        if (isset($params['id'])) {
            $sql = 'select * from transactions where paycom_transaction_id=:trx_id';
            $db_stmt = $this->db_conn->prepare($sql);
            $is_success = $db_stmt->execute([':trx_id' => $params['id']]);
        }

        // todo: Implement searching transactions by given parameters and return list of transactions

        // if SQL operation succeeded, then try to populate instance properties with values
        if ($is_success) {

            // fetch one row
            $row = $db_stmt->fetch();

            // if there is row available, then populate properties with values
            if ($row) {

                $this->id = $row['id'];
                $this->paycom_transaction_id = $row['paycom_transaction_id'];
                $this->paycom_time = 1 * $row['paycom_time'];
                $this->paycom_time_datetime = $row['paycom_time_datetime'];
                $this->create_time = $row['create_time'];
                $this->perform_time = $row['perform_time'];
                $this->cancel_time = $row['cancel_time'];
                $this->state = 1 * $row['state'];
                $this->reason = $row['reason'] ? 1 * $row['reason'] : null;
                $this->amount = 1 * $row['amount'];
                $this->order_id = 1 * $row['order_id'];

                // assume, receivers column contains list of receivers in JSON format as string
                $this->receivers = $row['receivers'] ? json_decode($row['receivers'], true) : null;

                // return populated instance
                return $this;
            }

        }

        // transaction not found, return null
        return null;

        // Possible features:
        // Search transaction by product/order id that specified in $params
        // Search transactions for a given period of time that specified in $params
    }

    /**
     * Gets list of transactions for the given period including period boundaries.
     * @param int $from_date start of the period in timestamp.
     * @param int $to_date end of the period in timestamp.
     * @return array list of found transactions converted into report format for send as a response.
     */
    public function report($from_date, $to_date)
    {
        $from_date = Format::timestamp2datetime($from_date);
        $to_date = Format::timestamp2datetime($to_date);

        // container to hold rows/document from data store
        $rows = [];

        // todo: Retrieve transactions for the specified period from data store

        // assume, here we have $rows variable that is populated with transactions from data store
        // normalize data for response
        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'id' => $row['paycom_transaction_id'], // paycom transaction id
                'time' => 1 * $row['paycom_time'], // paycom transaction timestamp as is
                'amount' => 1 * $row['amount'],
                'account' => [
                    'order_id' => $row['order_id'], // account parameters to identify client/order/service
                    // ... additional parameters may be listed here, which are belongs to the account
                ],
                'create_time' => Format::datetime2timestamp($row['create_time']),
                'perform_time' => Format::datetime2timestamp($row['perform_time']),
                'cancel_time' => Format::datetime2timestamp($row['cancel_time']),
                'transaction' => $row['id'],
                'state' => 1 * $row['state'],
                'reason' => isset($row['reason']) ? 1 * $row['reason'] : null,
                'receivers' => $row['receivers']
            ];
        }

        return $result;
    }
}