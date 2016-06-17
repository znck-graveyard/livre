<?php namespace Znck\Livre\Contracts;

/**
 * Interface Provider
 */
interface Provider
{
    /**
     * Find book by ISBN identifier.
     *
     * @param string $isbn
     *
     * @return $this
     */
    public function find($isbn);

    /**
     * Search book by tags like title, author, publisher etc.
     *
     * @param array $query
     *
     * @return $this
     */
    public function search($query);

    /**
     * Formatted results.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getResults();

    /**
     * Name of the provider
     *
     * @return string
     */
    public function getDefaultName();
}