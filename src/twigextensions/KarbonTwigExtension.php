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
    protected $timeZone;

    // Public Methods
    // =========================================================================

    public function __construct()
    {
        $this->timeZone = Craft::$app->getTimeZone(); // @todo: consider using config, this is global for all Craft sites.
        $this->locale = self::fromIETFtoPosix(Craft::$app->sites->currentSite->language);
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
        return Carbon::instance($date)->locale($this->locale)->toImmutable();
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
        $dateString = 'L'; // @todo: get fallback from config?
        $carbonFrom = $this->getImmutableCarbon($dateFrom);

        if ($dateTo === null) {
            // One day, just use dateFrom.
            $format = 'dddd LL'; // @todo: get from plugin config.
            $dateString = $carbonFrom->isoFormat($format);

        } else {
            // Both from and to are available

            $carbonTo = $this->getImmutableCarbon($dateTo);

            if ($carbonFrom->isSameDay($carbonTo)) {
                // The same day
                $format = 'dddd LL'; // @todo: get from config.
                $dateString = $carbonFrom->isoFormat($format);

            } elseif ($carbonFrom->isSameMonth($carbonTo)) {
                // Not the same day, but the same month
                $format1 = 'D.'; // @todo: get from config.
                $format2 = 'LL'; // @todo: get from config.
                $dateString = $carbonFrom->isoFormat($format1).'-'.$carbonTo->isoFormat($format2); // @todo: date separator from config

            } elseif (($carbonFrom->isSameMonth($carbonTo) === false) && $carbonFrom->isSameYear($carbonTo)) {
                // Not the same month, but the same year
                $format1 = 'D. MMMM'; // @todo: get from config.
                $format2 = 'LL'; // @todo: get from config.
                $dateString = $carbonFrom->isoFormat($format1).'-'.$carbonTo->isoFormat($format2);

            } elseif (($carbonFrom->isSameYear($carbonTo) === false)) {
                // Not the same year
                $format1 = 'LL'; // @todo: get from config.
                $format2 = 'LL'; // @todo: get from config.
                $dateString = $carbonFrom->isoFormat($format1).'-'.$carbonTo->isoFormat($format2);

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
        return '13:37'; // @todo get from config
    }


}
