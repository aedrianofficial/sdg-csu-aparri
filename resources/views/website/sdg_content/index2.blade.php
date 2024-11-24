@extends('layouts.website2')
@section('styles')
    <style>
        #map {
            height: 400px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .list-group-item {
            cursor: pointer;
            padding: 10px;
        }

        /* Search Address Input Styles */
        #searchAddress {
            border: 1px solid #007bff;
            border-radius: 0.25rem;
            padding: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: border-color 0.3s;
        }

        #searchAddress:focus {
            border-color: #0056b3;
            outline: none;
        }

        .leaflet-popup .fas {
            color: #007bff;
        }

        .post {
            transition: transform 0.2s ease-in-out;
        }

        .post:hover {
            transform: scale(1.03);
        }
    </style>
@endsection
@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card card-primary card-outline">
                        <div class="card-body">
                            <img src="{{ asset('assets/website/images/SDG-icons/E_SDG_logo_UN_emblem_square_trans_WEB.png') }}"
                                alt="SDG Logo" class="img-fluid rounded-top">
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="card-title m-0">Sustainable Development Goals</h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Cagayan State University (CSU) exemplifies an organization committed to
                                achieving the Sustainable Development Goals (SDGs) of the United Nations. With a vision to
                                be
                                a global center of excellence in the arts, culture, agriculture, fisheries, and the
                                sciences, CSU continues to create significant strides towards achieving the United Nations
                                Sustainable Development Goals (SDGs).</p>
                        </div>
                    </div>
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="card-title m-0">About CSU and SDGs</h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Its provision of quality instruction, engagement in
                                innovative and creative research, delivery of responsive public service, and engagement in
                                productive industry and community reflect its dedication to the SDGs. By integrating
                                sustainability and social responsibility into every aspect of its institution, CSU is not
                                only shaping the future but leading the way in creating a more equitable and sustainable
                                world for all.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="mt-5 mb-3" style="font-weight: 500;">Project Locations</h3>
                    <div id="map" class="card card-primary card-outline" style="height: 400px; border-radius: 10px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Charts for SDG -->
    <div class="content">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-12 mb-4">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h4 class="text-center mb-4" style="font-weight: 600;">
                                Overview of SDG Contributions: Projects, Reports, and Research
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="position: relative; height: 500px; width: 100%;">
                                <canvas id="sdgCombinedChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Section -->
    <div class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> Latest Projects</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">

                        <li class="breadcrumb-item"><a href="{{ route('website.sdg_project_main2') }}">Show All Projects</a>
                        </li>
                        <li class="breadcrumb-item active">Home</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container">
            @if ($projects !== null && count($projects) > 0)
                <div class="row">
                    @foreach ($projects as $project)
                        <div class="col-lg-4 mb-4"> <!-- Column for each card -->
                            <div class="card card-primary card-outline h-100 d-flex flex-column post">
                                <div class="card-header">
                                    <h5 class="card-title m-0 text-truncate" title="{{ $project->title }}">
                                        {{ Str::limit($project->title, 40) }}
                                    </h5>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <a href="{{ route('website.display_single_project2', $project->id) }}">
                                        <img src="{{ $project->projectimg->image }}" class="card-img-top" alt=""
                                            style="height: 200px; object-fit: cover;">
                                    </a>
                                    <div class="post-meta mt-3">
                                        <ul class="list-unstyled">
                                            <li>
                                                <i class="ion-calendar"></i>
                                                {{ date('d M Y', strtotime($project->created_at)) }}
                                            </li>
                                            <li>
                                                @foreach ($project->sdg as $project_sdgs)
                                                    <i class="ion-pricetags">{{ $project_sdgs->name }}&nbsp;</i>
                                                @endforeach
                                            </li>
                                        </ul>
                                    </div>
                                    <a href="{{ route('website.display_single_project2', $project->id) }}"
                                        class="btn btn-primary mt-auto continue-reading">Continue Reading</a>
                                </div>
                            </div>
                        </div> <!-- End of card column -->
                    @endforeach
                </div>
        </div>
    @else
        <h2 class="text-center text-danger mt-5">No Projects & Programs added</h2>
        @endif
    </div>

    <!-- Reports Section -->
    <div class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Latest Reports</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('website.sdg_report_main2') }}">Show All Reports</a>
                        </li>
                        <li class="breadcrumb-item active">Home</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container">
            @if ($reports !== null && count($reports) > 0)
                <div class="row">
                    @foreach ($reports as $report)
                        <div class="col-lg-4 mb-4"> <!-- Column for each card -->
                            <div class="card card-success card-outline h-100 d-flex flex-column post">
                                <div class="card-header">
                                    <h5 class="card-title m-0 text-truncate" title="{{ $report->title }}">
                                        {{ Str::limit($report->title, 40) }}
                                    </h5>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <a href="{{ route('website.display_single_report2', $report->id) }}">
                                        <img src="{{ $report->reportimg->image }}" class="card-img-top" alt=""
                                            style="height: 200px; object-fit: cover;">
                                    </a>
                                    <div class="post-meta mt-3">
                                        <ul class="list-unstyled">
                                            <li>
                                                <i class="ion-calendar"></i>
                                                {{ date('d M Y', strtotime($report->created_at)) }}
                                            </li>
                                            <li>
                                                @foreach ($report->sdg as $report_sdgs)
                                                    <i class="ion-pricetags">{{ $report_sdgs->name }}&nbsp;</i>
                                                @endforeach
                                            </li>
                                        </ul>
                                    </div>

                                    <a href="{{ route('website.display_single_report2', $report->id) }}"
                                        class="btn btn-success mt-auto continue-reading">Continue Reading</a>
                                </div>
                            </div>
                        </div> <!-- End of card column -->
                    @endforeach
                </div>
        </div>
    @else
        <h2 class="text-center text-danger mt-5">No Reports added</h2>
        @endif
    </div>

    <!-- Research Section -->
    <div class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Latest Research</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('website.sdg_research_main2') }}">Show All
                                Research</a></li>
                        <li class="breadcrumb-item active">Home</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container">
            @if ($research !== null && count($research) > 0)
                <div class="row">
                    @foreach ($research as $singleResearch)
                        <div class="col-lg-4 mb-4"> <!-- Column for each card -->
                            <div class="card card-secondary card-outline h-100 d-flex flex-column post">
                                <div class="card-header">
                                    <h5 class="card-title m-0 text-truncate" title="{{ $singleResearch->title }}">
                                        {{ Str::limit($singleResearch->title, 40) }}
                                    </h5>
                                </div>
                                <div class="card-body d-flex flex-column">

                                    <div class="post-meta mt-3">
                                        <ul class="list-unstyled">
                                            <li>
                                                <i class="ion-calendar"></i>
                                                {{ date('d M Y', strtotime($singleResearch->created_at)) }}
                                            </li>
                                            <li>
                                                @foreach ($singleResearch->sdg as $singleResearch_sdgs)
                                                    <i class="ion-pricetags">{{ $singleResearch_sdgs->name }}&nbsp;</i>
                                                @endforeach
                                            </li>
                                            <li>
                                                <i class="ion-pricetags"></i>
                                                {{ $singleResearch->researchcategory->name ?? 'No Category' }}
                                                <!-- Display the category name -->
                                            </li>
                                        </ul>
                                    </div>

                                    <a href="{{ route('website.display_single_research2', $singleResearch->id) }}"
                                        class="btn btn-secondary mt-auto continue-reading">Continue Reading</a>
                                </div>
                            </div>
                        </div> <!-- End of card column -->
                    @endforeach
                </div>
        </div>
    @else
        <h2 class="text-center text-danger mt-5">No Research added</h2>
        @endif
    </div>



@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize the map and set the default location to CSU-Aparri
            var map = L.map('map').setView([18.3515316, 121.6489289], 15);
            let currentLayer;

            let setMapLayer = (theme) => {
                if (currentLayer) {
                    map.removeLayer(currentLayer);
                }

                if (theme === "dark") {
                    currentLayer = L.tileLayer(
                        'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                            subdomains: 'abcd'
                        }).addTo(map);
                } else if (theme === "light") {
                    currentLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(map);
                }
            };

            const preferredTheme = localStorage.getItem("theme") || "light";
            setMapLayer(preferredTheme);

            document.querySelectorAll("[data-bs-theme-value]").forEach((toggle) => {
                toggle.addEventListener("click", () => {
                    const newTheme = toggle.getAttribute("data-bs-theme-value");
                    setMapLayer(newTheme);
                });
            });

            var locatorIcon = L.icon({
                iconUrl: '{{ asset('assets/auth/images/leaflet/marker-icon-red.png') }}',
                iconSize: [20, 30],
                iconAnchor: [10, 30],
                popupAnchor: [1, -30],
                shadowUrl: '{{ asset('assets/auth/images/leaflet/marker-shadow.png') }}',
                shadowSize: [30, 30],
                shadowAnchor: [10, 30]
            });

            var mapProjects = @json($mapProjects);

            mapProjects.forEach(function(project) {
                if (project.latitude && project.longitude) {
                    var marker = L.marker([project.latitude, project.longitude], {
                            icon: locatorIcon
                        })
                        .addTo(map)
                        .bindPopup(`
                        <div style="font-size: 14px;">
                            <strong>
                                <a href="/sdg/project2/${project.id}">
                                    <i class="fas fa-project-diagram"></i> ${project.title}
                                </a><span>(Click to view more info)</span>
                            </strong><br><br>
                            <i class="fas fa-map-marker-alt"></i> <strong>Address:</strong> ${project.location_address}<br>
                            <i class="fas fa-globe"></i> <strong>Coordinates:</strong> ${project.latitude}, ${project.longitude}
                        </div>
                        `);

                    if ('ontouchstart' in window || navigator.maxTouchPoints) {
                        marker.on('click', function(e) {
                            this.openPopup();
                        });
                    } else {
                        marker.on('mouseover', function(e) {
                            this.openPopup();
                        });
                    }
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            let combinedChart; // For the combined chart

            // Fetch and update chart data via AJAX
            function fetchReviewStatusData() {
                $.ajax({
                    url: "{{ route('analytics.sdgComparison') }}", // Update this route to fetch SDG comparison data
                    method: 'GET',
                    success: function(response) {
                        // Combine data from projects, reports, and research
                        const combinedLabels = response
                            .sdgLabels; // Assuming this contains unique SDG labels
                        const combinedData = [];
                        const projectData = response.projectData; // Array of project counts
                        const reportData = response.reportData; // Array of report counts
                        const researchData = response.researchData; // Array of research counts

                        // Aggregate data from projects, reports, and research
                        for (let i = 0; i < combinedLabels.length; i++) {
                            combinedData.push((projectData[i] || 0) + (reportData[i] || 0) + (
                                researchData[i] || 0));
                        }

                        updateCombinedChart(combinedData, combinedLabels, projectData, reportData,
                            researchData);
                    }
                });
            }

            // Function to update the combined chart
            function updateCombinedChart(data, labels, projectData, reportData, researchData) {
                if (combinedChart) {
                    combinedChart.data.labels = labels; // Set new labels
                    combinedChart.data.datasets[0].data = data; // Set new data
                    combinedChart.options.plugins.tooltip.callbacks.label = function(context) {
                        const index = context.dataIndex;
                        const totalCount = context.raw; // Total count from combined data
                        const projects = projectData[index] || 0; // Count of projects
                        const reports = reportData[index] || 0; // Count of reports
                        const research = researchData[index] || 0; // Count of research

                        return `Total: ${totalCount} (Projects: ${projects}, Reports: ${reports}, Research: ${research})`;
                    };
                    combinedChart.update(); // Update the chart
                }
            }

            // Chart options for a professional look
            const chartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y', // Set indexAxis to 'y' for horizontal bars
                plugins: {
                    legend: {
                        display: true,
                        position: 'top', // Position legend to the right
                        labels: {
                            maxWidth: 100,
                            boxWidth: 10,
                        },
                    },
                    title: {
                        display: false,
                    },
                }
            };

            function initCombinedChart() {
                if (combinedChart) {
                    combinedChart.destroy(); // Destroy if exists
                }

                combinedChart = new Chart(document.getElementById('sdgCombinedChart').getContext('2d'), {
                    type: 'bar', // Create a bar chart
                    data: {
                        labels: [], // SDG labels will be set dynamically
                        datasets: [{
                            label: 'Combined SDG Status',
                            data: [], // SDG data will be set dynamically
                            backgroundColor: [
                                '#E44C4D', // No Poverty
                                '#E1A42B', // Zero Hunger
                                '#4BBF6B', // Good Health and Well-Being
                                '#F04F25', // Quality Education
                                '#D94E9A', // Gender Equality
                                '#0073A3', // Clean Water and Sanitation
                                '#F7D75D', // Affordable and Clean Energy
                                '#C83A2B', // Decent Work and Economic Growth
                                '#6F7E92', // Industry, Innovation, and Infrastructure
                                '#C2008A', // Reduced Inequality
                                '#E14F40', // Sustainable Cities and Communities
                                '#57B6F4', // Responsible Consumption and Production
                                '#F38F4C', // Climate Action
                                '#006B7F', // Life Below Water
                                '#8A0B5B', // Life on Land
                                '#009C4A', // Peace, Justice, and Strong Institutions
                                '#A45F77' // Partnerships for the Goals
                            ],
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        ...chartOptions,
                        scales: {
                            x: {
                                beginAtZero: true, // Ensure x-axis starts at zero
                            }
                        },
                        plugins: {
                            legend: {
                                display: true, // Ensure the legend is displayed
                                position: 'top', // Position the legend at the top
                                labels: {
                                    maxWidth: 100,
                                    boxWidth: 10,
                                }
                            },
                            title: {
                                display: false,
                            }
                        }
                    }
                });
            }


            // Initialize charts on page load
            initCombinedChart();

            // Fetch and update data on page load
            fetchReviewStatusData();

            // Automatically refresh the data every 30 seconds
            setInterval(fetchReviewStatusData, 30000);
        });
    </script>
@endsection
