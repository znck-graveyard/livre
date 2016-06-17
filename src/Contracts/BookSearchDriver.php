<?php namespace Znck\Livre\Contracts;

interface BookSearchDriver
{
    /**
     * Find book by ISBN/ISSN identifier.
     *
     * @param string $isbn
     *
     * @return $this
     */
    public function find(string $isbn);

    /**
     * Search book by tags like title, author, publisher etc.
     *
     * @param array $query
     *
     * @return $this
     */
    public function search(array $query);


    public function title(string $title);

    /**
     * Formatted results.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getResults();
}