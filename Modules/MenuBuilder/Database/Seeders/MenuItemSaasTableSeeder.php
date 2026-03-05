<?php

namespace Modules\MenuBuilder\Database\Seeders;

use Illuminate\Database\Seeder;

class MenuItemSaasTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        addMenuItem('saas vendor', '{"en": "Dashboard","bn": "ড্যাশবোর্ড","fr": "Tableau de bord","zh": "仪表板","ar": "لوحة القيادة","be": "Панэль прыбораў","bg": "Табло","ca": "Tauler de control","et": "Armatuurlaud","nl": "Dashboard"}', [
            'link' => 'dashboard',
            'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\VendorController@index","route_name":["vendor-dashboard"]}',
            'sort' => '1.00000000',
            'icon' => 'fas fa-home',
        ]);

        $parentId = addMenuItem('saas vendor', '{"en":"Products","bn":"পণ্য","fr":"Produits","zh":"产品","ar":"منتجات","be":"Тавары","bg":"Продукти","ca":"Productes","et":"Tooted","nl":"Producten"}', [
            'link' => null,
            'params' => '',
            'sort' => '2.00000000',
            'icon' => 'fas fa-cube',
        ]);
        addMenuItem('saas vendor', '{"en":"Add New Product","bn":"নতুন পণ্য যোগ করুন","fr":"Ajouter un nouveau produit","zh":"新增產品","ar":"إضافة منتج جديد","be":"Дадаць новы прадукт","bg":"Добавяне на продукт","ca":"Afegeix producte","et":"Lisa uus toode","nl":"Product toevoegen"}', [
            'link' => 'product/create',
            'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\Vendor\\\\ProductController@createProduct","route_name":["vendor.product.create"]}',
            'sort' => '1.00000000',
            'parent' => $parentId,
        ]);
        addMenuItem('saas vendor', '{"en":"All Products","bn":"সমস্ত পণ্য","fr":"Tous les produits","zh":"所有产品","ar":"جميع المنتجات","be":" прадукты","bg":"Всички продукти","ca":"Tots els productes","et":"Koik tooted","nl":"Alle producten"}', [
            'link' => 'products',
            'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\Vendor\\\\ProductController@index","route_name":["vendor.products", "vendor.product.edit"]}',
            'sort' => '2.00000000',
            'parent' => $parentId,
        ]);
        addMenuItem('saas vendor', '{"en":"Brands","bn":"ব্র্যান্ড","fr":"Marques","zh":"品牌","ar":"الماركات","be":"Брэнды","bg":"Марки","ca":"Marques","et":"Markused","nl":"Merkken"}', [
            'link' => 'all-brands',
            'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\Vendor\\\\BrandController@index","route_name":["vendor.brands.index", "vendor.brands.create", "vendor.brands.edit"]}',
            'sort' => '3.00000000',
            'parent' => $parentId,
        ]);
        addMenuItem('saas vendor', '{"en":"Attributes","bn":"গুণগুলি","fr":"Attributs","zh":"属性","ar":"السمات","be":"Атрыбуты","bg":"Атрибути","ca":"Atributs","et":"Atribuudid","nl":"Eigenschappen"}', [
            'link' => 'all-attributes',
            'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\Vendor\\\\AttributeController@index", "route_name":["vendor.attribute.index", "vendor.attribute.create", "vendor.attribute.edit", "vendor.attribute.store", "vendor.attribute.destroy"]}',
            'sort' => '4.00000000',
            'parent' => $parentId,
        ]);
        addMenuItem('saas vendor', '{"en":"Categories","bn":"বিভাগ","fr":"Catégories","zh":"类别","ar":"فئات","be":"Катэгорыі","bg":"Категории","ca":"Categories","et":"Kategooriad","nl":"Categorieën"}', [
            'link' => 'all-categories',
            'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\Vendor\\\\CategoryController@index","route_name":["vendor.categories.index"]}',
            'sort' => '5.00000000',
            'parent' => $parentId,
        ]);
        addMenuItem('saas vendor', '{"en":"Barcode","bn":"বারকোড","fr":"Code-barres","zh":"条码","ar":"الباركود","be":"Штрых-код","bg":"Баркод","ca":"Codi de barres","et":"Vöötkood","nl":"Barcode"}', [
            'link' => 'barcode/product',
            'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\Vendor\\\\BarcodeController@product","route_name":["vendor.barcode.product"]}',
            'sort' => '6.00000000',
            'parent' => $parentId,
        ]);
        addMenuItem('saas vendor', '{"en":"Export Products","bn":"পণ্য রফতানি করুন","fr":"Exporter des produits","zh":"导出产品","ar":"تصدير المنتجات","be":"Экспарт прадукцыі","bg":"Изнасяне на продукти","ca":"Exportar productes","et":"Eksportige tooteid","nl":"Producten exporteren"}', [
            'link' => 'export/products',
            'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\Vendor\\\\ExportController@productExport", "route_name":["vendor.epz.export.products"]}',
            'sort' => '7.00000000',
            'parent' => $parentId,
        ]);
        addMenuItem('saas vendor', '{"en":"Import Products","bn":"পণ্য আমদানি করুন","fr":"Importer des produits","zh":"导入产品","ar":"استيراد المنتجات","be":"Імпартаваць прадукцыю","bg":"Импортиране на продукти","ca":"Importar productes","et":"Impordi tooteid","nl":"Producten importeren"}', [
            'link' => 'import/products',
            'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\Vendor\\\\ImportController@productImport", "route_name":["vendor.epz.import.products"]}',
            'sort' => '8.00000000',
            'parent' => $parentId,
        ]);

        addMenuItem('saas vendor', '{"en":"Customers","bn":"গ্রাহকদের","fr":"Clients","zh":"客户","ar":"العملاء","be":"Кліенты","bg":"Клиенти","ca":"Clients","et":"Kliendid","nl":"Klanten"}', [
            'link' => 'customer',
            'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\Vendor\\\\CustomerController@index", "route_name":["vendor.customer", "vendor.customer.create"]}',
            'sort' => '3.00000000',
            'icon' => 'fas fa-users',
        ]);
        addMenuItem('saas vendor', '{"en":"Orders","bn":"অর্ডার","fr":"Commandes","zh":"订单","ar":"الطلبات","be":"Замовы","bg":"Поръчки","ca":"Comandes","et":"Tellimused","nl":"Bestellingen"}', [
            'link' => 'orders',
            'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\Vendor\\\\VendorOrderController@index", "route_name":["vendorOrder.index", "vendorOrder.view", "vendorOrder.pdf", "vendorOrder.csv"]}',
            'sort' => '4.00000000',
            'icon' => 'fas fa-shopping-bag',
        ]);
        $parentId = addMenuItem('saas vendor', '{"en":"Inventory","bn":"মজুদ","fr":"Inventaire","zh":"库存","ar":"المخزون","be":"Сток","bg":"Наличност","ca":"Inventari","et":"Varu","nl":"Voorraad"}', [
            'link' => null,
            'params' => '',
            'sort' => '5.00000000',
            'icon' => 'fas fa-table',
        ]);

        addMenuItem('saas vendor', '{"en":"Location","bn":"অবস্থান","fr":"Emplacement","zh":"位置","ar":"الموقع","be":"Месцазнаходжанне","bg":"Местоположение","ca":"Ubicació","et":"Asukoht","nl":"Locatie"}', [
            'link' => 'inventory/location',
            'params' => '{"permission":"Modules\\\\Inventory\\\\Http\\\\Controllers\\\\Vendor\\\\LocationController@index","route_name":["vendor.location.index", "vendor.location.edit", "vendor.location.create", "vendor.location.store", "vendor.location.update", "vendor.location.destroy"]}',
            'sort' => '2.00000000',
            'parent' => $parentId,
        ]);
        addMenuItem('saas vendor', '{"en":"Supplier","bn":"সরবরাহকারী","fr":"Fournisseur","zh":"供应商","ar":"المورد","be":"Пастаўшчык","bg":"Доставчик","ca":"Proveïdor","et":"Tarnija","nl":"Leverancier"}', [
            'link' => 'inventory/supplier',
            'params' => '{"permission":"Modules\\\\Inventory\\\\Http\\\\Controllers\\\\Vendor\\\\SupplierController@index","route_name":["vendor.supplier.index", "vendor.supplier.edit", "vendor.supplier.create", "vendor.supplier.store", "vendor.supplier.update", "vendor.supplier.destroy"]}',
            'sort' => '2.00000000',
            'parent' => $parentId,
        ]);
        addMenuItem('saas vendor', '{"en":"Transaction","bn":"লেনদেন","fr":"Transaction","zh":"交易","ar":"المعاملة","be":"Трансакцыя","bg":"Транзакция","ca":"Transacció","et":"Tehing","nl":"Transactie"}', [
            'link' => 'inventory/transaction',
            'params' => '{"permission":"Modules\\\\Inventory\\\\Http\\\\Controllers\\\\Vendor\\\\InventoryController@transaction","route_name":["vendor.inventory.transaction"]}',
            'sort' => '7.00000000',
            'parent' => $parentId,
        ]);
        addMenuItem('saas vendor', '{"en":"Stock","bn":"স্টক","fr":"Stock","zh":"股票","ar":"المخزون","be":"Запас","bg":"Акции","ca":"Estoc","et":"Aksiad","nl":"Voorraad"}', [
            'link' => 'inventory',
            'params' => '{"permission":"Modules\\\\Inventory\\\\Http\\\\Controllers\\\\Vendor\\\\InventoryController@index","route_name":["vendor.inventory.index", "vendor.inventory.adjust"]}',
            'sort' => '5.00000000',
            'parent' => $parentId,
        ]);
        addMenuItem('saas vendor', '{"en":"Transfer","bn":"স্থানান্তর","fr":"Transfert","zh":"转账","ar":"تحويل","be":"Перанос","bg":"Трансфер","ca":"Transferència","et":"Ülekanne","nl":"Overdracht"}', [
            'link' => 'inventory/transfer',
            'params' => '{"permission":"Modules\\\\Inventory\\\\Http\\\\Controllers\\\\Vendor\\\\TransferController@index","route_name":["vendor.transfer.index", "vendor.transfer.create", "vendor.transfer.edit", "vendor.transfer.receive"]}',
            'sort' => '6.00000000',
            'parent' => $parentId,
        ]);
        addMenuItem('saas vendor', '{"en":"Purchase Order","bn":"ক্রয় আদেশ","fr":"Bon de commande","zh":"采购订单","ar":"طلب الشراء","be":"Заказ","bg":"Поръчка","ca":"Ordre de compra","et":"Tellimus","nl":"Aankooporder"}', [
            'link' => 'inventory/purchase-order',
            'params' => '{"permission":"Modules\\\\Inventory\\\\Http\\\\Controllers\\\\Vendor\\\\PurchaseController@index","route_name":["vendor.purchase.index", "vendor.purchase.edit", "vendor.purchase.create", "vendor.purchase.store", "vendor.purchase.update", "vendor.purchase.destroy", "vendor.purchase.receive"]}',
            'sort' => '6.00000000',
            'icon' => 'fas fa-money-bill-alt',
        ]);
        addMenuItem('saas vendor', '{"en":"Staff","bn":"স্টাফ","fr":"Personnel","zh":"员工","ar":"الموظفون","be":"Персанал","bg":"Персонал","ca":"Personal","et":"Töötajad","nl":"Personeel"}', [
            'link' => 'staffs',
            'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\Vendor\\\\StaffController@index", "route_name":["vendor.staffs.index", "vendor.staffs.create", "vendor.staffs.edit", "vendor.roles.index", "vendor.roles.create", "vendor.roles.edit", "vendor.permission.index"]}',
            'sort' => '7.00000000',
            'icon' => 'fas fa-users',
        ]);
        $parentId = addMenuItem('saas vendor', '{"en":"POS","bn":"পস","fr":"PDV","zh":"销售点","ar":"نقطة البيع","be":"Крама","bg":"ПОС","ca":"TPV","et":"Müügikoht","nl":"POS"}', [
            'link' => null,
            'params' => '',
            'sort' => '8.00000000',
            'icon' => 'fas fa-desktop',
        ]);
        addMenuItem('saas vendor', '{"en":"Setup","bn":"সেটআপ","fr":"Configuration","zh":"设置","ar":"إعداد","be":"Наладка","bg":"Настройка","ca":"Configuració","et":"Seadistus","nl":"Instelling"}', [
            'link' => 'pos/setup',
            'params' => '{"permission":"Modules\\\\Pos\\\\Http\\\\Controllers\\\\Vendor\\\\POSController@setup", "route_name":["vendor.pos.setup", "vendor.pos.receipt", "vendor.pos.payment_method", "vendor.pos.discount"]}',
            'sort' => '1.00000000',
            'parent' => $parentId,
        ]);
        addMenuItem('saas vendor', '{"en":"Terminals","bn":"টার্মিনাল","fr":"Terminaux","zh":"终端","ar":"المحطات","be":"Тэрміналы","bg":"Терминали","ca":"Terminals","et":"Terminalid","nl":"Terminals"}', [
            'link' => 'pos/terminals',
            'params' => '{"permission":"Modules\\\\Pos\\\\Http\\\\Controllers\\\\Vendor\\\\TerminalController@index", "route_name":["vendor.pos.terminals.index", "vendor.pos.terminals.create", "vendor.pos.terminals.edit", "vendor.pos.terminals.history"]}',
            'sort' => '2.00000000',
            'parent' => $parentId,
        ]);
        addMenuItem('saas vendor', '{"en":"Orders","bn":"অর্ডারসমূহ","fr":"Commandes","zh":"订单","ar":"الطلبات","be":"Заказы","bg":"Поръчки","ca":"Comandes","et":"Tellimused","nl":"Bestellingen"}', [
            'link' => 'pos/orders',
            'params' => '{"permission":"Modules\\\\Pos\\\\Http\\\\Controllers\\\\Vendor\\\\OrderController@index", "route_name":["vendor.pos.orders.index"]}',
            'sort' => '3.00000000',
            'parent' => $parentId,
        ]);
        addMenuItem('saas vendor', '{"en":"Staff Activities","bn":"স্টাফ কার্যক্রম","fr":"Activités du personnel","zh":"员工活动","ar":"أنشطة الموظفين","be":"Дзейнасць персаналу","bg":"Дейности на персонала","ca":"Activitats del personal","et":"Töötajate tegevused","nl":"Personeelsactiviteiten"}', [
            'link' => 'pos/activities',
            'params' => '{"permission":"Modules\\\\Pos\\\\Http\\\\Controllers\\\\Vendor\\\\ActivityController@index", "route_name":["vendor.pos.activities.index"]}',
            'sort' => '4.00000000',
            'parent' => $parentId,
        ]);
        addMenuItem('saas vendor', '{"en":"Subscriptions","bn":"সাবস্ক্রিপশনসমূহ","fr":"Abonnements","zh":"订阅","ar":"الاشتراكات","be":"Падпіскі","bg":"Абонаменти","ca":"Subscripcions","et":"Tellijused","nl":"Abonnementen"}', [
            'link' => 'subscription',
            'params' => '{"permission":"Modules\\\\Subscription\\\\Http\\\\Controllers\\\\Vendor\\\\SubscriptionController@index","route_name":["vendor.subscription.index", "vendor.subscription.store", "vendor.subscription.paid", "vendor.subscription.history", "vendor.subscription.invoice"]}',
            'sort' => '9.00000000',
            'icon' => 'fas fa-money-bill-alt',
        ]);
        addMenuItem('saas vendor', '{"en":"Transactions","bn":"লেনদেন","fr":"Transactions","zh":"交易","ar":"المعاملات","be":"Транзакцыі","bg":"Транзакции","ca":"Transaccions","et":"Tehingud","nl":"Transacties"}', [
            'link' => 'transactions',
            'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\Vendor\\\\VendorTransactionController@index", "route_name":["vendorTransaction.index", "vendorTransaction.pdf", "vendorTransaction.csv"]}',
            'sort' => '10.00000000',
            'icon' => 'fas fa-exchange-alt',
        ]);
        addMenuItem('saas vendor', '{"en":"Reports","bn":"রিপোর্ট","fr":"Rapports","zh":"报告","ar":"تقارير","be":"Справаздачы","bg":"Доклади","ca":"Informes","et":"Aruanded","nl":"Rapporten"}', [
            'link' => 'reports',
            'params' => '{"permission":"Modules\\\\Report\\\\Http\\\\Controllers\\\\Vendor\\\\ReportController@index", "route_name":["vendor.reports"]}',
            'sort' => '11.00000000',
            'icon' => 'fas fa-chart-bar',
        ]);
        addMenuItem('saas vendor', '{"en":"Tickets","bn":"টিকিট","fr":"Tickets","zh":"票","ar":"تذاكر","be":"Квіткі","bg":"Билети","ca":"Tiquets","et":"Piletid","nl":"Tickets"}', [
            'link' => 'ticket/list',
            'params' => '{"permission":"Modules\\\\Ticket\\\\Http\\\\Controllers\\\\Vendor\\\\TicketController@index", "route_name":["vendor.threads", "vendor.threadAdd", "vendor.threadReply", "vendor.threadPdf", "vendor.threadCsv"]}',
            'sort' => '12.00000000',
            'icon' => 'fas fa-ticket-alt',
        ]);
        addMenuItem('saas vendor', '{"en":"Coupons","bn":"কুপন","fr":"Coupons","zh":"优惠券","ar":"كوبونات","be":"Купоны","bg":"Купоны","ca":"Cupons","et":"Kupongid","nl":"Kupons"}', [
            'link' => 'coupons',
            'params' => '{"permission":"Modules\\\\Coupon\\\\Http\\\\Controllers\\\\Vendor\\\\CouponController@index", "route_name":["vendor.coupons", "vendor.couponCreate", "vendor.couponEdit", "vendor.couponProduct"]}',
            'sort' => '4.00000000',
            'icon' => 'fas fa-ticket-alt',
        ]);
    }
}
