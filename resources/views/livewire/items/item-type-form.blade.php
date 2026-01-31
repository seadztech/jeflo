<div class="mb-10 md:mb-0 page-header">
    <livewire:components.breadcrum :title="$title"/>

    <div class="min-h-screen py-4 bg-gray-100">
        <div class="flex items-center justify-end my-10 space-x-3 text-lg font-semibold md:my-0 md:mb-6">

            @if ($isCreate)
                <div>
                    <button wire:click="save" class="btn btn-primary btn-sm"> <i class="fa fa-save"></i> SAVE</button>
                </div>
            @endif

            @if ($isEdit)
                <div>
                    <button wire:click="cancel" class="btn btn-secondary btn-sm"> <i class="fa fa-cancel"></i>
                        CANCEL</button>
                </div>

                <div>
                    <button wire:click="update" class="btn btn-primary btn-sm"> <i class="fa fa-save"></i>
                        UPDATE</button>
                </div>
            @elseif ($isView)
                <div>
                    <button wire:click="back" class="btn btn-secondary btn-sm"> <i class="fa fa-reply"></i>
                        Back</button>
                </div>
                <div>
                    <button wire:click="edit" class="btn btn-primary btn-sm"> <i class="fa fa-edit"></i> Edit</button>
                </div>

                <div>
                    <button wire:click='delete' class="btn btn-danger btn-sm"> <i class="fa fa-trash"></i>
                        DELETE</button>
                </div>
            @endif
        </div>
        <form wire:submit="save">
            <div class="max-w-6xl p-8 mx-auto bg-white shadow-xl rounded-2xl">

                <div class="grid grid-cols-1 gap-6 md:grid-cols-1 lg:grid-cols-1">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Item Itype Name</label>
                        <input {{ $isView ? 'disabled' : '' }} type="text" id="name" wire:model="name"
                            placeholder="Enter Item Itype name E.g Pain Killer"
                            class="{{ $isView ? 'bg-slate-100 cursor-not-allowed' : '' }}  mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                        @error('name') border-red-500 ring-red-500 @enderror" />

                        @error('name')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                </div>


        </form>
    </div>
</div>
