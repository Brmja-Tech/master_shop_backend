<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- BEGIN: Vendor JS-->
<script src="{{ asset('dashboard') }}/app-assets/vendors/js/vendors.min.js"></script>
<!-- BEGIN Vendor JS-->


<!-- BEGIN: Page Vendor JS-->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="{{ asset('dashboard') }}/app-assets/vendors/js/extensions/toastr.min.js"></script>
<script src="{{ asset('dashboard') }}/app-assets/js/scripts/extensions/ext-component-toastr.js"></script>

<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="{{ asset('dashboard') }}/app-assets/js/core/app-menu.js"></script>
<script src="{{ asset('dashboard') }}/app-assets/js/core/app.js"></script>
<!-- END: Theme JS-->

<!-- BEGIN: Page JS-->
<script src="{{ asset('dashboard') }}/app-assets/js/scripts/pages/dashboard-ecommerce.js"></script>
<!-- END: Page JS-->
<script src="{{ asset('dashboard') }}/app-assets/vendors/js/extensions/sweetalert2.all.min.js"></script>
<script src="{{ asset('dashboard') }}/app-assets/js/scripts/extensions/ext-component-sweet-alerts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js"
    integrity="sha512-b+nQTCdtTBIRIbraqNEwsjB6UvL3UEMkXnhzd8awtCYh0Kcsjl9uEgwVFVbhoj3uu1DO1ZMacNvLoyJJiNfcvg=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

{{-- file input to upload image and show it --}}
<script src="{{ asset('vendor/file-input/js/fileinput.min.js') }}"></script>
<script src="{{ asset('vendor/file-input/themes/fa5/theme.min.js') }}"></script>
@if (Config::get('app.locale') == 'ar')
    <script src="{{ asset('vendor/file-input/js/locales/LANG.js') }}"></script>
    <script src="{{ asset('vendor/file-input/js/locales/ar.js') }}"></script>
@endif
<script>
    var lang = "{{ app()->getLocale() }}";
    $(function() {
        $('#singel-image').fileinput({
            theme: 'fa5',
            language: lang,
            allowedFileTypes: ['image'],
            maxFileCount: 1,
            enableResumableUpload: false,
            showUpload: false,
            browseOnZoneClick: true,
        });
    });
</script>
<script>
    var lang = "{{ app()->getLocale() }}";
    $(function() {
        $('#multiple-images').fileinput({
            theme: 'fa5',
            language: lang,
            allowedFileTypes: ['image'],
            maxFileCount: 10,
            showUpload: false,
            showRemove: true,
            showCaption: true,
            showClose: false,
            browseOnZoneClick: true,
            fileActionSettings: {
                showZoom: true,
                showDrag: false,
            },
            initialPreviewAsData: true,
            overwriteInitial: false,
        });
    });
</script>

{{-- end file input to upload image and show it --}}


@stack('js')
@php
    $firebaseDbUrl = config('services.firebase.database_url');
    $firebaseWebConfig = config('services.firebase.web', []);
    $hasFirebaseWebConfig = filled($firebaseWebConfig['api_key'] ?? null)
        && filled($firebaseWebConfig['project_id'] ?? null)
        && filled($firebaseWebConfig['messaging_sender_id'] ?? null)
        && filled($firebaseWebConfig['app_id'] ?? null)
        && filled($firebaseWebConfig['vapid_key'] ?? null);
    $projectId = $firebaseWebConfig['project_id'] ?: 'master-shop-df984';
@endphp

@if (auth('admin')->check() && ($hasFirebaseWebConfig || $firebaseDbUrl))
    <script type="module">
        import { initializeApp, getApp, getApps } from 'https://www.gstatic.com/firebasejs/10.13.2/firebase-app.js';
        import { getMessaging, getToken, isSupported } from 'https://www.gstatic.com/firebasejs/10.13.2/firebase-messaging.js';
        import { getDatabase, ref, onChildAdded, onValue } from 'https://www.gstatic.com/firebasejs/10.13.2/firebase-database.js';

        const firebaseConfig = {
            projectId: @js($projectId),
            @if ($firebaseDbUrl)
                databaseURL: @js($firebaseDbUrl),
            @endif
            @if ($hasFirebaseWebConfig)
                apiKey: @js($firebaseWebConfig['api_key']),
                authDomain: @js($firebaseWebConfig['auth_domain']),
                storageBucket: @js($firebaseWebConfig['storage_bucket']),
                messagingSenderId: @js($firebaseWebConfig['messaging_sender_id']),
                appId: @js($firebaseWebConfig['app_id']),
            @endif
        };

        let app;
        try {
            app = getApps().length === 0 ? initializeApp(firebaseConfig) : getApp();
        } catch (e) {
            console.error('Firebase app initialization failed', e);
        }

        const adminId = @js(auth('admin')->id());

        // 1. Setup Firebase Realtime Database Notifications
        @if ($firebaseDbUrl)
        try {
            const db = getDatabase(app);
            const notificationsRef = ref(db, 'notifications/admin_' + adminId);

            let isInitialLoad = true;

            onValue(notificationsRef, () => {
                isInitialLoad = false;
            }, { onlyOnce: true });

            onChildAdded(notificationsRef, (snapshot) => {
                if (!isInitialLoad) {
                    const notification = snapshot.val();
                    
                    if (window.Livewire) {
                        window.Livewire.dispatch('refreshNotifications');
                    }

                    if (window.toastr) {
                        window.toastr.success(
                            notification.message || 'لديك إشعار جديد',
                            notification.title || 'إشعار جديد',
                            {
                                closeButton: true,
                                tapToDismiss: false,
                                rtl: true,
                                positionClass: 'toast-top-left'
                            }
                        );
                    }

                    try {
                        const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-200.wav');
                        audio.volume = 0.5;
                        audio.play();
                    } catch (e) {
                        console.log('Audio play blocked', e);
                    }
                }
            });
        } catch (error) {
            console.error('Firebase Realtime Database setup failed:', error);
        }
        @endif

        // 2. Setup Firebase Cloud Messaging (FCM)
        @if ($hasFirebaseWebConfig)
        const vapidKey = @js($firebaseWebConfig['vapid_key']);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        async function syncAdminFcmToken(token) {
            if (!token || !csrfToken) return;

            const lastToken = window.localStorage.getItem('admin_fcm_token');
            if (lastToken === token) return;

            const response = await fetch('/admin/fcm-token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ fcm_token: token }),
            });

            if (response.ok) {
                window.localStorage.setItem('admin_fcm_token', token);
            }
        }

        async function initAdminMessaging() {
            const supported = await isSupported();
            if (!supported || !('Notification' in window) || !('serviceWorker' in navigator)) return;
            if (Notification.permission === 'denied') return;

            const permission = Notification.permission === 'granted'
                ? 'granted'
                : await Notification.requestPermission();

            if (permission !== 'granted') return;

            const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
            const messaging = getMessaging(app);
            const token = await getToken(messaging, {
                vapidKey,
                serviceWorkerRegistration: registration,
            });

            await syncAdminFcmToken(token);
        }

        initAdminMessaging().catch(error => {
            console.error('Admin FCM setup failed:', error);
        });
        @endif
    </script>
@endif
<script>
    $(window).on('load', function() {
        if (feather) {
            feather.replace({
                width: 14,
                height: 14
            });
        }
    })
</script>


{{-- Sweetalert from delete confirmtion --}}
<script>
    // sweetalert from basic table and refresh the page
    document.addEventListener('click', function(event) {
        if (event.target.id === 'confirm-delete-text' || event.target.closest('#confirm-delete-text')) {
            const button = event.target.closest('#confirm-delete-text');
            const formId = button.getAttribute('data-form-id');

            Swal.fire({
                title: "{{ __('dashboard.are_you_sure') }}",
                text: "{{ __('dashboard.confirm_delete_message') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "{{ __('dashboard.yes_delete') }}",
                cancelButtonText: "{{ __('dashboard.cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    });
    // sweetalert from datatable and refresh table only
    document.addEventListener('click', function(event) {
        if (event.target.id === 'confirm-text' || event.target.closest('#confirm-text')) {
            const button = event.target.closest('#confirm-text');
            const formId = button.getAttribute('data-form-id');
            const form = document.getElementById(formId);
            const actionUrl = form.getAttribute('action');

            Swal.fire({
                title: "{{ __('dashboard.are_you_sure') }}",
                text: "{{ __('dashboard.confirm_delete_message') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "{{ __('dashboard.yes_delete') }}",
                cancelButtonText: "{{ __('dashboard.cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(actionUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                _method: 'DELETE'
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: "{{ __('dashboard.success') }}",
                                    text: data.message,
                                    icon: "success",
                                    timer: 3000,
                                    showConfirmButton: true
                                });
                                $('#DataTables_Table').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire("{{ __('dashboard.error') }}", data.message, "error");
                            }
                        })
                        .catch(error => {
                            Swal.fire("{{ __('dashboard.error') }}",
                                "{{ __('dashboard.error_occurred') }}", "error");
                        });
                }
            });
        }
    });
</script>
{{-- End sweetalert from delete confirmtion --}}


{{-- Ajax change status and message confirmation --}}
<script>
    $(document).on('click', '.change-status-btn', function(e) {
        e.preventDefault();
        let button = $(this);
        let url = button.data('url');

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                if (response.status ==true) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: '{{ __('dashboard.status-change') }}',
                        showConfirmButton: false,
                        timer: 1500,
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    if (response.new_status == 1) {
                        button
                            .removeClass('btn-warning')
                            .addClass('btn-success')
                            .text('{{ __('dashboard.active') }}');
                    } else {
                        button
                            .removeClass('btn-success')
                            .addClass('btn-warning')
                            .text('{{ __('dashboard.inactive') }}');
                    }
                } else {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: '{{ __('validation.something-valid') }}',
                        showConfirmButton: false,
                        timer: 1500,
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                }
            },
            error: function(error) {
                console.error(error);
                showMessage('An error occurred. Please try again.', 'error');
            },
        });
    });
</script>
{{-- End ajax change status and message confirmation --}}


{{-- Ajax change approved and message confirmation --}}
<script>
    $(document).on('click', '.change-approved-btn', function(e) {
        e.preventDefault();
        let button = $(this);
        let url = button.data('url');

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: '{{ __('dashboard.approved-change') }}',
                        showConfirmButton: false,
                        timer: 1500,
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    if (response.new_approved == 1) {
                        button
                            .removeClass('btn-warning')
                            .addClass('btn-success')
                            .html('<i class="fa-solid fa-check"></i>');
                    } else {
                        button
                            .removeClass('btn-success')
                            .addClass('btn-warning')
                            .html('<i class="fa-solid fa-x"></i>');
                    }

                } else {
                    showMessage(response.message, 'error');
                }
            },
            error: function(error) {
                console.error(error);
                showMessage('An error occurred. Please try again.', 'error');
            },
        });
    });
</script>
{{-- End ajax change approved and message confirmation --}}


{{-- Optimize modal in livewire to open and close --}}
<script>
    window.addEventListener('createModalToggle', event => {
        $('#createModal').modal('toggle');
    })

    window.addEventListener('updateModalToggle', event => {
        $('#updateModal').modal('toggle');
    })

    window.addEventListener('deleteModalToggle', event => {
        $('#deleteModal').modal('toggle');
    })

    window.addEventListener('showModalToggle', event => {
        $('#showModal').modal('toggle');
    })

    // Livewire.on('changeStatus', data => {
    //     console.log('Received changeStatus event:', data);
    // });
</script>
{{-- End optimize modal in livewire to open and close --}}
