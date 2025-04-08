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

        /* Make the cards perfect square */
        .sdg-image {
            height: 100%;
            object-fit: cover;
        }

        @media (max-width: 767px) {
            .sdg-image {
                height: auto;
                aspect-ratio: 1 / 1;
                /* Ensures a perfect square */
            }
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

    <!-- All Project Locations Map -->
    <div class="content">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-12 mb-4">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h4 class="text-center mb-4" style="font-weight: 600;">All Project Locations
                            </h4>
                        </div>
                        <div class="card-body">
                            <div id="map" style="height: 400px; border-radius: 10px;"></div>
                        </div>
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
                            <!-- College Filter Dropdown -->
                            <div class="form-group">
                                <select id="collegeFilter" class="form-control">
                                    <option value="0">All Colleges</option>
                                    <option value="1">College of Teacher Education</option>
                                    <option value="2">College of Information and Computing Sciences</option>
                                    <option value="3">College of Industrial Technology</option>
                                    <option value="4">College of Hospitality Management</option>
                                    <option value="5">College of Fisheries and Aquatic Sciences</option>
                                    <option value="6">College of Criminal Justice Education</option>
                                    <option value="7">College of Business Entrepreneurship and Accountancy</option>
                                </select>
                            </div>
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

    <div class="content">
        <div class="container">
            @if ($sdgs !== null && count($sdgs) > 0)
                <div class="row">
                    @foreach ($sdgs as $index => $sdg)
                        @php
                            $sdgColors = [
                                '#E5243B',
                                '#DCA93A',
                                '#4C9E39',
                                '#C4182D',
                                '#FF3B20',
                                '#26BCE3',
                                '#FCC30B',
                                '#A21942',
                                '#FC6825',
                                '#DD1367',
                                '#FD9C25',
                                '#BF8A2F',
                                '#3E7E45',
                                '#0A96D8',
                                '#56C12A',
                                '#01689C',
                                '#19486A',
                            ];
                            $bgColor = $sdgColors[$index % count($sdgColors)];
                        @endphp

                        <div class="col-lg-3 col-md-4 col-6 mb-4 d-flex">
                            <div class="card card-primary card-outline w-100 d-flex flex-column post "
                                style="background-color: {{ $bgColor }}; border: none;">
                                <div class="card-body p-0">
                                    <a href="{{ route('website.sdg.show', ['id' => $sdg->id]) }}">
                                        <img src="{{ asset($sdg->sdgimage ? $sdg->sdgimage->image : 'images/sdg/default.png') }}"
                                            class="card-img-top w-100 sdg-image rounded" alt="{{ $sdg->name }}">
                                    </a>

                                </div>
                            </div>
                        </div>

                        @if ($index == 16)
                            <!-- Special case: Add SDG Partnership logo beside SDG 17 -->
                            <div class="col-lg-9 col-md-12 col-6 mb-4 d-flex">
                                <div class="card card-primary card-outline w-100 d-flex flex-column post"
                                    style="background-color: #ffffff; border: none;">
                                    <div class="card-body p-0 d-flex align-items-center justify-content-center">
                                        <img src="{{ asset('images/sdg/landing/E_SDG_logo_without_UN_emblem_horizontal_CMYK_Transparent.png') }}"
                                            class="w-100 d-none d-md-block rounded" alt="SDG Partnership Logo"
                                            style="max-height: 250px; object-fit: contain;">
                                        <img src="{{ asset('images/sdg/landing/E_SDG_logo_UN_emblem_square_trans_WEB-400x343.png') }}"
                                            class="w-100 d-block d-md-none" alt="SDG Partnership Logo (Mobile)">
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <h2 class="text-center text-danger mt-5">No SDG Data Available</h2>
            @endif
        </div>
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
            // Group projects by coordinates
            var groupedProjects = {};

            mapProjects.forEach(function(project) {
                if (project.latitude && project.longitude) {
                    var coords = `${project.latitude},${project.longitude}`;
                    if (!groupedProjects[coords]) {
                        groupedProjects[coords] = [];
                    }
                    groupedProjects[coords].push(project);
                }
            });

            // Loop through the grouped projects
            for (const coords in groupedProjects) {
                const projectsAtCoords = groupedProjects[coords];
                const [latitude, longitude] = coords.split(',');

                var marker = L.marker([latitude, longitude], {
                        icon: locatorIcon
                    })
                    .addTo(map)
                    .bindPopup(function() {
                        let popupContent =
                            `<div style="font-size: 14px;"><strong>Projects at this location:</strong><br><br>`;

                        // Display up to 5 projects
                        const visibleProjects = projectsAtCoords.slice(0, 5);
                        visibleProjects.forEach(project => {
                            popupContent += `
                        <a href="/sdg/project/${project.id}">
                            <i class="fas fa-project-diagram"></i> ${project.title} 
                        </a><br>
                    `;
                        });

                        // Add "Click to see more" if there are more than 5 projects
                        if (projectsAtCoords.length > 5) {
                            popupContent += `<br>
                        <a href="/sdg/projects/coordinates/${latitude}/${longitude}" data-coordinates="${coords}">
                            <strong>Click to see more projects</strong>
                        </a><br>
                    `;
                        }

                        // Add the address and coordinates
                        popupContent += `
                    <br><i class="fas fa-map-marker-alt"></i> <strong>Address:</strong> ${projectsAtCoords[0].location_address}<br>
                    <i class="fas fa-globe"></i> <strong>Coordinates:</strong> ${latitude}, ${longitude}
                </div>`;

                        return popupContent;
                    });

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
    </script>
    <script>
        $(document).ready(function() {
            let combinedChart; // For the combined chart
            let selectedCollege = 0; // Default to "All Colleges"

            // Handle college filter change
            $('#collegeFilter').on('change', function() {
                selectedCollege = $(this).val();
                fetchReviewStatusData();
            });

            // Fetch and update chart data via AJAX
            function fetchReviewStatusData() {
                $.ajax({
                    url: "{{ route('analytics.sdgComparison') }}",
                    method: 'GET',
                    data: {
                        college_id: selectedCollege
                    },
                    success: function(response) {
                        // Combine data from projects, status reports, terminal reports, and research
                        const combinedLabels = response
                        .sdgLabels; // Assuming this contains unique SDG labels
                        const combinedData = [];
                        const projectData = response.projectData; // Array of project counts
                        const statusReportData = response
                        .statusReportData; // Array of status report counts
                        const terminalReportData = response
                        .terminalReportData; // Array of terminal report counts
                        const researchData = response.researchData; // Array of research counts

                        // Aggregate data from projects, status reports, terminal reports, and research
                        for (let i = 0; i < combinedLabels.length; i++) {
                            combinedData.push((projectData[i] || 0) + (statusReportData[i] || 0) + (
                                terminalReportData[i] || 0) + (researchData[i] || 0));
                        }

                        updateCombinedChart(combinedData, combinedLabels, projectData, statusReportData,
                            terminalReportData, researchData);
                    }
                });
            }

            // Function to update the combined chart
            function updateCombinedChart(data, labels, projectData, statusReportData, terminalReportData,
                researchData) {
                if (combinedChart) {
                    combinedChart.data.labels = labels; // Set new labels
                    combinedChart.data.datasets[0].data = data; // Set new data
                    combinedChart.options.plugins.tooltip.callbacks.label = function(context) {
                        const index = context.dataIndex;
                        const totalCount = context.raw; // Total count from combined data
                        const projects = projectData[index] || 0; // Count of projects
                        const statusReports = statusReportData[index] || 0; // Count of status reports
                        const terminalReports = terminalReportData[index] || 0; // Count of terminal reports
                        const research = researchData[index] || 0; // Count of research

                        return `Total: ${totalCount} (Projects: ${projects}, Status Reports: ${statusReports}, Terminal Reports: ${terminalReports}, Research: ${research})`;
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
                        position: 'top', // Position legend to the top
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
                                '#E5243B', // No Poverty
                                '#DCA93A', // Zero Hunger
                                '#4C9E39', // Good Health and Well-Being
                                '#C4182D', // Quality Education
                                '#D94E9A', // Gender Equality
                                '#C4182D', // Clean Water and Sanitation
                                '#FCC30B', // Affordable and Clean Energy
                                '#A21942', // Decent Work and Economic Growth
                                '#FC6825', // Industry, Innovation, and Infrastructure
                                '#DD1367', // Reduced Inequality
                                '#FD9C25', // Sustainable Cities and Communities
                                '#BF8A2F', // Responsible Consumption and Production
                                '#3E7E45', // Climate Action
                                '#0A96D8', // Life Below Water
                                '#56C12A', // Life on Land
                                '#01689C', // Peace, Justice, and Strong Institutions
                                '#19486A' // Partnerships for the Goals
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
