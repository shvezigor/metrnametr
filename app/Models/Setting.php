<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    const TYPE_STRING = 0;
    const TYPE_ARRAY = 1;

    protected $table = 'settings';

    public $timestamps = true;

    protected $fillable = [
        'key',
        'type',
        'value',
    ];

    static function getValue($key) {
        $model = self::where('key', $key)->first();

        if (!$model) {
            return '';
        }

        $type = $model->type;

        if ($type === self::TYPE_STRING) {
            return $model->value;
        } elseif ($type === self::TYPE_ARRAY) {
            return json_decode($model->value, true);
        } else {
            return $model->value;
        }
    }

    static function setValue($key, $value) {

        $model = self::where('key', $key)->first();

        if (!$model) {
            return false;
        }

        $type = $model->type;

        if ($type === self::TYPE_STRING) {
            // TODO: Alright
        } elseif ($type === self::TYPE_ARRAY) {
            $value = json_encode($value);
        } else {
            // TODO: Alright
        }

        $model->update([
            'value' => $value,
        ]);

        return true;
    }

    static function existValue($key) {
        $model = self::where('key', $key)->first();

        if (!$model) {
            return false;
        }

        $value = $model->value;
        $type = $model->type;

        if ($type === self::TYPE_ARRAY && $value !== null) {
            $value = json_encode($value);
        }

        return $value && $value !== null;
    }
}
