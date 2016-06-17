<?php namespace Znck\Livre\Identifiers;

use Illuminate\Support\Str;

class Isbn extends Ean
{
    static protected $groups;
    static protected $isbnPrefixes;
    protected $format = 'ISBN-13';

    public function __construct($code)
    {
        parent::__construct(Str::length($code) === 10 ? '978'.$code : $code);
        if (Str::length($code) === 10) {
            // TODO: Choose 978 or 979 as per $isbnPrefixes rules.
            $this->setCode('978'.Str::substr($code, 0, 9).$this->computeChecksum());
            $this->format = 'ISBN-10';
        }
    }

    public function computeChecksum()
    {
        $d = str_split($this->code);
        if (count($d) === 10) {
            // https://en.wikipedia.org/wiki/International_Standard_Book_Number
            $checksum = (
                    11 -
                    (
                        10 * $d[0] + 9 * $d[1] + 8 * $d[2] + 7 * $d[3] + 6 * $d[4] +
                        5 * $d[5] + 4 * $d[6] + 3 * $d[7] + 2 * $d[8]
                    ) % 11
                ) % 11;

            return $checksum === 10 ? 'X' : $checksum;
        }

        return parent::computeChecksum();
    }

    protected function verifyLength()
    {
        return Str::length($this->code) === 10 or parent::verifyLength();
    }

    public function isIsbn()
    {
        return $this->isValid() and (strlen($this->code) === 10 or preg_match('/^(978|979)/', $this->code));
    }

    public function formatIsbn13()
    {
        $pre = $this->getPrefix();
        $grp = $this->getGroupCode();
        $pub = $this->getPublisherCode();
        $tit = $this->getTitleCode();
        $sum = $this->getChecksum();

        return "${pre}-${grp}-${pub}-${tit}-${sum}";
    }

    public function getGroupCode()
    {
        return Str::substr($this->code, 3, 2);
    }

    public function getGroupName() {
        // TODO: Should I add ISBN look-ups here?
    }

    public function getPublisherCode()
    {
        return Str::substr($this->code, 5, 4);
    }

    public function getTitleCode()
    {
        return Str::substr($this->code, 9, 3);
    }

    public function formatIsbn10()
    {
        $grp = $this->getGroupCode();
        $pub = $this->getPublisherCode();
        $tit = $this->getTitleCode();
        $sum = $this->getChecksum();

        return "${grp}-${pub}-${tit}-${sum}";
    }

    static protected function loadGroupsAndPrefixes()
    {
        $items = require __DIR__.'/../../resources/isbn.php';
        static::$groups = array_map(
            function (array $item) {
                return [
                    'prefix' => $item['Prefix'],
                    'name'   => $item['Agency'],
                    'rules'  => static::transformRules(array_get($item, 'Rules.Rule', [])),
                ];
            },
            array_get($items, 'RegistrationGroups.Group')
        );
        static::$isbnPrefixes = array_map(
            function (array $item) {
                return [
                    'prefix' => $item['Prefix'],
                    'name'   => $item['Agency'],
                    'rules'  => static::transformRules(array_get($item, 'Rules.Rule', [])),
                ];
            },
            $items['EAN.UCCPrefixes']['EAN.UCC']
        );
    }

    static protected function transformRules(array $rules)
    {
        return array_map(
            function ($item) {
                list($min, $max) = explode('-', $item['Range']);

                return [
                    'min'    => (int)$min,
                    'max'    => (int)$max,
                    'length' => (int)$item['Length'],
                ];
            },
            $rules
        );
    }
}
