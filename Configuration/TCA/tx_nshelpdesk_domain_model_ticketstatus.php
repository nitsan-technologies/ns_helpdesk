<?php

$ll = 'LLL:EXT:ns_helpdesk/Resources/Private/Language/locallang_db.xlf:tx_nshelpdesk_domain_model_tickets.';
$corell = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:';

return [
    'ctrl' => [
        'title' => 'LLL:EXT:ns_helpdesk/Resources/Private/Language/locallang_db.xlf:tx_nshelpdesk_domain_model_ticketstatus',
        'label' => 'status_title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'status_title,status_color',
        'iconfile' => 'EXT:ns_helpdesk/Resources/Public/Icons/tx_nshelpdesk_domain_model_ticketstatus.svg',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_diffsource, hidden, status_title, status_color, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => $corell . 'LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => $corell . 'LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => $corell . 'LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => $corell . 'LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'datetime',
                'eval' => 'datetime',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'label' => $corell . 'LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'datetime',
                'eval' => 'datetime',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],

        'status_title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_helpdesk/Resources/Private/Language/locallang_db.xlf:tx_nshelpdesk_domain_model_ticketstatus.status_title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'required' => true
            ],
        ],
        'status_color' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_helpdesk/Resources/Private/Language/locallang_db.xlf:tx_nshelpdesk_domain_model_ticketstatus.status_color',
            'config' => [
                'type' => 'color',
                'size' => 10,
                'eval' => 'trim'
            ],
        ],

    ],
];
