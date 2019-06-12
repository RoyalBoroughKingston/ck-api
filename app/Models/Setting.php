<?php

namespace App\Models;

use App\Models\Mutators\SettingMutators;
use App\Models\Relationships\SettingRelationships;
use App\Models\Scopes\SettingScopes;
use Illuminate\Http\JsonResponse;

class Setting extends Model
{
    use SettingMutators;
    use SettingRelationships;
    use SettingScopes;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'key';

    /**
     * Determines if the primary key is a UUID.
     *
     * @var bool
     */
    protected $keyIsUuid = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public static function toResponse(): JsonResponse
    {
        return response()->json([
            'data' => static::all()->mapWithKeys(function (self $setting) {
                return [$setting->key => $setting->value];
            }),
        ]);
    }

    /**
     * @return \App\Models\Setting
     */
    public static function cms(): self
    {
        return static::findOrFail('cms');
    }
}
