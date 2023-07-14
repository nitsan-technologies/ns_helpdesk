<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die('Access denied.');

$_EXTKEY = 'ns_helpdesk';


ExtensionManagementUtility::addPageTSConfig(
    "@import 'EXT:ns_helpdesk/Configuration/TSconfig/ContentElementWizard.tsconfig'"
);
