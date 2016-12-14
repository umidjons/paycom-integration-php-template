<?php
namespace Paycom;

class Request
{
    /** @var array decoded request payload */
    public $payload;

    /** @var int id of the request */
    public $id;

    /** @var string method name, such as <em>CreateTransaction</em> */
    public $method;

    /** @var array request parameters, such as <em>amount</em>, <em>account</em> */
    public $params;

    /** @var int amount value in coins */
    public $amount;

    /**
     * Request constructor.
     * Parses request payload and populates properties with values.
     */
    public function __construct()
    {
        $request_body = file_get_contents('php://input');
        $this->payload = json_decode($request_body, true);

        if (!$this->payload) {
            throw new PaycomException(
                null,
                'Invalid JSON-RPC object.',
                PaycomException::ERROR_INVALID_JSON_RPC_OBJECT
            );
        }

        // populate request object with data
        $this->id = isset($this->payload['id']) ? 1 * $this->payload['id'] : null;
        $this->method = isset($this->payload['method']) ? trim($this->payload['method']) : null;
        $this->params = isset($this->payload['params']) ? $this->payload['params'] : [];
        $this->amount = isset($this->payload['params']['amount']) ? 1 * $this->payload['params']['amount'] : null;
    }

    /**
     * Gets account parameter if such exists, otherwise returns null.
     * @param string $param name of the parameter.
     * @return mixed|null account parameter value or null if such parameter doesn't exists.
     */
    public function account($param)
    {
        return isset($this->params['account'], $this->params['account'][$param]) ? $this->params['account'][$param] : null;
    }
}