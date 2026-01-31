<div>

    <div class="mb-10 md:mb-0 page-header">
        <x-volt-livewire::spinner-component />
        <livewire:components.breadcrum :title="$title" />

        <div class="min-h-screen py-4 bg-gray-100 w-full">
            <div class="flex items-center justify-end my-10 space-x-3 text-lg font-semibold md:my-0 md:mb-6 w-full">
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

            <form wire:submit.prevent="{{ $isCreate ? 'save' : 'update' }}" enctype="multipart/form-data">
                <div class="p-8 mx-auto shadow-xl border-2 border-primary rounded-2xl">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">

                        <!-- Image Upload & Preview Section -->
                        <div class="md:col-span-2 lg:col-span-3">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Item Image</label>
                            <div class="flex flex-col items-center justify-center gap-6 md:flex-row">
                                <!-- Image Preview -->
                                <div class="w-full md:w-1/3">
                                    <div class="relative overflow-hidden bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg h-64">
                                        @if($image)
                                        <!-- Show temporary preview when new image is selected -->
                                        <img src="{{ $image->temporaryUrl() }}"
                                            alt="Item preview"
                                            class="object-cover w-full h-full">
                                        <!-- Remove image button -->
                                        @if(!$isView)
                                        <button type="button"
                                            wire:click="removeImage"
                                            class="absolute top-2 right-2 p-1 bg-red-500 rounded-full hover:bg-red-600">
                                            <i class="text-white fa fa-times"></i>
                                        </button>
                                        @endif
                                        @elseif($existingImage)
                                        <!-- Show existing image -->

                                        <img src="{{ $existingImage }}"
                                            alt="Item image"
                                            class="object-cover w-full h-full">
                                        @if($isEdit)
                                        <button type="button"
                                            wire:click="removeImage"
                                            class="absolute top-2 right-2 p-1 bg-red-500 rounded-full hover:bg-red-600">
                                            <i class="text-white fa fa-times"></i>
                                        </button>
                                        @endif
                                        @else
                                        <!-- Show placeholder when no image -->
                                        <div class="flex flex-col items-center justify-center h-full text-gray-400">
                                            <i class="mb-2 text-4xl fa fa-image"></i>
                                            <p class="text-sm">No image selected</p>
                                        </div>
                                        @endif
                                    </div>

                                    @if($image || $existingImage)
                                    <p class="mt-2 text-xs text-center text-gray-500">
                                        @if($image)
                                        New image selected
                                        @else
                                        Current image
                                        @endif
                                    </p>
                                    @endif
                                </div>

                                <!-- Upload Controls -->
                                <div class="w-full md:w-2/3">
                                    @if(!$isView)
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <label class="block mb-3 text-sm font-medium text-gray-700">
                                            {{ $existingImage ? 'Change Image' : 'Upload Image' }}
                                        </label>

                                        <!-- File Input -->
                                        <div class="mb-4">
                                            <input type="file"
                                                id="image_upload"
                                                wire:model="image"
                                                accept="image/*"
                                                class="w-full px-3 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            @error('image')
                                            <span class="text-sm text-red-600">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <!-- Image Requirements -->
                                        <div class="p-3 text-sm text-gray-600 bg-gray-100 rounded">
                                            <p class="font-medium mb-1">Image Requirements:</p>
                                            <ul class="pl-5 space-y-1 list-disc">
                                                <li>Max file size: 2MB</li>
                                                <li>Supported formats: JPG, PNG, GIF, WebP</li>
                                                <li>Recommended size: 800x800 pixels</li>
                                            </ul>
                                        </div>

                                        <!-- Upload Progress -->
                                        @if($image)
                                        <div class="mt-3">
                                            <div class="flex justify-between mb-1 text-xs text-gray-600">
                                                <span>Uploading...</span>
                                                <span>{{ $image->getClientOriginalName() }}</span>
                                            </div>

                                        </div>
                                        @endif
                                    </div>
                                    @else
                                    <div class="p-4 text-gray-600 bg-gray-50 rounded-lg">
                                        <p class="mb-2"><i class="mr-2 fa fa-info-circle"></i>Image view mode</p>
                                        <p class="text-sm">To change the image, switch to edit mode.</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Item Type Select -->
                        <div x-data="{
                        open: false,
                        search: '',
                        selectedId: @entangle('item_type_id'),
                        filtered: {{ Js::from($itemTypes->toArray()) }}
                    }" class="relative">
                            <label class="block mb-1 text-sm font-medium text-gray-700">Select Item Type</label>

                            <input type="text" x-model="search" @focus="open = true" @click.away="open = false"
                                placeholder="Search item type..."
                                class="{{ $isView ? 'bg-slate-100 cursor-not-allowed' : '' }} w-full px-4 py-2 border rounded shadow-sm focus:ring @error('item_type_id') border-red-500 ring-red-500 @enderror" />

                            <ul x-show="open"
                                class="absolute z-10 w-full mt-1 overflow-y-auto bg-white border border-gray-300 rounded shadow-lg max-h-60">
                                <template
                                    x-for="type in filtered.filter(i => i.name.toLowerCase().includes(search.toLowerCase()))"
                                    :key="type.id">
                                    <li @click="
                                    selectedId = type.id;
                                    search = type.name;
                                    open = false;
                                "
                                        class="px-4 py-2 cursor-pointer hover:bg-blue-100" x-text="type.name"></li>
                                </template>
                            </ul>

                            @error('item_type_id')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Item Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Item Name</label>
                            <input {{ $isView ? 'disabled' : '' }} type="text" id="name" wire:model="name"
                                placeholder="Enter Item name E.g Panadol"
                                class="{{ $isView ? 'bg-slate-100 cursor-not-allowed' : '' }}  mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                        @error('name') border-red-500 ring-red-500 @enderror" />

                            @error('name')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Item Strength (Optional) -->
                        <div>
                            <label for="strength" class="block text-sm font-medium text-gray-700">Strength</label>
                            <input {{ $isView ? 'disabled' : '' }} type="text" id="strength" wire:model="strength"
                                placeholder="Enter strength (e.g., 500mg)"
                                class="{{ $isView ? 'bg-slate-100 cursor-not-allowed' : '' }}  mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                        @error('strength') border-red-500 ring-red-500 @enderror" />

                            @error('strength')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Buying Price -->
                        <div>
                            <label for="buyingPrice" class="block text-sm font-medium text-gray-700">Buying Price (Ksh)</label>
                            <input {{ $isView ? 'disabled' : '' }} type="number" id="buyingPrice" wire:model="buyingPrice"
                                placeholder="Enter buying price"
                                step="0.01"
                                class="{{ $isView ? 'bg-slate-100 cursor-not-allowed' : '' }}  mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                        @error('buyingPrice') border-red-500 ring-red-500 @enderror" />

                            @error('buyingPrice')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Selling Price -->
                        <div>
                            <label for="unit_price" class="block text-sm font-medium text-gray-700">Selling Unit Price (Ksh)</label>
                            <input {{ $isView ? 'disabled' : '' }} type="number" id="unit_price" wire:model="unit_price"
                                placeholder="Enter selling price"
                                step="0.01"
                                class="{{ $isView ? 'bg-slate-100 cursor-not-allowed' : '' }}  mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                        @error('unit_price') border-red-500 ring-red-500 @enderror" />

                            @error('unit_price')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Supplier -->
                        <div>
                            <label for="supplier" class="block text-sm font-medium text-gray-700">Supplier</label>
                            <input {{ $isView ? 'disabled' : '' }} type="text" id="supplier" wire:model="supplier"
                                placeholder="Enter supplier name"
                                class="{{ $isView ? 'bg-slate-100 cursor-not-allowed' : '' }}  mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                        @error('supplier') border-red-500 ring-red-500 @enderror" />

                            @error('supplier')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Manufacturer -->
                        <div>
                            <label for="manufacturer" class="block text-sm font-medium text-gray-700">Manufacturer</label>
                            <input {{ $isView ? 'disabled' : '' }} type="text" id="manufacturer" wire:model="manufacturer"
                                placeholder="Enter manufacturer name"
                                class="{{ $isView ? 'bg-slate-100 cursor-not-allowed' : '' }}  mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                        @error('manufacturer') border-red-500 ring-red-500 @enderror" />

                            @error('manufacturer')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2 lg:col-span-3">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea {{ $isView ? 'disabled' : '' }} id="description" wire:model="description"
                                placeholder="Enter item description"
                                rows="4"
                                class="{{ $isView ? 'bg-slate-100 cursor-not-allowed' : '' }}  mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                        @error('description') border-red-500 ring-red-500 @enderror"></textarea>

                            @error('description')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                </div>
            </form>

            @if ($isView)
            <!-- Stockin section -->
            <div class="text-right w-full my-4 border-2 mb-2">
                <button type="button" wire:click="addStockin"
                    class="btn {{ !$isAddStockin ? 'btn-primary' : 'btn-warning' }} inline-flex items-center gap-2">

                    <x-volt-livewire::button-spinner target='addStockin' />

                    <i class="fa {{ !$isAddStockin ? 'fa-plus' : 'fa-times' }}"></i>
                    {{ !$isAddStockin ? 'Add stock in' : 'Cancel' }}
                </button>
            </div>

            @if ($isAddStockin)
            <form wire:submit.prevent="saveStockins">
                <div class="max-w-6xl p-8 mx-auto bg-white shadow-xl rounded-2xl space-y-6 my-10">

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <!-- Batch Number - Auto Generated -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="batch_id" class="block text-sm font-medium text-gray-700">
                                    Batch Number (Auto-generated)
                                </label>
                                <button type="button"
                                    wire:click="generateNewBatchNumber"
                                    class="text-xs text-blue-600 hover:text-blue-800">
                                    <i class="fa fa-refresh mr-1"></i> Generate New
                                </button>
                            </div>

                            <div class="relative">
                                <input type="text"
                                    id="batch_id"
                                    wire:model="batch_id"
                                    readonly
                                    class="w-full px-4 py-2 font-mono font-bold text-center text-gray-800 bg-gray-100 border border-gray-300 rounded-lg shadow-sm cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="text-gray-500 fa fa-lock"></i>
                                </div>
                            </div>

                            <p class="mt-1 text-xs text-gray-500">
                                Format: 3 uppercase letters + 3 numbers (e.g., ABC123)
                            </p>

                            @error('batch_id')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Branch Selection -->
                        <div>
                            <label for="branch_id" class="block text-sm font-medium text-gray-700">Branch</label>
                            <div class="relative">
                                <select
                                    id="branch_id"
                                    wire:model="branch_id"
                                    class=" w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('branch_id') border-red-500 ring-red-500 @enderror">


                                    <option value="">-- Select Branch --</option>
                                    @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ $branch_id == $branch->id ? 'selected' : '' }}>
                                        {{ strtoupper($branch->name) }}
                                    </option>
                                    @endforeach

                                </select>

                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="text-gray-500 fa fa-building"></i>
                                </div>
                            </div>

                            @error('branch_id')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror

                            @if($isView)
                            <p class="mt-1 text-xs text-gray-500">View only mode</p>
                            @else
                            <p class="mt-1 text-xs text-gray-500">Select the branch for this item</p>
                            @endif
                        </div>

                        <!-- Received By (Auto-filled from Auth - Capitalized) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Received By</label>
                            <div class="relative">
                                <input type="text"
                                    value="{{ strtoupper(Auth::user()->name) }}"
                                    readonly
                                    class="w-full px-4 py-2 font-bold text-center text-gray-800 uppercase bg-gray-100 border border-gray-300 rounded-lg shadow-sm cursor-not-allowed focus:outline-none" />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="text-gray-500 fa fa-user"></i>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Auto-filled with logged-in user</p>
                        </div>

                        <!-- Quantity -->
                        <div>
                            <label for="stockinQuantity"
                                class="block text-sm font-medium text-gray-700">Quantity</label>
                            <input type="number" id="stockinQuantity" wire:model="stockinQuantity"
                                placeholder="Enter quantity"
                                min="1"
                                class="mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('stockinQuantity') border-red-500 ring-red-500 @enderror" />
                            @error('stockinQuantity')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Supplier -->
                        <div>
                            <label for="stockin_supplier"
                                class="block text-sm font-medium text-gray-700">Supplier Name</label>
                            <input type="text" id="stockin_supplier" wire:model="stockin_supplier"
                                placeholder="Enter supplier name"
                                class="mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('stockin_supplier') border-red-500 ring-red-500 @enderror" />
                            @error('stockin_supplier')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Expiry Date -->
                        <div>
                            <label for="expiry_date" class="block text-sm font-medium text-gray-700">Expiry
                                Date</label>
                            <input type="date"
                                id="expiry_date"
                                wire:model="expiry_date"
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                class="mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('expiry_date') border-red-500 ring-red-500 @enderror" />
                            @error('expiry_date')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Additional Info -->
                        <div class="md:col-span-2 lg:col-span-3">
                            <label for="additional_info" class="block text-sm font-medium text-gray-700">Additional Information</label>
                            <textarea id="additional_info" wire:model="additional_info"
                                placeholder="Enter any additional information"
                                rows="3"
                                class="mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('additional_info') border-red-500 ring-red-500 @enderror"></textarea>
                            @error('additional_info')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Auto-filled Information Summary -->
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h3 class="mb-2 text-sm font-semibold text-blue-800">Auto-filled Information:</h3>
                        <div class="grid grid-cols-1 gap-2 text-sm md:grid-cols-3">
                            <div class="flex items-center">
                                <i class="mr-2 text-blue-600 fa fa-hashtag"></i>
                                <span class="text-gray-700">Batch No:</span>
                                <span class="ml-2 font-mono font-bold text-blue-800">{{ $batch_id }}</span>
                            </div>

                            <div class="flex items-center">
                                <i class="mr-2 text-blue-600 fa fa-user"></i>
                                <span class="text-gray-700">Received By:</span>
                                <span class="ml-2 font-bold uppercase text-blue-800">{{ strtoupper(Auth::user()->name) }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="mr-2 text-blue-600 fa fa-calendar"></i>
                                <span class="text-gray-700">Date:</span>
                                <span class="ml-2 font-bold text-blue-800">{{ date('Y-m-d') }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="mr-2 text-blue-600 fa fa-clock"></i>
                                <span class="text-gray-700">Time:</span>
                                <span class="ml-2 font-bold text-blue-800">{{ date('H:i:s') }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="mr-2 text-blue-600 fa fa-barcode"></i>
                                <span class="text-gray-700">Item:</span>
                                <span class="ml-2 font-bold text-blue-800">{{ $Item->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-right w-full flex items-center justify-end space-x-3">
                        <button type="button"
                            wire:click="generateNewBatchNumber"
                            class="btn btn-secondary">
                            <i class="fa fa-refresh"></i> New Batch
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <x-volt-livewire::button-spinner target='saveStockins' />
                            <i class="fa fa-save"></i> Save Stockin
                        </button>
                    </div>
                </div>
            </form>
            @endif

            <!-- Stockins table -->
            <div class="border-2 border-secondary p-2 rounded my-10">
                <h2 class="text-2xl">Stokin Records</h2>
                <livewire:components.table-component
                    :modelNamespace="$model"
                    :columns="['supplier', 'receivedBy.name', 'quantity', 'additional_info', 'expiry_date', 'created_at']"
                    :search="$search"
                    :itemsPerPage="$itemsPerPage"
                    searchPlaceHolder='Search stock ins'
                    viewRoute='stockChanges.show'
                    :showCreateButton="$showCreateButton"
                    :whereClauses="[['column' => 'item_id', 'operator' => '=', 'value' => $ItemId]]" />
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            // Listen for image upload events
            Livewire.on('image-uploaded', (event) => {
                // You can add custom JavaScript here if needed
                console.log('Image uploaded:', event);
            });

            // Listen for image removed event
            Livewire.on('image-removed', (event) => {
                // You can add custom JavaScript here if needed
                console.log('Image removed:', event);
            });
        });
    </script>
    @endpush


</div>