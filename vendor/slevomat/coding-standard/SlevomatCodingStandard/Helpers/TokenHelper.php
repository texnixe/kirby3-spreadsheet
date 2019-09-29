<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use function count;
use const T_ARRAY_HINT;
use const T_BREAK;
use const T_CALLABLE;
use const T_CLASS;
use const T_CLOSURE;
use const T_COMMENT;
use const T_CONTINUE;
use const T_DOC_COMMENT;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_DOC_COMMENT_STAR;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_TAG;
use const T_DOC_COMMENT_WHITESPACE;
use const T_EXIT;
use const T_FUNCTION;
use const T_INTERFACE;
use const T_NS_SEPARATOR;
use const T_PARENT;
use const T_PHPCS_DISABLE;
use const T_PHPCS_ENABLE;
use const T_PHPCS_IGNORE;
use const T_PHPCS_IGNORE_FILE;
use const T_PHPCS_SET;
use const T_RETURN;
use const T_SELF;
use const T_STRING;
use const T_THROW;
use const T_TRAIT;
use const T_WHITESPACE;
use const T_YIELD;
use const T_YIELD_FROM;

class TokenHelper
{

	/** @var (int|string)[] */
	public static $nameTokenCodes = [
		T_NS_SEPARATOR,
		T_STRING,
	];

	/** @var (int|string)[] */
	public static $typeKeywordTokenCodes = [
		T_CLASS,
		T_TRAIT,
		T_INTERFACE,
	];

	/** @var (int|string)[] */
	public static $ineffectiveTokenCodes = [
		T_WHITESPACE,
		T_COMMENT,
		T_DOC_COMMENT,
		T_DOC_COMMENT_OPEN_TAG,
		T_DOC_COMMENT_CLOSE_TAG,
		T_DOC_COMMENT_STAR,
		T_DOC_COMMENT_STRING,
		T_DOC_COMMENT_TAG,
		T_DOC_COMMENT_WHITESPACE,
		T_PHPCS_DISABLE,
		T_PHPCS_ENABLE,
		T_PHPCS_IGNORE,
		T_PHPCS_IGNORE_FILE,
		T_PHPCS_SET,
	];

	/** @var (int|string)[] */
	public static $typeHintTokenCodes = [
		T_NS_SEPARATOR,
		T_STRING,
		T_SELF,
		T_PARENT,
		T_ARRAY_HINT,
		T_CALLABLE,
	];

	/** @var (int|string)[] */
	public static $earlyExitTokenCodes = [
		T_RETURN,
		T_CONTINUE,
		T_BREAK,
		T_THROW,
		T_YIELD,
		T_YIELD_FROM,
		T_EXIT,
	];

	/** @var (int|string)[] */
	public static $functionTokenCodes = [
		T_FUNCTION,
		T_CLOSURE,
	];

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param (int|string)|(int|string)[] $types
	 * @param int $startPointer
	 * @param int|null $endPointer
	 * @return int|null
	 */
	public static function findNext(File $phpcsFile, $types, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findNext($types, $startPointer, $endPointer, false);
		return $token === false ? null : $token;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param (int|string)|(int|string)[] $types
	 * @param int $startPointer
	 * @param int|null $endPointer
	 * @return int[]
	 */
	public static function findNextAll(File $phpcsFile, $types, int $startPointer, ?int $endPointer = null): array
	{
		$pointers = [];

		$actualStartPointer = $startPointer;
		while (true) {
			$pointer = self::findNext($phpcsFile, $types, $actualStartPointer, $endPointer);
			if ($pointer === null) {
				break;
			}

			$pointers[] = $pointer;
			$actualStartPointer = $pointer + 1;
		}

		return $pointers;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param (int|string)|(int|string)[] $types
	 * @param string $content
	 * @param int $startPointer
	 * @param int|null $endPointer
	 * @return int|null
	 */
	public static function findNextContent(File $phpcsFile, $types, string $content, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findNext($types, $startPointer, $endPointer, false, $content);
		return $token === false ? null : $token;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $startPointer search starts at this token, inclusive
	 * @param int|null $endPointer search ends at this token, exclusive
	 * @return int|null
	 */
	public static function findNextEffective(File $phpcsFile, int $startPointer, ?int $endPointer = null): ?int
	{
		return self::findNextExcluding($phpcsFile, self::$ineffectiveTokenCodes, $startPointer, $endPointer);
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param (int|string)|(int|string)[] $types
	 * @param int $startPointer search starts at this token, inclusive
	 * @param int|null $endPointer search ends at this token, exclusive
	 * @return int|null
	 */
	public static function findNextExcluding(File $phpcsFile, $types, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findNext($types, $startPointer, $endPointer, true);
		return $token === false ? null : $token;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param (int|string)|(int|string)[] $types
	 * @param int $startPointer
	 * @param int|null $endPointer
	 * @return int|null
	 */
	public static function findNextLocal(File $phpcsFile, $types, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findNext($types, $startPointer, $endPointer, false, null, true);
		return $token === false ? null : $token;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param (int|string)|(int|string)[] $types
	 * @param int $startPointer
	 * @param int|null $endPointer
	 * @return int|null
	 */
	public static function findNextLocalExcluding(File $phpcsFile, $types, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findNext($types, $startPointer, $endPointer, true, null, true);
		return $token === false ? null : $token;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $startPointer search starts at this token, inclusive
	 * @param int|null $endPointer search ends at this token, exclusive
	 * @return int|null
	 */
	public static function findNextAnyToken(File $phpcsFile, int $startPointer, ?int $endPointer = null): ?int
	{
		return self::findNextExcluding($phpcsFile, [], $startPointer, $endPointer);
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param (int|string)|(int|string)[] $types
	 * @param int $startPointer search starts at this token, inclusive
	 * @param int|null $endPointer search ends at this token, exclusive
	 * @return int|null
	 */
	public static function findPrevious(File $phpcsFile, $types, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findPrevious($types, $startPointer, $endPointer, false);
		return $token === false ? null : $token;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param (int|string)|(int|string)[] $types
	 * @param string $content
	 * @param int $startPointer
	 * @param int|null $endPointer
	 * @return int|null
	 */
	public static function findPreviousContent(File $phpcsFile, $types, string $content, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findPrevious($types, $startPointer, $endPointer, false, $content);
		return $token === false ? null : $token;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $startPointer search starts at this token, inclusive
	 * @param int|null $endPointer search ends at this token, exclusive
	 * @return int|null
	 */
	public static function findPreviousEffective(File $phpcsFile, int $startPointer, ?int $endPointer = null): ?int
	{
		return self::findPreviousExcluding($phpcsFile, self::$ineffectiveTokenCodes, $startPointer, $endPointer);
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param (int|string)|(int|string)[] $types
	 * @param int $startPointer search starts at this token, inclusive
	 * @param int|null $endPointer search ends at this token, exclusive
	 * @return int|null
	 */
	public static function findPreviousExcluding(File $phpcsFile, $types, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findPrevious($types, $startPointer, $endPointer, true);
		return $token === false ? null : $token;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param (int|string)|(int|string)[] $types
	 * @param int $startPointer
	 * @param int|null $endPointer
	 * @return int|null
	 */
	public static function findPreviousLocal(File $phpcsFile, $types, int $startPointer, ?int $endPointer = null): ?int
	{
		/** @var int|false $token */
		$token = $phpcsFile->findPrevious($types, $startPointer, $endPointer, false, null, true);
		return $token === false ? null : $token;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $pointer search starts at this token, inclusive
	 * @return int|null
	 */
	public static function findFirstTokenOnNextLine(File $phpcsFile, int $pointer): ?int
	{
		$newLinePointer = self::findNextContent($phpcsFile, [T_WHITESPACE, T_DOC_COMMENT_WHITESPACE], $phpcsFile->eolChar, $pointer);
		if ($newLinePointer === null) {
			return null;
		}
		$tokens = $phpcsFile->getTokens();
		return isset($tokens[$newLinePointer + 1]) ? $newLinePointer + 1 : null;
	}

	public static function getContent(File $phpcsFile, int $startPointer, ?int $endPointer = null): string
	{
		$tokens = $phpcsFile->getTokens();
		$endPointer = $endPointer ?: self::getLastTokenPointer($phpcsFile);

		$content = '';
		for ($i = $startPointer; $i <= $endPointer; $i++) {
			$content .= $tokens[$i]['content'];
		}

		return $content;
	}

	public static function getLastTokenPointer(File $phpcsFile): int
	{
		$tokenCount = count($phpcsFile->getTokens());
		if ($tokenCount === 0) {
			throw new EmptyFileException($phpcsFile->getFilename());
		}
		return $tokenCount - 1;
	}

}
