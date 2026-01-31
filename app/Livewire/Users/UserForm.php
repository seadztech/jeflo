<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Models\User;
use App\Models\Branch;
use App\Traits\AlertTrait;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserForm extends Component
{
    use AlertTrait;

    public $isCreate = false;
    public $isView = false;
    public $isEdit = false;
    public $user;
    public $userId;
    public $title;
    public $roles = [];
    public $allPermissions = [];
    public $assignedRoles = [];
    public $branches = [];

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public $selectedRoles = [];
    public $permissions = [];
    public $branch_id = null;

    public function mount($id)
    {
        $this->roles = Role::all();
        $this->allPermissions = Permission::all();
        $this->branches = Branch::orderBy('name')->get();

        if ($id === 'createForm') {
            $this->isCreate = true;
            // Set default branch to current user's branch for create mode
            $this->branch_id = auth()->user()->branch_id ?? null;
        } else {
            $this->isView = true;
            $this->userId = $id;
            $this->user = User::findOrFail($id);
            $this->name = $this->user->name;
            $this->email = $this->user->email;
            $this->branch_id = $this->user->branch_id;
            $this->selectedRoles = $this->user->roles->pluck('name')->toArray();
            $this->permissions = $this->user->permissions->pluck('id')->toArray();
            $this->assignedRoles = $this->user->roles;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
            'selectedRoles' => 'required|array',
            'selectedRoles.*' => 'exists:roles,name',
            'branch_id' => 'required|exists:branches,id'
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'branch_id' => $this->branch_id,
        ]);

        $user->syncRoles($this->selectedRoles);
        $user->syncPermissions($this->permissions);

        User::saveAuditTrail('User creation', 'Successfully created ' . $user->name);
        $this->showAlert('success', 'User creation', 'Successfully created ' . $user->name);
        return $this->redirect(route('users'), navigate: true);
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'selectedRoles' => 'required|array',
            'selectedRoles.*' => 'exists:roles,name',
            'branch_id' => 'required|exists:branches,id'
        ]);

        $updateData = [
            'name' => $this->name,
            'email' => $this->email,
            'branch_id' => $this->branch_id,
        ];

        // Only update password if provided
        if ($this->password) {
            $updateData['password'] = Hash::make($this->password);
        }

        $this->user->update($updateData);

        $this->user->syncRoles($this->selectedRoles);
        $this->user->syncPermissions($this->permissions);

        User::saveAuditTrail('User update', 'Successfully updated ' . $this->user->name);
        $this->showAlert('success', 'User update', 'Successfully updated ' . $this->user->name);
        return $this->redirect(route('users'), navigate: true);
    }

    public function delete()
    {
        LivewireAlert::title('Delete user')->text('Are you sure you want to delete this user?')->asConfirm()->onConfirm('commitDelete')->show();
    }

    public function commitDelete()
    {
        $this->user->delete();
        User::saveAuditTrail('User deletion', 'Successfully deleted ' . $this->user->name);
        $this->showAlert('success', 'User deletion', 'Successfully deleted ' . $this->user->name);
        return $this->redirect(route('users'), navigate: true);
    }

    public function edit()
    {
        $this->isView = false;
        $this->isEdit = true;
        $this->isCreate = false;
    }

    public function back()
    {
        $this->redirect(route('users'), navigate: true);
    }

    public function cancel()
    {
        $this->isView = true;
        $this->isEdit = false;
        $this->isCreate = false;
        // Reset password fields
        $this->password = '';
        $this->password_confirmation = '';
    }

    public function getTitleProperty()
    {
        if ($this->isEdit && $this->user) {
            return $this->title = strtoupper($this->user->name . ' edit page');
        }

        if ($this->isView && $this->user) {
            return $this->title = strtoupper($this->user->name . ' view page');
        }

        if ($this->isCreate) {
            return $this->title = strtoupper('user create page');
        }

        return $this->title;
    }

    public function render()
    {
        view()->share('title', $this->getTitleProperty());
        return view('livewire.users.user-form');
    }
}