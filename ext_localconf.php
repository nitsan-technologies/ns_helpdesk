<?php

defined('TYPO3') || die('Access denied.');
$_EXTKEY = 'ns_helpdesk';

$ticketsController = \NITSAN\NsHelpdesk\Controller\TicketsController::class;

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'NsHelpdesk',
    'HelpdeskList',
    [
        $ticketsController => 'list, show, closeTicket, reopenTicket',
    ],
    // non-cacheable actions
    [
        $ticketsController => 'list, show, closeTicket, reopenTicket',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'NsHelpdesk',
    'HelpdeskTicket',
    [
        $ticketsController => 'new, create, quickPopupTicket',
    ],
    // non-cacheable actions
    [
        $ticketsController => 'new, create, quickPopupTicket',
    ]
);

$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);

$icon = [
    'ns_helpdesk-plugin-helpdesk'
    , 'module-nshelpdesk'
    , 'parent-module-nshelpdesk'
];

foreach ($icon as $value) {
    $iconRegistry->registerIcon(
        $value,
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:ns_helpdesk/Resources/Public/Icons/' . $value . '.svg']
    );
}

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['NsHelpdesk'] = [
    'NITSAN\NsHelpdesk\ViewHelpers',
];

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['ns_helpdesk'] =
    \NITSAN\NsHelpdesk\Hooks\DataHandler::class;

$GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['security.backend.enforceContentSecurityPolicy'] = false;
