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



    /**
     * Create a new service instance.
     *
     * @return void
     */
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
     * @param String  $setting_id
     * @return Setting
     *
     * @throws NotFoundEntityException
     */
    public function find(string $setting_id)
    {
        $entity = $this->repository->find($setting_id);

        if (empty($entity))
            throw new NotFoundEntityException($entity);

        return $entity;
    }

    /**
     * @param Setting|String  $source
     * @return Setting
     *
     * @throws NotExpectedEntityException
     */
    public function findBySource($source)
    {
        if (is_string($source))
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
     * @param Setting|String  $source
     * @param String          $setting_id
     * @return Bool
     */
    public function clearItems($source, string $setting_id): bool
    {
        $setting = $this->findBySource($source);

        return $setting->items()->delete();
    }

    /**
     * @param String  $host_type
     * @param String  $host_id
     * @param String  $area
     * @param String  $region
     * @param String  $district
     * @param String  $attribute
     * @param Float   $min
     * @param Float   $max
     * @return Bool
     *
     * @throws NotExpectedEntityException
     */
    public function checkOverlap(?string $host_type, ?string $host_id, string $area, ?string $region, ?string $district, string $attribute, float $min, float $max): bool
    {
        if (!is_numeric($min))
            throw new NotExpectedEntityException($min);

        $items = $this->repository_item->getItemsForCheck($host_type, $host_id, $area, $region, $district, $attribute);

        foreach ($items as $item) {
            if (
                $min >= $item->min
                && $min <= $item->max
            ) {
                return true;
            } elseif (
                $min <= $item->min
                && $max >= $item->min
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param String  $host_type
     * @param String  $host_id
     * @param String  $area
     * @param String  $region
     * @param String  $district
     * @param String  $attribute
     * @param Int     $nums
     * @return Array
     *
     * @throws NotExpectedEntityException
     */
    public function getItemForCalculate(?string $host_type, ?string $host_id, string $area, ?string $region, ?string $district, string $attribute, int $nums): ?array
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
     * @param String  $host_type
     * @param String  $host_id
     * @param String  $area
     * @param String  $region
     * @param String  $district
     * @param String  $attribute
     * @param Int     $nums
     * @param Float   $target
     * @return Float
     *
     * @throws NotExpectedEntityException
     */
    public function calculate(?string $host_type, ?string $host_id, string $area, ?string $region, ?string $district, string $attribute, int $nums, float $target): float
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
                    return (float) $item['value'];
                case '+=':
                    return (float) ($target + $item['value']);
                case '-=':
                    return (float) ($target - $item['value']);
                case '*=':
                    return (float) ($target * $item['value']);
                case '/=':
                    return (float) ($target / $item['value']);
                default:
                    throw new NotExpectedEntityException($item['operator']);
            }
        }
    }
}
