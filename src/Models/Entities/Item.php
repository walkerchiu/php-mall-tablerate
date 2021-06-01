<?php

namespace WalkerChiu\MallTableRate\Models\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\UuidModel;

class Item extends UuidModel
{
    use SoftDeletes;

    protected $fillable = [
        'setting_id',
        'area', 'region', 'district', 'attribute',
        'min', 'max',
        'operator', 'value'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        $this->table = config('wk-core.table.mall-tablerate.items');

        parent::__construct($attributes);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function setting()
    {
        return $this->belongsTo(config('wk-core.class.mall-tablerate.setting'), 'setting_id', 'id');
    }
}
