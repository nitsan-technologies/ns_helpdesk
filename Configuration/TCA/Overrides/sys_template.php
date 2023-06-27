<?php

defined('TYPO3') || die('Access denied.');

$_EXTKEY = $GLOBALS['_EXTKEY'] = 'ns_helpdesk';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'NS Helpdesk');
