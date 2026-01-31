<?php

namespace App\Livewire\Branches;

use App\Models\Branch;
use App\Models\User;
use App\Traits\AlertTrait;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;

class BranchForm extends Component
{
     use AlertTrait;

    public $name = '';
    public $isCreate = false;
    public $isView = false;
    public $isEdit = false;
    public $branch;
    public $branchId;
    public $title;

    public function mount($id)
    {
        if ($id == 'createForm') {
            $this->isCreate = true;
        } else {
            $this->isView = true;
            $this->branchId = $id;
            $this->branch = Branch::find($id);
            $this->name = $this->branch->name;
        }
    }

    // start of the Component Logics

    public function save()
    {
        $this->validate([
            'name' => 'required|unique:branches,name',
        ]);
        $branch = new branch();
        $branch->name = $this->name;
        $branch->location = $this->name;
        $branch->company_id = 0;
        $branch->save();

        $action = 'Branch  creation';
        $description = 'Successfully created ' . $branch->name;

        User::saveAuditTrail($action, $description);

        $this->showAlert('success', $action, $description);
        return $this->redirect(route('branches'), navigate: true);
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|unique:branches,name',
        ]);
        $this->branch->name = $this->name;
        $this->branch->location = $this->name;
        $this->branch->company_id = 0;
        $this->branch->update();

        $action = 'Branch  Updation';
        $description = 'Successfully updated ' . $this->branch->name;

        User::saveAuditTrail($action, $description);

        $this->showAlert('success', $action, $description);
        return $this->redirect(route('branches'), navigate: true);
    }

     public function delete()
    {
        LivewireAlert::title('Delete branch')->text('Are you sure you want to delete this branch ?')->asConfirm()->onConfirm('commitDelete')->show();
    }


    public function commitDelete()
    {
        $this->branch->delete();

        $action = 'Branch  Deleation';
        $description = 'Successfully deleted ' . $this->branch->name;

        User::saveAuditTrail($action, $description);

        $this->showAlert('success', $action, $description);
        return $this->redirect(route('branches'), navigate: true);
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
        $this->redirect(route('branches'), navigate: true);
    }

    public function cancel()
    {
        $this->isView = true;
        $this->isEdit = false;
        $this->isCreate = false;


    }

    // end of Ui configurations

    public function getTitleProperty()
    {
        if ($this->isEdit && $this->branch) {
            return $this->title = strtoupper($this->branch->name . ' edit page');
        }

        if ($this->isView && $this->branch) {
            return $this->title = strtoupper($this->branch->name . ' view page');
        }

        if ($this->isCreate) {
            return $this->title = strtoupper('branch create page');
        }

        return $this->title;
    }

    public function render()
    {
         view()->share('title', $this->getTitleProperty());
        return view('livewire.branches.branch-form');
    }
}
