<?php

namespace WalkerChiu\MallTableRate\Models\Services;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Exceptions\NotExpectedEntityException;
use WalkerChiu\Core\Models\Exceptions\NotFoundEntityException;
use WalkerChiu\Core\Models\Services\CheckExistTrait;

class TableRateService
{
    use CheckExistTrait;

    protected $repository;
    protected $repository_item;

    public function __construct()
    {
        $this->repository      = App::make(config('wk-core.class.mall-tablerate.settingRepository'));
        $this->repository_item = App::make(config('wk-core.class.mall-tablerate.itemRepository'));
    }

    /*
    |--------------------------------------------------------------------------
    | Get Setting
    |--------------------------------------------------------------------------
    */

    /**
     * @param  Int $setting_id
     * @return Setting
     */
    public function find(Int $setting_id)
    {
        $entity = $this->repository->find($setting_id);

        if (empty($entity))
            throw new NotFoundEntityException($entity);

        return $entity;
    }

    /**
     * @param  Setting|Int $source
     * @return Setting
     */
    public function findBySource($source)
    {
        if (is_integer($source))
            $entity = $this->find($source);
        elseif (is_a($source, config('wk-core.class.mall-tablerate.setting')))
            $entity = $source;
        else
            throw new NotExpectedEntityException($source);

        return $entity;
    }



    /*
    |--------------------------------------------------------------------------
    | Operation
    |--------------------------------------------------------------------------
    */

    /**
     * @param Setting|Int $source
     * @param Int $setting_id
     * @return Boolean
     */
    public function clearItems($source, $setting_id)
    {
        $setting = $this->findBySource($source);

        return $setting->items()->delete();
    }

    /**
     * @param String $host_type
     * @param Int    $host_id
     * @param String $area
     * @param String $region
     * @param String $district
     * @param String $attribute
     * @param Number $min
     * @param Number $max
     * @return Boolean
     */
    public function checkOverlap($host_type, $host_id, String $area, $region, $district, String $attribute, $min, $max)
    {
        if (!is_numeric($min))
            throw new NotExpectedEntityException($min);

        $items = $this->repository_item->getItemsForCheck($host_type, $host_id, $area, $region, $district, $attribute);

        foreach ($items as $item) {
            if ($min >= $item->min && $min <= $item->max) return true;
            if ($min <= $item->min && $max >= $item->min) return true;
        }

        return false;
    }

    /**
     * @param String $host_type
     * @param Int    $host_id
     * @param String $area
     * @param String $region
     * @param String $district
     * @param String $attribute
     * @param Number $nums
     * @return Boolean
     */
    public function getItemForCalculate($host_type, $host_id, String $area, $region, $district, String $attribute, $nums)
    {
        if (!is_numeric($nums))
            throw new NotExpectedEntityException($nums);

        $item = $this->repository_item->getItemForCalculate($host_type, $host_id, $area, $region, $district, $attribute, $nums);

        if (empty($item))
            return null;
        else
            return [
                'id'       => $item->id,
                'operator' => $item->operator,
                'value'    => $item->value
            ];
    }

    /**
     * @param String $host_type
     * @param Int    $host_id
     * @param String $area
     * @param String $region
     * @param String $district
     * @param String $attribute
     * @param Number $nums
     * @param Number $target
     * @return Null|Number
     */
    public function calculate($host_type, $host_id, String $area, $region, $district, String $attribute, $nums, $target)
    {
        if (!is_numeric($nums))
            throw new NotExpectedEntityException($nums);
        if (!is_numeric($target))
            throw new NotExpectedEntityException($target);

        $item = $this->getItemForCalculate($host_type, $host_id, $area, $region, $district, $attribute, $nums);

        if (empty($item)) {
            return null;
        } else {
            switch ($item['operator']) {
                case '=':
                    return $item['value'];

                case '+=':
                    return $target + $item['value'];

                case '-=':
                    return $target - $item['value'];

                case '*=':
                    return $target * $item['value'];

                case '/=':
                    return $target / $item['value'];

                default:
                    throw new NotExpectedEntityException($item['operator']);
            }
        }
    }
}
