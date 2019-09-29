<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\CleanCode;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class ForbiddenReferenceSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_VARIABLE];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $tokens = $file->getTokens();
        if (! isset($tokens[$position - 1]) || ! isset($tokens[$position - 2])) {
            return;
        }

        $previousToken = $tokens[$position - 1];
        $previousPreviousToken = $tokens[$position - 2];

        // check for "&$var" and "& $var"
        if ($previousToken['code'] === T_BITWISE_AND || ($previousPreviousToken['code'] === T_BITWISE_AND && $previousToken['code'] === T_WHITESPACE)) {
            $file->addError(
                sprintf(
                    'Use explicit return values over magic "&%s" reference',
                    $file->getTokens()[$position]['content']
                ),
                $position,
                self::class
            );
        }
    }
}
