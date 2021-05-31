<?php

namespace WalkerChiu\MallTableRate\Models\Repositories;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Forms\FormHasHostTrait;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryHasHostTrait;

class SettingRepository extends Repository
{
    use FormHasHostTrait;
    use RepositoryHasHostTrait;

    protected $entity;
    protected $morphType;

    public function __construct()
    {
        $this->entity = App::make(config('wk-core.class.mall-tablerate.setting'));
    }

    /**
     * @param String  $host_type
     * @param Int     $host_id
     * @param String  $code
     * @param Array   $data
     * @param Int     $page
     * @param Int     $nums per page
     * @param Boolean $is_enabled
     * @param String  $target
     * @param Boolean $target_is_enabled
     * @return Array
     */
    public function list($host_type, $host_id, String $code, Array $data, $page = null, $nums = null, $is_enabled = null, $target = null, $target_is_enabled = null)
    {
        $this->assertForPagination($page, $nums);

        if (empty($host_type) || empty($host_id)) {
            $entity = $this->entity;
        } else {
            $entity = $this->baseQueryForRepository($host_type, $host_id, $target, $target_is_enabled);
        }
        if ($is_enabled === true)      $entity = $entity->ofEnabled();
        elseif ($is_enabled === false) $entity = $entity->ofDisabled();

        $data = array_map('trim', $data);
        $records = $entity->with(['langs' => function ($query) use ($code) {
                                $query->ofCurrent()
                                      ->ofCode($code);
                            }])
                            ->when($data, function ($query, $data) {
                                return $query->unless(empty($data['id']), function ($query) use ($data) {
                                            return $query->where('id', $data['id']);
                                        })
                                        ->unless(empty($data['type']), function ($query) use ($data) {
                                            return $query->where('type', $data['type']);
                                        })
                                        ->unless(empty($data['serial']), function ($query) use ($data) {
                                            return $query->where('serial', $data['serial']);
                                        })
                                        ->unless(empty($data['identifier']), function ($query) use ($data) {
                                            return $query->where('identifier', $data['identifier']);
                                        })
                                        ->unless(empty($data['name']), function ($query) use ($data) {
                                            return $query->whereHas('langs', function($query) use ($data) {
                                                $query->ofCurrent()
                                                      ->where('key', 'name')
                                                      ->where('value', 'LIKE', "%".$data['name']."%");
                                            });
                                        })
                                        ->unless(empty($data['description']), function ($query) use ($data) {
                                            return $query->whereHas('langs', function($query) use ($data) {
                                                $query->ofCurrent()
                                                      ->where('key', 'description')
                                                      ->where('value', 'LIKE', "%".$data['description']."%");
                                            });
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
            array_push($list,
                array_merge($data, [
                    'name'        => $record->findLangByKey('name'),
                    'description' => $record->findLangByKey('description')
                ])
            );
        }

        return $list;
    }

    /**
     * @param Setting $entity
     * @param String|Array $code
     * @return Array
     */
    public function show($entity, $code)
    {
        $data = [
            'id' => $entity ? $entity->id : '',
            'basic' => []
        ];

        if (empty($entity))
            return $data;

        $this->setEntity($entity);

        if (is_string($code)) {
            $data['basic'] = [
                'host_type'   => $entity->host_type,
                'host_id'     => $entity->host_id,
                'type'        => $entity->type,
                'serial'      => $entity->serial,
                'identifier'  => $entity->identifier,
                'name'        => $entity->findLang($code, 'name'),
                'description' => $entity->findLang($code, 'description'),
                'is_enabled'  => $entity->is_enabled,
                'updated_at'  => $entity->updated_at,
                'items'       => $entity->items
            ];

        } elseif (is_array($code)) {
            foreach ($code as $language) {
                $data['basic'][$language] = [
                    'host_type'   => $entity->host_type,
                    'host_id'     => $entity->host_id,
                    'type'        => $entity->type,
                    'serial'      => $entity->serial,
                    'identifier'  => $entity->identifier,
                    'name'        => $entity->findLang($language, 'name'),
                    'description' => $entity->findLang($language, 'description'),
                    'is_enabled'  => $entity->is_enabled,
                    'updated_at'  => $entity->updated_at,
                    'items'       => $entity->items
                ];
            }
        }

        return $data;
    }
}
