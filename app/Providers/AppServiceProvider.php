<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ফাংশনটি register() মেথডে নিয়ে আসা হলো
        if (!function_exists('theme_vendor_assets')) {
            function theme_vendor_assets(string $vendorName, string $type = 'js'): array
            {
                $vendorConfig = config("theme.KT_THEME_VENDORS.{$vendorName}");

                if (empty($vendorConfig) || empty($vendorConfig[$type])) {
                    return [];
                }

                return $vendorConfig[$type];
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // // Metronic Theme Vendor Assets Helper Function
        // if (!function_exists('theme_vendor_assets')) {
        //     /**
        //      * Get the list of assets (CSS/JS) for a specific vendor from the theme config.
        //      *
        //      * @param string $vendorName The key of the vendor (e.g., 'datatables').
        //      * @param string $type The type of asset ('js' or 'css').
        //      * @return array
        //      */
        //     function theme_vendor_assets(string $vendorName, string $type = 'js'): array
        //     {
        //         // config/theme.php ফাইল থেকে ভেন্ডরের কনফিগারেশন লোড করা হচ্ছে
        //         $vendorConfig = config("theme.KT_THEME_VENDORS.{$vendorName}");

        //         if (empty($vendorConfig) || empty($vendorConfig[$type])) {
        //             return [];
        //         }

        //         return $vendorConfig[$type];
        //     }
        // }
    }
}