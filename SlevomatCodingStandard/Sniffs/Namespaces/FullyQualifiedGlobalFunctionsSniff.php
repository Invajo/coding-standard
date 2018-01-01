<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Helpers\ReferencedName;

class FullyQualifiedGlobalFunctionsSniff
	extends \SlevomatCodingStandard\Sniffs\Namespaces\AbstractFullyQualifiedGlobalReference
{

	protected function getNotFullyQualifiedMessage(): string
	{
		return 'Function %s() should be referenced via a fully qualified name.';
	}

	protected function isCaseSensitive(): bool
	{
		return true;
	}

	protected function isValidType(ReferencedName $name): bool
	{
		return $name->isFunction();
	}

}
