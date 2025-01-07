@extends('layouts.approver')
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
                    <h3 class="mb-0">View Rejected Project/Program</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('approver.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            View Rejected Project/Program
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
                            <!-- Feedback Section -->
                            @if ($project->feedbacks->count() > 0)
                                <h4>Feedback</h4>
                                <div class="mb-4">
                                    @foreach ($project->feedbacks as $feedback)
                                        <div class="feedback-item mb-3">
                                            <div class="mb-3">
                                                <label for="feedback" class="form-label">Feedback:</label>
                                                @php
                                                    $feedbackText = $feedback->feedback;
                                                    $rowCount =
                                                        substr_count($feedbackText, "\n") +
                                                        ceil(strlen($feedbackText) / 100); // Adjust based on length
                                                    $rowCount = $rowCount < 3 ? 3 : $rowCount; // Ensure at least 3 rows
                                                @endphp
                                                <textarea name="feedback" id="feedback" class="form-control" rows="{{ $rowCount }}" readonly>{{ $feedbackText }}</textarea>
                                            </div>
                                            <strong>{{ $feedback->user->first_name }}
                                                {{ $feedback->user->last_name }}</strong>
                                            <small class="text-muted">on
                                                {{ $feedback->created_at->format('M d, Y H:i') }}</small>
                                            <hr>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div>
                                    <h4>Feedback</h4>
                                    <p>No feedback available for this Project.</p>
                                    <hr>
                                </div>
                            @endif
                            <!-- End Feedback Section -->

                            <h4>Rejected Project/Program</h4>
                            <div class="mb-3">
                                <label for="title" class="form-label">Title:</label>
                                <input type="text" name="title" id="title" class="form-control"
                                    value="{{ $project->title }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description:</label>
                                @php
                                    $description = $project->description;
                                    $rowCount = substr_count($description, "\n") + floor(strlen($description) / 100); // Adjust row count based on length
                                    $rowCount = $rowCount < 3 ? 3 : $rowCount; // Ensure a minimum of 3 rows
                                @endphp
                                <textarea name="description" id="description" cols="30" rows="{{ $rowCount }}" class="form-control" readonly>{{ $description }}</textarea>
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
                            <div class="mb-3">
                                <label for="">Image: </label>
                                <div>
                                    <img src="{{ $project->projectimg->image }}" alt="project-image"
                                        style="max-width: 500px; height: auto;">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="created_by" class="form-label">Address:</label>
                                <input type="text" name="created_by" id="created_by" class="form-control"
                                    value="{{ $project->location_address }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="coordinates" class="form-label">Coordinates:</label>
                                <input type="text" name="coordinates" id="coordinates" class="form-control"
                                    value="{{ $project->latitude }}, {{ $project->longitude }}" readonly>
                            </div>

                            <div class="mb-3" id="map" style="height: 400px; margin-top: 20px;"></div>

                            <div class="mb-3">
                                <label for="created_by" class="form-label">Created by:</label>
                                <input type="text" name="created_by" id="created_by" class="form-control"
                                    value="{{ $project->user->first_name }} {{ $project->user->last_name }}" readonly>
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
@endsection
