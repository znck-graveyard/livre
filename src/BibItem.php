<?php namespace Znck\Livre;

/**
 * @property-read string $title
 * @property-read array $authors
 * @property-read string $publisher
 * @property-read string $publishedDate
 * @property-read string $description
 * @property-read string $isbn10
 * @property-read string $isbn13
 * @property-read int $pageCount
 * @property-read array $dimensions
 * @property-read string $printType
 * @property-read string $mainCategory
 * @property-read array $categories
 * @property-read string $rating
 * @property-read string $contentVersion
 * @property-read array $images
 * @property-read string $language
 * @property-read array $retailPrice
 */
class BibItem
{
    protected $attributes;

    public function __construct($attributes) {
        $this->attributes = $attributes;
    }

    public function __get($name) {
        return $this->get($name);
    }

    public function all() {
        return $this->attributes;
    }

    public function get(string $name) {
        if (!empty($this->attributes[$name])) {
            return $this->attributes[$name];
        }

        return null;
    }
}