<?php
namespace App\Repositories;

use PDO;
use PDOException;

class Repository
{
    protected PDO $connection;

    function __construct()
    {
        $dbConfig = require __DIR__ . '/../../config/config.php';

        try {
            // Extract values from the config array
            $type = $dbConfig['db']['type'];
            $host = $dbConfig['db']['host'];
            $dbname = $dbConfig['db']['database'];
            $username = $dbConfig['db']['username'];
            $password = $dbConfig['db']['password'];

            // Create PDO connection
            $this->connection = new PDO(
                "$type:host=$host;dbname=$dbname",
                $username,
                $password
            );

            // Set PDO error mode
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
}

