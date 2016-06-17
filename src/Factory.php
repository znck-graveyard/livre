<?php namespace Znck\Livre;

use Exception;
use Illuminate\Support\Collection;

class Factory
{
    /**
     * @var \Znck\Livre\Contracts\BookSearchDriver[]
     */
    protected $providers;
    /**
     * @var array
     */
    protected $configs;

    public function __construct(array $providers, array $configs) {
        $this->configs = $configs;
        $this->providers = $this->load($providers);
    }

    public function journal(string $issn) {
        $issues = true;

        return $this->find(compact('issn', 'issues'));
    }

    public function lookup(string $query) {
        return $this->find($this->createLookupQuery($query))->first();
    }

    public function title(string $title) {
        return $this->find(compact('title'));
    }

    public function author(string $author) {
        return $this->find(compact('author'));
    }

    public function search(
        string $query,
        string $title = null,
        string $author = null,
        string $category = null,
        string $publisher = null
    ) {
        return $this->find(compact('title', 'author', 'category', 'publisher', 'query'));
    }

    /**
     * @param $options
     *
     * @return \Illuminate\Support\Collection
     */
    protected function find($options) {
        foreach ($this->providers as $provider) {
            $results = $provider->search($options)->getResults();

            if (!empty($results)) {
                return $results;
            }
        }

        return new Collection;
    }

    /**
     * Sort providers according to priorities.
     *
     * @param $providers
     *
     * @return \Znck\Livre\Contracts\BookSearchDriver[]
     * @throws \Exception
     */
    protected function load($providers) {
        $instances = [];
        foreach ($providers as $provider) {
            $instances[] = $this->makeProvider($provider);
        }

        return $instances;
    }

    protected function makeProvider(string $provider) {
        $config = $this->configs[$provider];

        $class = $config['driver'];

        return new $class($config);
    }

    protected function createLookupQuery(string $query) {
        $query = preg_replace('/[^0-9Xx]/', '', $query);

        $l = strlen($query);

        if ($l < 10 or ($l === 13 and preg_match('/^977/', $query))) {
            return ['issn' => $query];
        }

        return ['isbn' => $query];
    }
}
