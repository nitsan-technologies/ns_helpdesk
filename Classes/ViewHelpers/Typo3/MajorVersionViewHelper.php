<?php

declare(strict_types=1);

namespace NITSAN\NsHelpdesk\ViewHelpers\Typo3;

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

final class MajorVersionViewHelper extends AbstractConditionViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('version', 'int', 'TYPO3 major version to compare against', true);
        $this->registerArgument('operator', 'string', 'Comparison operator: ==, !=, <, <=, >, >=', false, '==');
    }

    public static function verdict(
        array $arguments,
        RenderingContextInterface $renderingContext
    ): bool {
        $current  = GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion();
        $compare  = (int)$arguments['version'];
        $operator = (string)($arguments['operator'] ?? '==');

        return match ($operator) {
            '=='    => $current === $compare,
            '!='    => $current !== $compare,
            '>'     => $current >   $compare,
            '>='    => $current >=  $compare,
            '<'     => $current <   $compare,
            '<='    => $current <=  $compare,
            default => throw new \InvalidArgumentException(
                'Invalid operator "' . $operator . '". Allowed: ==, !=, <, <=, >, >=',
                1700000000
            ),
        };
    }
}