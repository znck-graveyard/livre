<?php namespace Znck\Livre\Identifiers;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Znck\Livre\Contracts\Identifiers\Ean as EanInterface;

class Ean implements EanInterface
{
    protected $code;
    protected $valid;

    protected $parts = [];

    protected $format = 'EAN';

    protected static $prefixes = [
        '00-13'   => 'USA & Canada',
        '20-29'   => 'In-Store Functions',
        '30-37'   => 'France',
        '40-44'   => 'Germany',
        '45'      => 'Japan',
        '46'      => 'Russian Federation',
        '471'     => 'Taiwan',
        '474'     => 'Estonia',
        '475'     => 'Latvia',
        '477'     => 'Lithuania',
        '479'     => 'Sri Lanka',
        '480'     => 'Philippines',
        '482'     => 'Ukraine',
        '484'     => 'Moldova',
        '485'     => 'Armenia',
        '486'     => 'Georgia',
        '487'     => 'Kazakhstan',
        '489'     => 'Hong Kong',
        '49'      => 'Japan',
        '50'      => 'United Kingdom',
        '520'     => 'Greece',
        '528'     => 'Lebanon',
        '529'     => 'Cyprus',
        '531'     => 'Macedonia',
        '535'     => 'Malta',
        '539'     => 'Ireland',
        '54'      => 'Belgium & Luxembourg',
        '560'     => 'Portugal',
        '569'     => 'Iceland',
        '57'      => 'Denmark',
        '590'     => 'Poland',
        '594'     => 'Romania',
        '599'     => 'Hungary',
        '600'     => 'South Africa',
        '601'     => 'South Africa',
        '609'     => 'Mauritius',
        '611'     => 'Morocco',
        '613'     => 'Algeria',
        '619'     => 'Tunisia',
        '622'     => 'Egypt',
        '625'     => 'Jordan',
        '626'     => 'Iran',
        '64'      => 'Finland',
        '690-692' => 'China',
        '70'      => 'Norway',
        '729'     => 'Israel',
        '73'      => 'Sweden',
        '740'     => 'Guatemala',
        '741'     => 'El Salvador',
        '742'     => 'Honduras',
        '743'     => 'Nicaragua',
        '744'     => 'Costa Rica',
        '746'     => 'Dominican Republic',
        '750'     => 'Mexico',
        '759'     => 'Venezuela',
        '76'      => 'Switzerland',
        '770'     => 'Colombia',
        '773'     => 'Uruguay',
        '775'     => 'Peru',
        '777'     => 'Bolivia',
        '779'     => 'Argentina',
        '780'     => 'Chile',
        '784'     => 'Paraguay',
        '785'     => 'Peru',
        '786'     => 'Ecuador',
        '789'     => 'Brazil',
        '80-83'   => 'Italy',
        '84'      => 'Spain',
        '850'     => 'Cuba',
        '858'     => 'Slovakia',
        '859'     => 'Czech Republic',
        '860'     => 'Yugloslavia',
        '869'     => 'Turkey',
        '87'      => 'Netherlands',
        '880'     => 'South Korea',
        '885'     => 'Thailand',
        '888'     => 'Singapore',
        '890'     => 'India',
        '893'     => 'Vietnam',
        '899'     => 'Indonesia',
        '90-91'   => 'Austria',
        '93'      => 'Australia',
        '94'      => 'New Zealand',
        '955'     => 'Malaysia',
        '977'     => 'International Standard Serial Number for Periodicals (ISSN)',
        '978'     => 'International Standard Book Numbering (ISBN)',
        '979'     => 'International Standard Music Number (ISMN)',
        '980'     => 'Refund receipts',
        '981-982' => 'Common Currency Coupons',
        '99'      => 'Coupons',
    ];

    public function __construct(string $code = null)
    {
        if (is_string($code)) {
            $this->setCode($code);
        }
    }

    /**
     * @param string $code
     *
     * @return Ean
     */
    public function setCode(string $code)
    {
        $this->code = $this->clean($code);
        $this->valid = null;
        $this->parts = [];

        return $this;
    }

    protected function clean(string $isbn)
    {
        return str_replace(['-', '_', ' '], '', $isbn);
    }

    public function isValid()
    {
        if (! is_bool($this->valid)) {
            $this->valid = is_string($this->code)
            and is_numeric($this->code)
            and $this->verifyLength()
            and $this->verifyChecksum();
        }

        return $this->valid;
    }

    public function verifyChecksum()
    {
        return $this->computeChecksum() == $this->code[strlen($this->code) - 1];
    }

    public function computeChecksum()
    {
        $d = str_split($this->code);
        if (count($d) === 13) {
            // https://en.wikipedia.org/wiki/International_Article_Number_(EAN)
            $checksum = (
                10 - (
                    ($d[0] + $d[2] + $d[4] + $d[6] + $d[8] + $d[10]) +
                    ($d[1] + $d[3] + $d[5] + $d[7] + $d[9] + $d[11]) * 3
                ) % 10
            );

            return $checksum;
        } else {
            throw new InvalidArgumentException("$this->code should have 10 or 13 digits.");
        }
    }

    public function getPrefix()
    {
        if (isset($this->parts['prefix'])) {
            return $this->parts['prefix'];
        }

        $pr2 = Str::substr($this->code, 0, 2);
        $pr3 = Str::substr($this->code, 0, 3);

        $matches = array_filter(array_keys(static::$prefixes), function ($rule) use ($pr2, $pr3) {
            $match = $rule == $pr3 or $rule == $pr2;
            $parts = explode('-', $rule);
            if (count($parts) === 2) {
                list($min, $max) = $parts;

                return $match or ($min <= $pr2 and $pr2 <= $max) or ($min <= $pr3 and $pr3 <= $max);
            }
            return $match;
        });

        if (count($matches) == 1) {
            $l = Str::length($matches[0]);
            $this->parts['prefix'] = ($l == 2 or $l == 5) ? $pr2 : $pr3;
            $this->parts['prefix-name'] = static::$prefixes[$matches[0]];
        } else {
            throw new InvalidArgumentException("$pr2 or $pr3 is not a valid prefix.");
        }

        return $this->parts['prefix'];
    }

    public function getCompany()
    {
        $l = Str::length($this->getPrefix());
        return Str::substr($this->code, $l, 7 - $l);
    }

    public function getItem()
    {
        return Str::substr($this->code, 7, 5);
    }

    public function isIssn()
    {
        return $this->isValid() and (strlen($this->code) === 10 or preg_match('/^(977)/', $this->code));
    }

    /**
     * @param string $format
     *
     * @return self
     */
    public function setFormat(string $format)
    {
        if (method_exists($this, 'format'.Str::studly(Str::lower($format)))) {
            $this->format = $format;
        }

        return $this;
    }

    public function format(string $format = null)
    {
        if (is_string($format)) {
            $this->setFormat($format);
        }

        return call_user_func([$this, 'format'.Str::studly(Str::lower($format))]);
    }

    public function formatEan()
    {
        return $this->code;
    }

    public function __toString()
    {
        return (string)$this->format();
    }

    /**
     * @return bool
     */
    protected function verifyLength()
    {
        return Str::length($this->code) === 13;
    }

    public function getChecksum()
    {
        return $this->isValid() ? Str::substr($this->code, 12, 1) : $this->computeChecksum();
    }
}
