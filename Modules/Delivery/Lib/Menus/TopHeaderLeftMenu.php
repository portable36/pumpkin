<?php

/**
 * @author TechVillage <mailto:support@techvill.org>
 *
 * @contributor Md. Mostafijur Rahman <[mailto:mostafijur.techvill@gmail.com]>
 *
 * @created 18-10-2023
 */

namespace Modules\Delivery\Lib\Menus;

class TopHeaderLeftMenu
{
    /**
     * Get menu items
     */
    public static function get(): array
    {
        $items = [
            [
                'item' => '<a href="javascript:" class="full-screen text-decoration-none ltr:ps-2 rtl:pe-2" onclick="javascript:toggleFullScreen()"><i class="feather icon-maximize"></i></a>',
                'position' => '10',
                'visibility' => true,
            ],
            [
                'item' => '<a class="d-flex align-items-center text-decoration-none" href="' . route('site.index') . '" target="_blank">
                <i class="feather icon-globe"></i><span class="list-curent-color ltr:ms-2 rtl:me-2">' . __('Visit Site') . '</span></a>',
                'position' => '20',
                'visibility' => true,
            ],
            [
                'item' => '<a class="d-flex align-items-center text-decoration-none" href="' . route('site.dashboard') . '" target="_blank">
                <i class="feather icon-external-link"></i><span class="ltr:ms-2 rtl:me-2 list-curent-color">' . __('Customer Panel') . '</span></a>',
                'position' => '30',
                'visibility' => true,
            ],
        ];

        $items = apply_filters('delivery_top_header_left_menu', $items);

        // Sort items based on position, placing items without a position at the beginning
        usort($items, function ($a, $b) {
            $positionA = isset($a['position']) ? $a['position'] : -1;
            $positionB = isset($b['position']) ? $b['position'] : -1;

            return $positionA <=> $positionB;
        });

        return $items;
    }
}
