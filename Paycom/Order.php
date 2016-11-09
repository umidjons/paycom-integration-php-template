<?php
namespace Paycom;

class Order
{
    /** Order is available for sell, anyone can buy it. */
    const STATE_AVAILABLE = 0;

    /** Pay in progress, order must not be changed. */
    const STATE_WAITING_PAY = 1;

    /** Order completed and not available for sell. */
    const STATE_PAY_ACCEPTED = 2;

    /** Order is cancelled. */
    const STATE_CANCELLED = 3;

    public $request_id;
    public $params;

    public function __construct($request_id)
    {
        $this->request_id = $request_id;
    }

    /**
     * Validates amount and account values.
     * @param array $params amount and account parameters to validate.
     * @return bool true - if validation passes
     * @throws PaycomException - if validation fails
     */
    public function validate(array $params)
    {
        // todo: Validate amount, if failed throw error
        // for example, check amount is numeric
        if (!is_numeric($params['amount'])) {
            throw new PaycomException(
                $this->request_id,
                'Incorrect amount.',
                PaycomException::ERROR_INVALID_AMOUNT
            );
        }

        // todo: Validate account, if failed throw error
        // assume, we should have order_id
        if (!isset($params['account']['order_id'])) {
            throw new PaycomException(
                $this->request_id,
                PaycomException::message(
                    'Неверный код заказа.',
                    'Harid kodida xatolik.',
                    'Incorrect order code.'
                ),
                PaycomException::ERROR_INVALID_ACCOUNT,
                'order_id'
            );
        }

        // todo: Check is order available

        // assume, after find() $this will be populated with Order data
        $this->find($params['account']['order_id']);

        // for example, order state before payment should be 'waiting pay'
        if ($this->state != self::STATE_WAITING_PAY) {
            throw new PaycomException(
                $this->request_id,
                'Order state is invalid.',
                PaycomException::ERROR_COULD_NOT_PERFORM
            );
        }

        // keep params for further use
        $this->params = $params;

        return true;
    }

    /**
     * Find order by given parameters.
     * @param mixed $params parameters.
     * @return Order|Order[] found order or array of orders.
     */
    public function find($params)
    {
        // todo: Implement searching order(s) by given parameters, populate current instance with data
    }

    /**
     * Change order's state to specified one.
     * @param int $state new state of the order
     * @return void
     */
    public function changeState($state)
    {
        // todo: Implement changing order state (reserve order after create transaction or free order after cancel)
    }

    /**
     * Check, whether order can be cancelled or not.
     * @return bool true - order is cancellable, otherwise false.
     */
    public function allowCancel()
    {
        // todo: Implement order cancelling allowance check
    }
}