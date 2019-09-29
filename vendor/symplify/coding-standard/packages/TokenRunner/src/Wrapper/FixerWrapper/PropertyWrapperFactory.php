<?php declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\DocBlock\DocBlockManipulator;
use Symplify\CodingStandard\TokenRunner\Guard\TokenTypeGuard;
use Symplify\CodingStandard\TokenRunner\Naming\Name\NameFactory;

final class PropertyWrapperFactory
{
    /**
     * @var NameFactory
     */
    private $nameFactory;

    /**
     * @var TokenTypeGuard
     */
    private $tokenTypeGuard;

    /**
     * @var DocBlockManipulator
     */
    private $docBlockManipulator;

    public function __construct(
        NameFactory $nameFactory,
        TokenTypeGuard $tokenTypeGuard,
        DocBlockManipulator $docBlockManipulator
    ) {
        $this->nameFactory = $nameFactory;
        $this->tokenTypeGuard = $tokenTypeGuard;
        $this->docBlockManipulator = $docBlockManipulator;
    }

    public function createFromTokensAndPosition(Tokens $tokens, int $position): PropertyWrapper
    {
        $this->tokenTypeGuard->ensureIsTokenType($tokens[$position], [T_VARIABLE], __METHOD__);

        return new PropertyWrapper($tokens, $position, $this->nameFactory, $this->docBlockManipulator);
    }
}
