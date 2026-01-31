<?php

namespace App\Livewire\Items;

use App\Models\ItemType;
use App\Models\stockins;
use App\Models\User;
use App\Traits\AlertTrait;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;


class ItemTypeForm extends Component
{
    use AlertTrait;

    public $name = '';
    public $isCreate = false;
    public $isView = false;
    public $isEdit = false;
    public $itemType;
    public $itemTypeId;
    public $title;
    public $model;


    public function mount($id)
    {

        if ($id == 'createForm') {
            $this->isCreate = true;
        } else {
            $this->isView = true;
            $this->itemTypeId = $id;
            $this->itemType = ItemType::find($id);
            $this->name = $this->itemType->name;
        }
      
    }

    // start of the Component Logics

    public function save()
    {
        $this->validate([
            'name' => 'required|unique:item_types,name',
        ]);
        $itemType = new itemType();
        $itemType->name = $this->name;
        $itemType->save();

        $action = 'itemType  creation';
        $description = 'Successfully created ' . $itemType->name;

        User::saveAuditTrail($action, $description);

        $this->showAlert('success', $action, $description);
        return $this->redirect(route('itemTypes'), navigate: true);
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|unique:item_types,name',
        ]);
        $this->itemType->name = $this->name;
        $this->itemType->update();



        $action = 'itemType  Updation';
        $description = 'Successfully updated ' . $this->itemType->name;

        User::saveAuditTrail($action, $description);

        $this->showAlert('success', $action, $description);
        return $this->redirect(route('itemTypes'), navigate: true);
    }

     public function delete()
    {
        LivewireAlert::title('Delete itemType')->text('Are you sure you want to delete this itemType ?')->asConfirm()->onConfirm('commitDelete')->show();
    }


    public function commitDelete()
    {
        $this->itemType->delete();

        $action = 'itemType  Deleation';
        $description = 'Successfully deleted ' . $this->itemType->name;

        User::saveAuditTrail($action, $description);

        $this->showAlert('success', $action, $description);
        return $this->redirect(route('itemTypes'), navigate: true);
    }

    // End of the Component Logics

    // start of Ui configurations

    public function edit()
    {
        $this->isView = false;
        $this->isEdit = true;
        $this->isCreate = false;
    }

    public function back()
    {
        $this->redirect(route('itemTypes'), navigate: true);
    }

    public function cancel()
    {
        $this->isView = true;
        $this->isEdit = false;
        $this->isCreate = false;

        $this->redirect(route('itemType.show', $this->itemTypeId), navigate: true);
    }

    // end of Ui configurations

    public function getTitleProperty()
    {
        if ($this->isEdit && $this->itemType) {
            return $this->title = strtoupper($this->itemType->name . ' edit page');
        }

        if ($this->isView && $this->itemType) {
            return $this->title = strtoupper($this->itemType->name . ' view page');
        }

        if ($this->isCreate) {
            return $this->title = strtoupper('itemType create page');
        }

        return $this->title;
    }

    public function render()
    {
         view()->share('title', $this->getTitleProperty());
        return view('livewire.items.item-type-form');
    }
}
