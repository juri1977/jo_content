<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_person',
        'label' => 'lastname',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
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
        'searchFields' => 'gender,title,lastname,firstname,email,phone,mobile,website,fax,city,zip,street,streetnumber,description,department,departmenttext,publikationen,image,federalstate',
        'iconfile' => 'EXT:jo_content/Resources/Public/Icons/Extension.svg'
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, --palette--;;person_p, --palette--;;contact_p, --palette--;;address_p, description, --palette--;;department_p, publikationen, image, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
    'palettes' => [
        'person_p' => [
            'label' => 'Person Angaben',
            'showitem' => 'gender, title, --linebreak--, lastname, firstname'
        ],
        'contact_p' => [
            'label' => 'Kontakt Angaben',
            'showitem' => 'email, website, --linebreak--, phone, mobile, fax'
        ],
        'address_p' => [
            'label' => 'Adresse Angaben',
            'showitem' => 'federalstate, --linebreak--, street, streetnumber, --linebreak--, city, zip'
        ],
        'department_p' => [
            'label' => 'Department Angaben',
            'showitem' => 'department, departmenttext'
        ]
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, gender, title, lastname, firstname, email, phone, mobile, website, fax, city, zip, street, streetnumber, federalstate, description, department, departmenttext, publikationen, image',
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
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
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_jocontent_domain_model_person',
                'foreign_table_where' => 'AND tx_jocontent_domain_model_person.pid=###CURRENT_PID### AND tx_jocontent_domain_model_person.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'
                    ]
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'behaviour' => [
                'allowLanguageSynchronization' => true
            ],
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'default' => 0,
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'behaviour' => [
                'allowLanguageSynchronization' => true
            ],
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
            ],
        ],
        'gender' => [
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_person.gender',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'size' => 1,
                'maxitems' => 1,
                'items' => [
                    ['LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_person.gender.undefined', ''],
                    ['LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_person.gender.m', 'm'],
                    ['LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_person.gender.f', 'f'],
                    ['LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_person.gender.v', 'v']
                ]
            ]
        ],

        'title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_person.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'lastname' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_person.lastname',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'firstname' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_person.firstname',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'email' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_person.email',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,nospace,email',
            ],
        ],
        'phone' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_person.phone',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'mobile' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_person.mobile',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'website' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_person.website',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'fax' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_person.fax',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'city' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_person.city',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'zip' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_person.zip',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'street' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_person.street',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'streetnumber' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_person.streetnumber',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'federalstate' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_institut.federalstate',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_jocontent_domain_model_federalstate'
            ],
        ],
        'description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_person.description',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'department' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_person.department',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'size' => 1,
                'maxitems' => 1,
                'items' => [
                    ['', ''],
                    ['Landesvertreter', 'LaVe'],
                    ['Fachgebietsvertreter', 'FaVe'],
                    ['Unbekannt', 'unknown']
                ]
            ]
        ],
        'departmenttext' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_person.departmenttext',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'publikationen' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_person.publikationen',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'image' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_person.image',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'image',
                [
                    'appearance' => [
                        'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference'
                    ],
                    'foreign_types' => [
                        '0' => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => [
                            'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                        ]
                    ],
                    'maxitems' => 1
                ],
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            ),
        ],
    ],
];
