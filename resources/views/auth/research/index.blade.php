@extends('layouts.admin')
@section('title', 'All Research Lists')
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">All Research</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            All Research
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
                     

                            <!-- Search and Filter Form -->
                            <form action="{{ route('research.index') }}" method="GET" class="mb-4">
                                <div class="row">
                                    <!-- Title Filter -->
                                    <div class="col-md-3 mb-3">
                                        <h6 for="title">Search: </h6>
                                        <input id="search" type="text" name="title" class="form-control"
                                            value="{{ request('title') }}" placeholder="Enter Title">
                                    </div>
                                </div>

                                <h6>Filter by:</h6>
                                <div class="row"> <!-- Start a new row for the next filter -->
                                    <!-- Research Status Filter -->
                                    <div class="col-md-3 mb-3">
                                        <label for="research_status" class="form-label">Research Status:</label>
                                        <select name="research_status" class="form-select select2">
                                            <option value="" disabled selected>Select Research Status</option>
                                            <option value="Proposed"
                                                {{ request('research_status') == 'Proposed' ? 'selected' : '' }}>
                                                Proposed
                                            </option>
                                            <option value="On-Going"
                                                {{ request('research_status') == 'On-Going' ? 'selected' : '' }}>
                                                On-Going
                                            </option>
                                            <option value="On-Hold"
                                                {{ request('research_status') == 'On-Hold' ? 'selected' : '' }}>
                                                On-Hold
                                            </option>
                                            <option value="Completed"
                                                {{ request('research_status') == 'Completed' ? 'selected' : '' }}>
                                                Completed
                                            </option>
                                            <option value="Rejected"
                                                {{ request('research_status') == 'Rejected' ? 'selected' : '' }}>
                                                Rejected
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Research Category Filter -->
                                    <div class="col-md-3 mb-3">
                                        <label for="researchcategory_id" class="form-label">Research Category:</label>
                                        <select name="researchcategory_id" class="form-select select2">
                                            <option value="">Select Category</option>
                                            @foreach ($researchCategories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ request('researchcategory_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Review Status Filter -->
                                    <div class="col-md-3 mb-3">
                                        <label for="review_status" class="form-label">Review Status:</label>
                                        <select id="review_status" name="review_status" class="form-select select2">
                                            <option value="">All</option>
                                            @foreach ($reviewStatuses as $status)
                                                <option value="{{ $status->id }}"
                                                    {{ request('review_status') == $status->id ? 'selected' : '' }}>
                                                    {{ $status->status }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- SDG Filter -->
                                    <div class="col-md-3 mb-3">
                                        <label for="sdg" class="form-label">Select SDG:</label>
                                        <select id="sdg" name="sdg[]" class="form-select select2-multiple" multiple>
                                            @foreach ($sdgs as $sdg)
                                                <option value="{{ $sdg->id }}"
                                                    {{ in_array($sdg->id, request('sdg', [])) ? 'selected' : '' }}>
                                                    {{ $sdg->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Filter and Reset Buttons -->
                                <div class="row mb-3">
                                    <div class="col-md-12 text-left">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('research.index') }}" class="btn btn-secondary">Reset</a>
                                    </div>
                                </div>
                            </form>

                            <!-- Responsive Research Table -->
                            <div class="table-responsive">
                                @if (count($researches) > 0)
                                    <h4 class="card-title">All Research</h4>
                                    <table id="research-table" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th> Title </th>
                                                <th> Research Categories </th>
                                                <th> SDGs </th>
                                                <th> Review Status </th>
                                                <th> Research Status </th>
                                                <th>Created At</th>
                                                <th> Action </th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($researches as $research)
                                                <tr>
                                                    <td> {{ $research->title }} </td>
                                                    <td> {{ $research->researchcategory->name ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        <ul>
                                                            @foreach ($research->sdg as $sdg)
                                                                <li>{{ $sdg->name }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </td>
                                                    <td> {{ $research->reviewStatus->status ?? 'N/A' }} </td>
                                                    <td> {{ $research->research_status }} </td>
                                                    <td>{{ $research->created_at->format('F j, Y, g:i A') }}</td>
                                                    <td>
                                                        @if ($research->reviewStatus && $research->reviewStatus->status === 'Need Changes')
                                                            <a href="{{ route('research.need_changes', $research->id) }}"
                                                                class="btn btn-sm btn-success">View</a>
                                                        @elseif ($research->reviewStatus && $research->reviewStatus->status === 'Rejected')
                                                            <a href="{{ route('research.rejected', $research->id) }}"
                                                                class="btn btn-sm btn-success">View</a>
                                                        @else
                                                            <a href="{{ route('research.show', $research->id) }}"
                                                                class="btn btn-sm btn-success">View</a>
                                                        @endif
                                                      
                                                    </td>
                                                    <td>  <a href="{{ route('research.edit', $research->id) }}"
                                                        class="btn btn-sm btn-info">Edit</a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                            </div>

                            <div class="container">
                                <!-- Custom Pagination Links -->
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination justify-content-center">
                                        <!-- Previous Button -->
                                        <li class="page-item {{ $researches->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $researches->previousPageUrl() }}"
                                                tabindex="-1">Previous</a>
                                        </li>

                                        <!-- Page Number Links -->
                                        @php
                                            $currentPage = $researches->currentPage(); // Current page number
                                            $lastPage = $researches->lastPage(); // Last page number
                                            $start = max($currentPage - 1, 1); // Calculate start of the visible page items
                                            $end = min($start + 2, $lastPage); // Calculate end of the visible page items
                                        @endphp

                                        @for ($i = $start; $i <= $end; $i++)
                                            <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                                                <a class="page-link"
                                                    href="{{ $researches->url($i) }}">{{ $i }}</a>
                                            </li>
                                        @endfor

                                        <!-- Next Button -->
                                        <li class="page-item {{ $researches->hasMorePages() ? '' : 'disabled' }}">
                                            <a class="page-link" href="{{ $researches->nextPageUrl() }}">Next</a>
                                        </li>
                                    </ul>
                                </nav>
                            @else
                                <h3 class="text-danger text-center">No research found</h3>
                                @endif
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
    <script src="{{ asset('assets/auth/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {


            $('.select2-multiple').select2({
                width: '100%',
                placeholder: 'Select SDGs',
                allowClear: true
            });
        });
    </script>
@endsection
