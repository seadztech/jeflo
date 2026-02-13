<?php

namespace App\Livewire\Stocks;

use Livewire\Component;
use App\Models\StockChange;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\stockins;
use App\Traits\AlertTrait;
use Carbon\Carbon;
use Exception;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Support\Facades\DB;

class StockChanges extends Component
{
    use AlertTrait;

    public $stockinId;
    public $model;
    public $title;
    public $itemsPerPage = 10;
    public $showCreateButton = false;
    public $search = '';
    public $isAlterStockin = false;

    public $stockinQuantity;
    public $reason;
    public $stockin;
    public $stockchange;

    public function mount($id)
    {

        // dd($id);
        $this->stockinId = (int)$id;
        $this->model = 'App\Models\StockChange';
        // $this->stockchange = StockChange::with('stockin.item')->find($id);
        $this->stockchange = StockChange::with('stockin.item')->where('stockins_id',$id)->first();
        // dd($this->stockchange);
        $this->stockin = $this->stockchange->stockin;


        $item = $this->stockin->item->name;

        // $date = Carbon::parse($this->stockin->created_at)->format('l, jS F Y');
        // $date = Carbon::parse($this->stockin->created_at)->format('F Y');

        $this->title = "$item Stock changes";

        // dd(StockChange::with('actionBy')->first()->actionBy->name);
    }

    public function removeStock()
    {
        $this->isAlterStockin = !$this->isAlterStockin;
    }

    public function commitRemoveStock()
    {
        (int)$this->stockinQuantity;
        LivewireAlert::title('Stock Removal Action ')
            ->text("Are you sure you want to remove  $this->stockinQuantity stock items for $this->reason?")
            ->asConfirm()
            ->onConfirm('actualizeCommitRemoveStock')
            ->show();
    }

    public function actualizeCommitRemoveStock()
    {
        $this->validate([
            'reason' => 'required',
            'stockinQuantity' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $newQuantity = (int)$this->stockin->quantity - (int)$this->stockinQuantity;
            $this->stockin->quantity = $newQuantity;
            $this->stockin->save();

            $change = new StockChange();
            $change->stockins_id = $this->stockinId;
            $change->quantity = $this->stockinQuantity;
            $change->actionBy = Auth::user()->id;
            $change->changeType = 'decrement';
            $change->reason = $this->reason;
            $change->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $this->showAlert('error', 'Exception Error', $e->getMessage());
        }

        $action = 'Stock Removal Action';
        $description = "Successfully removed $change->quantity from stockinID $change->stockins_id";

        User::saveAuditTrail($action, $description);
        $this->showAlert('success', $action, $description);

        return $this->redirect(route('stockChanges.show', $this->stockinId), navigate: true);
    }
    public function render()
    {
        return view('livewire.stocks.stock-changes');
    }
}
