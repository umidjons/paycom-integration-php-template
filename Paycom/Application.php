<?php
namespace Paycom;

class Application
{
    public $config;
    public $request;
    public $response;
    public $merchant;

    /**
     * Application constructor.
     * @param array $config configuration array with <em>merchant_id</em>, <em>login</em>, <em>keyFile</em> keys.
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->request = new Request();
        $this->response = new Response($this->request);
        $this->merchant = new Merchant($this->config);
    }

    /**
     * Authorizes session and handles requests.
     */
    public function run()
    {
        // authorize session
        $this->merchant->Authorize($this->request->id);

        // handle request
        try {
            switch ($this->request->method) {
                case 'CheckPerformTransaction':
                    $this->CheckPerformTransaction();
                    break;
                case 'CheckTransaction':
                    $this->CheckTransaction();
                    break;
                case 'CreateTransaction':
                    $this->CreateTransaction();
                    break;
                case 'PerformTransaction':
                    $this->PerformTransaction();
                    break;
                case 'CancelTransaction':
                    $this->CancelTransaction();
                    break;
                case 'ChangePassword':
                    $this->ChangePassword();
                    break;
                case 'GetStatement':
                    $this->GetStatement();
                    break;
                default:
                    $this->response->error(
                        PaycomException::ERROR_METHOD_NOT_FOUND,
                        'Запрашиваемый метод не найден.',
                        $this->request->method
                    );
                    break;
            }
        } catch (PaycomException $exc) {
            $exc->send();
        }
    }

    private function CheckPerformTransaction()
    {
        $order = new Order($this->request->id, $this->request->params);

        // validate parameters
        $order->validate($this->request->params);

        // todo: Check is there another active or completed transaction for this order
        $transaction = new Transaction();
        $found = $transaction->find($this->request->params);
        if ($found && ($found->state == Transaction::STATE_CREATED || $found->state == Transaction::STATE_COMPLETED)) {
            $this->response->error(
                PaycomException::ERROR_COULD_NOT_PERFORM,
                'There is other active/completed transaction for this order.'
            );
        }

        // if control is here, then we pass all validations and checks
        // send response, that order is ready to be paid.
        $this->response->send(['allow' => true]);
    }

    private function CheckTransaction()
    {
        // todo: Find transaction by id
        $transaction = new Transaction();
        $found = $transaction->find($this->request->params);
        if (!$found) {
            $this->response->error(
                PaycomException::ERROR_TRANSACTION_NOT_FOUND,
                'Transaction not found.'
            );
        }

        // todo: Prepare and send found transaction
        $this->response->send([
            'create_time' => Format::datetime2timestamp($found->create_time),
            'perform_time' => Format::datetime2timestamp($found->perform_time),
            'cancel_time' => Format::datetime2timestamp($found->cancel_time),
            'transaction' => $found->id,
            'state' => $found->state,
            'reason' => isset($found->reason) ? $found->reason : null
        ]);
    }

    private function CreateTransaction()
    {
        $order = new Order($this->request->id, $this->request->params);

        // validate parameters
        $order->validate($this->request->params);

        // todo: Find transaction by id
        $transaction = new Transaction();
        $found = $transaction->find($this->request->params);

        if ($found) {
            if ($found->state != Transaction::STATE_CREATED) { // validate transaction state
                $this->response->error(
                    PaycomException::ERROR_COULD_NOT_PERFORM,
                    'Transaction found, but is not active.'
                );
            } elseif ($found->isExpired()) { // if transaction timed out, cancel it and send error
                $found->cancel(Transaction::REASON_CANCELLED_BY_TIMEOUT);
                $this->response->error(
                    PaycomException::ERROR_COULD_NOT_PERFORM,
                    'Transaction is expired.'
                );
            } else { // if transaction found and active, send it as response
                $this->response->send([
                    'create_time' => Format::datetime2timestamp($found->create_time),
                    'transaction' => $found->id,
                    'state' => $found->state,
                    'receivers' => $found->receivers
                ]);
            }
        } else { // transaction not found, create new one

            // validate new transaction time
            if (Format::timestamp2milliseconds(1 * $this->request->params['time']) - Format::timestamp(true) >= Transaction::TIMEOUT) {
                $this->response->error(
                    PaycomException::ERROR_INVALID_ACCOUNT,
                    PaycomException::message(
                        'С даты создания транзакции прошло ' . Transaction::TIMEOUT . 'мс',
                        'Tranzaksiya yaratilgan sanadan ' . Transaction::TIMEOUT . 'ms o`tgan',
                        'Since create time of the transaction passed ' . Transaction::TIMEOUT . 'ms'
                    ),
                    'time'
                );
            }

            // create new transaction
            // keep create_time as timestamp, it is necessary in response
            $create_time = Format::timestamp();
            $transaction->paycom_transaction_id = $this->request->params['id'];
            $transaction->paycom_time = $this->request->params['time'];
            $transaction->paycom_time_datetime = Format::timestamp2datetime($this->request->params['time']);
            $transaction->create_time = Format::timestamp2datetime($create_time);
            $transaction->state = Transaction::STATE_CREATED;
            $transaction->amount = $this->request->amount;
            $transaction->order_id = $this->request->account('order_id');
            $transaction->save(); // after save $transaction->id will be populated with the newly created transaction's id.

            // send response
            $this->response->send([
                'create_time' => $create_time,
                'transaction' => $transaction->id,
                'state' => $transaction->state,
                'receivers' => null
            ]);
        }
    }

    private function PerformTransaction()
    {
    }

    private function CancelTransaction()
    {
    }

    private function ChangePassword()
    {
    }

    private function GetStatement()
    {
    }
}