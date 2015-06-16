<?php namespace Znck\Livre\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Znck\Livre\Book;

/**
 * This file belongs to book-finder.
 *
 * Author: Rahul Kadyan, <hi@znck.me>
 * Find license in root directory of this project.
 */
class GoogleBooksProvider extends Provider
{
    /**
     *
     */
    const API = 'https://www.googleapis.com/books/v1/volumes?q=';
    /**
     * @type \GuzzleHttp\Client
     */
    protected $client;
    /**
     * @type \GuzzleHttp\Psr7\Response
     */
    protected $response;
    /**
     * @type string
     */
    protected $key;

    /**
     * Create new book provider.
     *
     * @param array $options Developer api key.
     */
    function __construct($options)
    {
        if (empty($options['key'])) {
            throw new \InvalidArgumentException;
        }

        $this->client = new Client();
        $this->key = $options['key'];
    }

    /**
     * Find book by ISBN identifier.
     *
     * @param string $isbn
     *
     * @return $this
     */
    public function find($isbn)
    {
        return $this->search(compact('isbn'));
    }

    /**
     * Search book by tags like title, author, publisher etc.
     *
     * @param array $query
     *
     * @return mixed
     */
    public function search($query)
    {
        $processed = $this->transformQueryParameters($query, $this->queryTransformationMap());

        $query = implode(' ', $processed[1]);

        foreach ($processed[0] as $key => $value) {
            $query .= ' ' . $key . $value;
        }

        $query = $this->prepareQuery($query);

        $this->response = $this->client->get($query);

        if ($this->response->getStatusCode() != 200) {
            $this->response = null;
        }

        return $this;
    }

    /**
     * Formatted results.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getResults()
    {
        $results = [];

        if (empty($this->response)) {
            return $results;
        }

        $data = json_decode($this->response->getBody()->getContents(), true);

        $items = array_get($data, 'items');
        foreach ($items as $item) {
            $attributes = $this->transformBookAttributes((array)$item, $this->bookTransformationMap());

            $attributes['isbn10'] = '';
            $attributes['isbn13'] = '';
            foreach ($attributes['identifiers'] as $v) {
                if ($v['type'] == 'ISBN_10') {
                    $attributes['isbn10'] = $v['identifier'];
                } else {
                    if ($v['type'] = 'ISBN_13') {
                        $attributes['isbn13'] = $v['identifier'];
                    }
                }
            }

            $results[] = new Book(array_except($attributes,
                ['kind', 'id', 'etag', 'selfLink', 'saleInfo', 'accessInfo', 'searchInfo', 'volumeInfo', 'identifiers']));
        }

        return new Collection($results);
    }


    /**
     * @param $query
     *
     * @return string
     */
    protected function prepareQuery($query)
    {
        $query = urlencode(trim($query));
        $query = str_replace('%3A', ':', $query);

        return static::API . $query . '&key=' . $this->key;
    }

    /**
     * Name of the provider
     *
     * @return string
     */
    public function getDefaultName()
    {
        return 'Google Books API';
    }

    /**
     * @return array
     */
    protected function queryTransformationMap()
    {
        return [
            'title'     => 'intitle:',
            'author'    => 'inauthor:',
            'publisher' => 'inpublisher:',
            'category'  => 'subject:',
            'isbn'      => 'isbn:',
            'lccn'      => 'lccn:',
            'oclc'      => 'oclc:',
        ];
    }

    /**
     * @return array
     */
    protected function bookTransformationMap()
    {
        return [
            'volumeInfo' => [
                'title'               => 'title',
                'subtitle'            => 'subTitle',
                'maturityRating'      => 'maturityRating',
                'authors'             => 'authors',
                'publisher'           => 'publisher',
                'publishedDate'       => 'publishedDate',
                'description'         => 'description',
                'industryIdentifiers' => 'identifiers',
                'pageCount'           => 'pageCount',
                'dimensions'          => 'dimensions',
                'printType'           => 'printType',
                'mainCategory'        => 'mainCategory',
                'categories'          => 'categories',
                'averageRating'       => 'rating',
                'contentVersion'      => 'contentVersion',
                'imageLinks'          => 'images',
                'language'            => 'language',
            ],
        ];
    }
}