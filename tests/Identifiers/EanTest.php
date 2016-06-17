<?php namespace Znck\Tests\Livre\Identifiers;

use GrahamCampbell\TestBench\AbstractTestCase;
use Znck\Livre\Identifiers\Ean;

class EanTest extends AbstractTestCase
{
    public function test_it_computes_correct_checksum()
    {
        $this->assertEquals(0, (new Ean('9971-5-0210-0'))->computeChecksum());
        $this->assertEquals(2, (new Ean('0-943396-04-2'))->computeChecksum());
        $this->assertEquals(3, (new Ean('1-84356-028-3'))->computeChecksum());
        $this->assertEquals(5, (new Ean('85-359-0277-5'))->computeChecksum());
        $this->assertEquals(6, (new Ean('80-902734-1-6'))->computeChecksum());
        $this->assertEquals(7, (new Ean('99921-58-10-7'))->computeChecksum());
        $this->assertEquals(9, (new Ean('0-85131-041-9'))->computeChecksum());
        $this->assertEquals('X', (new Ean('0-8044-2957-X'))->computeChecksum());

        $this->assertEquals(8, (new Ean('979-0-2600-0043-8'))->computeChecksum());
        $this->assertEquals(9, (new Ean('7501031311309'))->computeChecksum());
    }

    public function test_it_can_validate_checksum()
    {
        $this->assertTrue((new Ean('9971-5-0210-0'))->verifyChecksum());
        $this->assertTrue((new Ean('0-943396-04-2'))->verifyChecksum());
        $this->assertTrue((new Ean('1-84356-028-3'))->verifyChecksum());
        $this->assertTrue((new Ean('85-359-0277-5'))->verifyChecksum());
        $this->assertTrue((new Ean('80-902734-1-6'))->verifyChecksum());
        $this->assertTrue((new Ean('99921-58-10-7'))->verifyChecksum());
        $this->assertTrue((new Ean('0-85131-041-9'))->verifyChecksum());
        $this->assertTrue((new Ean('0-8044-2957-X'))->verifyChecksum());

        $this->assertTrue((new Ean('979-0-2600-0043-8'))->verifyChecksum());
        $this->assertTrue((new Ean('7501031311309'))->verifyChecksum());
    }
}
