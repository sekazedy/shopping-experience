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

    private string $table;

    /**
     * Either sets pdo parameter to the passed connection OR creates a new one.
     *
     * @param PDO|null $pdo
     * @throws DatabaseConnectionException
     * @return void
     */
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

    protected function setTable(string $table): void
    {
        $this->table = $table;
    }

    /**
     * $conditions is an array of arrays, where each child array is a separate condition.
     * Each condition consists of the three indexes: 0 - operator, 1 - column name, 2 - column value.
     *
     * @param array $conditions
     * @param string $fieldsToSelect
     * @return array
     */
    protected function select(array $conditions = [], string $fieldsToSelect = "*"): array
    {
        $where = '';
        $values = [];
        if ($conditions) {
            $placeholders = [];

            foreach ($conditions as $condition) {
                $placeholders[] = "{$condition[1]} {$condition[0]} :{$condition[1]}";
                $values[$condition[1]] = $condition[2];
            }

            $where = 'WHERE ' . implode(' AND ', $placeholders);
        }

        $statement = $this->pdo->prepare("SELECT {$fieldsToSelect} FROM {$this->table} {$where}");

        foreach ($values as $placeholder => $value) {
            $statement->bindValue($placeholder, $value);
        }

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * $fields argument must be a key-value pairs array.
     * Example: [
     *     'field_1' => 'value_1',
     *     'field_2' => 'value_2',
     * ];
     *
     * @param array $fields
     * @return int
     */
    protected function insert(array $fields): int
    {
        $fieldNames = array_keys($fields);
        $fieldNamesCommaSeparated = implode(', ', $fieldNames);

        $colonPrependedFieldNames = array_map(function (string $fieldName) {
            return ':' . $fieldName;
        }, $fieldNames);
        $fieldBinders = implode(', ', $colonPrependedFieldNames);

        $statement = $this->pdo->prepare(
            "INSERT INTO {$this->table} ({$fieldNamesCommaSeparated}) VALUES({$fieldBinders})"
        );

        $statement->execute($fields);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * $fields argument must be a key-value pairs array.
     * Example: [
     *     'field_1' => 'value_1',
     *     'field_2' => 'value_2',
     * ];
     *
     * $conditions is an array of arrays, where each child array is a separate condition.
     * Each condition consists of the three indexes: 0 - operator, 1 - column name, 2 - column value.
     *
     * @param array $fields
     * @param array $conditions
     * @return int
     */
    protected function update(array $fields, array $conditions = []): int
    {
        $stringifiedFieldsAndValues = [];
        foreach ($fields as $name => $value) {
            $stringifiedFieldsAndValues[] = "{$name}={$value}";
        }

        $setFields = implode(', ', $stringifiedFieldsAndValues);

        $where = '';
        $values = [];
        if ($conditions) {
            $placeholders = [];

            foreach ($conditions as $condition) {
                $placeholders[] = "{$condition[1]} {$condition[0]} :{$condition[1]}";
                $values[$condition[1]] = $condition[2];
            }

            $where = 'WHERE ' . implode(' AND ', $placeholders);
        }

        $statement = $this->pdo->prepare("UPDATE {$this->table} SET {$setFields} {$where}");

        foreach ($values as $placeholder => $value) {
            $statement->bindValue($placeholder, $value);
        }

        $statement->execute();

        return $statement->rowCount();
    }

    /**
     * $conditions is an array of arrays, where each child array is a separate condition.
     * Each condition consists of the three indexes: 0 - operator, 1 - column name, 2 - column value.
     * For the delete method $conditions is not optional in order to prevent deleting all data with empty confitions by an accident.
     *
     * @param array $conditions
     * @return bool
     */
    protected function delete(array $conditions): bool
    {
        $where = '';
        $values = [];
        if ($conditions) {
            $placeholders = [];

            foreach ($conditions as $condition) {
                $placeholders[] = "{$condition[1]} {$condition[0]} :{$condition[1]}";
                $values[$condition[1]] = $condition[2];
            }

            $where = implode(' AND ', $placeholders);
        }

        $statement = $this->pdo->prepare("DELETE FROM {$this->table} WHERE {$where}");

        foreach ($values as $placeholder => $value) {
            $statement->bindValue($placeholder, $value);
        }

        return $statement->execute();
    }

    /**
     * $conditions is an array of arrays, where each child array is a separate condition.
     * Each condition consists of the three indexes: 0 - operator, 1 - column name, 2 - column value.
     *
     * @param array $conditions
     * @param string $fieldsToSelect
     * @return array
     */
    protected function getOne(array $conditions = [], string $fieldsToSelect = "*"): array
    {
        $where = '';
        $values = [];
        if ($conditions) {
            $placeholders = [];

            foreach ($conditions as $condition) {
                $placeholders[] = "{$condition[1]} {$condition[0]} :{$condition[1]}";
                $values[$condition[1]] = $condition[2];
            }

            $where = 'WHERE ' . implode(' AND ', $placeholders);
        }

        $statement = $this->pdo->prepare("SELECT {$fieldsToSelect} FROM {$this->table} {$where} LIMIT 1");

        foreach ($values as $placeholder => $value) {
            $statement->bindValue($placeholder, $value);
        }

        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC) ?: [];
    }
}