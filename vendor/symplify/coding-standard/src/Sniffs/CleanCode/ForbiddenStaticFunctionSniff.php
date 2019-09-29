<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\CleanCode;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class ForbiddenStaticFunctionSniff implements Sniff
{
    /**
     * @var string[]
     */
    private $allowedStaticFunctions = [
        'getSubscribedEvents', # Symfony of event subscriber
    ];

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_STATIC];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $functionTokenPosition = $file->findNext([T_FUNCTION], $position, $position + 3);
        if ($functionTokenPosition === false) {
            return;
        }

        $functionNameTokenPosition = $file->findNext([T_STRING], $functionTokenPosition, $functionTokenPosition + 3);
        $functionNameToken = $file->getTokens()[$functionNameTokenPosition];

        if ($functionNameToken === false) {
            return;
        }

        if (in_array($functionNameToken['content'], $this->allowedStaticFunctions, true)) {
            return;
        }

        $file->addError(
            sprintf('Use services and constructor injection over static method "%s"', $functionNameToken['content']),
            $position,
            self::class
        );
    }
}
