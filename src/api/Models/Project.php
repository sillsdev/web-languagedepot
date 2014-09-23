<?php
namespace App\api\Models;

class Project extends \ActiveRecord\Model
{
    static $connection = 'public';

    const TYPE_UNKNOWN  = 'unknown';
    const TYPE_LIFT     = 'lift';
    const TYPE_FLEX     = 'flex';
    const TYPE_OURWORD  = 'ourword';
    const TYPE_ONESTORY = 'onestory';
    const TYPE_TEST     = 'test';

    public function type() {
        // Type is not a first class field, so attempt to infer the type from:
        // a) Identifier
        // b) Text in description

        // a) Identifier
        $identifier = strtolower($this->identifier);
        $tokens = explode('-', $identifier);
        $c = count($tokens);
        switch ($tokens[$c - 1]) {
            case 'lift':
            case 'dictionary':
                return self::TYPE_LIFT;
            case 'flex':
                return self::TYPE_FLEX;
            case 'test':
                return self::TYPE_TEST;
        }

        // b) Text in description
        $words = array(
            'flex' => self::TYPE_FLEX,
            'wesay' => self::TYPE_LIFT,
            'dictionary' => self::TYPE_LIFT,
            'ourword' => self::TYPE_OURWORD,
            'story' => self::TYPE_ONESTORY,
            'translation' => self::TYPE_OURWORD,
            'test' => self::TYPE_TEST,
        );
        foreach ($words as $word => $type) {
            if (stristr($this->description, $word) !== false) {
                return $type;
            }
        }
        return self::TYPE_UNKNOWN;
    }
}