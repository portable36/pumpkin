<?php

namespace Modules\AdvanceReport\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\MenuBuilder\Http\Models\MenuItems;

class MenuItemsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        addMenuItem('admin', 'Advance Reports', [
            'link' => 'advance-reports',
            'params' => '{"permission":"Modules\\\\AdvanceReport\\\\Http\\\\Controllers\\\\AdvanceReportController@index","route_name":["advance-reports", "advance-reports.show"]}',
            'sort' => 56,
            'icon' => 'fas fa-chart-bar',
        ]);
        addMenuItem('vendor', 'Advance Reports', [
            'link' => 'advance-reports',
            'params' => '{"permission":"Modules\\\\AdvanceReport\\\\Http\\\\Controllers\\\\Vendor\\\\AdvanceReportController@index","route_name":["vendor.advance-reports", "vendor.advance-reports.show"]}',
            'sort' => 10,
            'icon' => 'fas fa-chart-bar',
        ]);
        addMenuItem('saas vendor', 'Advance Reports', [
            'link' => 'advance-reports',
            'params' => '{"permission":"Modules\\\\AdvanceReport\\\\Http\\\\Controllers\\\\Vendor\\\\AdvanceReportController@index","route_name":["vendor.advance-reports", "vendor.advance-reports.show"]}',
            'sort' => 11,
            'icon' => 'fas fa-chart-bar',
        ]);

        MenuItems::where('link', 'advance-reports')->update([
            'label' => '{"en":"Advance Reports","bn":"অ্যাডভান্স রিপোর্ট","fr":"Rapports avancés","zh":"高级报告","ar":"تقارير متقدمة","be":"Пашыраныя справаздачы","bg":"Разширени доклади","ca":"Informes avançats","et":"Täpsemad aruanded","nl":"Geavanceerde rapporten"}',
        ]);
    }
}
