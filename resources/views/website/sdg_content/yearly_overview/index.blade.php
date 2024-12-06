@extends('layouts.website2')
@section('styles')
    <style>
        .chart-container {
            position: relative;
            height: 500px;
            width: 100%;
        }



        .rounded-circle {
            border: 2px solid #007bff;
            /* Optional: border for user image */
        }

        .card-title {
            font-weight: bold;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <label for="yearFilter" class="mr-2 mb-0">Select Year:</label>
                <select id="yearFilter" class="form-control">
                    @foreach (range(date('Y'), 2000) as $year)
                        <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div id="yearlyData">
            <!-- This section will be dynamically updated -->
            @include('website.sdg_content.yearly_overview.partial', [
                'totalPublishedReports' => $totalPublishedReports,
                'totalPublishedProjects' => $totalPublishedProjects,
                'totalPublishedResearch' => $totalPublishedResearch,
                'popularReportSdg' => $popularReportSdg,
                'popularProjectSdg' => $popularProjectSdg,
                'popularResearchSdg' => $popularResearchSdg,
                'topContributors' => $topContributors,
                'sdgs' => $sdgs,
                'selectedYear' => date('Y'), // Pass the selected year
            ])
        </div>
    </div>
@endsection
@section('scripts')
    <!--line bar chart-->
    <script>
        $(document).ready(function() {
            let lineChart;

            // Function to fetch line chart data based on the selected year
            function fetchLineChartData(year) {
                $.ajax({
                    url: "{{ route('analytics.sdgLineChart') }}",
                    method: 'GET',
                    data: {
                        year: year
                    }, // Send the selected year as a query parameter
                    success: function(response) {
                        if (!response.months || response.months.length === 0) {
                            displayNoDataMessage();
                        } else {
                            updateLineChart(response.months, response.reportsData, response
                                .projectsData, response.researchData);
                        }
                    },
                    error: function() {
                        console.error('Failed to fetch chart data');
                        displayNoDataMessage();
                    }
                });
            }

            function updateLineChart(months, reportsData, projectsData, researchData) {
                if (lineChart) {
                    lineChart.destroy();
                }

                const ctx = document.getElementById('sdgLineChart').getContext('2d');
                lineChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [{
                                label: 'Reports',
                                data: reportsData,
                                borderColor: '#F44336',
                                fill: false,
                                tension: 0.4,
                            },
                            {
                                label: 'Projects',
                                data: projectsData,
                                borderColor: '#5E35B1',
                                fill: false,
                                tension: 0.4,
                            },
                            {
                                label: 'Research',
                                data: researchData,
                                borderColor: '#558B2F',
                                fill: false,
                                tension: 0.4,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.dataset.label}: ${context.raw}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Months'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Count'
                                }
                            }
                        }
                    }
                });
            }

            function displayNoDataMessage() {
                $('#lineChartContainer').html(`
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h4 class="text-center" style="font-weight: 600;">SDG Monthly Contributions Overview</h4>
                        </div>
                        <div class="card-body text-center">
                            <p class="text-muted">No data available for the chart.</p>
                        </div>
                    </div>
                `);
            }

            // Initial fetch for the current year
            fetchLineChartData(new Date().getFullYear());

            // Event listener for year selection
            $('#yearFilter').change(function() {
                const selectedYear = $(this).val();
                fetchLineChartData(selectedYear); // Fetch data for the selected year
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Trigger the change event on page load to fetch data for the default selected year
            let defaultYear = $('#yearFilter').val(); // Get the default selected year
            fetchYearlyData(defaultYear); // Fetch data for the selected year when page loads

            // Event listener for year selection
            $('#yearFilter').change(function() {
                let selectedYear = $(this).val();
                fetchYearlyData(selectedYear); // Fetch data for the selected year
            });

            // Function to fetch yearly data based on the selected year
            function fetchYearlyData(year) {
                $.ajax({
                    url: "{{ route('website.yearly_overview') }}",
                    method: "GET",
                    data: {
                        year: year
                    },
                    success: function(response) {
                        // Update the content dynamically
                        $('#yearlyData').html(response.html);

                        // Update the year in titles
                        $('[data-year="title"]').text(`${year} SDG Overview`);
                        $('[data-year="chart"]').text(`SDG Monthly Contributions Overview (${year})`);
                        $('[data-year="contributors"]').text(`Top Contributors (${year})`);
                        $('[data-year="sdg"]').text(`Sustainable Development Goals (${year})`);
                    },
                    error: function() {
                        console.error('Error fetching yearly data.');
                    }
                });
            }
        });
    </script>
@endsection
