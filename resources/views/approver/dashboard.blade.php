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
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
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
                    <!-- Project, Report, and Research Status Overview -->
                    <div class="content">
                        <div class="container">
                            <div class="row text-center">
                                <div class="col-md-12 mb-4">
                                    <div class="card card-primary card-outline">
                                        <div class="card-header">
                                            <h4 class="text-center mb-4" style="font-weight: 600;">Project, Report, and
                                                Research Status Overview</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4 mb-4">
                                                    <h5 class="mb-3" style="font-weight: 500;"><span
                                                            id="totalProjectsHeader"></span></h5>
                                                    <div class="chart-container"
                                                        style="position: relative; height: 350px; width: 100%;">
                                                        <canvas id="projectChart"></canvas>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-4">
                                                    <h5 class="mb-3" style="font-weight: 500;"><span
                                                            id="totalReportsHeader"></span></h5>
                                                    <div class="chart-container"
                                                        style="position: relative; height: 350px; width: 100%;">
                                                        <canvas id="reportChart"></canvas>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-4">
                                                    <h5 class="mb-3" style="font-weight: 500;"><span
                                                            id="totalResearchHeader"></span></h5>
                                                    <div class="chart-container"
                                                        style="position: relative; height: 350px; width: 100%;">
                                                        <canvas id="researchChart"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- My Projects, My Reports, and My Research Status -->
                    <div class="content">
                        <div class="container">
                            <div class="row text-center">
                                <div class="col-md-12 mb-4">
                                    <div class="card card-primary card-outline">
                                        <div class="card-header">
                                            <h4 class="text-center mb-4" style="font-weight: 600;">My Projects, My Reports,
                                                and My Research Status</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4 mb-4">
                                                    <h5 class="mb-3" style="font-weight: 500;"><span
                                                        id="myTotalProjectsHeader"></span></h5>
                                                    <div class="chart-container"
                                                        style="position: relative; height: 350px; width: 100%;">
                                                        <canvas id="myProjectChart"></canvas>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-4">
                                                    <h5 class="mb-3" style="font-weight: 500;"><span
                                                        id="myTotalReportsHeader"></span></h5>
                                                    <div class="chart-container"
                                                        style="position: relative; height: 350px; width: 100%;">
                                                        <canvas id="myReportChart"></canvas>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-4">
                                                    <h5 class="mb-3" style="font-weight: 500;"><span
                                                        id="myTotalResearchHeader"></span></h5>
                                                    <div class="chart-container"
                                                        style="position: relative; height: 350px; width: 100%;">
                                                        <canvas id="myResearchChart"></canvas>
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


    <!--chart for review statuses of projects, reports, research-->
    <script>
        $(document).ready(function() {


            // Fetch and update chart data via AJAX
            function fetchReviewStatusData() {
                $.ajax({
                    url: "{{ route('analytics.reviewStatusAnalytics') }}", // Add correct route
                    method: 'GET',
                    success: function(response) { // Update headers with total counts
                        $('#totalProjectsHeader').text(`Projects Status (${response.totalProjects})`);
                        $('#totalReportsHeader').text(`Reports Status (${response.totalReports})`);
                        $('#totalResearchHeader').text(`Research Status (${response.totalResearch})`);

                        // Update the charts
                        updateChart(projectChart, response.projectStatusCounts, response.statuses,
                            'Projects');
                        updateChart(reportChart, response.reportStatusCounts, response.statuses,
                            'Reports');
                        updateChart(researchChart, response.researchStatusCounts, response.statuses,
                            'Research');
                    }
                });
            }

            // Function to update a chart
            function updateChart(chart, data, statuses, label) {
                const chartLabels = Object.values(statuses); // Labels from statuses
                const chartData = chartLabels.map((_, index) => data[index + 1] || 0);

                chart.data.labels = chartLabels; // Set new labels
                chart.data.datasets[0].data = chartData; // Set new data
                chart.update(); // Update the chart
            }

            // Chart options for a professional look
            const chartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom', // Move legend below the chart
                        labels: {
                            maxWidth: 100, // Set maximum width for legend labels
                            boxWidth: 10, // Width of the color box
                        },
                    },
                    title: {
                        display: false, // Set to false to not display default title
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

            // Initialize pie charts with professional styling
            var projectChart = new Chart(document.getElementById('projectChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Projects',
                        data: [],
                        backgroundColor: ['#4BC0C0', '#FF6384', '#36A2EB', '#FFCE56', '#9966FF'],
                        hoverOffset: 10
                    }]
                },
                options: {
                    ...chartOptions,
                    plugins: {
                        ...chartOptions.plugins,
                        legend: {
                            ...chartOptions.plugins.legend,
                            align: 'start',
                        }
                    }
                }
            });

            var reportChart = new Chart(document.getElementById('reportChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Reports',
                        data: [],
                        backgroundColor: ['#9966FF', '#FF9F40', '#FF6384', '#36A2EB', '#4BC0C0'],
                        hoverOffset: 10
                    }]
                },
                options: {
                    ...chartOptions,
                    plugins: {
                        ...chartOptions.plugins,
                        legend: {
                            ...chartOptions.plugins.legend,
                            align: 'start',
                        }
                    }
                }
            });

            var researchChart = new Chart(document.getElementById('researchChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Research',
                        data: [],
                        backgroundColor: ['#FF9F40', '#4BC0C0', '#9966FF', '#FF6384', '#36A2EB'],
                        hoverOffset: 10
                    }]
                },
                options: {
                    ...chartOptions,
                    plugins: {
                        ...chartOptions.plugins,
                        legend: {
                            ...chartOptions.plugins.legend,
                            align: 'start', // Align legend to the left
                        }
                    }
                }
            });

            // Fetch and update data on page load
            fetchReviewStatusData();

            // Automatically refresh the data every 30 seconds
            setInterval(fetchReviewStatusData, 30000); // 30,000 ms = 30 seconds


        });
    </script>
    <!-- Chart for review statuses of my projects, reports, and research -->
    <script>
        $(document).ready(function() {
            // Fetch and update chart data via AJAX
            function fetchMyStatusData() {
                $.ajax({
                    url: "{{ route('analytics.myStatusAnalytics') }}", // Ensure the route is correct
                    method: 'GET',
                    success: function(response) {

                        $('#myTotalProjectsHeader').text(
                            `My Projects Status (${response.myTotalProjects})`);
                        $('#myTotalReportsHeader').text(`My Reports Status (${response.myTotalReports})`);
                        $('#myTotalResearchHeader').text(
                            `My Research Status (${response.myTotalResearch})`);

                        updateChart(myProjectChart, response.myProjectStatusCounts, response
                            .myStatuses);
                        updateChart(myReportChart, response.myReportStatusCounts, response.myStatuses);
                        updateChart(myResearchChart, response.myResearchStatusCounts, response
                            .myStatuses);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching status data:", error);
                    }
                });
            }

            // Function to update a chart
            function updateChart(chart, data, myStatuses) {
                const chartLabels = Object.values(myStatuses); // Labels from myStatuses
                const chartData = chartLabels.map((_, index) => data[chartLabels[index]] ||
                    0); // Use labels for mapping

                chart.data.labels = chartLabels; // Set new labels
                chart.data.datasets[0].data = chartData; // Set new data
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

                            maxWidth: 100,
                            boxWidth: 10,
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

            // Initialize pie charts with professional styling
            var myProjectChart = new Chart(document.getElementById('myProjectChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'My Projects',
                        data: [],
                        backgroundColor: ['#4BC0C0', '#FF6384', '#36A2EB', '#FFCE56', '#9966FF'],
                        hoverOffset: 10
                    }]
                },
                options: {
                    ...chartOptions,
                    plugins: {
                        ...chartOptions.plugins,
                        legend: {
                            ...chartOptions.plugins.legend,
                            align: 'start', // Align legend to the left
                        }
                    }
                }
            });

            var myReportChart = new Chart(document.getElementById('myReportChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'My Reports',
                        data: [],
                        backgroundColor: ['#9966FF', '#FF9F40', '#FF6384', '#36A2EB', '#4BC0C0'],
                        hoverOffset: 10
                    }]
                },
                options: {
                    ...chartOptions,
                    plugins: {
                        ...chartOptions.plugins,
                        legend: {
                            ...chartOptions.plugins.legend,
                            align: 'start', // Align legend to the left
                        }
                    }
                }
            });

            var myResearchChart = new Chart(document.getElementById('myResearchChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'My Research',
                        data: [],
                        backgroundColor: ['#FF9F40', '#4BC0C0', '#9966FF', '#FF6384', '#36A2EB'],
                        hoverOffset: 10
                    }]
                },
                options: {
                    ...chartOptions,
                    plugins: {
                        ...chartOptions.plugins,
                        legend: {
                            ...chartOptions.plugins.legend,
                            align: 'start', // Align legend to the left
                        }
                    }
                }
            });

            // Fetch and update data on page load
            fetchMyStatusData();

            // Automatically refresh the data every 30 seconds
            setInterval(fetchMyStatusData, 30000);
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
@endsection
