   <div class="flex flex-wrap flex-col">
       <!-- M-PESA Payment Section -->
       <div class="relative w-full text-right p-4">
           <button wire:click="showPaymentsForm" class="btn {{ $isPaymentForm ? 'btn-danger' : 'btn-primary' }} "><i
                   class="fa {{ $isPaymentForm ? 'fa-cancel' : 'fa-money-bill' }}"> </i>
               {{ $isPaymentForm ? 'Close Form' : 'Make Payments' }}</button>
       </div>
       <div class="p-3 space-y-2">

           @if ($isMpesa)
               <div class="shadow border p-2">
                <div class="flex flex-row justify-between items-center p-2">
                       <div class="text-2xl text-slate">
                           <h1>Initiate STK Push </h1>
                       </div>

                       <div>
                           <button wire:click="setMpesaForm"
                               class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-700">
                               <i class="fa fa-cancel"></i> Cancel
                           </button>
                       </div>
                   </div>
                   <div class="my-3">
                       <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                       <input type="text" wire:model.defer="mpesaPhone" placeholder="e.g. 07XXXXXXXX" maxlength="12"
                           class="w-full px-3 py-2 mt-1 border rounded shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                   </div>

                   <div class="flex justify-between">
                       <button wire:click="sendSTK"
                           class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">
                           Send STK Push
                       </button>
                   </div>
               </div>
           @else
               <div class="border shadow p-3">
                   <div class="flex flex-row justify-between items-center p-2">
                       <div class="text-2xl text-slate">
                           <h1>Recieve Payments </h1>
                       </div>

                       <div>
                           <button wire:click="setMpesaForm"
                               class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">
                               <i class="fa fa-credit-card"></i> Send STK Push
                           </button>
                       </div>
                   </div>
                   <div class="shadow py-4 px-3 border my-2">
                       <label class="block text-sm font-medium text-gray-700">Amount</label>
                     
                       <input wire:model='cashAmount' type="number" " step="0.01"
                             class="w-full px-3 py-2 mt-1 border rounded shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                @error('cashAmount')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                        @enderror
                   </div>
       
                 </div>
                  <div class="flex justify-between">
            <button wire:click="recordCashPayment"
                class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
                Confirm Cash Payment
            </button>
        </div>
        

       
        </div>
 @endif
                   </div>


                   <!-- Selectable Transactions Table -->
                   <div class="mb-4 border rounded-lg max-h-48 overflow-y-auto p-2">
                       <div class="p-2 font-medium text-gray-700 bg-gray-100 border-b">Select Transactions</div>
                       <table class="w-full text-sm text-left text-gray-700 border">
                           <thead class="bg-gray-50">
                               <tr>
                                   <th class="px-2 py-2 text-center">#</th>
                                   <th class="px-2 py-2">Type</th>
                                   <th class="px-2 py-2">Transaction</th>
                                   <th class="px-2 py-2 text-right">Amount</th>
                               </tr>
                           </thead>
                           <tbody>
                               @forelse ($transactions as $trx)
                                   <tr
                                       class="border-t {{ in_array($trx['id'], $selectedTransactions) ? 'bg-green-100' : '' }}">
                                       <td class="px-2 py-1 text-center">
                                           <input type="checkbox" wire:click="toggleTransaction({{ $trx['id'] }})"
                                               {{ in_array($trx['id'], $selectedTransactions) ? 'checked' : '' }}
                                               class="text-green-600 border-gray-300 rounded shadow-sm focus:ring-green-500">
                                       </td>
                                       <td class="px-2 py-1">
                                           {{ $trx['description'] ?? 'Transaction #' . $trx['id'] }}
                                       </td>
                                       <td class="px-2 py-1 text-right">
                                           Ksh {{ number_format($trx['amount'], 2) }}
                                       </td>
                                   </tr>
                               @empty
                                   <tr>
                                       <td colspan="3" class="px-2 py-2 text-center text-gray-400">
                                           No available transactions
                                       </td>
                                   </tr>
                               @endforelse
                           </tbody>
                       </table>
                   </div>

                   <!-- Order Summary Section -->
                   <div class="mb-4 border rounded-lg p-2">
                       <div class="p-2 font-medium text-gray-700 bg-gray-100 border-b">Order Summary</div>

                       <div class="p-4 space-y-2 text-sm text-gray-800">
                           <div class="flex justify-between">
                               <span>Total Items:</span>
                               <span>{{ $totalItems }}</span>
                           </div>
                           <div class="flex justify-between">
                               <span>Subtotal:</span>
                               <span>Ksh {{ number_format($totalAmount + $discount, 2) }}</span>
                           </div>
                           <div class="flex justify-between">
                               <span>Discount:</span>
                               <span class="text-red-500">- Ksh {{ number_format($discount, 2) }}</span>
                           </div>
                           <div class="flex justify-between font-semibold text-lg border-t pt-2">
                               <span>Total:</span>
                               <span class="text-blue-600">Ksh {{ number_format($totalAmount, 2) }}</span>
                           </div>
                       </div>

                       <!-- Confirm Payment Button -->

                       <button wire:click="confirmPayment"
                           class="w-full px-4 py-4 font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 ">
                           Confirm Payment
                       </button>
                       @error('paymentError')
                           <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                       @enderror

                   </div>


               </div>
