<?php namespace Znck\Livre;

use ArrayAccess;

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
class BibItem implements ArrayAccess
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

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset) {
        return isset($this->attributes[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     *
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset) {
        return $this->attributes[$offset];
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value) {
        $this->attributes[$offset] = $value;
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset) {
        unset($this->attributes[$offset]);
    }

    public function __toString() {
        return json_encode($this->attributes, JSON_PRETTY_PRINT);
    }
}