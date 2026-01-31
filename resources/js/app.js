import './bootstrap';
import Swal from 'sweetalert2'

window.Swal = Swal


Echo.channel('mpesa.transactions')
    .listen('MpesaTransactionReceived', (payload) => {
        console.log('Livewire received transaction', payload);
    });
