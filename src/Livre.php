<?php namespace Znck\Livre;

use Illuminate\Support\Facades\Facade;

/**
 * Class Livre
 * @method BibItem lookup(string $isbnOrIssn)
 * @method BibItem[]|\Illuminate\Support\Collection title(string $title)
 * @method BibItem[]|\Illuminate\Support\Collection author(string $name)
 * @method BibItem[]|\Illuminate\Support\Collection issues(string $issn)
 */
class Livre extends Facade
{
    protected static function getFacadeAccessor() {
        return 'livre';
    }
}
