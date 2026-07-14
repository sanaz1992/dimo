<?php

namespace Modules\ACL\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\ACL\Entities\Permission;
use Modules\ACL\Entities\Role;
use Modules\Core\Helpers\CodeGeneratorHelper;
use Modules\Order\Enums\OrderListTabs;
use Modules\User\Entities\User;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'warehouse_items_list',
                'title' => 'لیست محصولات انبارها',
            ],
            [
                'name' => 'warehouse_items_create',
                'title' => 'درج محصول جدید در انبار',
            ],
            [
                'name' => 'warehouse_items_edit',
                'title' => 'ویرایش محصولات انبار',
            ],
            [
                'name' => 'warehouse_items_stock_edit',
                'title' => 'ویرایش موجودی محصولات انبار',
            ],
            [
                'name' => 'warehouse_custom_fabrics_need_for_orders',
                'title' => 'پارچه های سفارشی مورد نیاز برای سفارشات',
            ],
            [
                'name' => 'raw_materials_list_edit',
                'title' => 'ویرایش یکجا مواد اولیه',
            ],
            [
                'name' => 'colors_list',
                'title' => 'لیست رنگ ها',
            ],
            [
                'name' => 'colors_create',
                'title' => 'ثبت رنگ جدید',
            ],
            [
                'name' => 'colors_edit',
                'title' => 'ویرایش رنگ',
            ],
            [
                'name' => 'sellers_list',
                'title' => 'لیست نمایندگان',
            ],
            [
                'name' => 'sellers_show',
                'title' => 'مشاهده نماینده',
            ],
            [
                'name' => 'sellers_create',
                'title' => 'ایجاد نماینده',
            ],
            [
                'name' => 'sellers_edit',
                'title' => 'ویرایش نماینده',
            ],
            [
                'name' => 'sellers_delete',
                'title' => 'حذف نماینده',
            ],
            [
                'name' => 'seller_levels_list',
                'title' => 'لیست سطوح نماینده ها',
            ],
            [
                'name' => 'seller_levels_create',
                'title' => 'ایجاد سطح نماینده',
            ],
            [
                'name' => 'seller_levels_edit',
                'title' => 'ویرایش سطح نماینده',
            ],
            [
                'name' => 'seller_levels_delete',
                'title' => 'حذف سطح نماینده',
            ],
            [
                'name' => 'seller_levels_sellers_show',
                'title' => 'مشاهده نماینده های هر سطح',
            ],
            [
                'name' => 'products_list',
                'title' => 'لیست محصولات',
            ],
            [
                'name' => 'products_create',
                'title' => 'ایجاد محصول',
            ],
            [
                'name' => 'products_edit',
                'title' => 'ویرایش محصول',
            ],
            [
                'name' => 'products_publish',
                'title' => 'انتشار محصول',
            ],
            [
                'name' => 'intermediate_products_create',
                'title' => 'ایجاد محصول میانی',
            ],
            [
                'name' => 'intermediate_products_edit',
                'title' => 'ویرایش محصول میانی',
            ],
            [
                'name' => 'send_intermediate_products_to_order',
                'title' => 'ارسال محصول میانی برای تولید',
            ],
            [
                'name' => 'cost_items_list',
                'title' => 'لیست هوشمند سازی قیمت',
            ],
            [
                'name' => 'hall_production_process_list',
                'title' => 'لیست فرایندهای تولید سالن ها',
            ],
            [
                'name' => 'hall_production_process_create',
                'title' => 'ایجاد فرایند تولید سالن',
            ],
            [
                'name' => 'hall_production_process_edit',
                'title' => 'ویرایش فرایند تولید سالن',
            ],
            [
                'name' => 'factory_stations_overview',
                'title' => 'نمای کلی ایستگاه‌های کارخانه',
            ],
            [
                'name' => 'admins_list',
                'title' => 'لیست مدیران',
            ],
            [
                'name' => 'admins_show',
                'title' => 'مشاهده مدیر',
            ],
            [
                'name' => 'admins_create',
                'title' => 'ایجاد مدیر',
            ],
            [
                'name' => 'admins_edit',
                'title' => 'ویرایش مدیر',
            ],
            [
                'name' => 'admins_delete',
                'title' => 'حذف مدیر',
            ],
            [
                'name' => 'admins_roles_edit',
                'title' => 'ویرایش دسترسی های مدیر',
            ],
            [
                'name' => 'roles_list',
                'title' => 'لیست دسترسی ها',
            ],
            [
                'name' => 'roles_create',
                'title' => 'ایجاد دسترسی',
            ],
            [
                'name' => 'roles_edit',
                'title' => 'ویرایش دسترسی ها',
            ],
            [
                'name' => 'orders_list',
                'title' => 'لیست سفارشات',
            ],
            [
                'name' => 'orders_show',
                'title' => 'مشاهده جزئیات سفارش',
            ],
            [
                'name' => 'orders_create',
                'title' => 'ایجاد سفارش جدید',
            ],
            [
                'name' => 'orders_edit',
                'title' => 'ویرایش سفارش',
            ],
            [
                'name' => 'orders_delete',
                'title' => 'حذف سفارش',
            ],
            [
                'name' => 'order_tracking',
                'title' => 'پیگیری سفارش',
            ],
            [
                'name' => 'order_price_show',
                'title' => 'مشاهده قیمت در سفارش',
            ],
            [
                'name' => 'production_order_item_steps_list',
                'title' => 'لیست محصولات هر استپ',
            ],
            [
                'name' => 'orders_approved',
                'title' => 'تایید سفارش برای شروع تولید محصولات',
            ],
            [
                'name' => 'orders_check_quality',
                'title' => 'کنترل کیفیت سفارش',
            ],
            [
                'name' => 'orders_packaging',
                'title' => 'بسته بندی سفارش',
            ],
            [
                'name' => 'orders_shipped',
                'title' => 'ارسال سفارش',
            ],
            // [
            //     'name'  => 'orders_view_production_process',
            //     'title' => 'بررسی پروسه تولید سفارش',
            // ],
            [
                'name' => 'production_order_items_list',
                'title' => 'لیست محصولات در جریان تولید',
            ],
            [
                'name' => 'production_order_items_show',
                'title' => 'مشاهده جزئیات محصولات در جریان تولید',
            ],

            [
                'name' => 'settings_edit',
                'title' => 'تنظیمات سایت',
            ],
            [
                'name' => 'admins_charts_edit',
                'title' => 'جایگاه کاربر در کارخانه',
            ],
            [
                'name' => 'drivers_list',
                'title' => 'لیست راننده ها',
            ],
            [
                'name' => 'drivers_create',
                'title' => 'ایجاد راننده',
            ],
            [
                'name' => 'drivers_edit',
                'title' => 'ویرایش راننده',
            ],
            [
                'name' => 'vehicles_list',
                'title' => 'لیست ماشین های راننده',
            ],
            [
                'name' => 'vehicles_create',
                'title' => 'ایجاد ماشین',
            ],
            [
                'name' => 'vehicles_edit',
                'title' => 'ویرایش ماشین',
            ],
            [
                'name' => 'shipments_list',
                'title' => 'لیست بارگیری ها',
            ],
            [
                'name' => 'shipments_create',
                'title' => 'ایجاد بارگیری',
            ],
            [
                'name' => 'shipments_edit',
                'title' => 'ویرایش بارگیری',
            ],
            [
                'name' => 'shipments_confirmed',
                'title' => 'تایید بارگیری',
            ],
            [
                'name' => 'shipments_loading',
                'title' => 'در حال بارگیری',
            ],
            [
                'name' => 'shipments_dispatched',
                'title' => 'ارسال بارگیری',
            ],
            [
                'name' => 'shipments_delivered',
                'title' => 'تحویل بار',
            ],

            // [
            //     'name'  => 'users_list',
            //     'title' => 'لیست مشتری ها',
            // ],
            // [
            //     'name'  => 'users_show',
            //     'title' => 'مشاهده مشتری',
            // ],
            // [
            //     'name'  => 'users_create',
            //     'title' => 'ایجاد مشتری',
            // ],
            // [
            //     'name'  => 'users_edit',
            //     'title' => 'ویرایش مشتری',
            // ],
            // [
            //     'name'  => 'users_delete',
            //     'title' => 'حذف مشتری',
            // ],

            // [
            //     'name'  => 'warehouses_list',
            //     'title' => 'لیست انبارها',
            // ],
            // [
            //     'name'  => 'warehouses_create',
            //     'title' => 'ایجاد انبار',
            // ],
            // [
            //     'name'  => 'warehouses_edit',
            //     'title' => 'ویرایش انبار',
            // ],

            [
                'name' => 'categories_list',
                'title' => 'لیست گروهبندی محصولات',
            ],
            [
                'name' => 'categories_create',
                'title' => 'ایجاد گروهبندی محصولات',
            ],
            [
                'name' => 'categories_edit',
                'title' => 'ویرایش گروهبندی محصولات',
            ],
            [
                'name' => 'categories_delete',
                'title' => 'ویرایش گروهبندی محصولات',
            ],
            [
                'name' => 'units_list',
                'title' => 'لیست واحد ها',
            ],
            [
                'name' => 'units_create',
                'title' => 'ایجاد واحد ها',
            ],
            [
                'name' => 'units_edit',
                'title' => 'ویرایش واحد ها',
            ],
            [
                'name' => 'units_delete',
                'title' => 'حذف واحد ها',
            ],
            [
                'name' => 'warehouse_vouchers_create',
                'title' => 'صدور حواله انبار',
            ],
            [
                'name' => 'warehouse_vouchers_list',
                'title' => 'لیست حواله های انبار',
            ],
            [
                'name' => 'warehouse_vouchers_confirm',
                'title' => 'تایید حواله انبار',
            ],
            [
                'name' => 'warehouse_vouchers_reject',
                'title' => 'رد حواله انبار',
            ],
            [
                'name' => 'suppliers_list',
                'title' => 'لیست تامین کننده ها',
            ],
            [
                'name' => 'suppliers_create',
                'title' => 'ایجاد تامین کننده',
            ],
            [
                'name' => 'suppliers_edit',
                'title' => 'ویرایش تامین کننده',
            ],
            [
                'name' => 'suppliers_delete',
                'title' => 'حذف تامین کننده',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                [
                    'title' => $permission['title'],
                    'guard_name' => 'web',
                ]
            );
        }

        foreach (OrderListTabs::labels() as $key => $value) {
            if ($key != OrderListTabs::DRAFT->value) {
                Permission::firstOrCreate(
                    ['name' => 'orders_'.$key],
                    [
                        'title' => 'لیست سفارشات '.$value,
                        'guard_name' => 'web',
                    ]
                );
            }
        }

        $role = Role::firstOrCreate(
            ['name' => 'super_admin'],
            [
                'title' => 'سوپر ادمین',
                'guard_name' => 'web',
            ]
        );

        $allPermissions = Permission::all();
        $role->syncPermissions($allPermissions);

        $user = User::firstOrCreate(
            ['mobile' => '09358364707'],
            [
                'name' => 'sanaz',
                'password' => Hash::make('12345678'),
                'level' => 'admin',
                'unique_code' => CodeGeneratorHelper::generate(get_class(new User), 'unique_code'),
            ]
        );
        $user->assignRole($role);
    }
}
