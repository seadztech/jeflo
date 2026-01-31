<?php

namespace App\Livewire\Roles;

use App\Models\User;
use App\Traits\AlertTrait;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class RoleForm extends Component
{
    use AlertTrait;

    public string $name = '';
    public bool $isCreate = false;
    public bool $isView = false;
    public bool $isEdit = false;

    public $role;
    public $roleId;
    public $title;

    public $permissions;
    public array $selectedPermissions = [];

    public function mount($id)
    {
        $this->permissions = Permission::orderBy('name')->get();

        if ($id === 'createForm') {
            $this->isCreate = true;
            return;
        }

        $this->roleId = $id;
        $this->role = Role::findOrFail($id);
        $this->isView = true;

        $this->name = $this->role->name;
        $this->selectedPermissions = $this->role
            ->permissions
            ->pluck('name')
            ->toArray();
    }

    /* =========================
        CREATE ROLE
    ==========================*/
    public function save()
    {
        $this->validate([
            'name' => 'required|unique:roles,name',
            'selectedPermissions' => 'array'
        ]);

        $role = Role::create(['name' => $this->name]);
        $role->syncPermissions($this->selectedPermissions);

        User::saveAuditTrail(
            'Role Creation',
            'Created role ' . $role->name
        );

        $this->showAlert('success', 'Success', 'Role created successfully');
        return $this->redirect(route('roles'), navigate: true);
    }

    /* =========================
        UPDATE ROLE
    ==========================*/
    public function update()
    {
        $this->validate([
            'name' => 'required|unique:roles,name,' . $this->role->id,
            'selectedPermissions' => 'array'
        ]);

        $this->role->update(['name' => $this->name]);
        $this->role->syncPermissions($this->selectedPermissions);

        User::saveAuditTrail(
            'Role Update',
            'Updated role ' . $this->role->name
        );

        $this->showAlert('success', 'Success', 'Role updated successfully');
        return $this->redirect(route('roles'), navigate: true);
    }

    /* =========================
        DELETE ROLE
    ==========================*/
    public function delete()
    {
        LivewireAlert::title('Delete Role')
            ->text('Are you sure you want to delete this role?')
            ->asConfirm()
            ->onConfirm('commitDelete')
            ->show();
    }

    public function commitDelete()
    {
        $this->role->delete();

        User::saveAuditTrail(
            'Role Deletion',
            'Deleted role ' . $this->role->name
        );

        $this->showAlert('success', 'Deleted', 'Role deleted');
        return $this->redirect(route('roles'), navigate: true);
    }

    /* =========================
        UI STATE CONTROLS
    ==========================*/
    public function edit()
    {
        $this->isView = false;
        $this->isEdit = true;
    }

    public function cancel()
    {
        $this->isView = true;
        $this->isEdit = false;
    }

    public function back()
    {
        return $this->redirect(route('roles'), navigate: true);
    }

    public function getTitleProperty()
    {
        if ($this->isCreate) return 'ROLE CREATE PAGE';
        if ($this->isEdit) return strtoupper($this->role->name . ' EDIT PAGE');
        if ($this->isView) return strtoupper($this->role->name . ' VIEW PAGE');

        return '';
    }

    public function render()
    {
        view()->share('title', $this->getTitleProperty());
        return view('livewire.roles.role-form');
    }
}
