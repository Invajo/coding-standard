<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use SlevomatCodingStandard\Helpers\TokenHelper;

class InlineDocCommentDeclarationSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_INVALID_FORMAT = 'InvalidFormat';
	public const CODE_INVALID_COMMENT_TYPE = 'InvalidCommentType';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_DOC_COMMENT_OPEN_TAG,
			T_COMMENT,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $commentOpenPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $commentOpenPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$commentOpenPointer]['code'] === T_COMMENT) {
			if (!preg_match('~^/\*\\s*@var\\s+~', $tokens[$commentOpenPointer]['content'])) {
				return;
			}

			$fix = $phpcsFile->addFixableError(
				'Invalid comment type /* */ for inline documentation comment, use /** */.',
				$commentOpenPointer,
				self::CODE_INVALID_COMMENT_TYPE
			);

			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->replaceToken($commentOpenPointer, sprintf('/**%s', substr($tokens[$commentOpenPointer]['content'], 2)));
				$phpcsFile->fixer->endChangeset();
			}

			$commentClosePointer = $commentOpenPointer;
			$commentContent = trim(substr($tokens[$commentOpenPointer]['content'], 2, -2));
		} else {
			$commentClosePointer = $tokens[$commentOpenPointer]['comment_closer'];
			$commentContent = trim(TokenHelper::getContent($phpcsFile, $commentOpenPointer + 1, $commentClosePointer - 1));
		}

		if (!preg_match('~^@var~', $commentContent)) {
			return;
		}

		$pointerAfterComment = TokenHelper::findNextExcluding($phpcsFile, T_WHITESPACE, $commentClosePointer + 1);
		if ($pointerAfterComment === null || !in_array($tokens[$pointerAfterComment]['code'], [T_VARIABLE, T_FOREACH, T_WHILE], true)) {
			return;
		}

		if (preg_match('~^@var\\s+\\S+\s+\$\\S+(?:\\s+.+)?$~', $commentContent)) {
			return;
		}

		if (preg_match('~^@var\\s+(\$\\S+)\\s+(\\S+)(\\s+.+)?$~', $commentContent, $matches)) {
			$fix = $phpcsFile->addFixableError(
				sprintf(
					'Invalid inline documentation comment format "%s", expected "@var %s %s%s".',
					$commentContent,
					$matches[2],
					$matches[1],
					$matches[3] ?? ''
				),
				$commentOpenPointer,
				self::CODE_INVALID_FORMAT
			);

			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				for ($i = $commentOpenPointer; $i <= $commentClosePointer; $i++) {
					$phpcsFile->fixer->replaceToken($i, '');
				}
				$phpcsFile->fixer->addContent(
					$commentOpenPointer,
					sprintf(
						'%s @var %s %s%s */',
						$tokens[$commentOpenPointer]['code'] === T_DOC_COMMENT_OPEN_TAG ? '/**' : '/*',
						$matches[2],
						$matches[1],
						$matches[3] ?? ''
					)
				);
				$phpcsFile->fixer->endChangeset();
			}
		} else {
			$phpcsFile->addError(
				sprintf('Invalid inline documentation comment format "%1$s", expected "@var type $variable".', $commentContent),
				$commentOpenPointer,
				self::CODE_INVALID_FORMAT
			);
		}
	}

}
