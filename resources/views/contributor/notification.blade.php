@extends('layouts.contributor')

@section('styles')
    <style>
        .unread-notification {
            background-color: #e4e2de;
            /* Background color for unread notifications */
            color: #212529;
            /* Default text color */
        }

        .notification-message {
            white-space: normal;
            /* Allows text to wrap normally */
        }

        .card-body {
            padding: 0;
            /* Remove default padding to have full background color */
        }
    </style>
@endsection

@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Notifications</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Notifications</li>
                    </ol>
                </div>
            </div> <!--end::Row-->
        </div> <!--end::Container-->
    </div> <!--end::App Content Header-->

    <!--begin::App Content-->
    <div class="app-content">
        <div class="container-fluid">
            <div class="row"> <!--begin::Col-->
                <div class="col-12">
                    @php
                        $unreadCount = auth()->user()->notifications()->whereNull('read_at')->count();
                    @endphp

                    <div class="card">
                        <div class="card-header">
                            <span class="text-primary">{{ $unreadCount }} Unread Notifications</span>
                        </div>
                        <div class="card-body">
                            <div class="list-group"> <!-- Use list-group for better styling -->
                                @forelse (auth()->user()->notifications()->latest()->get() as $notification)
                                    @php
                                        $data = json_decode($notification->data, true);
                                        $bgColor = $notification->read_at ? 'bg-light' : 'unread-notification';

                                        // Determine the appropriate route based on type and status
                                        $route = '';
                                        if ($data['type'] === 'project') {
                                            $route = match ($data['status']) {
                                                'request_changes' => route(
                                                    'contributor.projects.request_changes.show',
                                                    [
                                                        'id' => $notification->related_id,
                                                        'notification_id' => $notification->id,
                                                    ],
                                                ),
                                                'rejected' => route('contributor.projects.rejected.show', [
                                                    'id' => $notification->related_id,
                                                    'notification_id' => $notification->id,
                                                ]),
                                                default => route('contributor.projects.show', [
                                                    'id' => $notification->related_id,
                                                    'notification_id' => $notification->id,
                                                ]),
                                            };
                                        } elseif ($data['type'] === 'report') {
                                            $route = match ($data['status']) {
                                                'request_changes' => route('contributor.reports.request_changes.show', [
                                                    'id' => $notification->related_id,
                                                    'notification_id' => $notification->id,
                                                ]),
                                                'rejected' => route('contributor.reports.rejected_show', [
                                                    'id' => $notification->related_id,
                                                    'notification_id' => $notification->id,
                                                ]),
                                                default => route('contributor.reports.show', [
                                                    'id' => $notification->related_id,
                                                    'notification_id' => $notification->id,
                                                ]),
                                            };
                                        } elseif ($data['type'] === 'research') {
                                            $route = match ($data['status']) {
                                                'request_changes' => route(
                                                    'contributor.research.request_changes.show',
                                                    [
                                                        'id' => $notification->related_id,
                                                        'notification_id' => $notification->id,
                                                    ],
                                                ),
                                                'rejected' => route('contributor.research.rejected.show', [
                                                    'id' => $notification->related_id,
                                                    'notification_id' => $notification->id,
                                                ]),
                                                default => route('contributor.research.show', [
                                                    'id' => $notification->related_id,
                                                    'notification_id' => $notification->id,
                                                ]),
                                            };
                                        }
                                    @endphp

                                    <a href="{{ $route }}"
                                        class="list-group-item list-group-item-action {{ $bgColor }} notification-message"
                                        onclick="markAsRead('{{ $notification->id }}')">
                                        <div><i class="bi {{ $data['icon'] ?? 'bi-bell' }} me-2"></i>
                                            {{ $data['message'] }}
                                            <small
                                                class="text-secondary float-end">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                    </a>

                                    <div class="dropdown-divider"></div>
                                @empty
                                    <div class="text-center text-secondary">No new notifications</div>
                                @endforelse
                            </div> <!--end::List Group-->
                        </div> <!--end::Card Body-->
                    </div> <!--end::Card-->
                </div> <!--end::Col-->
            </div> <!--end::Row-->
        </div> <!--end::Container-->
    </div> <!--end::App Content-->
@endsection