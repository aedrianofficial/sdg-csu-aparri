@extends('layouts.publisher')
@section('title', 'Published Research Lists')
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Published Research</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Published Research
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
                            <form action="{{ route('publisher.research.published_list') }}" method="GET" class="mb-4">
                                <div class="row">
                                    <!-- Title Filter -->
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Search:</label>
                                            <input id="search" type="text" name="title" class="form-control"
                                                value="{{ request('title') }}" placeholder="Enter Title">
                                        </div>
                                    </div>
                                </div>

                                <h6>Filter by:</h6>
                                <div class="row">
                                    <!-- Research Status Filter -->
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="research_status" class="form-label">Research Status:</label>
                                            <select name="research_status" class="form-select select2">
                                                <option value="" disabled selected>Select Research Status</option>
                                                <option value="Proposed"
                                                    {{ request('research_status') == 'Proposed' ? 'selected' : '' }}>
                                                    Proposed</option>
                                                <option value="On-Going"
                                                    {{ request('research_status') == 'On-Going' ? 'selected' : '' }}>
                                                    On-Going</option>
                                                <option value="On-Hold"
                                                    {{ request('research_status') == 'On-Hold' ? 'selected' : '' }}>On-Hold
                                                </option>
                                                <option value="Completed"
                                                    {{ request('research_status') == 'Completed' ? 'selected' : '' }}>
                                                    Completed</option>
                                                <option value="Rejected"
                                                    {{ request('research_status') == 'Rejected' ? 'selected' : '' }}>
                                                    Rejected</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Research Category Filter -->
                                    <div class="col-md-3">
                                        <div class="mb-3">
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
                                    </div>



                                    <!-- SDG Filter -->
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="sdg" class="form-label">Select SDG:</label>
                                            <select id="select2-multiple" name="sdg[]"
                                                class="form-select select2-multiple" multiple>
                                                @foreach ($sdgs as $sdg)
                                                    <option value="{{ $sdg->id }}"
                                                        {{ in_array($sdg->id, request('sdg', [])) ? 'selected' : '' }}>
                                                        {{ $sdg->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Filter and Reset Buttons -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('publisher.research.published_list') }}"
                                            class="btn btn-secondary">Reset</a>
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
                                                <th>Title</th>
                                                <th>Research Categories</th>
                                                <th>SDGs</th>
                                                <th>Review Status</th>
                                                <th>Research Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($researches as $research)
                                                <tr>
                                                    <td>{{ Str::limit($research->title, 15, '...') }}</td>
                                                    <td>{{ Str::limit($research->researchcategory->name ?? 'N/A', 15, '...') }}
                                                    </td>
                                                    <td>
                                                        <ul>
                                                            @foreach ($research->sdg as $sdg)
                                                                <li>{{ Str::limit($sdg->name, 15, '...') }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </td>
                                                    <td>{{ $research->reviewStatus->status ?? 'N/A' }}</td>
                                                    <td>{{ $research->research_status }}</td>
                                                    <td>
                                                        <a href="{{ route('publisher.research.show', $research->id) }}"
                                                            class="btn btn-sm btn-success">View</a>
                                                    </td>
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

    <script>
        $(document).ready(function() {

            $('#select2-multiple').select2({
                width: '100%',
                placeholder: 'Select SDGs',
                allowClear: true
            });
        });
    </script>
@endsection
