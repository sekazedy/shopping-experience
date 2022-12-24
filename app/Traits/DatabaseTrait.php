<?php

declare(strict_types=1);

namespace App\Traits;

use App\Exceptions\DatabaseConnectionException;
use App\Models\DatabaseConnection;
use PDO;
use PDOException;

trait DatabaseTrait
{
    protected bool $isConnected = false;
    protected PDO $pdo;

    protected function connect(?PDO $pdo = null): void
    {
        if ($pdo) {
            $this->isConnected = true;
            $this->pdo = $pdo;
        }

        if (!$this->isConnected) {
            try {
                $this->pdo = (new DatabaseConnection())->getPDO();

                $this->isConnected = true;
            } catch (PDOException $e) {
                throw new DatabaseConnectionException('Unable to establish database connection');
            }
        }
    }

    protected function select($table, $where = [])
    {
        // Build the SELECT statement using $this->pdo and execute it
    }

    /**
     * $fields argument must be a key-value pairs array
     * Example: [
     *     'field_1' => 'value_1',
     *     'field_2' => 'value_2',
     * ];
     *
     * @param string $table
     * @param array $fields
     * @return bool
     */
    protected function insert(string $table, array $fields): bool
    {
        $fieldNames = array_keys($fields);
        $fieldNamesCommaSeparated = implode(', ', $fieldNames);

        $colonPrependedFieldNames = array_map(function (string $fieldName) {
            return ':' . $fieldName;
        }, $fieldNames);
        $fieldBinders = implode(', ', $colonPrependedFieldNames);

        $statement = $this->pdo->prepare(
            "INSERT INTO {$table} ({$fieldNamesCommaSeparated}) VALUES({$fieldBinders})"
        );

        return $statement->execute($fields);
    }

    // Other CRUD methods here
}