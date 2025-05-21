@extends('layouts.reviewer')
@section('title', 'View Project/Program')
@section('styles')
    <style>
        #map {
            height: 400px;
            border: 2px solid #007bff;
            /* Border color */
            border-radius: 8px;
            /* Rounded corners */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            /* Shadow effect */
        }


        .list-group-item {
            cursor: pointer;
            padding: 10px;
            /* Padding for better spacing */
        }

        .leaflet-popup .fas {
            color: #007bff;
            /* Icon color */
        }
    </style>
@endsection
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">View Project/Program</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('reviewer.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            View Project/Program
                        </li>
                    </ol>
                </div>
            </div> <!--end::Row-->
        </div> <!--end::Container-->
    </div> <!--end::App Content Header--> <!--begin::App Content-->
    <div class="app-content"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row"> <!--begin::Col-->
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="forms-sample">
                                @if ($notificationData)
                                    <div class="alert alert-info">
                                        <strong>Notification:</strong> {{ $notificationData['message'] }}<br>
                                        <strong>
                                            @php
                                                // Initialize an array to hold role names
                                                $roleNames = [];

                                                // Variable to hold the final name to display
                                                $finalName = 'N/A'; // Default name if none are found

                                                // Check if role is an array
                                                if (is_array($notificationData['role'])) {
                                                    foreach ($notificationData['role'] as $role) {
                                                        if ($role === 'contributor') {
                                                            $roleNames[] = 'Contributor';
                                                            $finalName = $notificationData['contributor'] ?? 'N/A'; // Save contributor name
                                                        } elseif ($role === 'reviewer') {
                                                            $roleNames[] = 'Reviewer';
                                                            $finalName = $notificationData['reviewer'] ?? 'N/A'; // Save reviewer name
                                                        } elseif ($role === 'approver') {
                                                            $roleNames[] = 'Approver';
                                                            $finalName = $notificationData['approver'] ?? 'N/A'; // Save approver name
                                                        } elseif ($role === 'publisher') {
                                                            $roleNames[] = 'Publisher';
                                                            $finalName = $notificationData['publisher'] ?? 'N/A'; // Save publisher name
                                                        } elseif ($role === 'admin') {
                                                            // Handle admin role
                                                            $roleNames[] = 'Admin';
                                                            $finalName = $notificationData['admin'] ?? 'N/A'; // Save admin name
                                                        }
                                                    }
                                                } else {
                                                    // Handle the case where role is a single string
                                                    if ($notificationData['role'] === 'contributor') {
                                                        $roleNames[] = 'Contributor';
                                                        $finalName = $notificationData['contributor'] ?? 'N/A';
                                                    } elseif ($notificationData['role'] === 'reviewer') {
                                                        $roleNames[] = 'Reviewer';
                                                        $finalName = $notificationData['reviewer'] ?? 'N/A';
                                                    } elseif ($notificationData['role'] === 'approver') {
                                                        $roleNames[] = 'Approver';
                                                        $finalName = $notificationData['approver'] ?? 'N/A';
                                                    } elseif ($notificationData['role'] === 'publisher') {
                                                        $roleNames[] = 'Publisher';
                                                        $finalName = $notificationData['publisher'] ?? 'N/A';
                                                    } elseif ($notificationData['role'] === 'admin') {
                                                        // Handle admin role
                                                        $roleNames[] = 'Admin';
                                                        $finalName = $notificationData['admin'] ?? 'N/A'; // Save admin name
                                                    }
                                                }

                                                // Join role names with a comma
                                                $rolesString = implode(', ', $roleNames);

                                                // Combine roles and final name for output
                                                $outputString = !empty($finalName)
                                                    ? "{$rolesString}: {$finalName}"
                                                    : $rolesString;
                                            @endphp

                                            {{ $outputString }}
                                        </strong>
                                    </div>
                                @endif
                                @php
                                    // Calculate the number of rows for the textarea based on description length
                                    $rowCount = ceil(strlen($project->description) / 100); // Adjust divisor based on character length per row
                                @endphp

                                <div class="mb-3">
                                    <label for="title" class="form-label">Title:</label>
                                    <input type="text" name="title" id="title" class="form-control"
                                        value="{{ $project->title }}" readonly>
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description:</label>
                                    <div class="form-control" style="min-height: 100px; overflow-y: auto;"
                                        contenteditable="false">
                                        {!! $project->description !!}
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="project_status" class="form-label">Project Status:</label>
                                    <input type="text" name="project_status" id="project_status" class="form-control"
                                        value="{{ $project->status->status ?? 'N/A' }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="review_status" class="form-label">Review Status:</label>
                                    <input type="text" name="review_status" id="review_status" class="form-control"
                                        value="{{ $project->reviewStatus->status ?? 'N/A' }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="is_publish" class="form-label">Is Published:</label>
                                    <input type="text" name="is_publish" id="is_publish" class="form-control"
                                        value="{{ $project->is_publish == 1 ? 'Published' : 'Draft' }}" readonly>
                                </div>

                                <!-- SDGs -->
                                <div class="mb-3">
                                    <label for="sdg" class="form-label">SDGs:</label>
                                    <textarea name="sdg" id="sdg" cols="30" rows="3" class="form-control" readonly>
@foreach ($project->sdg as $sdg)
{{ $sdg->name }}
@endforeach
</textarea>
                                </div>
                                <!-- SDG Sub Categories -->
                                <div class="mb-3">
                                    <label for="sdg_sub_categories" class="form-label">SDG Targets:</label>
                                    <textarea name="sdg_sub_categories" id="sdg_sub_categories" cols="30" rows="5" class="form-control" readonly>
        @if ($project->sdgSubCategories->isEmpty())
No SDG Targets available.
@else
@foreach ($project->sdgSubCategories as $subCategory)
{{ $subCategory->sub_category_name }} {{ $subCategory->sub_category_description }}
@endforeach
@endif
    </textarea>
                                    <p>
                                        Source: <a
                                            href="https://sustainabledevelopment.un.org/content/documents/11803Official-List-of-Proposed-SDG-Indicators.pdf"
                                            target="_blank">https://sustainabledevelopment.un.org/content/documents/11803Official-List-of-Proposed-SDG-Indicators.pdf</a>
                                    </p>
                                </div>
                                
                                <!-- Gender Impact Analysis -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Gender Impact Analysis:</label>
                                    @if($project->genderImpact)
                                        <div class="card border-primary mb-3">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <ul class="list-group mb-3">
                                                            <li class="list-group-item {{ $project->genderImpact->benefits_women ? 'list-group-item-success' : 'list-group-item-light' }}">
                                                                <i class="fas {{ $project->genderImpact->benefits_women ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' }} me-2"></i>
                                                                Benefits Women/Girls
                                                                @if($project->genderImpact->women_count)
                                                                    <span class="badge bg-info ms-2">{{ $project->genderImpact->women_count }} mentioned</span>
                                                                @endif
                                                            </li>
                                                            <li class="list-group-item {{ $project->genderImpact->benefits_men ? 'list-group-item-success' : 'list-group-item-light' }}">
                                                                <i class="fas {{ $project->genderImpact->benefits_men ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' }} me-2"></i>
                                                                Benefits Men/Boys
                                                                @if($project->genderImpact->men_count)
                                                                    <span class="badge bg-info ms-2">{{ $project->genderImpact->men_count }} mentioned</span>
                                                                @endif
                                                            </li>
                                                            <li class="list-group-item {{ $project->genderImpact->benefits_all ? 'list-group-item-success' : 'list-group-item-light' }}">
                                                                <i class="fas {{ $project->genderImpact->benefits_all ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' }} me-2"></i>
                                                                Benefits All Genders
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card h-100">
                                                            <div class="card-body">
                                                                <h6 class="card-subtitle mb-2 text-muted">Gender Equality Focus</h6>
                                                                <p class="card-text">
                                                                    <span class="badge {{ $project->genderImpact->addresses_gender_inequality ? 'bg-success' : 'bg-secondary' }} p-2">
                                                                        <i class="fas {{ $project->genderImpact->addresses_gender_inequality ? 'fa-check' : 'fa-times' }} me-1"></i>
                                                                        {{ $project->genderImpact->addresses_gender_inequality ? 'Addresses Gender Inequality' : 'No Explicit Focus on Gender Inequality' }}
                                                                    </span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if($project->genderImpact->gender_notes)
                                                    <div class="alert alert-info mt-3">
                                                        <i class="fas fa-info-circle me-2"></i>
                                                        {{ $project->genderImpact->gender_notes }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-secondary">
                                            <i class="fas fa-info-circle me-2"></i>No gender impact analysis available for this project.
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label for="">Image: </label>
                                    <div>
                                        <img src="{{ $project->projectimg->image }}" alt="project-image"
                                            style="max-width: 500px; height: auto;">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="location_address" class="form-label">Address:</label>
                                    <input type="text" name="location_address" id="location_address" class="form-control"
                                        value="{{ $project->location_address }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="longitude" class="form-label">Coordinates:</label>
                                    <input type="text" name="longitude" id="longitude" class="form-control"
                                        value="{{ $project->latitude }}, {{ $project->longitude }}" readonly>
                                </div>

                                <div class="mb-3" id="map" style="height: 400px; margin-top: 20px;"></div>

                                <div class="mb-3">
                                    <label for="created_by" class="form-label">Created by:</label>
                                    <input type="text" name="created_by" id="created_by" class="form-control"
                                        value="{{ $project->user->first_name }} {{ $project->user->last_name }}"
                                        readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="created_at" class="form-label">Created at:</label>
                                    <input type="text" name="created_at" id="created_at" class="form-control"
                                        value="{{ $project->created_at->format('M d, Y H:i') }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="updated_at" class="form-label">Updated at:</label>
                                    <input type="text" name="updated_at" id="updated_at" class="form-control"
                                        value="{{ $project->updated_at->format('M d, Y H:i') }}" readonly>
                                </div>

                                <!-- Action Buttons -->
                                <div class="mb-3">
                                    <!-- Button for 'Need Changes' -->
                                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal"
                                        data-bs-target="#needChangesModal">Need Changes</button>

                                    <!-- Button for 'Reject' -->
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#rejectModal">Reject</button>

                                    <!-- Forward to Approver Button -->
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                        data-bs-target="#confirmReviewModal">Reviewed</button>
                                </div>

                                <!-- 'Reviewed' Modal -->
                                <div class="modal fade" id="confirmReviewModal" tabindex="-1"
                                    aria-labelledby="confirmReviewModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="confirmReviewModalLabel">Confirm Review</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to mark this project as "Reviewed" and forward it to
                                                the approver?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('reviewer.projects.reviewed', $project->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-success">Confirm</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 'Need Changes' Modal -->
                                <div class="modal fade" id="needChangesModal" tabindex="-1"
                                    aria-labelledby="needChangesModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="needChangesModalLabel">Need Changes Feedback
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('reviewer.projects.needchanges') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="project_id" value="{{ $project->id }}">
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="feedback" class="form-label">Feedback
                                                            (Required):</label>
                                                        <textarea name="feedback" id="feedback" class="form-control" rows="4" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- 'Reject' Modal -->
                                <div class="modal fade" id="rejectModal" tabindex="-1"
                                    aria-labelledby="rejectModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="rejectModalLabel">Reject Project</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('reviewer.projects.reject') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="project_id" value="{{ $project->id }}">
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="feedback" class="form-label">Feedback
                                                            (Optional):</label>
                                                        <textarea name="feedback" id="feedback" class="form-control" rows="4"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-danger">Reject Project</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div> <!--end::Container-->
    </div>
    <!--end::App Content-->
@endsection

@section('scripts')

    <script>
        $(document).ready(function() {
            // Initialize the map
            var latitude = {{ $project->latitude }};
            var longitude = {{ $project->longitude }};
            var address = "{{ $project->location_address }}";

            // Set up the map
            var map = L.map('map').setView([latitude, longitude], 16);

            // Add MapTiler tile layer
            L.tileLayer('https://api.maptiler.com/maps/streets/{z}/{x}/{y}.png?key=nnLs4mWhpJaZMAiwkL9K', {
                tileSize: 512,
                zoomOffset: -1,
                minZoom: 1,
                attribution: '&copy; <a href="https://www.maptiler.com/copyright/">MapTiler</a> | ' +
                    '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> Contributors',
                crossOrigin: true
            }).addTo(map);

            // Custom icon for markers
            var redMarkerIcon = L.icon({
                iconUrl: '{{ asset('assets/auth/images/leaflet/marker-icon-red.png') }}',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowUrl: '{{ asset('assets/auth/images/leaflet/marker-shadow.png') }}',
                shadowSize: [41, 41]
            });

            // Add a marker at the project's location with a popup displaying the address and coordinates
            L.marker([latitude, longitude], {
                    icon: redMarkerIcon
                })
                .addTo(map)
                .bindPopup(
                    `<strong>Address:</strong> ${address}<br><strong>Coordinates:</strong> (${latitude}, ${longitude})`
                )
                .openPopup();
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#needChangesModal').on('shown.bs.modal', function() {
                $('#feedback').trigger('focus');
            });
            $('#rejectModal').on('shown.bs.modal', function() {
                $('#feedback').trigger('focus');
            });
        });
    </script>
@endsection
