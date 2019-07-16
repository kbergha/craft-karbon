<?php
/**
 * karbon plugin for Craft CMS 3.x
 *
 * Brings the power of date / datetime handling from Carbon to Twig, hopefully helping to answer the age old question of: what (date)-time is it?
 *
 * @link      https://github.com/kbergha
 * @copyright Copyright (c) 2019 Knut Erik Berg-Hansen
 */

/**
 * karbon config.php
 *
 * This file exists only as a template for the karbon settings.
 * It does nothing on its own.
 *
 * Don't edit this file, instead copy it to 'craft/config' as 'karbon.php'
 * and make your changes there to override default settings.
 *
 * Once copied to 'craft/config', this file will be multi-environment aware as
 * well, so you can have different settings groups for each environment, just as
 * you do for 'general.php'
 *
 * One set of configuration per locale you want to use.
 * If a locale is not defined below, the 'default' config is used.
 *
 * Use IETF locale codes, the same as is used in Craft, as array keys.
 *
 * 'default' is required.
 *
 * Too see supported formats, take look at:
 * https://momentjs.com/docs/#/displaying/format/
 *
 */

return [
    'dateConfiguration' => [
        'default' => [
            'timeZone' => Craft::$app->getTimeZone(),
            'date' => [
                'fallback' => 'L',
                'oneDay' => 'dddd LL',
                'multiDaySeparator' => '-',
                'multiDaySameMonth' => [
                    'from' => 'D.',
                    'to' => 'LL'
                ],
                'multiDayDifferentMonth' => [
                    'from' => 'D. MMMM',
                    'to' => 'LL'
                ],
                'multiDayDifferentYear' => [
                    'from' => 'LL',
                    'to' => 'LL'
                ]
            ],
            'time' => [
                'prefix' => '',
                'postfix' => '',
                'spanSeparator' => ' - ',
                'format' => 'HH:mm'
            ]
        ],
        'nb-NO' => [
            'timeZone' => 'Europe/Oslo',
            'date' => [
                'fallback' => 'L',
                'oneDay' => 'dddd LL',
                'multiDaySeparator' => '-',
                'multiDaySameMonth' => [
                    'from' => 'D.',
                    'to' => 'LL'
                ],
                'multiDayDifferentMonth' => [
                    'from' => 'D. MMMM',
                    'to' => 'LL'
                ],
                'multiDayDifferentYear' => [
                    'from' => 'LL',
                    'to' => 'LL'
                ]
            ],
            'time' => [
                'prefix' => 'kl. ',
                'postfix' => '',
                'spanSeparator' => ' - ',
                'format' => 'HH:mm'
            ]
        ]
    ]
];
