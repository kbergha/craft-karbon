<?php
/**
 * karbon plugin for Craft CMS 3.x
 *
 * Brings the power of date / datetime handling from Carbon to Twig, hopefully helping to answer the age old question of: what (date)-time is it?
 *
 * @link      https://github.com/kbergha
 * @copyright Copyright (c) 2019 Knut Erik Berg-Hansen
 */

namespace kbergha\karbon\twigextensions;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use craft\fields\Date;
use kbergha\karbon\Karbon;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use DateTime;

use Craft;

/**
 * Twig can be extended in many ways; you can add extra tags, filters, tests, operators,
 * global variables, and functions. You can even extend the parser itself with
 * node visitors.
 *
 * http://twig.sensiolabs.org/doc/advanced.html
 *
 * @author    Knut Erik Berg-Hansen
 * @package   Karbon
 * @since     0.0.1
 */
class KarbonTwigExtension extends AbstractExtension
{
    protected $locale;
    protected $dateConfiguration = [];

    // Public Methods
    // =========================================================================

    public function __construct()
    {
        $this->locale = Craft::$app->sites->currentSite->language;
        $this->resolveLocaleConfig($this->locale);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'Karbon';
    }

    /**
     * Convert from IETF locale codes from Craft to Posix style locale code. Ie: from en-GB to en_GB
     * Carbon uses Posix-style when including translation files.
     *
     * @param $locale
     * @return string
     */
    static public function fromIETFtoPosix($locale) {
        return str_replace('-','_', $locale);
    }

    /**
     * Return a CarbonImmutable-object with the correct locale set.
     *
     * @param DateTime $date
     * @return CarbonImmutable
     */
    protected function getImmutableCarbon(DateTime $date = null) {
        // @todo timezone
        if ($date === null) {
            $date = Carbon::now();
        }

        $locale = self::fromIETFtoPosix($this->locale);
        return Carbon::instance($date)->locale($locale)->toImmutable();
    }

    /**
     * @param null $locale
     */
    protected function resolveLocaleConfig($locale = null)
    {
        $config = Karbon::getInstance()->getSettings()->dateConfiguration;
        $this->dateConfiguration = $config['default'];

        if (! is_null($locale) && array_key_exists($locale, $config)) {
            $this->dateConfiguration = $config[$locale];
        }
    }


    /**
     *
     * Functions or filters callable from Twig
     *
     */


    /**
     * Returns an array of Twig filters, used in Twig templates via:
     *
     *      {{ 'something' | someFilter }}
     *
     * @return array
     */
    public function getFilters()
    {
        return [
            // new TwigFilter('karbonNowLocale', [$this, 'nowLocale']),
        ];
    }

    /**
     * Returns an array of Twig functions, used in Twig templates via:
     *
     *      {% set this = someFunction('something') %}
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('karbonGetDateString', [$this, 'getDateString']),
            new TwigFunction('karbonGetTimeString', [$this, 'getTimeString']),
            new TwigFunction('karbonIsoFormat',     [$this, 'isoFormat']),
        ];
    }

    /**
     * Get a date string from one date to another.
     *
     * @param DateTime $dateFrom
     * @param DateTime|null $dateTo
     * @return string
     */
    public function getDateString(DateTime $dateFrom, DateTime $dateTo = null)
    {
        $config = $this->dateConfiguration['date'];
        $carbonFrom = $this->getImmutableCarbon($dateFrom);
        $dateString = $carbonFrom->isoFormat($config['fallback']);

        if ($dateTo === null) {
            // One day, just use dateFrom.
            $dateString = $carbonFrom->isoFormat($config['oneDay']);

        } else {
            // Both from and to are available
            $carbonTo = $this->getImmutableCarbon($dateTo);

            if ($carbonFrom->isSameDay($carbonTo)) {
                // The same day
                $dateString = $carbonFrom->isoFormat($config['oneDay']);

            } elseif ($carbonFrom->isSameMonth($carbonTo)) {
                // Not the same day, but the same month
                $dateString = $carbonFrom->isoFormat($config['multiDaySameMonth']['from']);
                $dateString .= $config['multiDaySeparator'];
                $dateString .= $carbonTo->isoFormat($config['multiDaySameMonth']['to']);

            } elseif (($carbonFrom->isSameMonth($carbonTo) === false) && $carbonFrom->isSameYear($carbonTo)) {
                // Not the same month, but the same year
                $dateString = $carbonFrom->isoFormat($config['multiDayDifferentMonth']['from']);
                $dateString .= $config['multiDaySeparator'];
                $dateString .= $carbonTo->isoFormat($config['multiDayDifferentMonth']['to']);

            } elseif (($carbonFrom->isSameYear($carbonTo) === false)) {
                // Not the same year
                $dateString = $carbonFrom->isoFormat($config['multiDayDifferentYear']['from']);
                $dateString .= $config['multiDaySeparator'];
                $dateString .= $carbonTo->isoFormat($config['multiDayDifferentYear']['to']);

            }

        }

        return $dateString;
    }

    public function isoFormat(DateTime $date, $format = 'LL') {
        $carbon = $this->getImmutableCarbon($date);
        return $carbon->isoFormat($format);
    }

    /**
     * @todo: document why this is useful, and why it's useful to have it's own field. Author experience.
     *
     * @param DateTime $timeFrom
     * @param DateTime|null $timeTo
     * @return string
     */
    public function getTimeString(DateTime $timeFrom, DateTime $timeTo = null)
    {

        $config = $this->dateConfiguration['time'];
        $carbonFrom = $this->getImmutableCarbon($timeFrom);

        if ($timeTo === null) {
            // One day, just use timeFrom.
            $timeString = $config['prefix'];
            $timeString .= $carbonFrom->isoFormat($config['format']);
            $timeString .= $config['postfix'];

        } else {
            $carbonTo = $this->getImmutableCarbon($timeTo);

            $timeString = $config['prefix'];
            $timeString .= $carbonFrom->isoFormat($config['format']);
            $timeString .= $config['spanSeparator'];
            $timeString .= $carbonTo->isoFormat($config['format']);
            $timeString .= $config['postfix'];
        }

        return $timeString;
    }


}
