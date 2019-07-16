<?php
/**
 * karbon plugin for Craft CMS 3.x
 *
 * Brings the power of date / datetime handling from Carbon to Twig, hopefully helping to answer the age old question of: what (date)-time is it?
 *
 * @link      https://github.com/kbergha
 * @copyright Copyright (c) 2019 Knut Erik Berg-Hansen
 */

namespace \karbon\models;

use \karbon\Karbon;

use Craft;
use craft\base\Model;

/**
 * @author    test
 * @package   Karbon
 * @since     0.0.1
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $someAttribute = 'Some Default';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['someAttribute', 'string'],
            ['someAttribute', 'default', 'value' => 'Some Default'],
        ];
    }
}
