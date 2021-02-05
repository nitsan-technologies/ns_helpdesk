<?php
if (version_compare(TYPO3_branch, '9.2', '>=')) {
    $corell = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:';
} else {
    $corell = 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:';
}
return [
    'ctrl' => [
        'title' => 'LLL:EXT:ns_helpdesk/Resources/Private/Language/locallang_db.xlf:tx_nshelpdesk_domain_model_defaultassignee',
        'label' => 'ticket_type',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'sorting',
        'hideTable'=> true,
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => '',
        'iconfile' => 'EXT:ns_helpdesk/Resources/Public/Icons/tx_nshelpdesk_domain_model_defaultassignee.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, ticket_type, assignee_id, is_default',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, ticket_type, assignee_id, is_default, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => $corell . 'LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        $corell . 'LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ]
                ],
                'default' => 0,
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => $corell . 'LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_nshelpdesk_domain_model_defaultassignee',
                'foreign_table_where' => 'AND {#tx_nshelpdesk_domain_model_defaultassignee}.{#pid}=###CURRENT_PID### AND {#tx_nshelpdesk_domain_model_defaultassignee}.{#sys_language_uid} IN (-1,0)',
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
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
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
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
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
                'eval' => 'int'
            ]
        ],
        'assignee_id' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ns_helpdesk/Resources/Private/Language/locallang_db.xlf:tx_nshelpdesk_domain_model_defaultassignee.assignee_id',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
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
