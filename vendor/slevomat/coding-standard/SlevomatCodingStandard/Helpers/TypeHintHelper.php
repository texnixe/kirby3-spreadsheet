<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use function array_key_exists;
use function in_array;

class TypeHintHelper
{

	public static function isSimpleTypeHint(string $typeHint): bool
	{
		return in_array($typeHint, self::getSimpleTypeHints(), true);
	}

	public static function isSimpleIterableTypeHint(string $typeHint): bool
	{
		return in_array($typeHint, self::getSimpleIterableTypeHints(), true);
	}

	public static function convertLongSimpleTypeHintToShort(string $typeHint): string
	{
		$longToShort = [
			'integer' => 'int',
			'boolean' => 'bool',
		];
		return array_key_exists($typeHint, $longToShort) ? $longToShort[$typeHint] : $typeHint;
	}

	public static function getFullyQualifiedTypeHint(File $phpcsFile, int $pointer, string $typeHint): string
	{
		if (self::isSimpleTypeHint($typeHint)) {
			return self::convertLongSimpleTypeHintToShort($typeHint);
		}

		return NamespaceHelper::resolveClassName($phpcsFile, $typeHint, $pointer);
	}

	/**
	 * @return string[]
	 */
	public static function getSimpleTypeHints(): array
	{
		static $simpleTypeHints;

		if ($simpleTypeHints === null) {
			$simpleTypeHints = [
				'int',
				'integer',
				'float',
				'string',
				'bool',
				'boolean',
				'callable',
				'self',
				'array',
				'iterable',
				'void',
			];
		}

		return $simpleTypeHints;
	}

	/**
	 * @return string[]
	 */
	public static function getSimpleIterableTypeHints(): array
	{
		return [
			'array',
			'iterable',
		];
	}

	public static function isSimpleUnofficialTypeHints(string $typeHint): bool
	{
		static $simpleUnofficialTypeHints;

		if ($simpleUnofficialTypeHints === null) {
			$simpleUnofficialTypeHints = [
				'null',
				'mixed',
				'scalar',
				'numeric',
				'true',
				'false',
				'object',
				'resource',
				'static',
				'$this',
			];
		}

		return in_array($typeHint, $simpleUnofficialTypeHints, true);
	}

}
