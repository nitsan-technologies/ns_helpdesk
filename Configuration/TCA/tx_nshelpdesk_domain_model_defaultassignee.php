<?php

$corell = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:';
return [
    'ctrl' => [
        'title' => 'LLL:EXT:ns_helpdesk/Resources/Private/Language/locallang_db.xlf:tx_nshelpdesk_domain_model_defaultassignee',
        'label' => 'ticket_type',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'sortby' => 'sorting',
        'hideTable'=> true,
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => '',
        'iconfile' => 'EXT:ns_helpdesk/Resources/Public/Icons/tx_nshelpdesk_domain_model_defaultassignee.gif',
        'security' => [
            'ignorePageTypeRestriction' => true
        ],
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_diffsource, hidden, ticket_type, assignee_id, is_default, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
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

        'ticket_type' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_helpdesk/Resources/Private/Language/locallang_db.xlf:tx_nshelpdesk_domain_model_defaultassignee.ticket_type',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'number'
            ]
        ],
        'assignee_id' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_helpdesk/Resources/Private/Language/locallang_db.xlf:tx_nshelpdesk_domain_model_defaultassignee.assignee_id',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'number'
            ]
        ],
        'is_default' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_helpdesk/Resources/Private/Language/locallang_db.xlf:tx_nshelpdesk_domain_model_defaultassignee.is_default',
            'config' => [
                'type' => 'check',
            ]
        ]

    ],
];
