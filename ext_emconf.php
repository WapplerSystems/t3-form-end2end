<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Formular end to end testing',
    'description' => 'Formular end 2 end tests for TYPO3 form and powermail. Changes the email addresses by request header.',
    'category' => 'fe',
    'version' => '12.0.0',
    'state' => 'stable',
    'author' => 'Sven Wappler',
    'author_email' => 'typo3YYYY@wappler.systems',
    'author_company' => 'WapplerSystems',
    'constraints' => [
        'depends' => [
            'typo3' => '12.0.0-12.4.99',
            'form_extended' => '12.0.0',
        ],
    ],
];
