<div>
    <livewire:components.breadcrum :title="$title" />


    <div class="bg-white mt-20 md:mt-4 border">
        <div class="text-right w-full ">
            <button type="button" wire:click="removeStock"
                class="btn {{ !$isAlterStockin ? 'btn-danger' : 'btn-warning' }} inline-flex items-center gap-2 mx-4 mt-4 {{ !$isAlterStockin ? 'mb-4' : 'mb-0' }}">

                <x-volt-livewire::button-spinner target='removeStock' />


                <i class="fa {{ !$isAlterStockin ? 'fa-minus' : 'fa-times' }}"></i>
                {{ !$isAlterStockin ? 'Remove Stock' : 'Cancel' }}
            </button>
        </div>
        @if ($isAlterStockin)
            <form class="" wire:submit.prevent="commitRemoveStock">
                <div class="p-8  shadow-xl border-sky-300 border-2 mx-3 my-2 space-y-6">

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-2">
                        <!-- Batch Number -->

                        <!-- Quantity -->
                        <div>
                            <label for="stockinQuantity"
                                class="block text-sm font-medium text-gray-700">Quantity</label>
                            <input type="number" id="stockinQuantity" wire:model="stockinQuantity"
                                placeholder="Enter quantity"
                                class="mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('stockinQuantity') border-red-500 ring-red-500 @enderror" />
                            @error('stockinQuantity')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="reason">Reason for removal </label>
                            <select wire:model="reason"
                                class="mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('reason') border-red-500 ring-red-500 @enderror" />
                                <option value=""> -- Select Reason --</option>
                                <option value="drawings">Drawings</option>
                                <option value="spoilage">Spoilage/Expiry</option>
                                <option value="returnToSupplier">Return To Supplier</option>
                            </select>
                             @error('reason')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>



                    </div>

                    <!-- Submit Button -->
                    <div class="text-right w-full flex items-center justify-end">
                        <button type="submit" class="btn btn-primary">
                            <x-volt-livewire::button-spinner target='commitRemoveStock' />
                            <i class="fa fa-save"></i> Remove Stock ID: {{ $stockinId }}
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>




    <livewire:components.table-component :modelNamespace="$model" :columns="['quantity', 'reason', 'user.name', 'changeType', 'created_at']" :search="$search" :itemsPerPage="$itemsPerPage"
        searchPlaceHolder='Search stock ins' viewRoute='stockChanges.show' :showCreateButton=$showCreateButton
        :whereClauses="[['column' => 'stockins_id', 'operator' => '=', 'value' => $stockinId]]" />
</div>
