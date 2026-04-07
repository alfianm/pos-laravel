<?php

use App\Livewire\Branches\BranchForm;
use App\Livewire\Branches\BranchList;
use App\Livewire\CRM\CampaignForm;
use App\Livewire\CRM\CampaignList;
use App\Livewire\CRM\LeadForm;
use App\Livewire\CRM\LeadList;
use App\Livewire\CRM\LeadShow;
use App\Livewire\CRM\ProposalForm;
use App\Livewire\CRM\ProposalList;
use App\Livewire\CRM\ProposalShow;
use App\Livewire\CustomerPortal\Dashboard;
use App\Livewire\CustomerPortal\Login;
use App\Livewire\CustomerPortal\Orders;
use App\Livewire\CustomerPortal\Profile;
use App\Livewire\Dashboard\OwnerDashboard;
use App\Livewire\Dashboard\SummaryDashboard;
use App\Livewire\Expenses\ExpenseCategoryList;
use App\Livewire\Expenses\ExpenseForm;
use App\Livewire\Expenses\ExpenseList;
use App\Livewire\Inventory\InventoryList;
use App\Livewire\Inventory\OpeningStock;
use App\Livewire\Inventory\StockAdjustmentForm;
use App\Livewire\Inventory\StockAdjustmentList;
use App\Livewire\Inventory\StockTransferForm;
use App\Livewire\Inventory\StockTransferList;
use App\Livewire\Inventory\StockTransferShow;
use App\Livewire\Marketplace\AccountForm;
use App\Livewire\Marketplace\AccountIndex;
use App\Livewire\Marketplace\ProductMap;
use App\Livewire\Marketplace\ShopList;
use App\Livewire\Marketplace\SyncLogs;
use App\Livewire\MasterData\BulkImport;
use App\Livewire\MasterData\BrandList;
use App\Livewire\MasterData\CategoryList;
use App\Livewire\MasterData\CustomerGroupList;
use App\Livewire\MasterData\CustomerList;
use App\Livewire\MasterData\ProductDetail;
use App\Livewire\MasterData\ProductForm;
use App\Livewire\MasterData\ProductList;
use App\Livewire\MasterData\ProductPriceManagement;
use App\Livewire\MasterData\BarcodePrint;
use App\Livewire\MasterData\SupplierDetail;
use App\Livewire\MasterData\SupplierList;
use App\Livewire\MasterData\UnitList;
use App\Livewire\Membership\TierForm;
use App\Livewire\Membership\TierIndex;
use App\Livewire\Membership\VoucherForm;
use App\Livewire\Membership\VoucherIndex;
use App\Livewire\POS\Index;
use App\Livewire\Purchasing\PurchaseOrderForm;
use App\Livewire\Purchasing\PurchaseOrderList;
use App\Livewire\Purchasing\PurchaseOrderShow;
use App\Livewire\Reports\CrmConversionReport;
use App\Livewire\Reports\CustomerSpendingReport;
use App\Livewire\Reports\ExportBasic;
use App\Livewire\Reports\InventoryMovementReport;
use App\Livewire\Reports\MarketplaceSyncReport;
use App\Livewire\Reports\PurchaseSummaryReport;
use App\Livewire\Reports\SalesByBranchReport;
use App\Livewire\Reports\SalesByCashierReport;
use App\Livewire\Roles\RoleIndex;
use App\Livewire\Sales\Receipt;
use App\Livewire\Sales\ReturnForm;
use App\Livewire\Sales\ReturnList;
use App\Livewire\Sales\Show;
use App\Livewire\Tenants\TenantForm;
use App\Livewire\Tenants\TenantList;
use App\Livewire\Users\UserForm;
use App\Livewire\Users\UserIndex;
use App\Livewire\Saas\SubscriptionPlanList;
use App\Livewire\Saas\TenantDomainList;
use App\Livewire\Saas\PaymentGatewayManager;
use App\Livewire\Settings\DomainSettings;
use App\Livewire\Settings\WebhookSettings;
use App\Livewire\Settings\WebhookLogs;
use App\Livewire\Customer\Customer360View;
use App\Livewire\Customer\RFMDashboard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', SummaryDashboard::class)->name('dashboard')->middleware('can:view dashboard');
    Route::get('owner-dashboard', OwnerDashboard::class)->name('owner-dashboard')->middleware('can:view owner dashboard');
    Route::get('pos', Index::class)->name('pos.index')->middleware('can:access pos');
    Route::view('profile', 'profile')->name('profile');

    // Management (Super Admin only)
    Route::middleware('can:manage tenants')->group(function () {
        Route::get('tenants', TenantList::class)->name('tenants.index');
        Route::get('tenants/create', TenantForm::class)->name('tenants.create');
        Route::get('tenants/{tenant}/edit', TenantForm::class)->name('tenants.edit');
    });

    Route::middleware('can:view branches')->group(function () {
        Route::get('branches', BranchList::class)->name('branches.index');
        Route::get('branches/create', BranchForm::class)->name('branches.create')->middleware('can:manage branches');
        Route::get('branches/{branch}/edit', BranchForm::class)->name('branches.edit')->middleware('can:manage branches');
    });

    Route::middleware('can:view users')->group(function () {
        Route::get('users', UserIndex::class)->name('users.index');
        Route::get('users/create', UserForm::class)->name('users.create')->middleware('can:manage users');
        Route::get('users/{user}/edit', UserForm::class)->name('users.edit')->middleware('can:manage users');
    });

    Route::middleware('can:view roles')->group(function () {
        Route::get('roles', RoleIndex::class)->name('roles.index');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('domain', DomainSettings::class)->name('domain')->middleware('can:manage custom domain');
        Route::get('webhooks', WebhookSettings::class)->name('webhooks')->middleware('can:manage webhooks');
        Route::get('webhooks/logs', WebhookLogs::class)->name('webhooks.logs')->middleware('can:manage webhooks');
    });

    // Master Data
    Route::prefix('master-data')->name('master-data.')->group(function () {
        Route::middleware('can:view categories')->group(function () {
            Route::get('categories', CategoryList::class)->name('categories');
        });

        Route::middleware('can:view brands')->group(function () {
            Route::get('brands', BrandList::class)->name('brands');
        });

        Route::middleware('can:view units')->group(function () {
            Route::get('units', UnitList::class)->name('units');
        });

        Route::middleware('can:view products')->group(function () {
            Route::get('products', ProductList::class)->name('products');
            Route::get('products/create', ProductForm::class)->name('products.create')->middleware('can:manage products');
            Route::get('products/{product}', ProductDetail::class)->name('products.show');
            Route::get('products/{product}/prices', ProductPriceManagement::class)->name('products.prices');
            Route::get('products/{productId}/edit', ProductForm::class)->name('products.edit')->middleware('can:manage products');
        });

        Route::middleware('can:view customers')->group(function () {
            Route::get('customers', CustomerList::class)->name('customers');
            Route::get('customer-groups', CustomerGroupList::class)->name('customer-groups');
        });

        Route::middleware('can:view suppliers')->group(function () {
            Route::get('suppliers', SupplierList::class)->name('suppliers');
            Route::get('suppliers/{supplierId}', SupplierDetail::class)->name('suppliers.show');
        });

        Route::get('bulk-import', BulkImport::class)->name('bulk-import')->middleware('can:manage bulk import');
        Route::get('barcode-print', BarcodePrint::class)->name('barcode-print')->middleware('can:view products');
    });

    // Inventory
    Route::prefix('inventory')->name('inventory.')->middleware('can:view inventory')->group(function () {
        Route::get('/', InventoryList::class)->name('index');
        Route::get('opening-stock', OpeningStock::class)->name('opening-stock')->middleware('can:opening stock');

        Route::middleware('can:stock adjustment')->group(function () {
            Route::get('adjustments', StockAdjustmentList::class)->name('adjustments.index');
            Route::get('adjustments/create', StockAdjustmentForm::class)->name('adjustments.create');
        });

        Route::middleware('can:manage transfers')->group(function () {
            Route::get('transfers', StockTransferList::class)->name('transfers.index');
            Route::get('transfers/create', StockTransferForm::class)->name('transfers.create');
            Route::get('transfers/{stockTransfer}', StockTransferShow::class)->name('transfers.show');
        });
    });

    // Sales
    Route::prefix('sales')->name('sales.')->middleware('can:view sales')->group(function () {
        Route::get('/', App\Livewire\Sales\Index::class)->name('index');
        Route::get('returns', ReturnList::class)->name('returns.index')->middleware('can:view returns');
        Route::get('returns/create', ReturnForm::class)->name('returns.create')->middleware('can:manage returns');
        Route::get('/{saleId}', Show::class)->name('show');
        Route::get('/{saleId}/receipt', Receipt::class)->name('receipt');
    });

    // Invoices
    Route::prefix('invoices')->name('invoices.')->middleware('can:view sales')->group(function () {
        Route::get('/', App\Livewire\Invoice\InvoiceList::class)->name('index');
        Route::get('/{invoice}', App\Livewire\Invoice\InvoiceShow::class)->name('show');
    });

    // Purchasing
    Route::prefix('purchasing')->name('purchasing.')->middleware('can:view purchases')->group(function () {
        Route::get('/', PurchaseOrderList::class)->name('index');
        Route::get('/create', PurchaseOrderForm::class)->name('create')->middleware('can:create purchases');
        Route::get('/{purchaseOrder}', PurchaseOrderShow::class)->name('show');
        Route::get('/{purchaseOrder}/edit', PurchaseOrderForm::class)->name('edit')->middleware('can:create purchases');
    });

    // Expenses
    Route::prefix('expenses')->name('expenses.')->middleware('can:view expenses')->group(function () {
        Route::get('/', ExpenseList::class)->name('index');
        Route::get('/create', ExpenseForm::class)->name('create')->middleware('can:manage expenses');
        Route::get('/categories', ExpenseCategoryList::class)->name('categories');
    });

    // Accounting - AR/AP
    Route::prefix('accounting')->name('accounting.')->middleware('can:view reports')->group(function () {
        Route::get('accounts-receivable', \App\Livewire\Accounting\ArList::class)->name('ar-list');
        Route::get('accounts-payable', \App\Livewire\Accounting\ApList::class)->name('ap-list');
        Route::get('chart-of-accounts', \App\Livewire\Accounting\ChartOfAccountList::class)->name('chart-of-accounts.index');
        Route::get('chart-of-accounts/create', \App\Livewire\Accounting\ChartOfAccountForm::class)->name('chart-of-accounts.create');
        Route::get('chart-of-accounts/{chartOfAccount}/edit', \App\Livewire\Accounting\ChartOfAccountForm::class)->name('chart-of-accounts.edit');
        Route::get('journal-entries', \App\Livewire\Accounting\JournalEntryList::class)->name('journal-entries.index');
        Route::get('journal-entries/create', \App\Livewire\Accounting\JournalEntryForm::class)->name('journal-entries.create');
        Route::get('journal-entries/{journalEntry}/edit', \App\Livewire\Accounting\JournalEntryForm::class)->name('journal-entries.edit');
        Route::get('journal-entries/{journalEntry}', \App\Livewire\Accounting\JournalEntryForm::class)->name('journal-entries.show');
        
        Route::get('trial-balance', \App\Livewire\Accounting\TrialBalanceReport::class)->name('trial-balance')->middleware('can:view trial balance');
        Volt::route('profit-loss', 'accounting.profit-loss-report')->name('profit-loss')->middleware('can:view income statement');
        Volt::route('balance-sheet', 'accounting.balance-sheet')->name('balance-sheet')->middleware('can:view balance sheet');
        Volt::route('cash-flow', 'accounting.cash-flow-report')->name('cash-flow')->middleware('can:view income statement');
    });

    // CRM
    Route::prefix('crm')->name('crm.')->middleware('can:view leads')->group(function () {
        Route::get('leads', LeadList::class)->name('leads.index');
        Route::get('leads/create', LeadForm::class)->name('leads.create')->middleware('can:manage leads');
        Route::get('leads/{leadId}', LeadShow::class)->name('leads.show');
        Route::get('leads/{lead}/edit', LeadForm::class)->name('leads.edit')->middleware('can:manage leads');

        Route::middleware('can:view proposals')->group(function () {
            Route::get('proposals', ProposalList::class)->name('proposals.index');
            Route::get('proposals/create', ProposalForm::class)->name('proposals.create');
            Route::get('proposals/{proposalId}', ProposalShow::class)->name('proposals.show');
        });

        Route::prefix('campaigns')->name('campaigns.')->middleware('can:manage leads')->group(function () {
            Route::get('/', CampaignList::class)->name('index');
            Route::get('create', CampaignForm::class)->name('form');
            Route::get('{campaignId}/edit', CampaignForm::class)->name('form');
        });
    });

    // Customer 360 View & RFM Analysis
    Route::middleware('can:view customers')->group(function () {
        Route::get('customers/{customer}/360', Customer360View::class)->name('customers.360');
        Route::get('customers/rfm', RFMDashboard::class)->name('customers.rfm');
    });

    // Loyalty & Membership
    Route::prefix('membership')->name('membership.')->middleware('can:view loyalty')->group(function () {
        Route::get('tiers', TierIndex::class)->name('tiers.index');
        Route::get('tiers/create', TierForm::class)->name('tiers.create')->middleware('can:manage loyalty');
        Route::get('tiers/{tierId}/edit', TierForm::class)->name('tiers.edit')->middleware('can:manage loyalty');

        Route::middleware('can:view vouchers')->group(function () {
            Route::get('vouchers', VoucherIndex::class)->name('vouchers.index');
            Route::get('vouchers/create', VoucherForm::class)->name('vouchers.create')->middleware('can:manage vouchers');
            Route::get('vouchers/{voucherId}/edit', VoucherForm::class)->name('vouchers.edit')->middleware('can:manage vouchers');
        });
    });

    // Reports
    Route::prefix('reports')->name('reports.')->middleware('can:view reports')->group(function () {
        Route::get('export', ExportBasic::class)->name('export');
        Route::get('sales/branch', SalesByBranchReport::class)->name('sales.branch');
        Route::get('sales/cashier', SalesByCashierReport::class)->name('sales.cashier');
        Route::get('inventory/movement', InventoryMovementReport::class)->name('inventory.movement');
        Route::get('customers/spending', CustomerSpendingReport::class)->name('customers.spending');
        Route::get('purchases/summary', PurchaseSummaryReport::class)->name('purchases.summary');
        Route::get('crm/conversion', CrmConversionReport::class)->name('crm.conversion');
        Route::get('marketplace/sync', MarketplaceSyncReport::class)->name('marketplace.sync');
    });

    // Omnichannel Marketplace
    Route::prefix('omnichannel')->name('omnichannel.')->middleware('can:view marketplace')->group(function () {
        Route::middleware('can:manage marketplace accounts')->group(function () {
            Route::get('accounts', AccountIndex::class)->name('accounts.index');
            Route::get('accounts/create', AccountForm::class)->name('accounts.create');
            Route::get('accounts/{accountId}/edit', AccountForm::class)->name('accounts.edit');
        });

        Route::middleware('can:manage marketplace shops')->group(function () {
            Route::get('shops', ShopList::class)->name('shops.index');
        });

        Route::middleware('can:manage product mapping')->group(function () {
            Route::get('products/map', ProductMap::class)->name('products.map');
        });

        Route::middleware('can:view sync logs')->group(function () {
            Route::get('sync-logs', SyncLogs::class)->name('sync-logs.index');
        });
    });

    // SaaS Subscription Plans (Super Admin only)
    Route::middleware('can:view subscription plans')->group(function () {
        Route::get('subscription-plans', SubscriptionPlanList::class)->name('subscription-plans.index');
        Route::get('custom-domains', TenantDomainList::class)->name('custom-domains.index')->middleware('can:manage tenants');
        Route::get('payment-gateway-configs', PaymentGatewayManager::class)->name('payment-gateway-configs.index');
    });
});

Route::prefix('portal')->name('customer.')->group(function () {
    Route::get('login', Login::class)->name('login');
    Route::post('logout', function () {
        Auth::guard('customer')->logout();

        return redirect()->route('customer.login');
    })->name('logout');

    Route::middleware(['auth:customer'])->group(function () {
        Route::get('/', Dashboard::class)->name('dashboard');
        Route::get('orders', Orders::class)->name('orders');
        Route::get('profile', Profile::class)->name('profile');
    });
});

require __DIR__ . '/auth.php';
