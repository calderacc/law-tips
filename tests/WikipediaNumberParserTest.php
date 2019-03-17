<?php declare(strict_types=1);

namespace App\Tests;

use App\Vzkat\Parser\WikipediaNumberParser;
use PHPUnit\Framework\TestCase;

class WikipediaNumberParserTest extends TestCase
{
    public function testParser1(): void
    {
        $this->assertEquals('101', WikipediaNumberParser::parse('Zeichen 101'));
    }

    public function testParser2(): void
    {
        $this->assertEquals('103-10', WikipediaNumberParser::parse('Zeichen 103-10'));
    }

    public function testParser3(): void
    {
        $this->assertEquals('223.1-50', WikipediaNumberParser::parse('Zeichen 223.1-50'));
    }

    public function testParser4(): void
    {
        $this->assertEquals('262-5,5', WikipediaNumberParser::parse('Zeichen 262-5,5'));
    }

    public function testParser5(): void
    {
        $this->assertEquals('270.1', WikipediaNumberParser::parse('Zeichen 270.1'));
    }

    public function testParser6(): void
    {
        $this->assertEquals('1000-10', WikipediaNumberParser::parse('Zusatzzeichen 1000-10'));
    }

    public function testParser7(): void
    {
        $this->assertEquals('2531', WikipediaNumberParser::parse('Zusatzzeichen 2531'));
    }
}
