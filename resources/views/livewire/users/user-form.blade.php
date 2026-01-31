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
        <form wire:submit.prevent="{{ $isCreate ? 'save' : 'update' }}">
            <div class="max-w-6xl p-8 mx-auto bg-white shadow-xl rounded-2xl">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">User Name</label>
                        <input {{ $isView ? 'disabled' : '' }} type="text" id="name" wire:model="name"
                            placeholder="Enter user name"
                            class="{{ $isView ? 'bg-slate-100 cursor-not-allowed' : '' }} mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                        @error('name') border-red-500 ring-red-500 @enderror" />

                        @error('name')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">User Email</label>
                        <input {{ $isView ? 'disabled' : '' }} type="email" id="email" wire:model="email"
                            placeholder="Enter user email E.g user@domain.com"
                            class="{{ $isView ? 'bg-slate-100 cursor-not-allowed' : '' }} mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                        @error('email') border-red-500 ring-red-500 @enderror" />

                        @error('email')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Branch Selection -->
                    <div>
                        <label for="branch_id" class="block text-sm font-medium text-gray-700">Branch</label>
                        <div class="relative">
                            <select {{ $isView ? 'disabled' : '' }} 
                                    id="branch_id" 
                                    wire:model="branch_id"
                                    class="{{ $isView ? 'bg-slate-100 cursor-not-allowed' : '' }} w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('branch_id') border-red-500 ring-red-500 @enderror">
                                
                                @if($isCreate || $isEdit)
                                    <option value="">-- Select Branch --</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ $branch_id == $branch->id ? 'selected' : '' }}>
                                            {{ strtoupper($branch->name) }}
                                        </option>
                                    @endforeach
                                @else
                                    <!-- For view mode, show the selected branch -->
                                    @php
                                        $userBranch = $branches->firstWhere('id', $branch_id);
                                    @endphp
                                    <option value="{{ $branch_id }}" selected>
                                        {{ $userBranch ? strtoupper($userBranch->name) : 'NO BRANCH ASSIGNED' }}
                                    </option>
                                @endif
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
                            <p class="mt-1 text-xs text-gray-500">Select user's branch</p>
                        @endif
                    </div>

                    <!-- Password Fields (show conditionally) -->
                    @if($isCreate || ($isEdit && $password))
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                {{ $isCreate ? 'Password' : 'New Password' }}
                            </label>
                            <input {{ $isView ? 'disabled' : '' }} type="password" id="password" wire:model="password"
                                placeholder="{{ $isCreate ? 'Enter user password' : 'Enter new password' }}"
                                class="{{ $isView ? 'bg-slate-100 cursor-not-allowed' : '' }} mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                            @error('password') border-red-500 ring-red-500 @enderror" />

                            @error('password')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Password Confirmation -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                Confirm {{ $isCreate ? 'Password' : 'New Password' }}
                            </label>
                            <input {{ $isView ? 'disabled' : '' }} type="password" id="password_confirmation"
                                wire:model="password_confirmation" 
                                placeholder="Confirm password"
                                class="{{ $isView ? 'bg-slate-100 cursor-not-allowed' : '' }} mt-1 w-full px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                            @error('password_confirmation') border-red-500 ring-red-500 @enderror" />

                            @error('password_confirmation')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    <!-- Show password change button in edit mode when password is not being changed -->
                    @if($isEdit && !$password)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Password</label>
                            <div class="relative">
                                <input type="password" 
                                       value="********" 
                                       disabled
                                       class="w-full px-4 py-2 bg-slate-100 border rounded-lg shadow-sm cursor-not-allowed" />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="text-gray-500 fa fa-lock"></i>
                                </div>
                            </div>
                            <button type="button" 
                                    wire:click="$set('password', '')" 
                                    class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                                <i class="fa fa-key"></i> Change Password
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Roles Checkboxes -->
                <div class="mt-8">
                    <h3 class="mb-4 text-lg font-medium text-gray-700">Assign Roles</h3>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                        @foreach($roles as $role)
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    id="role_{{ $role->id }}"
                                    wire:model="selectedRoles"
                                    value="{{ $role->name }}"
                                    {{ $isView ? 'disabled' : '' }}
                                    class="{{ $isView ? 'bg-slate-100 cursor-not-allowed' : '' }} w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                >
                                <label for="role_{{ $role->id }}" class="ml-2 text-sm font-medium text-gray-700">
                                    {{ $role->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('selectedRoles')
                        <span class="text-sm text-red-600">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </form>
    </div>
</div>