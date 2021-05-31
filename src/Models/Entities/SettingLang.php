<?php

namespace WalkerChiu\MallTableRate\Models\Entities;

use WalkerChiu\Core\Models\Entities\Lang;

class SettingLang extends Lang
{
    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        $this->table = config('wk-core.table.mall-tablerate.settings_lang');

        parent::__construct($attributes);
    }
}
