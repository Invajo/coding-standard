<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class FullyQualifiedGlobalConstantsSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fullyQualifiedGlobalConstantsNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fullyQualifiedGlobalConstantsErrors.php');

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 17, FullyQualifiedGlobalConstantsSniff::CODE_NON_FULLY_QUALIFIED, 'Constant PHP_VERSION should be referenced via a fully qualified name.');
		self::assertSniffError($report, 31, FullyQualifiedGlobalConstantsSniff::CODE_NON_FULLY_QUALIFIED, 'Constant PHP_OS should be referenced via a fully qualified name.');

		self::assertAllFixedInFile($report);
	}

	public function testExcludeErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fullyQualifiedGlobalConstantsExcludeErrors.php', [
			'exclude' => ['PHP_VERSION'],
		]);

		self::assertSame(1, $report->getErrorCount());

		self::assertSniffError($report, 28, FullyQualifiedGlobalConstantsSniff::CODE_NON_FULLY_QUALIFIED, 'Constant PHP_OS should be referenced via a fully qualified name.');

		self::assertAllFixedInFile($report);
	}

}
