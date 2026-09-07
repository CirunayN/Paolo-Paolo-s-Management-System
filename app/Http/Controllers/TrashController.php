<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Customer;

class TrashController extends Controller
{
    /**
     * Display the centralized Trash / Archive module
     * Shows all soft-deleted products and customer profiles in one place.
     */
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'products');

        $archivedProductsCount = Product::onlyTrashed()->count();
        $archivedCustomersCount = Customer::onlyTrashed()->count();

        $archivedProducts = Product::onlyTrashed()
            ->with('category')
            ->latest('deleted_at')
            ->paginate(10, ['*'], 'products_page');

        $archivedCustomers = Customer::onlyTrashed()
            ->withCount('orders')
            ->latest('deleted_at')
            ->paginate(10, ['*'], 'customers_page');

        return view('trash.index', compact(
            'activeTab',
            'archivedProductsCount',
            'archivedCustomersCount',
            'archivedProducts',
            'archivedCustomers'
        ));
    }
}
