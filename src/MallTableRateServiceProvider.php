<?php

namespace WalkerChiu\MallTableRate;

use Illuminate\Support\ServiceProvider;

class MallTableRateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
           __DIR__ .'/config/mall-tablerate.php' => config_path('wk-mall-tablerate.php'),
        ], 'config');

        // Publish migration files
        $from = __DIR__ .'/database/migrations/';
        $to   = database_path('migrations') .'/';
        $this->publishes([
            $from .'create_wk_mall_tablerate_table.php'
                => $to .date('Y_m_d_His', time()) .'_create_wk_mall_tablerate_table.php'
        ], 'migrations');

        $this->loadTranslationsFrom(__DIR__.'/translations', 'php-mall-tablerate');
        $this->publishes([
            __DIR__.'/translations' => resource_path('lang/vendor/php-mall-tablerate'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                config('wk-mall-tablerate.command.cleaner')
            ]);
        }

        config('wk-core.class.mall-tablerate.setting')::observe(config('wk-core.class.mall-tablerate.settingObserver'));
        config('wk-core.class.mall-tablerate.settingLang')::observe(config('wk-core.class.mall-tablerate.settingLangObserver'));
        config('wk-core.class.mall-tablerate.item')::observe(config('wk-core.class.mall-tablerate.itemObserver'));
    }

    /**
     * Register the blade directives
     *
     * @return void
     */
    private function bladeDirectives()
    {
    }

    /**
     * Merges user's and package's configs.
     *
     * @return void
     */
    private function mergeConfig()
    {
        if (!config()->has('wk-mall-tablerate')) {
            $this->mergeConfigFrom(
                __DIR__ .'/config/mall-tablerate.php', 'wk-mall-tablerate'
            );
        }

        $this->mergeConfigFrom(
            __DIR__ .'/config/mall-tablerate.php', 'mall-tablerate'
        );
    }

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param String  $path
     * @param String  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        if (
            !(
                $this->app instanceof CachesConfiguration
                && $this->app->configurationIsCached()
            )
        ) {
            $config = $this->app->make('config');
            $content = $config->get($key, []);

            $config->set($key, array_merge(
                require $path, $content
            ));
        }
    }
}
