import './bootstrap';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import Swal from 'sweetalert2';

window.Swal = Swal;
window.Pusher = Pusher;

// Initialize Echo
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY, 
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'ap2',
    forceTLS: true,
});


// window.Echo.channel('mpesa.transactions')
// .listen('.MpesaTransactionReceived', (payload) => {
//     console.log('Received transaction:', payload);
//     Swal.fire({
//         icon: 'success',
//         title: 'Payment Received',
//         text: `Ksh ${payload.transaction.Amount} from ${payload.transaction.SenderName}`,
//     });
// });
