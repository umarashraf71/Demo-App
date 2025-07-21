<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('fixed_iban', function ($attribute, $value, $parameters, $validator) {
            $iban = preg_replace('/[^a-zA-Z0-9]/', '', $value);
            return strlen($iban) === 24;
        });
        Validator::replacer('fixed_iban', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'IBAN must be a valid 24-digit IBAN.');
        });

        // Loader Alias
        $loader = AliasLoader::getInstance();
        // SANCTUM CUSTOM PERSONAL-ACCESS-TOKEN
        $loader->alias(\Laravel\Sanctum\PersonalAccessToken::class, \App\Models\Sanctum\PersonalAccessToken::class);
    }
}
