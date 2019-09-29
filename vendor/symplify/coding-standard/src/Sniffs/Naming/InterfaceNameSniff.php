<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Naming;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class InterfaceNameSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Interface should have suffix "Interface".';

    /**
     * @var int
     */
    private $position;

    /**
     * @var File
     */
    private $file;

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_INTERFACE];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $this->file = $file;
        $this->position = $position;

        if (Strings::endsWith($this->getInterfaceName(), 'Interface')) {
            return;
        }

        $file->addError(self::ERROR_MESSAGE, $position, self::class);
    }

    private function getInterfaceName(): string
    {
        return (string) $this->file->getDeclarationName($this->position);
    }
}
