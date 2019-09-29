<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Naming;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder;

final class CatchExceptionNameMatchingTypeFixer extends AbstractSymplifyFixer
{
    /**
     * @var BlockFinder
     */
    private $blockFinder;

    public function __construct(BlockFinder $blockFinder)
    {
        $this->blockFinder = $blockFinder;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Type and name of catch exception should match', [
            new CodeSample(
                '<?php
try {
    // ...
} catch (SomeException $typoException) {
    $typoException->getMessage();
}'
            ),
        ]);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_STRING, T_VARIABLE, T_CATCH]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($this->reverseTokens($tokens) as $position => $token) {
            if (! $token->isGivenKind(T_CATCH)) {
                continue;
            }

            $exceptionTypePosition = $tokens->getNextMeaningfulToken($position + 2);
            $variableNamePosition = $tokens->getNextMeaningfulToken($exceptionTypePosition);

            // probably multiple types, unable to resolve right
            $variableToken = $tokens[$variableNamePosition];
            if (! $variableToken->isGivenKind(T_VARIABLE)) {
                continue;
            }

            $exceptionTypeToken = $tokens[$exceptionTypePosition];
            if ($this->isVariableNameMatchingType($variableToken, $exceptionTypeToken)) {
                continue;
            }

            $newVariableName = '$' . lcfirst($exceptionTypeToken->getContent());
            $oldVariableName = $variableToken->getContent();

            $tokens[$variableNamePosition] = new Token([T_VARIABLE, $newVariableName]);

            $this->updateCatchBodyVariableName($tokens, $variableNamePosition, $newVariableName, $oldVariableName);
        }
    }

    private function isVariableNameMatchingType(Token $variableToken, Token $exceptionTypeToken): bool
    {
        $variableName = ltrim($variableToken->getContent(), '$');
        if ($variableName === lcfirst($exceptionTypeToken->getContent())) {
            return true;
        }

        return false;
    }

    private function updateCatchBodyVariableName(
        Tokens $tokens,
        int $variableNamePosition,
        string $newVariableName,
        string $oldVariableName
    ): void {
        // fix also following occurrences
        $openingCatchBodyPosition = $tokens->getNextTokenOfKind($variableNamePosition, ['{']);
        $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $openingCatchBodyPosition);

        // no block to find
        if ($blockInfo === null) {
            return;
        }

        for ($i = $blockInfo->getStart(); $i < $blockInfo->getEnd(); $i++) {
            $currentToken = $tokens[$i];
            if (! $currentToken->isGivenKind(T_VARIABLE)) {
                continue;
            }

            if ($currentToken->getContent() !== $oldVariableName) {
                continue;
            }

            $tokens[$i] = new Token([T_VARIABLE, $newVariableName]);
        }
    }
}
