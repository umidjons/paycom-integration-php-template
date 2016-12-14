<?php
namespace Paycom;

class Merchant
{
    public $config;

    public function __construct($config)
    {
        $this->config = $config;

        // read key from key file
        if ($this->config['keyFile']) {
            $this->config['key'] = file_get_contents($this->config['keyFile']);
        }
    }

    public function Authorize($request_id)
    {
        $headers = getallheaders();

        if (!$headers || !isset($headers['Authorization']) ||
            !preg_match('/^\s*Basic\s+(\S+)\s*$/i', $headers['Authorization'], $matches) ||
            base64_decode($matches[1]) != $this->config['login'] . ":" . $this->config['key']
        ) {
            throw new PaycomException(
                $request_id,
                'Insufficient privilege to perform this method.',
                PaycomException::ERROR_INSUFFICIENT_PRIVILEGE
            );
        }

        return true;
    }
}