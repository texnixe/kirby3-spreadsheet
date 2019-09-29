<?php declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer;

final class BlockInfo
{
    /**
     * @var int
     */
    private $start;

    /**
     * @var int
     */
    private $end;

    public function __construct(int $start, int $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function getEnd(): int
    {
        return $this->end;
    }
}
