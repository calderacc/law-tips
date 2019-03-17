<?php declare(strict_types=1);

namespace App\Vzkat\Parser;

interface ParserInterface
{
    public function setContent(string $content): ParserInterface;
    public function parse(): array;
}