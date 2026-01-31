import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// -----------------
// Enhanced Laravel Echo Setup
// -----------------
window.Pusher = Pusher;

const echoConfig = {
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
    forceTLS: true,
    encrypted: true,
    disableStats: false,
    enabledTransports: ['ws', 'wss'],
    authEndpoint: '/broadcasting/auth', // needed for private/presence
};

window.Echo = new Echo(echoConfig);

// Listen on the public channel
window.Echo.channel('mpesa.transactions')
    .listen('.transaction.received', (e) => {
        console.log('Transaction from Echo:', e);
    });

// ======================
// Connection Debugging
// ======================
const pusher = window.Echo.connector.pusher;

pusher.connection.bind('connecting', () => {
    console.log('Pusher: Connecting to websocket...');
});

pusher.connection.bind('connected', () => {
    console.log('Pusher: Successfully connected!');
    console.log('Socket ID:', pusher.connection.socket_id);
});

pusher.connection.bind('disconnected', () => {
    console.log('Pusher: Disconnected');
});

pusher.connection.bind('error', (err) => {
    console.error('Pusher connection error:', err);
});
