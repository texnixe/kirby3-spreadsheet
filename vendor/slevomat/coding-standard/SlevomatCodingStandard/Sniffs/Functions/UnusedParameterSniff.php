<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\SuppressHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\VariableHelper;
use function sprintf;
use const T_CLOSURE;
use const T_FUNCTION;
use const T_VARIABLE;

class UnusedParameterSniff implements Sniff
{

	private const NAME = 'SlevomatCodingStandard.Functions.UnusedParameter';

	public const CODE_UNUSED_PARAMETER = 'UnusedParameter';

	/**
	 * @return (int|string)[]
	 */
	public function register(): array
	{
		return [
			T_FUNCTION,
			T_CLOSURE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $functionPointer
	 */
	public function process(File $phpcsFile, $functionPointer): void
	{
		if (FunctionHelper::isAbstract($phpcsFile, $functionPointer)) {
			return;
		}

		if (SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $this->getSniffName(self::CODE_UNUSED_PARAMETER))) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$currentPointer = $tokens[$functionPointer]['parenthesis_opener'] + 1;
		while (true) {
			$parameterPointer = TokenHelper::findNext($phpcsFile, T_VARIABLE, $currentPointer, $tokens[$functionPointer]['parenthesis_closer']);
			if ($parameterPointer === null) {
				break;
			}

			if (!VariableHelper::isUsedInScope($phpcsFile, $functionPointer, $parameterPointer)) {
				$phpcsFile->addError(sprintf('Unused parameter %s.', $tokens[$parameterPointer]['content']), $parameterPointer, self::CODE_UNUSED_PARAMETER);
			}

			$currentPointer = $parameterPointer + 1;
		}
	}

	private function getSniffName(string $sniffName): string
	{
		return sprintf('%s.%s', self::NAME, $sniffName);
	}

}
