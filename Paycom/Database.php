<?php

namespace Paycom;

class Database
{
    public $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function new_connection()
    {
        $db = null;

        // connect to the database
        $db_options = [\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC];

        $db = new \PDO(
            'mysql:dbname=' . $this->config['db']['database'] . ';host=localhost;charset=utf8',
            $this->config['db']['username'],
            $this->config['db']['password'],
            $db_options
        );

        return $db;
    }
}