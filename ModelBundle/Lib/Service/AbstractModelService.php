<?php

namespace AArizkuren\ModelBundle\Service;

class AbstractModelService implements ModelService
{
    protected $limit = 1;
    protected $offset = -1;

    /**
     * Returns the number of results limit
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Returns the offset in the result array
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Set the number of results limit
     *
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * Set the offset in the result
     *
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }
}