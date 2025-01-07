@extends('layouts.website2')

@section('styles')
    <style>
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
            <!-- SDG Content Header -->
            <div class="content-header">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Content for SDG: {{ $sdg->name }}</h1>
                        <label for="yearFilter" class="mr-2 mt-2">Select Year:</label>
                        <select id="yearFilter" class="form-control" data-sdg-id="{{ $sdg->id }}" style="width: auto">
                            @foreach (range(date('Y'), 2015) as $year)
                                <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6">

                    </div>
                </div>
            </div>

            <div class="row" id="yearlyData">
                @include('website.sdg_content.project_research_report.partial', [
                    'projects' => $projects,
                    'research' => $research,
                    'sdg' => $sdg,
                    'selectedYear' => $selectedYear,
                ])
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function fetchYearlyData(year, sdgId) {
            $.ajax({
                url: "{{ route('website.display_sdg_content', ':sdg') }}".replace(':sdg', sdgId),
                method: "GET",
                data: {
                    year: year
                },
                success: function(response) {
                    // Update the content dynamically
                    $('#yearlyData').html(response.html);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching yearly data:', xhr.responseText);
                }
            });
        }

        $(document).ready(function() {
            $('#yearFilter').change(function() {
                var selectedYear = $(this).val();
                var sdgId = $(this).data('sdg-id');
                fetchYearlyData(selectedYear, sdgId);
            });

        });
    </script>
@endsection
