<?php

defined('TYPO3') || die('Access denied.');
$_EXTKEY = 'ns_helpdesk';


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    "@import 'EXT:ns_helpdesk/Configuration/TSconfig/ContentElementWizard.tsconfig'"
);
