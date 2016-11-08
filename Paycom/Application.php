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
        if ($found && ($found['state'] == Transaction::STATE_CREATED || $found['state'] == Transaction::STATE_COMPLETED)) {
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

    }

    private function CreateTransaction()
    {
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