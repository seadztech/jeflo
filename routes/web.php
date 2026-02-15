
<?php

use App\Http\Controllers\api\PaymentController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\ReceiptController;
use App\Livewire\Dashboard;
use App\Livewire\Branches\Branches;
use App\Livewire\Branches\BranchForm;
use App\Livewire\Customers\CustomerForm;
use App\Livewire\Customers\Customers;
use App\Livewire\Items\Items;
use App\Livewire\Items\ItemForm;
use App\Livewire\Items\ItemTypes;
use App\Livewire\Items\ItemTypeForm;
use App\Livewire\POS\PointOfSale;
use App\Livewire\Receipt;
use App\Livewire\Reports\Reports;
use App\Livewire\Roles\Roles;
use App\Livewire\Roles\RoleForm;
use App\Livewire\Sales\Sales;
use App\Livewire\Sales\SalesForm;
use App\Livewire\ShopPos;
use App\Livewire\Stocks\StockChanges;
use App\Livewire\Transactions\Transactions;
use App\Livewire\Transactions\TransactionForm;
use App\Livewire\Users\Users;
use App\Livewire\Users\UserForm;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public / Auth Routes
|--------------------------------------------------------------------------
*/

Route::get('redirect', [GoogleAuthController::class, 'redirect'])->name('redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callbackFunction'])->name('callbackFunction');

// Route::post('stk/callback', [PaymentController::class, 'stkCallback'])->name('stk.callback');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/', Dashboard::class)
        ->middleware('permission:view dashboard')
        ->name('dashboard');

    Route::view('profile', 'profile')->name('profile');

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */
    Route::get('users', Users::class)
        ->middleware('permission:view users')
        ->name('users');

    Route::get('user/form/{id}', UserForm::class)
        ->middleware('permission:create users|edit users')
        ->name('user.show');

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */
    Route::get('roles', Roles::class)
        ->middleware('permission:view roles')
        ->name('roles');

    Route::get('role/form/{id}', RoleForm::class)
        ->middleware('permission:create roles|edit roles')
        ->name('role.show');

    /*
    |--------------------------------------------------------------------------
    | Branches
    |--------------------------------------------------------------------------
    */
    Route::get('branches', Branches::class)
        ->middleware('permission:view branches')
        ->name('branches');

    Route::get('branch/form/{id}', BranchForm::class)
        ->middleware('permission:create branches|edit branches')
        ->name('branch.show');

    /*
    |--------------------------------------------------------------------------
    | Item Types
    |--------------------------------------------------------------------------
    */
    Route::get('items/types', ItemTypes::class)
        ->middleware('permission:view item types')
        ->name('itemTypes');

    Route::get('itemType/form/{id}', ItemTypeForm::class)
        ->middleware('permission:create item types|edit item types')
        ->name('itemType.show');

    /*
    |--------------------------------------------------------------------------
    | Items & Stock
    |--------------------------------------------------------------------------
    */
    Route::get('items', Items::class)
        ->middleware('permission:view items')
        ->name('items');

    Route::get('item/form/{id}', ItemForm::class)
        ->middleware('permission:create items|edit items')
        ->name('item.show');

    Route::get('item-stock-changes-{id}', StockChanges::class)
        ->middleware('permission:view stock changes')
        ->name('stockChanges.show');

    /*
    |--------------------------------------------------------------------------
    | Transactions
    |--------------------------------------------------------------------------
    */
    Route::get('transactions', Transactions::class)
        ->middleware('permission:view transactions')
        ->name('transactions');

    Route::get('transaction/form/{id}', TransactionForm::class)
        ->middleware('permission:view transactions')
        ->name('transaction.show');

     /*
    |--------------------------------------------------------------------------
    | Customers
    |--------------------------------------------------------------------------
    */

    Route::get('customers', Customers::class)
    // ->middleware('permission: view customers')
    ->name('customers');


    Route::get('customer/{id}', CustomerForm::class)
    // ->middleware('permission: view customers')
    ->name('customer.show');



    /*
    |--------------------------------------------------------------------------
    | POS & Sales
    |--------------------------------------------------------------------------
    */
    Route::get('point/of/sale', ShopPos::class)
        ->middleware('permission:access pos')
        ->name('pos');

    Route::get('sales', Sales::class)
        ->middleware('permission:view sales')
        ->name('sales');

    Route::get('sales/form/{id}', SalesForm::class)
        ->middleware('permission:create sales')
        ->name('sale.show');

    Route::get('sales/receipt/{id}', Receipt::class)
        ->middleware('permission:view receipts')
        ->name('sale.receipt');

    Route::get('sales/{id}/receipt/pdf', [ReceiptController::class, 'generate'])
        ->middleware('permission:view receipts')
        ->name('sales.receipt.pdf');

    Route::get('print/sales/{id}/receipt/pdf', [ReceiptController::class, 'print'])
        ->middleware('permission:view receipts')
        ->name('print.receipt.pdf');

    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */
    Route::get('reports', Reports::class)
        ->middleware('permission:view reports')
        ->name('reports');
});

require __DIR__ . '/auth.php';
