/* global importScripts, firebase */

importScripts('https://www.gstatic.com/firebasejs/10.13.2/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.13.2/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: @json($firebaseWebConfig['api_key'] ?? null),
    authDomain: @json($firebaseWebConfig['auth_domain'] ?? null),
    projectId: @json($firebaseWebConfig['project_id'] ?? null),
    storageBucket: @json($firebaseWebConfig['storage_bucket'] ?? null),
    messagingSenderId: @json($firebaseWebConfig['messaging_sender_id'] ?? null),
    appId: @json($firebaseWebConfig['app_id'] ?? null),
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
    const notificationTitle = payload.notification?.title || 'New notification';
    const notificationOptions = {
        body: payload.notification?.body || '',
        data: payload.data || {},
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});
