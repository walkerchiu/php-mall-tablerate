<?php

namespace WalkerChiu\MallTableRate\Models\Observers;

class ItemObserver
{
    /**
     * Handle the model "retrieved" event.
     *
     * @param Model  $model
     * @return void
     */
    public function retrieved($model)
    {
        //
    }

    /**
     * Handle the model "creating" event.
     *
     * @param Model  $model
     * @return void
     */
    public function creating($model)
    {
        //
    }

    /**
     * Handle the model "created" event.
     *
     * @param Model  $model
     * @return void
     */
    public function created($model)
    {
        //
    }

    /**
     * Handle the model "updating" event.
     *
     * @param Model  $model
     * @return void
     */
    public function updating($model)
    {
        //
    }

    /**
     * Handle the model "updated" event.
     *
     * @param Model  $model
     * @return void
     */
    public function updated($model)
    {
        //
    }

    /**
     * Handle the model "saving" event.
     *
     * @param Model  $model
     * @return void
     */
    public function saving($model)
    {
        if (
            config('wk-core.class.mall-tablerate.item')
                ::where('id', '<>', $model->id)
                ->where('setting_id', $model->setting_id)
                ->where('area', $model->area)
                ->where('district', $model->district)
                ->where('attribute', $model->attribute)
                ->where('min', $model->min)
                ->exists()
        )
            return false;
    }

    /**
     * Handle the model "saved" event.
     *
     * @param Model  $model
     * @return void
     */
    public function saved($model)
    {
        //
    }

    /**
     * Handle the model "deleting" event.
     *
     * @param Model  $model
     * @return void
     */
    public function deleting($model)
    {
        //
    }

    /**
     * Handle the model "deleted" event.
     *
     * Its Lang will be automatically removed by database.
     *
     * @param Model  $model
     * @return void
     */
    public function deleted($model)
    {
        if (!config('wk-mall-tablerate.soft_delete')) {
            $model->forceDelete();
        }


        if ($model->isForceDeleting()) {
        }
    }

    /**
     * Handle the model "restoring" event.
     *
     * @param Model  $model
     * @return void
     */
    public function restoring($model)
    {
        //
    }

    /**
     * Handle the model "restored" event.
     *
     * @param Model  $model
     * @return void
     */
    public function restored($model)
    {
        //
    }
}
