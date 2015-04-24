<?php

namespace AArizkuren\ModelBundle\Service\Doctrine;

use Doctrine\DBAL\Connection;
use PDO;
use AArizkuren\ModelBundle\Entity\Entity;
use AArizkuren\ModelBundle\Service\AbstractModelService;

/**
 * AbstractDoctrineModelService
 */
abstract class AbstractDoctrineModelService extends AbstractModelService implements DoctrineModelService
{

    protected static $columnList = null;
    protected static $insertColumnList = null;

    protected $connection = null;
    protected $localConnection = null;
    protected $sharedConnection = null;
    protected $entityManager = null;
    protected $queryExtension = null;

    /**
     * @param Connection $localConnection  …
     * @param Connection $sharedConnection …
     */
    public function __construct(Connection $localConnection, Connection $sharedConnection = null, array $otherArguments = [])
    {
        $this->localConnection = $localConnection;
        $this->sharedConnection = $sharedConnection;

        $this->setOtherArguments($otherArguments);
    }

    /**
     * Applies the $rawResult to $objectInstance
     *
     * @param Entity $objectInstance …
     * @param array &$rawResult     …
     *
     * @return mixed
     */
    public abstract function applyRawResultTo(&$objectInstance, array &$rawResult);

    public function arrayOfStringsToList(&$values)
    {
        array_walk($values, function (&$val, $key) {
            $val = "'$val'";
        });

        return $this->prepareArrayToList($values);
    }

    public function arrayToList(array &$values, $glue = ',')
    {
        $returnString = '';
        foreach ($values as $value) {
            $returnString .= (is_array($value)) ? $this->arrayToList($value, $glue) : $glue . $value;
        }
        $returnString = substr($returnString, strlen($glue));
        return $returnString;
    }

    /**
     * @param string $query             …
     * @param string $columnList        …
     * @param boolean $excludeColumnList …
     * @param boolean $addQueryExtension …
     *
     * @return string
     */
    public function buildInsertQuery($query, $columnList, $excludeColumnList, $addQueryExtension)
    {
        return $this->buildQuery($query, $columnList, $excludeColumnList, $addQueryExtension);
    }

    /**
     * @param array &$rawResult
     *
     * @return mixed
     */
    public function buildResult(array &$rawResult)
    {
        $objectInstance = $this->createObjectInstance();

        return $this->applyRawResultTo($objectInstance, $rawResult);
    }

    /**
     * @param string $query             …
     * @param string $columnList        …
     * @param boolean $excludeColumnList …
     * @param boolean $addQueryExtension …
     *
     * @return string
     */
    public function buildQuery($query, $columnList, $excludeColumnList, $addQueryExtension)
    {
        if ($excludeColumnList === false) {
            if ($addQueryExtension == true) {
                $query = sprintf($query, $columnList, $this->queryExtension);
            } else {
                $query = sprintf($query, $columnList);
            }
        } else {
            if ($addQueryExtension == true) {
                $query = sprintf($query, $this->queryExtension);
            }
        }
        return $query;
    }

    /**
     * Calls a stored procedure on the server
     *
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return mixed The result (if any).
     */
    public function call($query, array &$values, array &$types)
    {
        $results = $this->getRawResults($query, $values, $types);

        return $results;
    }

    /**
     * Calls a stored procedure on the server, returns a single result (if any).
     *
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return mixed The result (if any).
     */
    public function callIndividual($query, array &$values, array &$types)
    {
        $result = $this->getRawResult($query, $values, $types);

        return $result;
    }

    /**
     * Retrieves an object based on query
     *
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return mixed The resulting object, null when no results have been returned.
     */
    public function retrieveObject($query, array &$values, array &$types)
    {
        $objectInstance = $this->getResult($query, $values, $types);

        return $objectInstance;
    }

    /**
     * Retrieves an array of objects.
     *
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return array The resulting objects, an empty string when no results have been returned.
     */
    public function retrieveObjects($query, array &$values, array &$types)
    {
        $objectInstances = $this->getResults($query, $values, $types);

        return $objectInstances;
    }

    /**
     * Creates an object instance
     *
     * @return mixed
     */
    public abstract function createObjectInstance();

    /**
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return int
     */
    public function deleteValues($query, array &$values, array &$types)
    {
        return $this->modifyValues($query, $values, $types);
    }

    /**
     * Returns Connection object
     *
     * @return \Doctrine\DBAL\Connection
     */
    protected function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return array
     */
    public function getCountResults($query, array &$values, array &$types)
    {
        $connection = $this->getConnection();

        $statement = $connection->executeQuery($query, $values, $types);

        $result = $statement->fetch(PDO::FETCH_COLUMN, 0);

        return $result;
    }

    /**
     * @return int|string
     */
    public function getLastInsertId()
    {
        $connection = $this->getConnection();

        return $connection->lastInsertId();
    }

    /**
     * Return one result of &4query as a large array
     *
     * @param $query
     * @param array $values
     * @param array $types
     * @return mixed|null
     */
    public function getRawResult($query, array &$values, array &$types)
    {
        $results = $this->getRawResults($query, $values, $types);

        $objectInstance = null;
        if (count($results) > 0) {
            $result = $results[0];
            $objectInstance = $this->buildResult($result);
        }

        return $objectInstance;
    }

    /**
     * Return all results of &query as a large array
     *
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return array
     */
    public function getRawResults($query, array &$values, array &$types)
    {
        $connection = $this->getConnection();

        $statement = $connection->executeQuery($query, $values, $types);

        $rawResults = $statement->fetchAll(PDO::FETCH_NUM);

        return $rawResults;
    }

    /**
     * Retrieve first result from results
     *
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return mixed
     */
    public function getResult($query, array &$values, array &$types)
    {
        $rawResults = $this->getRawResults($query, $values, $types);
        $objectInstance = null;

        if (count($rawResults) > 0) {
            $rawResult = $rawResults[0];

            $objectInstance = $this->buildResult($rawResult);
        }

        return $objectInstance;
    }

    /**
     * Returns all result of $query as objects
     *
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return array
     */
    public function getResults($query, array &$values, array &$types)
    {
        $connection = $this->getConnection();
        $statement = $connection->executeQuery($query, $values, $types);

        $results = [];

        while (($rawResult = $statement->fetch(PDO::FETCH_NUM)) != null) {
            $objectInstance = $this->buildResult($rawResult);

            $results [] = $objectInstance;
        }

        return $results;
    }

    /**
     * @param string $query       …
     * @param int $columnIndex …
     * @param array &$values     …
     * @param array &$types      …
     *
     * @return array
     */
    public function getResultsByColumnIndex($query, $columnIndex, array &$values, array &$types)
    {
        $connection = $this->getConnection();

        $statement = $connection->executeQuery($query, $values, $types);

        $results = $statement->fetchAll(PDO::FETCH_COLUMN, $columnIndex);

        return $results;
    }

    /**
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return array
     */
    public function getResultsByFirstColumn($query, array &$values, array &$types)
    {
        return $this->getResultsByColumnIndex($query, 0, $values, $types);
    }

    /**
     * Inserts values into database
     *
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return int Number of rows changed
     */
    public function insertValues($query, array &$values, array &$types)
    {
        return $this->modifyValues($query, $values, $types);
    }

    /**
     * Executes insert/update/delete in database
     *
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return int
     */
    public function modifyValues($query, array &$values, array &$types)
    {
        $connection = $this->getConnection();

        $affectedRows = $connection->executeUpdate($query, $values, $types);

        return $affectedRows;
    }

    /**
     * Prepares the query for insert
     *
     * @param string $query
     *
     * @return string
     */
    public function prepareInsertQuery($query)
    {
        $query = $this->buildInsertQuery($query, self::$insertColumnList, false, false);

        return $query;
    }

    /**
     * @param string $query             …
     * @param bool $excludeColumnList …
     * @param bool $addQueryExtension …
     * @param bool $limitsDisabled    …
     *
     * @return string
     */
    public function prepareQuery($query, $excludeColumnList = false, $addQueryExtension = false, $limitsDisabled = false)
    {
        $limit = $this->getLimit();
        $query = $this->buildQuery($query, static::$columnList, $excludeColumnList, $addQueryExtension);

        if ($limitsDisabled === false && $limit > 0) {
            $offset = $this->getOffset();

            if ($offset > 0) {
                $extra = "$offset, $limit";
            } else {
                $extra = $limit;
            }

            $query .= " LIMIT $extra";
        }

        return $query;
    }

    /**
     * @param string $query
     *
     * @return string
     */
    public function prepareUpdateQuery($query)
    {
        $query = $this->buildInsertQuery($query, '', true, false);

        return $query;
    }

    /**
     * Sets the other arguments, normally ignored.
     *
     * @param array $otherArguments
     */
    protected function setOtherArguments(array &$otherArguments)
    {

    }

    /**
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return int
     */
    public function updateValues($query, array &$values, array &$types)
    {
        return $this->modifyValues($query, $values, $types);
    }

    /**
     * Switch to local connection
     */
    public function useLocalConnection()
    {
        $this->connection = $this->localConnection;
    }

    /**
     * Switch to shared connection
     */
    public function useSharedConnection()
    {
        $this->connection = $this->localConnection;
    }

    /**
     * @param array $initialValues
     *
     * @return array
     */
    protected function cleanArray(array &$initialValues) {
        $cleanedArray = [];

        foreach($initialValues as $initialValue) {
            if (!is_null($initialValue) && $initialValue != 0) {
                $cleanedArray[] = $initialValue;
            }
        }

        return $cleanedArray;
    }
}
