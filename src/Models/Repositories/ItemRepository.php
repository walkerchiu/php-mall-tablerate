<?php

namespace WalkerChiu\MallTableRate\Models\Repositories;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Exceptions\NotExpectedmodelException;
use WalkerChiu\Core\Models\Forms\FormTrait;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryTrait;
use WalkerChiu\Core\Models\Services\PackagingFactory;

class ItemRepository extends Repository
{
    use FormTrait;
    use RepositoryTrait;

    protected $instance;



    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->instance = App::make(config('wk-core.class.mall-tablerate.item'));
    }

    /**
     * @param Array  $data
     * @param Bool   $auto_packing
     * @return Array|Collection|Eloquent
     */
    public function list(array $data, $auto_packing = false)
    {
        $instance = $this->instance;

        $data = array_map('trim', $data);
        $repository = $instance->when($data, function ($query, $data) {
                                    return $query->unless(empty($data['id']), function ($query) use ($data) {
                                                return $query->where('id', $data['id']);
                                            })
                                            ->unless(empty($data['setting_id']), function ($query) use ($data) {
                                                return $query->where('setting_id', $data['setting_id']);
                                            })
                                            ->unless(empty($data['area']), function ($query) use ($data) {
                                                return $query->where('area', $data['area']);
                                            })
                                            ->unless(empty($data['region']), function ($query) use ($data) {
                                                return $query->where('region', $data['region']);
                                            })
                                            ->unless(empty($data['district']), function ($query) use ($data) {
                                                return $query->where('district', $data['district']);
                                            })
                                            ->unless(empty($data['attribute']), function ($query) use ($data) {
                                                return $query->where('attribute', $data['attribute']);
                                            })
                                            ->unless(empty($data['min']), function ($query) use ($data) {
                                                return $query->where('min', $data['min']);
                                            })
                                            ->unless(empty($data['max']), function ($query) use ($data) {
                                                return $query->where('max', $data['max']);
                                            })
                                            ->unless(empty($data['operator']), function ($query) use ($data) {
                                                return $query->where('operator', $data['operator']);
                                            })
                                            ->unless(empty($data['value']), function ($query) use ($data) {
                                                return $query->where('value', $data['value']);
                                            });
                                })
                                ->orderBy('updated_at', 'DESC');

        if ($auto_packing) {
            $factory = new PackagingFactory(config('wk-mall-tablerate.output_format'), config('wk-mall-tablerate.pagination.pageName'), config('wk-mall-tablerate.pagination.perPage'));
            return $factory->output($repository);
        }

        return $repository;
    }

    /**
     * @param String  $host_type
     * @param String  $host_id
     * @param String  $area
     * @param String  $region
     * @param String  $district
     * @param String  $attribute
     * @return Collection
     *
     * @throws NotExpectedmodelException
     */
    public function getItemsForCheck($host_type, $host_id, string $area, $region, $district, string $attribute)
    {
        if (!is_numeric($nums))
            throw new NotExpectedmodelException($nums);

        $instance = $this->instance;
        $records = $instance->unless(is_null($host_type), function ($query) use ($host_type) {
                                return $query->where('host_type', $host_type);
                            })
                            ->unless(is_null($host_id), function ($query) use ($host_id) {
                                return $query->where('host_id', $host_id);
                            })
                            ->where('area', $area)
                            ->unless(is_null($region), function ($query) use ($region) {
                                return $query->where('region', $region);
                            })
                            ->unless(is_null($district), function ($query) use ($district) {
                                return $query->where('district', $district);
                            })
                            ->where('attribute', $attribute)
                            ->orderBy('min', 'ASC')
                            ->get();

        return $records;
    }

    /**
     * @param String  $host_type
     * @param String  $host_id
     * @param String  $area
     * @param String  $region
     * @param String  $district
     * @param String  $attribute
     * @param Int     $nums
     * @return Item
     *
     * @throws NotExpectedmodelException
     */
    public function getItemForCalculate($host_type, $host_id, string $area, $region, $district, string $attribute, $nums)
    {
        if (!is_numeric($nums))
            throw new NotExpectedmodelException($nums);

        $instance = $this->instance;
        $record = $instance->unless(is_null($host_type), function ($query) use ($host_type) {
                                return $query->where('host_type', $host_type);
                            })
                            ->unless(is_null($host_id), function ($query) use ($host_id) {
                                return $query->where('host_id', $host_id);
                            })
                            ->where('area', $area)
                            ->unless(is_null($region), function ($query) use ($region) {
                                return $query->where('region', $region);
                            })
                            ->unless(is_null($district), function ($query) use ($district) {
                                return $query->where('district', $district);
                            })
                            ->where('attribute', $attribute)
                            ->where('min', '<=', $nums)
                            ->orderBy('min', 'DESC')
                            ->first();

        return $record;
    }

    /**
     * @param Item  $instance
     * @return Array
     */
    public function show($instance): array
    {
        if (empty($instance))
            return [
                'id'         => '',
                'setting_id' => '',
                'area'       => '',
                'region'     => '',
                'district'   => '',
                'attribute'  => '',
                'min'        => '',
                'max'        => '',
                'operator'   => '',
                'value'      => '',
                'updated_at' => ''
            ];

        $this->setmodel($instance);

        return [
              'id'         => $instance->id,
              'setting_id' => $instance->setting_id,
              'area'       => $instance->area,
              'region'     => $instance->region,
              'district'   => $instance->district,
              'attribute'  => $instance->attribute,
              'min'        => $instance->min,
              'max'        => $instance->max,
              'operator'   => $instance->operator,
              'value'      => $instance->value,
              'updated_at' => $instance->updated_at
        ];
    }
}
