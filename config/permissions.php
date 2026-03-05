<?php

/**
 * Permission Display Configuration (Optional)
 *
 * This configuration file is OPTIONAL and only used for display customization.
 * Permission grouping is now managed in the database via the 'groups' column.
 *
 * This file provides:
 * - Group labels and icons (fallback if not in database)
 * - Method label formatting rules
 * - Pluralization rules for controller names
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Permission Groups Display Labels (Optional)
    |--------------------------------------------------------------------------
    |
    | Define display labels and icons for permission groups.
    | This is only used as a fallback if groups are created dynamically from database.
    | Permission grouping is managed in the database 'groups' column.
    |
    */

    'groups' => [
        'admin_panel' => [
            'label' => 'Admin Panel',
            'icon' => 'fas fa-user-shield',
            'sub_panels' => [
                'web' => [
                    'label' => 'Web',
                ],
                'api' => [
                    'label' => 'API',
                ],
            ],
        ],
        'vendor_panel' => [
            'label' => 'Vendor Panel',
            'icon' => 'fas fa-store',
            'sub_panels' => [
                'web' => [
                    'label' => 'Web',
                ],
                'api' => [
                    'label' => 'API',
                ],
            ],
        ],
        'customer_panel' => [
            'label' => 'Customer Panel',
            'icon' => 'fas fa-users',
            'sub_panels' => [
                'web' => [
                    'label' => 'Web',
                ],
                'api' => [
                    'label' => 'API',
                ],
            ],
        ],
        'delivery_panel' => [
            'label' => 'Delivery Panel',
            'icon' => 'fas fa-truck',
            'sub_panels' => [
                'web' => [
                    'label' => 'Web',
                ],
                'api' => [
                    'label' => 'API',
                ],
            ],
        ],
        'pos_panel' => [
            'label' => 'POS Panel',
            'icon' => 'fas fa-cash-register',
            'sub_panels' => [
                'web' => [
                    'label' => 'Web',
                ],
                'api' => [
                    'label' => 'API',
                ],
            ],
        ],
        'affiliate_panel' => [
            'label' => 'Affiliate Panel',
            'icon' => 'fas fa-users',
            'sub_panels' => [
                'web' => [
                    'label' => 'Web',
                ],
                'api' => [
                    'label' => 'API',
                ],
            ],
        ],
        'others' => [
            'label' => 'Others',
            'icon' => 'fas fa-th',
            'sub_panels' => [
                'web' => [
                    'label' => 'Web',
                ],
                'api' => [
                    'label' => 'API',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Method Label Mappings
    |--------------------------------------------------------------------------
    |
    | Define how method names should be displayed in the permission interface.
    | Format: 'method_name' => 'Display Label'
    |
    | Example: 'index' will display as "List" in "List Products"
    |
    */

    'method_labels' => [
        // Standard CRUD operations
        'index' => 'List',
        'show' => 'Show',
        'create' => 'Create',
        'store' => 'Store',
        'edit' => 'Edit',
        'update' => 'Update',
        'destroy' => 'Delete',
        'delete' => 'Delete',

        // Common operations
        'detail' => 'View Details',
        'details' => 'View Details',
        'view' => 'View',
        'export' => 'Export',
        'import' => 'Import',
        'status' => 'Change Status',
        'changeStatus' => 'Change Status',
        'bulkAction' => 'Bulk Action',
        'download' => 'Download',
        'print' => 'Print',
        'pdf' => 'Export PDF',
        'csv' => 'Export CSV',
        'search' => 'Search',

        // Profile & User operations
        'profile' => 'View Profile',
        'updateProfile' => 'Update Profile',
        'updatePassword' => 'Update Password',
        'updateProfilePassword' => 'Update Profile Password',
        'verification' => 'Verification',
        'logout' => 'Logout',
        'login' => 'Login',
        'signup' => 'Sign Up',
        'activate' => 'Activate',
        'wallet' => 'Wallet',
        'activity' => 'Activity',
        'loginActivity' => 'Login Activity',
        'allUserActivity' => 'All User Activity',
        'deleteUserActivity' => 'Delete User Activity',

        // Address operations
        'addresses' => 'Addresses',
        'storeAddress' => 'Store Address',
        'updateAddress' => 'Update Address',
        'destroyAddress' => 'Delete Address',
        'checkDefault' => 'Check Default',
        'makeDefault' => 'Make Default',
        'userAddress' => 'User Address',

        // Order operations
        'orderDetails' => 'Order Details',
        'orderAction' => 'Order Action',
        'invoicePrint' => 'Print Invoice',
        'invoiceSave' => 'Save Invoice',
        'invoiceTaxShipping' => 'Invoice Tax Shipping',
        'partialPayment' => 'Partial Payment',
        'customize' => 'Customize',
        'grantAccess' => 'Grant Access',
        'storeNote' => 'Store Note',

        // Payment operations
        'payment' => 'Payment',
        'paymentStore' => 'Store Payment',
        'paymentMethod' => 'Payment Method',
        'makePayment' => 'Make Payment',
        'paid' => 'Paid',
        'deletePayment' => 'Delete Payment',

        // Transaction & Ledger
        'transaction' => 'Transaction',
        'ledger' => 'Ledger',
        'withdraw' => 'Withdraw',
        'setting' => 'Setting',
        'settings' => 'Settings',
        'settingStore' => 'Store Setting',

        // Product operations
        'createProduct' => 'Create Product',
        'editProductAction' => 'Edit Product Action',
        'deleteProduct' => 'Delete Product',
        'forceDeleteProduct' => 'Force Delete Product',
        'duplicate' => 'Duplicate',
        'findDownloadProducts' => 'Find Download Products',
        'findProductAjaxQuery' => 'Find Product',
        'updateRelatedProduct' => 'Update Related Product',
        'productExport' => 'Product Export',
        'productImport' => 'Product Import',

        // Category operations
        'getData' => 'Get Data',
        'getParentData' => 'Get Parent Data',
        'moveNode' => 'Move Node',
        'findCategory' => 'Find Category',
        'findCategoryAjaxQuery' => 'Find Category',
        'suggestion' => 'Suggestion',
        'assignCategory' => 'Assign Category',

        // Brand operations
        'findBrand' => 'Find Brand',
        'suggestion' => 'Suggestion',
        'assignBrand' => 'Assign Brand',

        // Attribute operations
        'getAttribute' => 'Get Attribute',
        'assignAttribute' => 'Assign Attribute',

        // Vendor operations
        'findVendor' => 'Find Vendor',
        'findVendorAssignUsers' => 'Find Vendor Assign Users',
        'updateVendor' => 'Update Vendor',
        'vendorList' => 'Vendor List',
        'vendorRole' => 'Vendor Role',
        'vendorLocation' => 'Vendor Location',
        'findVendorLocation' => 'Find Vendor Location',
        'vendorStats' => 'Vendor Stats',
        'vendorStatsType' => 'Vendor Stats Type',
        'vendorReq' => 'Vendor Request',
        'vendorSettings' => 'Vendor Settings',

        // Customer operations
        'findCustomerByVendor' => 'Find Customer By Vendor',
        'findUser' => 'Find User',

        // Review operations
        'reviewStore' => 'Store Review',
        'updateReview' => 'Update Review',
        'deleteReview' => 'Delete Review',

        // Currency operations
        'validCurrencyName' => 'Validate Currency Name',
        'findCurrencyAjaxQuery' => 'Find Currency',
        'exchangeUpdate' => 'Update Exchange Rate',

        // Language operations
        'translation' => 'Translation',
        'translationStore' => 'Store Translation',
        'changeLanguage' => 'Change Language',
        'switchLanguage' => 'Switch Language',

        // Email & SMS operations
        'emailVerifySetting' => 'Email Verify Setting',
        'twilio' => 'Twilio',
        'storeTwilio' => 'Store Twilio',
        'nexmo' => 'Nexmo',
        'storeNexmo' => 'Store Nexmo',
        'fast2Sms' => 'Fast2SMS',
        'storeFast2Sms' => 'Store Fast2SMS',
        'sslWireless' => 'SSL Wireless',
        'storeSslWireless' => 'Store SSL Wireless',
        'mimSms' => 'MIM SMS',
        'storeMimSms' => 'Store MIM SMS',
        'msegat' => 'Msegat',
        'storeMsegat' => 'Store Msegat',
        'sparrow' => 'Sparrow',
        'storeSparrow' => 'Store Sparrow',
        'zender' => 'Zender',
        'storeZender' => 'Store Zender',

        // Notification operations
        'headerNotification' => 'Header Notification',
        'markAsRead' => 'Mark As Read',
        'markAsUnread' => 'Mark As Unread',
        'markAsReadAll' => 'Mark All As Read',
        'updateSetting' => 'Update Setting',
        'log' => 'Log',
        'destroyLog' => 'Delete Log',

        // Dashboard operations
        'getUserData' => 'Get User Data',
        'getProductData' => 'Get Product Data',
        'mostSoldProducts' => 'Most Sold Products',
        'mostActiveUsers' => 'Most Active Users',
        'salesOfTheMonth' => 'Sales Of The Month',
        'setWidgetData' => 'Set Widget Data',
        'setWidgetOption' => 'Set Widget Option',
        'forgetWidget' => 'Forget Widget',
        'clearCache' => 'Clear Cache',

        // System operations
        'loadSeedData' => 'Load Seed Data',
        'loadLiveSeedData' => 'Load Live Seed Data',
        'upgrade' => 'Upgrade',
        'checkVersion' => 'Check Version',
        'downloadVersion' => 'Download Version',
        'enable' => 'Enable',
        'enableModule' => 'Enable Module',
        'disableModule' => 'Disable Module',

        // Shipping operations
        'storeClass' => 'Store Class',
        'storeSetting' => 'Store Setting',
        'updateProvider' => 'Update Provider',
        'removeProvider' => 'Remove Provider',
        'storeProvider' => 'Store Provider',

        // Inventory operations
        'adjust' => 'Adjust',
        'receive' => 'Receive',
        'receiveStore' => 'Store Receive',
        'findSupplier' => 'Find Supplier',
        'findLocation' => 'Find Location',
        'findVendor' => 'Find Vendor',

        // Refund operations
        'refund' => 'Refund',
        'refundDetails' => 'Refund Details',
        'getProducts' => 'Get Products',
        'getReason' => 'Get Reason',
        'storeMessage' => 'Store Message',
        'getMessage' => 'Get Message',
        'process' => 'Process',
        'createRequest' => 'Create Request',

        // Ticket operations
        'getConversations' => 'Get Conversations',
        'sendProductDetails' => 'Send Product Details',
        'initiateChatWithVendor' => 'Initiate Chat With Vendor',
        'contact-list' => 'Contact List',
        'storeMessage' => 'Store Message',
        'createChat' => 'Create Chat',
        'inboxRefresh' => 'Refresh Inbox',
        'replyStore' => 'Store Reply',
        'changePriority' => 'Change Priority',
        'changeAssignee' => 'Change Assignee',
        'updateReply' => 'Update Reply',
        'add' => 'Add',
        'messages' => 'Messages',
        'storeMessage' => 'Store Message',
        'editMessage' => 'Edit Message',
        'updateMessage' => 'Update Message',
        'destroyMessage' => 'Delete Message',
        'links' => 'Links',
        'storeLink' => 'Store Link',
        'editLink' => 'Edit Link',
        'updateLink' => 'Update Link',
        'destroyLink' => 'Delete Link',
        'search' => 'Search',
        'downloadAttachment' => 'Download Attachment',

        // CMS operations
        'home' => 'Home',
        'createHomepage' => 'Create Homepage',
        'editHome' => 'Edit Home',
        'exportHome' => 'Export Home',
        'importHome' => 'Import Home',
        'quickUpdate' => 'Quick Update',
        'editElement' => 'Edit Element',
        'updateComponent' => 'Update Component',
        'deleteComponent' => 'Delete Component',
        'updateAllComponents' => 'Update All Components',
        'ajaxResourceFetch' => 'Fetch Resource',
        'layoutStore' => 'Store Layout',
        'storePrimaryColor' => 'Store Primary Color',
        'layoutUpdate' => 'Update Layout',
        'layoutDelete' => 'Delete Layout',
        'list' => 'List',
        'changeLanguage' => 'Change Language',

        // Menu operations
        'createNewMenu' => 'Create New Menu',
        'addCustomMenu' => 'Add Custom Menu',
        'generateMenuControl' => 'Generate Menu Control',
        'deleteMenu' => 'Delete Menu',

        // Media operations
        'upload' => 'Upload',
        'uploadedFiles' => 'Uploaded Files',
        'sortFiles' => 'Sort Files',
        'paginateFiles' => 'Paginate Files',
        'paginateData' => 'Paginate Data',
        'deleteImage' => 'Delete Image',
        'removeImage' => 'Remove Image',
        'storeExtension' => 'Store Extension',
        'getExtensionAjaxQuery' => 'Get Extension',

        // Coupon operations
        'downloadPdf' => 'Download PDF',
        'downloadCsv' => 'Download CSV',
        'getShopByVendor' => 'Get Shop By Vendor',
        'getCouponProduct' => 'Get Coupon Product',
        'getOldProducts' => 'Get Old Products',
        'getOldVendor' => 'Get Old Vendor',
        'item' => 'Item',

        // Subscription operations
        'invoice' => 'Invoice',
        'invoicePdf' => 'Invoice PDF',
        'invoiceEmail' => 'Send Invoice Email',
        'generateLink' => 'Generate Link',
        'getGenerateLink' => 'Get Generate Link',
        'generateLinkIndex' => 'Generate Link Index',
        'getInfo' => 'Get Info',
        'privatePlan' => 'Private Plan',
        'notification' => 'Notification',
        'updatePaid' => 'Update Paid',

        // Affiliate operations
        'userProfileUpdate' => 'Update User Profile',
        'userDestroy' => 'Delete User',
        'referrals' => 'Referrals',
        'topProducts' => 'Top Products',
        'payouts' => 'Payouts',
        'multiTier' => 'Multi Tier',
        'findAffiliateUserAjaxQuery' => 'Find Affiliate User',
        'findAffiliateTagAjaxQuery' => 'Find Affiliate Tag',

        // POS operations
        'setup' => 'Setup',
        'receipt' => 'Receipt',
        'canBypassTerminal' => 'Can Bypass Terminal',
        'terminals' => 'Terminals',
        'currentSessionBalance' => 'Current Session Balance',
        'isSessionActive' => 'Is Session Active',
        'check' => 'Check',

        // Delivery operations
        'assignCarrierView' => 'Assign Carrier View',
        'earning' => 'Earning',
        'pickup' => 'Pickup',
        'delivered' => 'Delivered',
        'completed' => 'Completed',

        // Report operations
        'export' => 'Export',

        // Form Builder / KYC operations
        'editSubmission' => 'Edit Submission',
        'viewSubmission' => 'View Submission',
        'submissionDelete' => 'Delete Submission',
        'userKycForm' => 'User KYC Form',
        'userKycSubmit' => 'Submit User KYC',
        'userKycUpdateSubmission' => 'Update User KYC Submission',

        // Other operations
        'assign' => 'Assign',
        'assignPermission' => 'Assign Permission',
        'generatePermission' => 'Generate Permission',
        'updatePermissions' => 'Update Permissions',
        'client' => 'Client',
        'deleteClient' => 'Delete Client',
        'getMeta' => 'Get Meta',
        'storeMeta' => 'Store Meta',
        'product' => 'Product',
        'filter' => 'Filter',
        'general' => 'General',
        'inventory' => 'Inventory',
        'history' => 'History',
        'active' => 'Active',
        'cancel' => 'Cancel',
        'sellerRequestStore' => 'Store Seller Request',
        'findTagsAjaxQuery' => 'Find Tags',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pluralization Rules
    |--------------------------------------------------------------------------
    |
    | Define irregular plural forms for controller names.
    | Used when displaying method labels like "List Products" instead of "List Product"
    |
    */

    'pluralization' => [
        'Product' => 'Products',
        'Category' => 'Categories',
        'Order' => 'Orders',
        'Customer' => 'Customers',
        'User' => 'Users',
        'Role' => 'Roles',
        'Permission' => 'Permissions',
        'Brand' => 'Brands',
        'Attribute' => 'Attributes',
        'Coupon' => 'Coupons',
        'Review' => 'Reviews',
        'Shipping' => 'Shipping',
        'Payment' => 'Payments',
        'Location' => 'Locations',
        'Purchase' => 'Purchases',
        'Terminal' => 'Terminals',
        'Inventory' => 'Inventories',
        'Vendor' => 'Vendors',
        'Transaction' => 'Transactions',
        'Withdrawal' => 'Withdrawals',
        'Refund' => 'Refunds',
        'Ticket' => 'Tickets',
        'Subscription' => 'Subscriptions',
        'Package' => 'Packages',
        'Affiliate' => 'Affiliates',
        'Supplier' => 'Suppliers',
        'Transfer' => 'Transfers',
        'Carrier' => 'Carriers',
        'Delivery' => 'Deliveries',
        'Tax' => 'Taxes',
        'Blog' => 'Blogs',
        'Popup' => 'Popups',
        'Media' => 'Media',
        'Report' => 'Reports',
        'Staff' => 'Staff',
        'Unit' => 'Units',
        'Menu' => 'Menus',
        'Notification' => 'Notifications',
        'CustomField' => 'Custom Fields',
        'Address' => 'Addresses',
        'Wishlist' => 'Wishlists',
        'Currency' => 'Currencies',
        'Language' => 'Languages',
        'MailTemplate' => 'Mail Templates',
        'EmailConfiguration' => 'Email Configurations',
        'CompanySetting' => 'Company Settings',
        'Preference' => 'Preferences',
        'OrderStatus' => 'Order Statuses',
        'Commission' => 'Commissions',
        'Gateway' => 'Gateways',
        'Stripe' => 'Stripe',
        'Theme' => 'Themes',
        'SmsConfiguration' => 'SMS Configurations',
        'SmsTemplate' => 'SMS Templates',
        'ApiKey' => 'API Keys',
        'Barcode' => 'Barcodes',
        'InvoiceSetting' => 'Invoice Settings',
        'OrderSetting' => 'Order Settings',
        'AccountSetting' => 'Account Settings',
        'AddressSetting' => 'Address Settings',
        'ProductSetting' => 'Product Settings',
        'CurrencySetting' => 'Currency Settings',
        'SystemInfo' => 'System Info',
        'Import' => 'Imports',
        'Export' => 'Exports',
        'Batch' => 'Batches',
        'DataTable' => 'Data Tables',
        'Sso' => 'SSO',
        'MaintenanceMode' => 'Maintenance Mode',
        'RegisteredSeller' => 'Registered Sellers',
        'Download' => 'Downloads',
        'Kyc' => 'KYC',
        'FormBuilder' => 'Form Builders',
        'Cms' => 'CMS',
        'Slide' => 'Slides',
        'Slider' => 'Sliders',
        'Builder' => 'Builders',
        'ThemeOption' => 'Theme Options',
        'AuthLayout' => 'Auth Layouts',
        'AdvanceReport' => 'Advance Reports',
        'BulkPayment' => 'Bulk Payments',
        'Dummy' => 'Dummies',
        'Upgrader' => 'Upgraders',
        'Pos' => 'POS',
        'PosVerify' => 'POS Verify',
        'SubscriptionVerify' => 'Subscription Verify',
    ],

    /*
    |--------------------------------------------------------------------------
    | Methods That Should Use Plural Form
    |--------------------------------------------------------------------------
    |
    | Methods that should display with plural controller names.
    | Example: "List Products" (plural) vs "Show Product" (singular)
    |
    */

    'plural_methods' => [
        'index',
        'export',
        'import',
        'bulkAction',
    ],

];
