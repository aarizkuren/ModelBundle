<?php
namespace AArizkuren\ModelBundle\Service\Doctrine;
use AArizkuren\ModelBundle\Entity\Entity;

/**
 * DoctrineModelService
 */
interface DoctrineModelService
{
    /**
     * Applies the $rawResult to $objectInstance
     *
     * @param Entity $objectInstance …
     * @param array &$rawResult     …
     */
    public function applyRawResultTo(&$objectInstance, array &$rawResult);

    /**
     * @param string $query             …
     * @param string $columnList        …
     * @param boolean $excludeColumnList …
     * @param boolean $addQueryExtension …
     *
     * @return string
     */
    public function buildInsertQuery($query, $columnList, $excludeColumnList, $addQueryExtension);

    /**
     * @param array &$rawResult
     *
     * @return mixed
     */
    public function buildResult(array &$rawResult);

    /**
     * @param string $query             …
     * @param string $columnList        …
     * @param boolean $excludeColumnList …
     * @param boolean $addQueryExtension …
     *
     * @return string
     */
    public function buildQuery($query, $columnList, $excludeColumnList, $addQueryExtension);

    /**
     * Calls a stored procedure on the server, returns multiple results (if any).
     *
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return array The results (if any).
     */
    public function call($query, array &$values, array &$types);

    /**
     * Calls a stored procedure on the server, returns a single result (if any).
     *
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return mixed The result (if any).
     */
    public function callIndividual($query, array &$values, array &$types);

    /**
     * Retrieves an object based on query
     *
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return mixed The resulting object, null when no results have been returned.
     */
    public function retrieveObject($query, array &$values, array &$types);

    /**
     * Retrieves an array of objects.
     *
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return array The resulting objects, an empty string when no results have been returned.
     */
    public function retrieveObjects($query, array &$values, array &$types);

    /**
     * Creates an object instance
     *
     * @return mixed
     */
    public function createObjectInstance();

    /**
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return int
     */
    public function deleteValues($query, array &$values, array &$types);

    /**
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return array
     */
    public function getCountResults($query, array &$values, array &$types);

    /**
     * @return int|string
     */
    public function getLastInsertId();

    /**
     * Return all results of &query as a large array
     *
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return array
     */
    public function getRawResults($query, array &$values, array &$types);

    /**
     * Retrieve first result from results
     *
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return mixed
     */
    public function getResult($query, array &$values, array &$types);

    /**
     * Returns all result of $query as objects
     *
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return array
     */
    public function getResults($query, array &$values, array &$types);

    /**
     * @param string $query       …
     * @param int $columnIndex …
     * @param array &$values     …
     * @param array &$types      …
     *
     * @return array
     */
    public function getResultsByColumnIndex($query, $columnIndex, array &$values, array &$types);

    /**
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return array
     */
    public function getResultsByFirstColumn($query, array &$values, array &$types);

    /**
     * Inserts values into database
     *
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return int Number of row changed
     */
    public function insertValues($query, array &$values, array &$types);

    /**
     * Executes insert/update/delete in database
     *
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return int
     */
    public function modifyValues($query, array &$values, array &$types);

    /**
     * Prepares the query for insert
     *
     * @param string $query
     *
     * @return string
     */
    public function prepareInsertQuery($query);

    /**
     * @param string $query             …
     * @param bool $excludeColumnList …
     * @param bool $addQueryExtension …
     * @param bool $limitsDisabled    …
     *
     * @return string
     */
    public function prepareQuery($query, $excludeColumnList = false, $addQueryExtension = false, $limitsDisabled = false);

    /**
     * @param string $query
     *
     * @return string
     */
    public function prepareUpdateQuery($query);

    /**
     * @param string $query   …
     * @param array &$values …
     * @param array &$types  …
     *
     * @return int
     */
    public function updateValues($query, array &$values, array &$types);

    /**
     * Switch to local connection
     */
    public function useLocalConnection();

    /**
     * Switch to shared connection
     */
    public function useSharedConnection();
}
