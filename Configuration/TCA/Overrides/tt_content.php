<?php
defined('TYPO3_MODE') || die('Access denied');
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'NITSAN.NsHelpdesk',
    'Helpdesk',
    'Helpdesk'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['nshelpdesk_helpdesk'] = 'recursive,select_key,pages';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['nshelpdesk_helpdesk'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'nshelpdesk_helpdesk',
    'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/NsHelpdesk_Flexform.xml'
);
