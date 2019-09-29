<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Commenting;

use Nette\Utils\Strings;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;

/**
 * Inspired by https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/2.8/src/Fixer/Phpdoc/NoEmptyPhpdocFixer.php
 * With difference: it doesn't add extra spaces instead of docblock.
 */
final class RemoveEmptyDocBlockFixer extends AbstractSymplifyFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'There should not be empty PHPDoc blocks.',
            [new CodeSample('<?php 

/**  */
')]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    public function fix(SplFileInfo $splFileInfo, Tokens $tokens): void
    {
        for ($index = count($tokens); $index > 0; --$index) {
            if ($this->shouldSkip($tokens, $index)) {
                continue;
            }

            $tokens->clearTokenAndMergeSurroundingWhitespace($index);

            $previousToken = $tokens[$index - 1];
            if ($previousToken->isWhitespace()) {
                $previousWhitespaceContent = $previousToken->getContent();

                $lastLineBreak = strrpos($previousWhitespaceContent, PHP_EOL);
                // nothing found
                if (is_bool($lastLineBreak)) {
                    continue;
                }

                $newWhitespaceContent = Strings::substring($previousWhitespaceContent, 0, $lastLineBreak);
                if ($newWhitespaceContent) {
                    $tokens[$index - 1] = new Token([T_WHITESPACE, $newWhitespaceContent]);
                } else {
                    $tokens->clearAt($index - 1);
                }
            }
        }
    }

    private function shouldSkip(Tokens $tokens, int $index): bool
    {
        if (! isset($tokens[$index])) {
            return true;
        }

        $token = $tokens[$index];
        if (! $token->isGivenKind(T_DOC_COMMENT)) {
            return true;
        }

        return ! Strings::match($token->getContent(), '#^/\*\*[\s\*]*\*/$#');
    }
}
