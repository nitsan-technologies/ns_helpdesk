<?php
$ll = 'LLL:EXT:ns_helpdesk/Resources/Private/Language/locallang_db.xlf:tx_nshelpdesk_domain_model_tickets.';
if (version_compare(TYPO3_branch, '9.2', '>=')) {
    $corell = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:';
} else {
    $corell = 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:';
}

return [
    'ctrl' => [
        'title' => 'LLL:EXT:ns_helpdesk/Resources/Private/Language/locallang_db.xlf:tx_nshelpdesk_domain_model_tickets',
        'label' => 'ticket_subject',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'crdate',
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
        'searchFields' => 'ticket_subject,ticket_text,slug',
        'iconfile' => 'EXT:ns_helpdesk/Resources/Public/Icons/tx_nshelpdesk_domain_model_tickets.svg'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, ticket_subject, slug, ticket_text, ticket_post_date, ticket_status, assignee_id, user_id',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, ticket_subject, slug, ticket_text, ticket_post_date, ticket_status, ticket_rating, assignee_id, user_id,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
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
                'foreign_table' => 'tx_nshelpdesk_domain_model_tickets',
                'foreign_table_where' => 'AND {#tx_nshelpdesk_domain_model_tickets}.{#pid}=###CURRENT_PID### AND {#tx_nshelpdesk_domain_model_tickets}.{#sys_language_uid} IN (-1,0)',
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
        'ticket_subject' => [
            'exclude' => true,
            'label' => $ll . 'ticket_subject',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'ticket_text' => [
            'exclude' => true,
            'label' => $ll . 'ticket_text',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'richtextConfiguration' => 'default',
                'fieldControl' => [
                    'fullScreenRichtext' => [
                        'disabled' => false,
                    ],
                ],
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim,required',
            ],

        ],
        'ticket_post_date' => [
            'exclude' => true,
            'label' => $ll . 'ticket_post_date',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 10,
                'eval' => 'datetime',
                'default' => time()
            ],
        ],
        'ticket_status' => [
            'exclude' => true,
            'label' => $ll . 'ticket_status',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_nshelpdesk_domain_model_ticketstatus',
                'foreign_table_where' => 'AND {#tx_nshelpdesk_domain_model_ticketstatus}.{#pid} >= 1',
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'assignee_id' => [
            'exclude' => true,
            'label' => $ll . 'assignee_id',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'be_users',
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'user_id' => [
            'exclude' => true,
            'label' => $ll . 'user_id',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'items' => [
                    [$ll . 'selectfeuser', -1],
                ],
                'minitems' => 0,
                'maxitems' => 1,
                'eval'=>'required'
            ],
        ],
        'slug' => [
            'label' => $ll . 'slug',
            'exclude' => 1,
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'nospace,alphanum_x,lower,unique',
                'readOnly' => true
            ]
        ],
        'ticket_rating' => [
            'label' => $ll . 'ticket_rating',
            'exclude' => 1,
            'config' => [
                'type' => 'input',
                'default' => 0,
                'readOnly' => 1
            ],
        ],
    ],
];
