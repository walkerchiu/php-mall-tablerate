<?php

namespace WalkerChiu\MallTableRate\Models\Repositories;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Exceptions\NotExpectedEntityException;
use WalkerChiu\Core\Models\Forms\FormTrait;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryTrait;

class ItemRepository extends Repository
{
    use FormTrait;
    use RepositoryTrait;

    protected $entity;
    protected $morphType;

    public function __construct()
    {
        $this->entity = App::make(config('wk-core.class.mall-tablerate.item'));
    }

    /**
     * @param Array $data
     * @param Int   $page
     * @param Int   $nums per page
     * @return Array
     */
    public function list(Array $data, $page = null, $nums = null)
    {
        $this->assertForPagination($page, $nums);

        $entity = $this->entity;

        $data = array_map('trim', $data);
        $records = $entity->when($data, function ($query, $data) {
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
                            ->orderBy('updated_at', 'DESC')
                            ->get()
                            ->when(is_integer($page) && is_integer($nums), function ($query) use ($page, $nums) {
                                return $query->forPage($page, $nums);
                            });
        $list = [];
        foreach ($records as $record) {
            $data = $record->toArray();
            array_push($list, $data);
        }

        return $list;
    }

    /**
     * @param String $host_type
     * @param String $host_id
     * @param String $area
     * @param String $region
     * @param String $district
     * @param String $attribute
     * @return Array
     */
    public function getItemsForCheck($host_type, $host_id, String $area, $region, $district, String $attribute)
    {
        if (!is_numeric($nums))
            throw new NotExpectedEntityException($nums);

        $entity = $this->entity;
        $records = $entity->unless(is_null($host_type), function ($query) use ($host_type) {
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
     * @param String $host_type
     * @param String $host_id
     * @param String $area
     * @param String $region
     * @param String $district
     * @param String $attribute
     * @param Number $nums
     * @return Item
     */
    public function getItemForCalculate($host_type, $host_id, String $area, $region, $district, String $attribute, $nums)
    {
        if (!is_numeric($nums))
            throw new NotExpectedEntityException($nums);

        $entity = $this->entity;
        $record = $entity->unless(is_null($host_type), function ($query) use ($host_type) {
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
     * @param Item $entity
     * @return Array
     */
    public function show($entity)
    {
        if (empty($entity))
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

        $this->setEntity($entity);

        return [
              'id'         => $entity->id,
              'setting_id' => $entity->setting_id,
              'area'       => $entity->area,
              'region'     => $entity->region,
              'district'   => $entity->district,
              'attribute'  => $entity->attribute,
              'min'        => $entity->min,
              'max'        => $entity->max,
              'operator'   => $entity->operator,
              'value'      => $entity->value,
              'updated_at' => $entity->updated_at
        ];
    }
}
