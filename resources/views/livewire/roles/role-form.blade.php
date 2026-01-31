<div class="mb-10 md:mb-0 page-header">
    <livewire:components.breadcrum :title="$title"/>

    <div class="min-h-screen py-4 bg-gray-100">
        <div class="flex items-center justify-end my-10 space-x-3 text-lg font-semibold md:my-0 md:mb-6">

            @if ($isCreate)
                <button wire:click="save" class="btn btn-primary btn-sm">
                    <i class="fa fa-save"></i> SAVE
                </button>
            @endif

            @if ($isEdit)
                <button wire:click="cancel" class="btn btn-secondary btn-sm">
                    CANCEL
                </button>

                <button wire:click="update" class="btn btn-primary btn-sm">
                    UPDATE
                </button>
            @elseif ($isView)
                <button wire:click="back" class="btn btn-secondary btn-sm">
                    Back
                </button>

                <button wire:click="edit" class="btn btn-primary btn-sm">
                    Edit
                </button>

                <button wire:click='delete' class="btn btn-danger btn-sm">
                    DELETE
                </button>
            @endif
        </div>

        <div class="max-w-6xl p-8 mx-auto bg-white shadow-xl rounded-2xl">

            {{-- ROLE NAME --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700">Role Name</label>
                <input
                    {{ $isView ? 'disabled' : '' }}
                    wire:model="name"
                    class="w-full px-4 py-2 mt-1 border rounded-lg
                    {{ $isView ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                />
                @error('name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            {{-- PERMISSIONS --}}
            <div class="mt-10">
                <h3 class="mb-4 text-lg font-semibold">Role Permissions</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($permissions as $permission)
                        <label class="flex items-center space-x-2">
                            <input
                                type="checkbox"
                                wire:model="selectedPermissions"
                                value="{{ $permission->name }}"
                                {{ $isView ? 'disabled' : '' }}
                            />
                            <span>{{ $permission->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>
