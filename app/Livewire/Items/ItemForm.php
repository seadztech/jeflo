<?php

namespace App\Livewire\Items;

use App\Models\Branch;
use App\Models\Items;
use App\Models\ItemType;
use App\Models\StockChange;
use App\Models\stockins;
use App\Models\User;
use App\Traits\AlertTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ItemForm extends Component
{
    use AlertTrait, WithFileUploads;
    

    public $isCreate = false;
    public $isView = false;
    public $isEdit = false;
    public $Item;
    public $ItemId;
    public $title;

    public $name = '';
    public $item_type_id;
    public $strength;
    public $unit_price;
    public $buyingPrice;
    public $supplier;
    public $manufacturer;
    public $description;
    public $itemTypes = [];
    
    // Image upload properties
    public $image;
    public $existingImage;
    public $tempImageUrl;

    // Stockins variables
    public $isAddStockin = true;
    public $item_id;
    public $batch_id;
    public $branch_id;
    public $received_by;
    public $stockinQuantity;
    public $expiry_date;
    public $stockin_supplier;
    public $additional_info;

    public $model;
    public $itemsPerPage = 10;
    public $search = '';
    public $showCreateButton;

    public $branches;


    // Validation rules
    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'unit_price' => 'required|numeric|min:0',
            'buyingPrice' => 'required|numeric|min:0',
            'supplier' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'description' => 'required|string',
            'item_type_id' => 'required|exists:item_types,id',
            'strength' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png,gif,webp',
        ];

        // For create, ensure name is unique
        if ($this->isCreate) {
            $rules['name'] = 'required|string|max:255|unique:items,name';
        }

        // For update, ignore current item's name
        if ($this->isEdit && $this->Item) {
            $rules['name'] = 'required|string|max:255|unique:items,name,' . $this->Item->id;
        }

        return $rules;
    }

    // Validation messages
    protected $messages = [
        'name.required' => 'The item name is required.',
        'name.unique' => 'This item name already exists.',
        'unit_price.required' => 'The unit price is required.',
        'unit_price.numeric' => 'The unit price must be a number.',
        'buyingPrice.required' => 'The buying price is required.',
        'buyingPrice.numeric' => 'The buying price must be a number.',
        'supplier.required' => 'The supplier name is required.',
        'manufacturer.required' => 'The manufacturer name is required.',
        'description.required' => 'The description is required.',
        'item_type_id.required' => 'Please select an item type.',
        'item_type_id.exists' => 'The selected item type is invalid.',
        'image.image' => 'The file must be an image.',
        'image.max' => 'The image may not be greater than 2MB.',
        'image.mimes' => 'The image must be a file of type: jpg, jpeg, png, gif, webp.',
        'batch_id.required' => 'The batch number is required.',
        'batch_id.unique' => 'This batch number already exists.',
        'stockinQuantity.required' => 'The quantity is required.',
        'stockinQuantity.integer' => 'The quantity must be a whole number.',
        'stockinQuantity.min' => 'The quantity must be at least 1.',
    ];

    public function mount($id)
    {
        if ($id == 'createForm') {
            $this->isCreate = true;
            $this->title = 'Create New Item';
        } else {
            $this->isView = true;
            $this->Item = Items::findOrFail($id);
            $this->ItemId = $id;

            // Populate form fields
            $this->name = $this->Item->name;
            $this->strength = $this->Item->strength;
            $this->unit_price = $this->Item->unit_price;
            $this->buyingPrice = $this->Item->buyingPrice;
            $this->supplier = $this->Item->supplier;
            $this->manufacturer = $this->Item->manufacturer;
            $this->description = $this->Item->description;
            $this->item_type_id = $this->Item->item_type_id;
            
            // Set existing image if available
            $this->existingImage = $this->Item->image 
                ? (filter_var($this->Item->image, FILTER_VALIDATE_URL) 
                    ? $this->Item->image 
                    : Storage::url($this->Item->image))
                : null;
        }

        $this->model = 'App\Models\stockins';
        $this->itemTypes = ItemType::all();
        $this->showCreateButton = false;
        $this->isAddStockin = false;

        $this->branches = Branch::all();
    }

    // Start of the Component Logics

    public function save()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            $item = new Items();
            $item->name = $this->name;
            $item->strength = $this->strength;
            $item->unit_price = $this->unit_price;
            $item->buyingPrice = $this->buyingPrice;
            $item->supplier = $this->supplier;
            $item->manufacturer = $this->manufacturer;
            $item->description = $this->description;
            $item->item_type_id = $this->item_type_id;

            // Handle image upload
            if ($this->image) {
                $imagePath = $this->image->store('items/images', 'public');
                $item->image = $imagePath;
            }

            $item->save();

            $action = 'Item Creation';
            $description = 'Successfully created item: ' . $item->name;

            User::saveAuditTrail($action, $description);

            $this->showAlert('success', $action, $description);
            
            DB::commit();
            return $this->redirect(route('items'), navigate: true);
            
        } catch (Exception $e) {
            DB::rollBack();
            $this->showAlert('error', 'Error', $e->getMessage());
            $this->dispatch('item-save-error', message: $e->getMessage());
        }
    }

    public function update()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            $this->Item->name = $this->name;
            $this->Item->strength = $this->strength;
            $this->Item->unit_price = $this->unit_price;
            $this->Item->buyingPrice = $this->buyingPrice;
            $this->Item->supplier = $this->supplier;
            $this->Item->manufacturer = $this->manufacturer;
            $this->Item->description = $this->description;
            $this->Item->item_type_id = $this->item_type_id;

            // Handle image upload/update
            if ($this->image) {
                // Delete old image if exists
                if ($this->Item->image && Storage::disk('public')->exists($this->Item->image)) {
                    Storage::disk('public')->delete($this->Item->image);
                }
                
                $imagePath = $this->image->store('items/images', 'public');
                $this->Item->image = $imagePath;
                $this->existingImage = Storage::url($imagePath);
                $this->image = null; // Clear temporary image after save
            }

            $this->Item->save();

            $action = 'Item Update';
            $description = 'Successfully updated item: ' . $this->Item->name;

            User::saveAuditTrail($action, $description);

            $this->showAlert('success', $action, $description);
            
            // Switch back to view mode
            $this->isView = true;
            $this->isEdit = false;
            
            DB::commit();
            
            // Dispatch event for any frontend updates
            $this->dispatch('item-updated');
            
        } catch (Exception $e) {
            DB::rollBack();
            $this->showAlert('error', 'Error', $e->getMessage());
            $this->dispatch('item-update-error', message: $e->getMessage());
        }
    }

    public function removeImage()
    {
        $this->validate([
            'image' => 'nullable|image|max:2048',
        ]);

        try {
            if ($this->image) {
                // Just clear the temporary image if it hasn't been saved yet
                $this->image = null;
                $this->dispatch('image-removed', type: 'temporary');
            } 
            elseif ($this->Item && $this->Item->image) {
                // Delete existing image from storage
                if (Storage::disk('public')->exists($this->Item->image)) {
                    Storage::disk('public')->delete($this->Item->image);
                }
                
                // Remove from database
                $this->Item->image = null;
                $this->Item->save();
                
                $this->existingImage = null;
                $this->dispatch('image-removed', type: 'existing');
            }
            
            $this->showAlert('success', 'Image Removed', 'Image has been removed successfully.');
            
        } catch (Exception $e) {
            $this->showAlert('error', 'Error', 'Failed to remove image: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        LivewireAlert::title('Delete Item')
            ->text('Are you sure you want to delete "' . $this->Item->name . '"? This action cannot be undone.')
            ->icon('warning')
            ->showConfirmButton('Yes, delete it!', '#d33')
            ->showCancelButton('Cancel', '#3085d6')
            ->asConfirm()
            ->onConfirm('commitDelete')
            ->show();
    }

    public function commitDelete()
    {
        DB::beginTransaction();
        
        try {
            // Delete image if exists
            if ($this->Item->image && Storage::disk('public')->exists($this->Item->image)) {
                Storage::disk('public')->delete($this->Item->image);
            }
            
            $itemName = $this->Item->name;
            $this->Item->delete();

            $action = 'Item Deletion';
            $description = 'Successfully deleted item: ' . $itemName;

            User::saveAuditTrail($action, $description);

            $this->showAlert('success', $action, $description);
            
            DB::commit();
            return $this->redirect(route('items'), navigate: true);
            
        } catch (Exception $e) {
            DB::rollBack();
            $this->showAlert('error', 'Error', $e->getMessage());
        }
    }

    // End of the Component Logics

    public function addStockin()
    {
        $this->isAddStockin = !$this->isAddStockin;
        
        // Reset stockin form when opening and generate batch number
        if ($this->isAddStockin) {
            $this->resetStockinForm();
            $this->generateBatchNumber();
        }
    }

    protected function resetStockinForm()
    {
        $this->batch_id = '';
        $this->stockinQuantity = '';
        $this->expiry_date = '';
        $this->stockin_supplier = '';
        $this->additional_info = '';
        $this->resetErrorBag(['batch_id', 'stockinQuantity', 'expiry_date', 'stockin_supplier']);
    }

    /**
     * Generate a random batch number with uppercase letters and numbers
     * Format: 6 characters (3 letters + 3 numbers)
     * Example: ABC123, XYZ789, QWE456
     */
    protected function generateBatchNumber()
    {
        // Generate 3 random uppercase letters
        $letters = '';
        for ($i = 0; $i < 3; $i++) {
            $letters .= chr(rand(65, 90)); // A-Z
        }
        
        // Generate 3 random numbers
        $numbers = '';
        for ($i = 0; $i < 3; $i++) {
            $numbers .= rand(0, 9);
        }
        
        // Combine letters and numbers
        $batchNumber = $letters . $numbers;
        
        // Check if batch number already exists, regenerate if needed
        while (stockins::where('batch_id', $batchNumber)->exists()) {
            // Regenerate letters if duplicate
            $letters = '';
            for ($i = 0; $i < 3; $i++) {
                $letters .= chr(rand(65, 90));
            }
            $batchNumber = $letters . $numbers;
        }
        
        $this->batch_id = $batchNumber;
    }

    public function saveStockins()
    {
        $this->validate([
            'batch_id' => 'required|unique:stockins,batch_id',
            'stockinQuantity' => 'required|integer|min:1',
            'expiry_date' => 'nullable|date|after:today',
            'stockin_supplier' => 'required|string|max:255',
            'additional_info' => 'nullable|string|max:500',
        ]);
        
        DB::beginTransaction();

        try {
            $stockin = new stockins();
            $stockin->item_id = $this->ItemId;
            $stockin->batch_id = $this->batch_id;
            $stockin->branch_id = $this->branch_id ?? 1;
            $stockin->quantity = $this->stockinQuantity;
            $stockin->expiry_date = $this->expiry_date;
            $stockin->supplier = $this->stockin_supplier;
            $stockin->received_by = Auth::user()->id;
            $stockin->additional_info = $this->additional_info ?? 'No additional information';
            $stockin->save();

            // Record stock change
            $change = new StockChange();
            $change->stockins_id = $stockin->id;
            $change->quantity = $this->stockinQuantity;
            $change->actionBy = Auth::user()->id;
            $change->changeType = 'increment';
            $change->reason = 'fromSupplier';
            $change->save();

            // Update item stock if needed (assuming you have a stock field)
            if (isset($this->Item->stock)) {
                $this->Item->stock += $this->stockinQuantity;
                $this->Item->save();
            }

            $action = 'Stockin Creation';
            $description = 'Successfully added stock for: ' . $this->Item->name;

            User::saveAuditTrail($action, $description);

            $this->showAlert('success', $action, $description);
            
            // Reset form and close
            $this->resetStockinForm();
            $this->isAddStockin = false;
            
            DB::commit();
            
            // Refresh the stockins table
            $this->dispatch('stockin-added');
            
        } catch (Exception $e) {
            DB::rollBack();
            $this->showAlert('error', 'Error', $e->getMessage());
        }
    }

    // Start of UI configurations
    public function edit()
    {
        $this->isView = false;
        $this->isEdit = true;
        $this->isCreate = false;
    }

    public function back()
    {
        $this->redirect(route('items'), navigate: true);
    }

    public function cancel()
    {
        if ($this->Item) {
            // Revert to original values
            $this->name = $this->Item->name;
            $this->strength = $this->Item->strength;
            $this->unit_price = $this->Item->unit_price;
            $this->buyingPrice = $this->Item->buyingPrice;
            $this->supplier = $this->Item->supplier;
            $this->manufacturer = $this->Item->manufacturer;
            $this->description = $this->Item->description;
            $this->item_type_id = $this->Item->item_type_id;
            $this->existingImage = $this->Item->image 
                ? (filter_var($this->Item->image, FILTER_VALIDATE_URL) 
                    ? $this->Item->image 
                    : Storage::url($this->Item->image))
                : null;
        }
        
        // Clear any temporary image
        $this->image = null;
        $this->resetErrorBag();
        
        $this->isView = true;
        $this->isEdit = false;
    }

    public function stockinsPage()
    {
        $this->redirect(route('items', $this->ItemId));
    }

    // End of UI configurations

    // Computed property for title
    public function getTitleProperty()
    {
        if ($this->isEdit && $this->Item) {
            return 'Edit Item: ' . $this->Item->name;
        }

        if ($this->isView && $this->Item) {
            return 'View Item: ' . $this->Item->name;
        }

        if ($this->isCreate) {
            return 'Create New Item';
        }

        return 'Item Management';
    }

    /**
     * Generate new batch number
     */
    public function generateNewBatchNumber()
    {
        $this->generateBatchNumber();
        $this->showAlert('success', 'Batch Number Generated', 'New batch number has been generated.');
    }

    // Listeners for events
    protected $listeners = [
        'commitDelete' => 'commitDelete',
        'refresh' => '$refresh',
        'generateNewBatchNumber' => 'generateNewBatchNumber',
    ];

    // Reset image when component updates
    public function updating($property, $value)
    {
        // Clear image validation errors when image is changed
        if ($property === 'image' && $value === null) {
            $this->resetErrorBag('image');
        }
    }

    // Clean up temporary files
    public function cleanupTemporaryFiles()
    {
        if ($this->image instanceof TemporaryUploadedFile) {
            $this->image->delete();
        }
    }

    public function render()
    {
        view()->share('title', $this->title);
        
        // Clean up temporary files on render if in view mode
        if ($this->isView && $this->image) {
            $this->cleanupTemporaryFiles();
            $this->image = null;
        }
        
        return view('livewire.items.item-form');
    }
}