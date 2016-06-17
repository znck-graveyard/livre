<?php namespace Znck\Livre;

/**
 * This file belongs to book-finder.
 *
 * Author: Rahul Kadyan, <hi@znck.me>
 * Find license in root directory of this project.
 *
 * @property-read string    $title
 * @property-read array     $authors
 * @property-read string    $publisher
 * @property-read string    $publishedDate
 * @property-read string    $description
 * @property-read string    $isbn10
 * @property-read string    $isbn13
 * @property-read int       $pageCount
 * @property-read array     $dimensions
 * @property-read string    $printType
 * @property-read string    $mainCategory
 * @property-read array     $categories
 * @property-read string    $rating
 * @property-read string    $contentVersion
 * @property-read array     $images
 * @property-read string    $language
 * @property-read array     $retailPrice
 */
class Book
{
    protected $attributes;

    function __construct($attributes)
    {
        $this->attributes = $attributes;
    }

    function __get($name)
    {
        if (! empty($this->attributes[$name])) {
            return $this->attributes[$name];
        }

        return null;
    }
}