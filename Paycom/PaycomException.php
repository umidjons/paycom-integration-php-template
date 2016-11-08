<?php
namespace Paycom;

class PaycomException extends \Exception
{
    const ERROR_INTERNAL_SYSTEM = -32400;
    const ERROR_INSUFFICIENT_PRIVILEGE = -32504;
    const ERROR_INVALID_JSON_RPC_OBJECT = -32600;
    const ERROR_METHOD_NOT_FOUND = -32601;
    const ERROR_INVALID_AMOUNT = -31001;
    const ERROR_INVALID_ACCOUNT = -31050;
    const ERROR_COULD_NOT_PERFORM = -31008;

    public $request_id;
    public $error;
    public $data;

    /**
     * PaycomException constructor.
     * @param int $request_id id of the request.
     * @param string $message error message.
     * @param int $code error code.
     * @param string|null $data parameter name, that resulted to this error.
     */
    public function __construct($request_id, $message, $code, $data = null)
    {
        $this->request_id = $request_id;
        $this->message = $message;
        $this->code = $code;
        $this->data = $data;

        // prepare error data
        $this->error = ['code' => $this->code];

        if ($this->message) {
            $this->error['message'] = $this->message;
        }

        if ($this->data) {
            $this->error['data'] = $this->data;
        }
    }

    public function send()
    {
        header('Content-Type: application/json; charset=UTF-8');

        // create response
        $response['id'] = $this->request_id;
        $response['result'] = null;
        $response['error'] = $this->error;

        echo json_encode($response);
    }

    public static function message($ru, $uz = '', $en = '')
    {
        return ['ru' => $ru, 'uz' => $uz, 'en' => $en];
    }
}