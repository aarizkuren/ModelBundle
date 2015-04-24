<?php

namespace AArizkuren\ModelBundle\Service;

/**
 * ModelService
 */
interface ModelService
{
    /**
     * Returns the number of results limit
     *
     * @return int
     */
    public function getLimit();

    /**
     * Returns the offset in the result array
     *
     * @return int
     */
    public function getOffset();

    /**
     * Set the number of results limit
     *
     * @param int $limit
     */
    public function setLimit($limit);

    /**
     * Set the offset in the result
     *
     * @param int $offset
     */
    public function setOffset($offset);
}