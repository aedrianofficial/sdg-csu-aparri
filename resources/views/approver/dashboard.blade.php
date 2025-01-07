@extends('layouts.approver')

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



        /* Search Address Input Styles */
        #searchAddress {
            border: 1px solid #007bff;
            /* Border color */
            border-radius: 0.25rem;
            /* Rounded corners */
            padding: 10px;
            /* Padding */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            /* Shadow */
            transition: border-color 0.3s;
            /* Smooth transition */
        }

        #searchAddress:focus {
            border-color: #0056b3;
            /* Darker border on focus */
            outline: none;
            /* Remove outline */
        }

        /* Pop-up styles for the marker */


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
                    <h3 class="mb-0">Dashboard</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('approver.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Dashboard
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


                    <!-- My Approval Activity Status -->
                    <div class="content">
                        <div class="container">
                            <div class="row text-center">
                                <div class="col-md-12 mb-4">
                                    <div class="card card-primary card-outline">
                                        <div class="card-header">
                                            <h4 class="text-center mb-4" style="font-weight: 600;">Approval Activity Status
                                                Overview</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3 mb-4">
                                                    <h5 class="mb-3" style="font-weight: 500;">
                                                        Projects Approval (<span id="projectsApprovalTotalCount"></span>)
                                                    </h5>
                                                    <div class="chart-container"
                                                        style="position: relative; height: 350px; width: 100%;">
                                                        <canvas id="myProjectsApprovalChart"></canvas>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-4">
                                                    <h5 class="mb-3" style="font-weight: 500;">
                                                        Status Reports Approval (<span
                                                            id="statusReportsApprovalTotalCount"></span>)
                                                    </h5>
                                                    <div class="chart-container"
                                                        style="position: relative; height: 350px; width: 100%;">
                                                        <canvas id="myStatusReportsApprovalChart"></canvas>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-4">
                                                    <h5 class="mb-3" style="font-weight: 500;">
                                                        Terminal Reports Approval (<span
                                                            id="terminalReportsApprovalTotalCount"></span>)
                                                    </h5>
                                                    <div class="chart-container"
                                                        style="position: relative; height: 350px; width: 100%;">
                                                        <canvas id="myTerminalReportsApprovalChart"></canvas>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-4">
                                                    <h5 class="mb-3" style="font-weight: 500;">
                                                        Research Approval (<span id="researchApprovalTotalCount"></span>)
                                                    </h5>
                                                    <div class="chart-container"
                                                        style="position: relative; height: 350px; width: 100%;">
                                                        <canvas id="myResearchApprovalChart"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SDG Contributions Overview -->
                    <div class="content">
                        <div class="container">
                            <div class="row text-center">
                                <div class="col-md-12 mb-4">
                                    <div class="card card-primary card-outline">
                                        <div class="card-header">
                                            <h4 class="text-center mb-4" style="font-weight: 600;">Overview of SDG
                                                Contributions: Projects, Reports, and Research</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="chart-container"
                                                style="position: relative; height: 500px; width: 100%;">
                                                <canvas id="sdgCombinedChart"></canvas>
                                            </div>
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

                </div>
            </div>
        </div> <!--end::Container-->
    </div>
    <!--end::App Content-->
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>




    <!-- Chart for approval statuses of projects, reports, and research -->
    <script>
        $(document).ready(function() {
            // Fetch and update approval activity data via AJAX
            function fetchApprovalActivityData() {
                $.ajax({
                    url: "{{ route('analytics.myApprovalActivity') }}", // Ensure the route is correct
                    method: 'GET',
                    success: function(response) {
                        $('#projectsApprovalTotalCount').text(response.projects.approved + response
                            .projects.rejected);
                        $('#statusReportsApprovalTotalCount').text(response.status_reports.approved +
                            response.status_reports.rejected);
                        $('#terminalReportsApprovalTotalCount').text(response.terminal_reports
                            .approved + response.terminal_reports.rejected);
                        $('#researchApprovalTotalCount').text(response.research.approved + response
                            .research.rejected);

                        // Update charts with the fetched approval activity data
                        updateApprovalChart(myProjectsApprovalChart, response.projects);
                        updateApprovalChart(myStatusReportsApprovalChart, response.status_reports);
                        updateApprovalChart(myTerminalReportsApprovalChart, response.terminal_reports);
                        updateApprovalChart(myResearchApprovalChart, response.research);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching approval activity data:", error);
                    }
                });
            }

            // Function to update a chart with the provided approval counts
            function updateApprovalChart(chart, data) {
                const labels = ['Approved', 'Rejected'];
                const chartData = [data.approved, data.rejected]; // Extract approved and rejected counts

                chart.data.labels = labels; // Set labels for the chart
                chart.data.datasets[0].data = chartData; // Set data (approved and rejected counts)
                chart.update(); // Update the chart
            }

            // Chart options for a professional look
            const chartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            maxWidth: 100,
                        },
                    },
                    title: {
                        display: false,
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.raw}`;
                            }
                        }
                    }
                }
            };

            // Initialize pie charts for projects, status reports, terminal reports, and research
            var myProjectsApprovalChart = new Chart(document.getElementById('myProjectsApprovalChart').getContext(
                '2d'), {
                type: 'pie',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Project Approval Status',
                        data: [],
                        backgroundColor: ['#4BC0C0', '#FF6384'],
                        hoverOffset: 10
                    }]
                },
                options: chartOptions
            });

            var myStatusReportsApprovalChart = new Chart(document.getElementById('myStatusReportsApprovalChart')
                .getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Status Reports Approval Status',
                            data: [],
                            backgroundColor: ['#FF9F40', '#FF6384'],
                            hoverOffset: 10
                        }]
                    },
                    options: chartOptions
                });

            var myTerminalReportsApprovalChart = new Chart(document.getElementById('myTerminalReportsApprovalChart')
                .getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Terminal Reports Approval Status',
                            data: [],
                            backgroundColor: ['#9966FF', '#FF6384'],
                            hoverOffset: 10
                        }]
                    },
                    options: chartOptions
                });

            var myResearchApprovalChart = new Chart(document.getElementById('myResearchApprovalChart').getContext(
                '2d'), {
                type: 'pie',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Research Approval Status',
                        data: [],
                        backgroundColor: ['#4BC0C0', '#FF6384'],
                        hoverOffset: 10
                    }]
                },
                options: chartOptions
            });

            // Fetch and update data on page load
            fetchApprovalActivityData();

            // Automatically refresh the data every 30 seconds
            setInterval(fetchApprovalActivityData, 30000);
        });
    </script>


    <!-- Chart for overall status of SDG projects, reports, research-->
    <!-- Chart for overall status of SDG projects, reports, research-->
    <script>
        $(document).ready(function() {
            let combinedChart; // For the combined chart

            // Fetch and update chart data via AJAX
            function fetchReviewStatusData() {
                $.ajax({
                    url: "{{ route('analytics.sdgComparison') }}", // Update this route to fetch SDG comparison data
                    method: 'GET',
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

    <!-- locator pin in the map for all projects-->
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

            var mapProjects = @json($projects);

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
@endsection
