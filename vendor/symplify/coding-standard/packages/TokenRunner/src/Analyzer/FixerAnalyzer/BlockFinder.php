<?php declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Exception\MissingImplementationException;
use Throwable;

final class BlockFinder
{
    /**
     * @var int[]
     */
    private $contentToBlockType = [
        '(' => Tokens::BLOCK_TYPE_PARENTHESIS_BRACE,
        ')' => Tokens::BLOCK_TYPE_PARENTHESIS_BRACE,
        '[' => Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE,
        ']' => Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE,
        '{' => Tokens::BLOCK_TYPE_CURLY_BRACE,
        '}' => Tokens::BLOCK_TYPE_CURLY_BRACE,
    ];

    /**
     * @var string[]
     */
    private $startEdges = ['(', '[', '{'];

    /**
     * Accepts position to both start and end token, e.g. (, ), [, ], {, }
     * also to: "array"(, "function" ...(, "use"(, "new" ...(
     */
    public function findInTokensByEdge(Tokens $tokens, int $position): ?BlockInfo
    {
        $token = $tokens[$position];

        // shift "array" to "(", event its position
        if ($token->isGivenKind(T_ARRAY)) {
            $position = $tokens->getNextMeaningfulToken($position);
            $token = $tokens[$position];
        }

        if ($token->isGivenKind([T_FUNCTION, CT::T_USE_LAMBDA, T_NEW])) {
            $position = $tokens->getNextTokenOfKind($position, ['(', ';']);
            $token = $tokens[$position];

            // end of line was sooner => has no block
            if ($token->equals(';')) {
                return null;
            }
        }

        // some invalid code
        if ($position === null) {
            return null;
        }

        $blockType = $this->getBlockTypeByToken($token);

        try {
            if (in_array($token->getContent(), $this->startEdges, true)) {
                $blockStart = $position;
                $blockEnd = $tokens->findBlockEnd($blockType, $blockStart);
            } else {
                $blockEnd = $position;
                $blockStart = $tokens->findBlockStart($blockType, $blockEnd);
            }
        } catch (Throwable $throwable) {
            // intentionally, no edge found
            return null;
        }

        return new BlockInfo($blockStart, $blockEnd);
    }

    public function findInTokensByPositionAndContent(Tokens $tokens, int $position, string $content): ?BlockInfo
    {
        $blockStart = $tokens->getNextTokenOfKind($position, [$content]);
        if ($blockStart === null) {
            return null;
        }

        $blockType = $this->getBlockTypeByContent($content);

        return new BlockInfo($blockStart, $tokens->findBlockEnd($blockType, $blockStart));
    }

    private function getBlockTypeByToken(Token $token): int
    {
        if ($token->isArray()) {
            if (in_array($token->getContent(), ['[', ']'], true)) {
                return Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE;
            }
            return Tokens::BLOCK_TYPE_ARRAY_INDEX_CURLY_BRACE;
        }

        return $this->getBlockTypeByContent($token->getContent());
    }

    private function getBlockTypeByContent(string $content): int
    {
        if (isset($this->contentToBlockType[$content])) {
            return $this->contentToBlockType[$content];
        }

        throw new MissingImplementationException(sprintf(
            'Implementation is missing for "%s" in "%s". Just add it to "%s" property with proper block type',
            $content,
            __METHOD__,
            '$contentToBlockType'
        ));
    }
}
