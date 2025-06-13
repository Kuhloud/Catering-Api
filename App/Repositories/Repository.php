<?php
namespace App\Repositories;

use PDO;
use PDOException;
use RuntimeException;

abstract class Repository
{
    /**
     * @var PDO The database connection instance
     */
    protected PDO $connection;

    /**
     * Repository constructor
     *
     * Initializes the database connection using configuration parameters from the config file.
     * The constructor:
     * - Loads database configuration
     * - Establishes a PDO connection
     * - Sets appropriate error handling modes
     *
     * @throws PDOException When database connection fails (caught and handled internally)
     *
     * Configuration file should be located at:
     * __DIR__ . '/../../config/config.php'
     * and should return an array with database configuration under the 'db' key:
     * [
     *     'db' => [
     *         'type'     => 'mysql',       // Database type
     *         'host'     => 'localhost',   // Database host
     *         'database' => 'db_name',     // Database name
     *         'username' => 'db_user',     // Database username
     *         'password' => 'db_password'  // Database password
     *     ]
     * ]
     */
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
            error_log("Database connection failed: " . $e->getMessage());
            throw new RuntimeException('Database connection failed', 0, $e);
        }
    }
}

