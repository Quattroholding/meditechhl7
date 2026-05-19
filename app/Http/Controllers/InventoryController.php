<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display a listing of inventory items.
     */
    public function index(Request $request)
    {
        return view('inventories.index');
    }

    /**
     * Show the form for creating a new inventory item.
     */
    public function create()
    {
        return view('inventories.create');
    }

    /**
     * Show the form for editing an inventory item.
     */
    public function edit($itemId)
    {
        return view('inventories.edit', compact('itemId'));
    }

    /**
     * Display the stock management interface.
     */
    public function stockIndex()
    {
        return view('inventories.stock');
    }

    /**
     * Display the low stock report.
     */
    public function lowStockReport()
    {
        return view('inventories.low-stock');
    }

    /**
     * Display the transaction history.
     */
    public function transactionHistory()
    {
        return view('inventories.transactions');
    }
}
