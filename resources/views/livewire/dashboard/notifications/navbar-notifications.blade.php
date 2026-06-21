<li class="nav-item dropdown dropdown-notification me-25">
    <a class="nav-link" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell ficon">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
        </svg>
        @if ($this->unreadCount > 0)
            <span class="badge rounded-pill bg-danger badge-up">{{ $this->unreadCount }}</span>
        @endif
    </a>
    <ul class="dropdown-menu dropdown-menu-media dropdown-menu-end">
        <li class="dropdown-menu-header">
            <div class="dropdown-header d-flex justify-content-between align-items-center">
                <h4 class="notification-title mb-0 me-auto">الإشعارات</h4>
                <div class="badge rounded-pill badge-light-primary">{{ $this->unreadCount }} جديدة</div>
            </div>
        </li>
        <li class="scrollable-container media-list ps" style="max-height: 350px; overflow-y: auto;">
            @forelse ($this->notifications as $notification)
                <a class="d-flex" 
                   href="{{ isset($notification->data['delivery_user_id']) ? route('dashboard.deliveries.show', ['id' => $notification->data['delivery_user_id']]) : (isset($notification->data['vendor_id']) ? route('dashboard.vendor.profile', ['id' => $notification->data['vendor_id']]) : '#') }}" 
                   wire:click="markAsRead('{{ $notification->id }}')">
                    <div class="list-item d-flex align-items-start {{ is_null($notification->read_at) ? 'bg-light' : '' }}" style="width: 100%; padding: 1rem; border-bottom: 1px solid #f0f0f0;">
                        <div class="me-1">
                            <div class="avatar bg-light-primary rounded-circle p-50 d-inline-block">
                                @if (isset($notification->data['vendor_id']))
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users text-primary">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-truck text-primary">
                                        <rect x="1" y="3" width="15" height="13"></rect>
                                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div class="list-item-body flex-grow-1">
                            <p class="media-heading mb-0">
                                <span class="fw-bolder text-dark">{{ $notification->data['title'] ?? 'إشعار جديد' }}</span>
                            </p>
                            <small class="notification-text text-muted d-block mt-25">{{ $notification->data['message'] ?? '' }}</small>
                        </div>
                        <div class="text-end">
                            <small class="notification-text text-muted" style="font-size: 0.7rem; white-space: nowrap;">
                                {{ $notification->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-2 text-center text-muted">لا توجد إشعارات حالياً</div>
            @endforelse
        </li>
        @if ($this->unreadCount > 0)
            <li class="dropdown-menu-footer" style="padding: 0.5rem;">
                <a class="btn btn-primary btn-sm w-100" href="#" wire:click.prevent="markAllAsRead">تحديد الكل كمقروء</a>
            </li>
        @endif
    </ul>
</li>
