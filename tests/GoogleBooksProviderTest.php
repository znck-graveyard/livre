<?php
use Znck\Livre\Providers\GoogleBooksProvider;

/**
 * This file belongs to book-finder.
 *
 * Author: Rahul Kadyan, <hi@znck.me>
 * Find license in root directory of this project.
 */
class GoogleBooksProviderTest extends PHPUnit_Framework_TestCase
{
    function test_it_should_throw_error()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        new GoogleBooksProvider([]);
    }

    function test_get_book_by_isbn()
    {
        $provider = new GoogleBooksProvider(['key' => getenv('GOOGLE_API_KEY')]);
        $books = $provider->find('9780440335702')->getResults();
        /** @type \Znck\Livre\Book $book */
        $book = $books->first();
        $this->assertEquals('The Google Story', $book->title);
        $this->assertEquals([
            "David A. Vise",
            "Mark Malseed",
        ], $book->authors);
        $this->assertEquals('Delacorte Press', $book->publisher);
        $this->assertEquals('2005-11-15', $book->publishedDate);
        $this->assertNotEmpty($book->description);
        $this->assertNotEmpty($book->isbn10);
        $this->assertNotEmpty($book->isbn13);
        $this->assertNotEmpty($book->pageCount);
        $this->assertNotEmpty($book->categories);
        $this->assertNotEmpty($book->rating);
        $this->assertNotEmpty($book->images);
    }
}