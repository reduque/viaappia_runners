importScripts('https://www.gstatic.com/firebasejs/8.1.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.1.1/firebase-messaging.js');


self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    payload = event.notification.data;
    mi_url=payload.FCM_MSG.data.url;
    event.waitUntil(clients.matchAll({ includeUncontrolled: true }).then(function (clientList) {
        for (const client of clientList) {
            console.log(client.url);
            client.postMessage({
                url: mi_url
            });
            return client.focus();
        }
        if (clients.openWindow){
            return clients.openWindow(mi_url);
        }
    }));
});

const firebaseConfig = {
    apiKey: "AIzaSyBFtSHXpJv7k_9-Y-PrIm32Z_SjXf0tX2s",
    authDomain: "viaappia-2ff1e.firebaseapp.com",
    projectId: "viaappia-2ff1e",
    storageBucket: "viaappia-2ff1e.appspot.com",
    messagingSenderId: "639035428084",
    appId: "1:639035428084:web:1d964e932f3173d7fa22a1",
    measurementId: "G-E78CLSNBNL"
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
});
