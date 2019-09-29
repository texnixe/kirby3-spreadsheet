<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Strings;

use Nette\Utils\Strings;

final class StringFormatConverter
{
    public function underscoreAndHyphenToCamelCase(string $value): string
    {
        $value = str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $value)));

        return lcfirst($value);
    }

    public function camelCaseToUnderscore(string $input): string
    {
        return self::camelCaseToGlue($input, '_');
    }

    /**
     * @param mixed[] $items
     * @return mixed[]
     */
    public function camelCaseToUnderscoreInArrayKeys(array $items): array
    {
        foreach ($items as $key => $value) {
            if (! is_string($key)) {
                continue;
            }

            $newKey = $this->camelCaseToUnderscore($key);
            if ($key === $newKey) {
                continue;
            }

            $items[$newKey] = $value;
            unset($items[$key]);
        }

        return $items;
    }

    private function camelCaseToGlue(string $input, string $glue): string
    {
        $matches = Strings::matchAll($input, '!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!');

        $parts = [];
        foreach ($matches as $match) {
            $parts[] = $match[0] === strtoupper($match[0]) ? strtolower($match[0]) : lcfirst($match[0]);
        }

        return implode($glue, $parts);
    }
}
