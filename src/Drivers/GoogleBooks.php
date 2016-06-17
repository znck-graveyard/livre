<?php namespace Znck\Livre\Drivers;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Znck\Livre\BibItem;
use Znck\Livre\Drivers\AbstractDriver;

class GoogleBooks extends AbstractDriver
{
    static protected $bookTransformationMap = [
        'volumeInfo' => [
            'title' => 'title',
            'subtitle' => 'subTitle',
            'maturityRating' => 'maturityRating',
            'authors' => 'authors',
            'publisher' => 'publisher',
            'publishedDate' => 'publishedDate',
            'description' => 'description',
            'industryIdentifiers' => 'identifiers',
            'pageCount' => 'pageCount',
            'dimensions' => 'dimensions',
            'printType' => 'printType',
            'mainCategory' => 'mainCategory',
            'categories' => 'categories',
            'averageRating' => 'rating',
            'contentVersion' => 'contentVersion',
            'imageLinks' => 'images',
            'language' => 'language',
        ],
    ];
    static protected $queryTransformationMap = [
        'title' => 'intitle:',
        'author' => 'inauthor:',
        'publisher' => 'inpublisher:',
        'category' => 'subject:',
        'isbn' => 'isbn:',
    ];
    /**
     *
     */
    const API = 'https://www.googleapis.com/books/v1/volumes?q=';
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;
    /**
     * @var \GuzzleHttp\Psr7\Response
     */
    protected $response;
    /**
     * @var string
     */
    protected $key;

    /**
     * Create new book provider.
     *
     * @param array $options Developer api key.
     */
    function __construct($options) {
        if (!isset($options['key'])) {
            throw new \InvalidArgumentException();
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
    public function find(string $isbn) {
        return $this->search(compact('isbn'));
    }

    /**
     * Search book by tags like title, author, publisher etc.
     *
     * @param array $query
     *
     * @return mixed
     */
    public function search(array $query) {
        $processed = $this->transformQueryParameters($query, $this->queryTransformationMap());

        $query = implode(' ', $processed[1]);

        foreach ($processed[0] as $key => $value) {
            $query .= ' '.$key.$value;
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
    public function getResults() {
        $results = [];

        if (empty($this->response)) {
            return $results;
        }

        $data = json_decode($this->response->getBody()->getContents(), true);

        $items = array_get($data, 'items', []);
        foreach ($items as $item) {
            $attributes = $this->transformBookAttributes((array)$item, $this->bookTransformationMap());

            $attributes['isbn10'] = '';
            $attributes['isbn13'] = '';
            foreach ($attributes['identifiers'] as $v) {
                if ($v['type'] == 'ISBN_10') {
                    $attributes['isbn10'] = $v['identifier'];
                } elseif ($v['type'] = 'ISBN_13') {
                    $attributes['isbn13'] = $v['identifier'];
                }
            }

            $results[] = new BibItem(
                array_except(
                    $attributes,
                    [
                        'kind',
                        'id',
                        'etag',
                        'selfLink',
                        'saleInfo',
                        'accessInfo',
                        'searchInfo',
                        'volumeInfo',
                        'identifiers',
                    ]
                )
            );
        }

        return new Collection($results);
    }


    public function title(string $title) {
        return $this->search(compact('title'));
    }

    /**
     * @param $query
     *
     * @return string
     */
    protected function prepareQuery($query) {
        $query = urlencode(trim($query));
        $query = str_replace('%3A', ':', $query);

        if (empty($this->key)) {
            return static::API.$query;
        }

        return static::API.$query.'&key='.$this->key;
    }
}