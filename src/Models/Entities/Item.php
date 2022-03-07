<?php

namespace WalkerChiu\MallTableRate\Models\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use WalkerChiu\Core\Models\Entities\UuidModel;

class Item extends UuidModel
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var Array
     */
    protected $fillable = [
        'setting_id',
        'area', 'region', 'district', 'attribute',
        'min', 'max',
        'operator', 'value'
    ];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var Array
	 */
    protected $hidden = [
        'deleted_at'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var Array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];



    /**
     * Create a new instance.
     *
     * @param Array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
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
