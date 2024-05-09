<?php
defined('TYPO3_MODE') || die('Access denied.');
$_EXTKEY = 'ns_helpdesk';
call_user_func(
    function ($_EXTKEY) {
        if (version_compare(TYPO3_branch, '10.0', '>=')) {
            $ticketsController = \NITSAN\NsHelpdesk\Controller\TicketsController::class;
        } else {
            $ticketsController = 'Tickets';
        }
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'NITSAN.NsHelpdesk',
            'Helpdesk',
            [
                    $ticketsController => 'list, show, new, create, closeTicket, reopenTicket',
                ],
                // non-cacheable actions
                [
                    $ticketsController => 'list, show, create, closeTicket, reopenTicket',
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
    },
    'ns_helpdesk'
);
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['NsHelpdesk'] = [
    'NITSAN\NsHelpdesk\ViewHelpers',
];

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['ns_helpdesk'] =
    \NITSAN\NsHelpdesk\Hooks\DataHandler::class;

