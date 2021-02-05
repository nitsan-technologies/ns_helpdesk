<?php
defined('TYPO3_MODE') || die('Access denied.');
$_EXTKEY = 'ns_helpdesk';

if (TYPO3_MODE === 'BE') {
    $isVersion9Up = \TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) >= 9000000;
    if (!array_key_exists('nitsan', $GLOBALS['TBE_MODULES']) || $GLOBALS['TBE_MODULES']['nitsan'] =='') {
        if (version_compare(TYPO3_branch, '8.0', '>=')) {
            if (!isset($GLOBALS['TBE_MODULES']['nitsan'])) {
                $temp_TBE_MODULES = [];
                foreach ($GLOBALS['TBE_MODULES'] as $key => $val) {
                    if ($key == 'web') {
                        $temp_TBE_MODULES[$key] = $val;
                        $temp_TBE_MODULES['nitsan'] = '';
                    } else {
                        $temp_TBE_MODULES[$key] = $val;
                    }
                }
                $GLOBALS['TBE_MODULES'] = $temp_TBE_MODULES;
                $GLOBALS['TBE_MODULES']['_configuration']['nitsan'] = [
                    'iconIdentifier' => 'parent-module-nshelpdesk',
                    'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/BackendModule.xlf',
                    'name' => 'nitsan'
                ];
            }
        }
    }
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'NITSAN.NsHelpdesk',
        'nitsan', // Make module a submodule of 'nitsan'
        'helpdeskmi1', // Submodule key
        '', // Position
        [
            'Tickets' => 'dashboard, list, show, premiumExtension, saveConfiguration, getConstants, saveConstant, closeTicket, reopenTicket',
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/module-nshelpdesk.svg',
            'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_helpdesk.xlf',
            'navigationComponentId' => ($isVersion9Up ? 'TYPO3/CMS/Backend/PageTree/PageTreeElement' : 'typo3-pagetree'),
            'inheritNavigationComponentFromMainModule' => false
        ]
    );
}

$tables = ['tickets', 'defaultassignee', 'ticketstatus'];

foreach ($tables as $table) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
        'tx_nshelpdesk_domain_model_' . $table,
        'EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_csh_tx_nshelpdesk_domain_model_' . $table . '.xlf'
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_nshelpdesk_domain_model_' . $table);
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $_EXTKEY . '/Configuration/TSconfig/ContentElementWizard.tsconfig">'
);
