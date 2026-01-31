<div class="mb-10 md:mb-0 page-header">
    <livewire:components.breadcrum :title="$title"/>

    <div class="min-h-screen py-4 bg-gray-100">
        <div class="flex items-center justify-end my-10 space-x-3 text-lg font-semibold md:my-0 md:mb-6">
            @if ($isCreate)
                <button wire:click="save" wire:loading.attr="disabled" class="btn btn-primary btn-sm">
                    <i class="fa fa-save"></i>
                    <span wire:loading.remove>SAVE</span>
                    <span wire:loading>Saving...</span>
                </button>
            @endif

            @if ($isEdit)
                <button wire:click="cancel" class="btn btn-secondary btn-sm">
                    <i class="fa fa-cancel"></i> CANCEL
                </button>
                <button wire:click="update" wire:loading.attr="disabled" class="btn btn-primary btn-sm">
                    <i class="fa fa-save"></i>
                    <span wire:loading.remove>UPDATE</span>
                    <span wire:loading>Updating...</span>
                </button>
            @elseif ($isView)
                <button wire:click="back" class="btn btn-secondary btn-sm">
                    <i class="fa fa-reply"></i> Back
                </button>
                <button wire:click="edit" class="btn btn-primary btn-sm">
                    <i class="fa fa-edit"></i> Edit
                </button>
                <button wire:click="delete" wire:confirm="Are you sure you want to delete this?" class="btn btn-danger btn-sm">
                    <i class="fa fa-trash"></i> DELETE
                </button>
            @endif
        </div>

        <form wire:submit.prevent="{{ $isCreate ? 'save' : 'update' }}">
            <div class="max-w-6xl p-8 mx-auto bg-white shadow-xl rounded-2xl">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-1 lg:grid-cols-1">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Branch Name</label>
                        <input {{ $isView ? 'disabled' : '' }}
                               type="text"
                               id="name"
                               wire:model="name"
                               placeholder="Enter branch name E.g Embu branch"
                               class="{{ $isView ? 'bg-slate-100 cursor-not-allowed' : '' }} mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 ring-red-500 @enderror" />

                        @error('name')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
