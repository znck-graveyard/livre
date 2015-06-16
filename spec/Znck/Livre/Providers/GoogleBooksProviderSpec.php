<?php
/**
 * This file belongs to book-finder.
 *
 * Author: Rahul Kadyan, <hi@znck.me>
 * Find license in root directory of this project.
 */

namespace spec\Znck\Livre\Providers;

use Illuminate\Support\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Znck\Livre\Book;
use Znck\Livre\Providers\GoogleBooksProvider;

date_default_timezone_set('UTC');

class GoogleBooksProviderSpec extends ObjectBehavior
{
    public function let(GoogleBooksProvider $provider)
    {
        $this->beConstructedWith(['key' => getenv('GOOGLE_API_KEY')]);
    }

    public function it_should_be_instantiable()
    {
        $this->shouldHaveType(GoogleBooksProvider::class);
    }

    public function it_should_respond_to_find_and_return_self()
    {
        $this->find('9780440335702')->shouldHaveType(GoogleBooksProvider::class);
    }

    public function it_should_return_collection()
    {
        $this->find('9780440335702')->getResults()->shouldHaveType(Collection::class);
    }

    public function it_should_have_one_item()
    {
        $this->find('9780440335702')->getResults()->count()->shouldBe(1);
    }

    public function it_should_have_one_book()
    {
        $this->book()->shouldHaveType(Book::class);
    }

    public function it_should_have_title()
    {
        $this->book()->title->shouldBeString();
    }

    public function it_should_have_authors()
    {
        $this->book()->authors->shouldBeArray();
    }

    public function it_should_have_publisher()
    {
        $this->book()->publisher->shouldBeString();
    }

    public function it_should_have_published_date()
    {
        $this->book()->publishedDate->shouldBeString();
    }

    public function it_should_have_description()
    {
        $this->book()->description->shouldBeString();
    }

    public function it_should_have_isbn10()
    {
        $this->book()->isbn10->shouldBeString();
    }

    public function it_should_have_isbn13()
    {
        $this->book()->isbn13->shouldBeString();
    }

    public function it_should_have_page_count()
    {
        $this->book()->pageCount->shouldBeInteger();
    }

    public function it_should_have_categories()
    {
        $this->book()->categories->shouldBeArray();
    }

    public function it_should_have_images()
    {
        $this->book()->images->shouldBeArray();
    }

    public function it_should_have_language()
    {
        $this->book()->language->shouldBeString();
    }

    private function book()
    {
        return $this->find('9780440335702')->getResults()->first();
    }
}