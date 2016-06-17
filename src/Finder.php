<?php namespace Znck\Livre;

use Exceptions\InvalidIsbnCodeException;
use Isbn\Isbn;
use Znck\Livre\Exceptions\ProviderNotFound;

/**
 * This file belongs to book-finder.
 *
 * Author: Rahul Kadyan, <hi@znck.me>
 * Find license in root directory of this project.
 */
class Finder
{
    /**
     * @var \Znck\Livre\Contracts\Provider[]
     */
    protected $providers;
    protected $isbn;

    /**
     * @param array      $providers
     * @param \Isbn\Isbn $isbn
     *
     * @throws \Znck\Livre\Exceptions\ProviderNotFound
     */
    public function __construct(array $providers, Isbn $isbn)
    {
        $this->providers = $this->sort($providers);
        $this->isbn = $isbn;
    }

    public function findByIsbn($isbn)
    {
        if (! $this->isbn->validation->isbn($isbn)) {
            throw new InvalidIsbnCodeException();
        }
        $isbn = $this->isbn->hyphens->fixHyphens($isbn);
        $books = $this->find(compact('isbn'));

        if (empty($books)) {
            return null;
        }

        return $books[0];
    }

    public function findByTitle($title)
    {
        return $this->find(compact('title'));
    }

    public function findByAuthor($author)
    {
        return $this->find(compact('author'));
    }

    public function findByPublisher($publisher)
    {
        return $this->find(compact('publisher'));
    }

    public function findByCategory($category)
    {
        return $this->find(compact('category'));
    }

    public function search($query, $title = null, $author = null, $category = null, $publisher = null)
    {
        return $this->find(compact('title', 'author', 'category', 'publisher', 'query'));
    }

    private function find($options)
    {
        foreach ($this->providers as $provider) {
            $results = $provider->search($options)->getResults();

            if (! empty($results)) {
                return $results;
            }
        }

        return [];
    }

    /**
     * Sort providers according to priorities.
     *
     * @param $providers
     *
     * @throws \Znck\Livre\Exceptions\ProviderNotFound
     *
     * @return \Znck\Livre\Contracts\Provider[]
     */
    private function sort($providers)
    {
        $callable = function ($a, $b) {
            if (! isset($a['priority']) && isset($b['priority'])) {
                return 1;
            }
            if (isset($a['priority']) && ! isset($b['priority'])) {
                return -1;
            }
            if (! isset($a['priority']) && ! isset($b['priority'])) {
                return 0;
            }
            if ($a['priority'] == $b['priority']) {
                return 0;
            }

            return ($a['priority'] < $b['priority']) ? -1 : 1;
        };

        usort($providers, $callable);

        $instances = [];
        foreach ($providers as $provider) {
            if (! empty($provider['provider'])) {
                $class = $provider['provider'];
                $instances[] = new $class($provider);
            } else {
                throw new ProviderNotFound(json_encode($provider));
            }
        }

        return $instances;
    }
}