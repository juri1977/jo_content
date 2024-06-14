<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_institut',
        'label' => 'name',
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
        'searchFields' => 'name,city,zip,street,streetnumber,email,phone,mobile,website,fax,description,image,employee,federalstate',
        'iconfile' => 'EXT:jo_content/Resources/Public/Icons/Extension.svg'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, city, zip, street, streetnumber, email, phone, mobile, website, fax, description, image, employee, federalstate',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, typ, name, --palette--;;address_p, --palette--;;contact_p, description, image, employee, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
    'palettes' => [
        'contact_p' => [
            'label' => 'Kontakt Angaben',
            'showitem' => 'email, website, --linebreak--, phone, mobile, fax'
        ],
        'address_p' => [
            'label' => 'Adresse Angaben',
            'showitem' => 'federalstate, --linebreak--, street, streetnumber, --linebreak--, city, zip'
        ],
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
                'foreign_table' => 'tx_jocontent_domain_model_institut',
                'foreign_table_where' => 'AND tx_jocontent_domain_model_institut.pid=###CURRENT_PID### AND tx_jocontent_domain_model_institut.sys_language_uid IN (-1,0)',
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
        'typ' => [
            'label' => 'Institutions-Typ',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'size' => 1,
                'maxitems' => 1,
                'items' => [
                    ['Bitte auswählen', ''],
                    ['Museen', 'Museen'],
					['LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_institut.insttype_uia', 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_institut.insttype_uia'],
                    ['Vereine', 'Vereine'],
                    ['Behörden für Denkmalpflege', 'Behörden für Denkmalpflege']
                ]
            ]
        ],
        'name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_institut.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'city' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_institut.city',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'zip' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_institut.zip',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'street' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_institut.street',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'streetnumber' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_institut.streetnumber',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ]
        ],
        'email' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_institut.email',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'nospace,email'
            ]
        ],
        'phone' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_institut.phone',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'mobile' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_institut.mobile',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'website' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_institut.website',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'fax' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_institut.fax',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_institut.description',
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
                'eval' => 'trim',
            ],
            
        ],
        'image' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_institut.image',
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
        'employee' => [
            'exclude' => true,
            'label' => 'LLL:EXT:jo_content/Resources/Private/Language/locallang_db.xlf:tx_jocontent_domain_model_institut.employee',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_jocontent_domain_model_person',
                'default' => 0,
                'size' => 10,
                'autoSizeMax' => 30,
                'maxitems' => 9999,
                'multiple' => 0,
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => false,
                    ],
                    'addRecord' => [
                        'disabled' => false,
                    ],
                    'listModule' => [
                        'disabled' => true,
                    ],
                ],
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
    
    ],
];
