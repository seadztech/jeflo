<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\Transaction;
use App\Models\Sale;
use App\Models\Allocation;
use App\Models\Transactions;
use App\Traits\AlertTrait;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class TransactionForm extends Component
{
    use AlertTrait;

    public $transactionId;
    public $transaction;
    public $allocations = [];
    
    // For adding new allocation
    public $selectedSaleId = '';
    public $allocatedAmount = 0;
    public $allocationNotes = '';
    
    // Search
    public $searchQuery = '';
    public $availableSales = [];
    
    // Calculated values
    public $remainingAmount = 0;
    public $totalAllocated = 0;

    public $allocationToRemove;
    
    public function mount($id)
    {
        $this->transactionId = $id;
        $this->loadData();
    }
    
    public function loadData()
    {
        // 1. Get the transaction with allocations
        $this->transaction = Transactions::with(['allocations.sale'])->find($this->transactionId);
        
        if (!$this->transaction) {
            return;
        }
        
        // 2. Get allocations
        $this->allocations = $this->transaction->allocations;
        
        // 3. Calculate totals
        $this->totalAllocated = $this->transaction->allocations()->sum('amount');
        $this->remainingAmount = max(0, $this->transaction->amount - $this->totalAllocated);
        
        // 4. Load available sales
        $this->loadAvailableSales();
    }
    
public function loadAvailableSales()
{
    $query = Sale::query()->where('status', '=', 'pending');
    
    // Search filter
    if ($this->searchQuery) {
        $query->where(function($q) {
            $q->where('id', 'like', "%{$this->searchQuery}%")
              ->orWhere('total_amount', 'like', "%{$this->searchQuery}%");
        });
    }
    
    $this->availableSales = $query->orderBy('created_at', 'desc')
        ->take(10)
        ->get();
}
    
    public function updatedSearchQuery()
    {
        $this->loadAvailableSales();
    }
    
    public function selectSale($saleId)
    {
        $sale = Sale::find($saleId);
        if ($sale) {
            $this->selectedSaleId = $saleId;
            // Suggest allocation amount
            $suggestedAmount = min($this->remainingAmount, $sale->unallocated_amount);
            $this->allocatedAmount = $suggestedAmount > 0 ? $suggestedAmount : 0;
        }
    }
    
    public function addAllocation()
    {
        $this->validate([
            'selectedSaleId' => 'required|exists:sales,id',
            'allocatedAmount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:' . $this->remainingAmount,
                function ($attribute, $value, $fail) {
                    $sale = Sale::find($this->selectedSaleId);
                    if ($sale && $value > $sale->unallocated_amount) {
                        $fail('Cannot allocate more than sale\'s unallocated amount (Ksh ' . number_format($sale->unallocated_amount, 2) . ')');
                    }
                }
            ],
        ]);

        // dd($this->sale);
        
        // Create allocation
        Allocation::create([
            'transactions_id' => $this->transactionId,
            'sale_id' => $this->selectedSaleId,
            'amount' => $this->allocatedAmount,
            'notes' => $this->allocationNotes,
            'allocated_by' => auth()->id(),
            'allocated_at' => now(),
        ]);

        
        
        // Reset form
        $this->reset(['selectedSaleId', 'allocatedAmount', 'allocationNotes', 'searchQuery']);
        
        // Reload data
        $this->loadData();
        
        $this->showAlert('success', 'Success', 'Allocation added successfully!');
    }
    
    public function editAllocation($allocationId)
    {
        $allocation = Allocation::find($allocationId);
        if ($allocation) {
            $this->selectedSaleId = $allocation->sale_id;
            $this->allocatedAmount = $allocation->amount;
            $this->allocationNotes = $allocation->notes;
            
            // Remove the old allocation
            $allocation->delete();
            $this->loadData();
            
            $this->showAlert('info', 'Edit Mode', 'Now editing allocation. Adjust amount and click "Add Allocation" to save changes.');
        }
    }
    
    public function removeAllocation($allocationId)
    {
       
         LivewireAlert::title('Remove Allocation')
                ->text("Are you sure you want to remove this allocation?")
                ->asConfirm()
                ->onConfirm('confirmRemoveAllocation')
                ->show();
        
        $this->allocationToRemove = $allocationId;
    }
    
    public function confirmRemoveAllocation()
    {
        $allocation = Allocation::find($this->allocationToRemove);
        if ($allocation) {
            $allocation->delete();
            $this->loadData();
            $this->showAlert('success', 'Success', 'Allocation removed successfully!');
        }

        
        $this->allocationToRemove = null;
    }
    
    public function render()
    {
        return view('livewire.transactions.transaction-form');
    }
}