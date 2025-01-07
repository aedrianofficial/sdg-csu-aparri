@extends('layouts.reviewer')

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
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Notifications</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('reviewer.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Notifications</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @php
                        $unreadCount = auth()->user()->notifications()->whereNull('read_at')->count();
                    @endphp

                    <div class="card">
                        <div class="card-header">
                            <span class="text-primary">{{ $unreadCount }} Unread Notifications</span>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                @forelse (auth()->user()->notifications()->latest()->get() as $notification)
                                    @php
                                        $data = json_decode($notification->data, true);
                                        $bgColor = $notification->read_at ? 'bg-light' : 'unread-notification';

                                        // Determine appropriate route based on type and status for reviewer
                                        $route = '';
                                        if ($data['type'] === 'project') {
                                            $route = match ($data['status']) {
                                                'submitted for review', 'resubmitted for review' => route(
                                                    'reviewer.projects.show',
                                                    [
                                                        'id' => $notification->related_id,
                                                        'notification_id' => $notification->id,
                                                    ],
                                                ),
                                                default => route('reviewer.projects.show', [
                                                    'id' => $notification->related_id,
                                                    'notification_id' => $notification->id,
                                                ]),
                                            };
                                        } elseif ($data['type'] === 'report') {
                                            $route = match ($data['status']) {
                                                'submitted for review', 'resubmitted for review' => route(
                                                    'reviewer.reports.show',
                                                    [
                                                        'id' => $notification->related_id,
                                                        'notification_id' => $notification->id,
                                                    ],
                                                ),
                                                default => route('reviewer.reports.show', [
                                                    'id' => $notification->related_id,
                                                    'notification_id' => $notification->id,
                                                ]),
                                            };
                                        } elseif ($data['type'] === 'status_report') {
                                            // Changed from 'report' to 'status_report'
                                            if ($data['status'] === 'submitted for review') {
                                                $route = route('reviewer.status_reports.show_project', [
                                                    'id' => $notification->related_id,
                                                    'notification_id' => $notification->id,
                                                ]);
                                            } elseif ($data['status'] === 'resubmitted for review') {
                                                $route = route('reviewer.status_reports.show_project', [
                                                    'id' => $notification->related_id,
                                                    'notification_id' => $notification->id,
                                                ]);
                                            }
                                        } elseif ($data['type'] === 'terminal_report') {
                                            // Changed from 'report' to 'terminal_report'
                                            if ($data['status'] === 'submitted for review') {
                                                $route = route('reviewer.terminal_reports.show_project', [
                                                    'id' => $notification->related_id,
                                                    'notification_id' => $notification->id,
                                                ]);
                                            } elseif ($data['status'] === 'resubmitted for review') {
                                                $route = route('reviewer.terminal_reports.show_project', [
                                                    'id' => $notification->related_id,
                                                    'notification_id' => $notification->id,
                                                ]);
                                            }
                                        } elseif ($data['type'] === 'research') {
                                            $route = match ($data['status']) {
                                                'submitted for review', 'resubmitted for review' => route(
                                                    'reviewer.research.show',
                                                    [
                                                        'id' => $notification->related_id,
                                                        'notification_id' => $notification->id,
                                                    ],
                                                ),
                                                default => route('reviewer.research.show', [
                                                    'id' => $notification->related_id,
                                                    'notification_id' => $notification->id,
                                                ]),
                                            };
                                        }
                                    @endphp

                                    <a href="{{ $route }}"
                                        class="list-group-item list-group-item-action {{ $bgColor }} notification-message"
                                        onclick="markAsRead('{{ $notification->id }}')">

                                        <div>
                                            <i class="bi {{ $data['icon'] ?? 'bi-bell' }} me-2"></i>
                                            {{ $data['message'] }}
                                            <small
                                                class="text-secondary float-end">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                    </a>

                                    <div class="dropdown-divider"></div>
                                @empty
                                    <div class="text-center text-secondary">No new notifications</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
