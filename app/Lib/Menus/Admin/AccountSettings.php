<?php

/**
 * @author TechVillage <mailto:support@techvill.org>
 *
 * @contributor Md. Mostafijur Rahman <[mailto:mostafijur.techvill@gmail.com]>
 *
 * @created 12-10-2023
 */

namespace App\Lib\Menus\Admin;

class AccountSettings
{
    /**
     * Get menu items
     */
    public static function get(): array
    {
        $items = [
            [
                'label' => __('Options'),
                'name' => 'options',
                'href' => route('account.setting.option'),
                'position' => '10',
                'visibility' => auth()->user()?->hasPermission('App\Http\Controllers\AccountSettingController@index'),
            ],
            [
                'label' => __('Single Sign On :x', ['x' => '(SSO)']),
                'name' => 'sso',
                'href' => route('sso.index'),
                'position' => '20',
                'visibility' => auth()->user()?->hasPermission('App\Http\Controllers\SsoController@index'),
            ],
            [
                'label' => __('User Verifications'),
                'name' => 'email_verify_setting',
                'href' => route('emailVerifySetting'),
                'position' => '30',
                'visibility' => auth()->user()?->hasPermission('App\Http\Controllers\EmailController@emailVerifySetting'),
            ],
            [
                'label' => __('Password Strength'),
                'name' => 'password_preference',
                'href' => route('preferences.password'),
                'position' => '40',
                'visibility' => auth()->user()?->hasPermission('App\Http\Controllers\PreferenceController@password'),
            ],
            [
                'label' => __('Roles'),
                'name' => 'role',
                'href' => route('roles.index'),
                'position' => '50',
                'visibility' => auth()->user()?->hasPermission('App\Http\Controllers\RoleController@index'),
            ],
            [
                'label' => __('Notifications'),
                'name' => 'notification',
                'href' => route('notifications.setting'),
                'position' => '60',
                'visibility' => auth()->user()?->hasPermission('App\Http\Controllers\NotificationController@setting'),
            ],
            [
                'label' => __('SSO Client'),
                'name' => 'sso_client',
                'href' => route('sso.client'),
                'position' => '70',
                'visibility' => auth()->user()?->hasPermission('App\Http\Controllers\SsoController@client'),
            ],
            [
                'label' => __('Email/Phone Change Settings'),
                'name' => 'email_phone_change_settings',
                'href' => route('account.setting.emailPhoneChange'),
                'position' => '80',
                'visibility' => auth()->user()?->hasPermission('App\Http\Controllers\AccountSettingController@emailPhoneChange'),
            ],
        ];

        $items = apply_filters('admin_sidebar_configuration_account_settings_menu', $items);

        // Sort items based on position, placing items without a position at the beginning
        usort($items, function ($a, $b) {
            $positionA = isset($a['position']) ? $a['position'] : -1;
            $positionB = isset($b['position']) ? $b['position'] : -1;

            return $positionA <=> $positionB;
        });

        return $items;
    }
}
