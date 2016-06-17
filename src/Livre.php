<?php namespace Znck\Livre;

use Illuminate\Support\Facades\Facade;

/**
 * Class Livre
 * @method BibItem findByTitle(string $title)
 * @method BibItem findBy(string $title)
 */
class Livre extends Facade
{
    protected static function getFacadeAccessor() {
        return 'livre';
    }
}
