@extends('layouts.reviewer')
@section('title', 'View Research')
@section('content')
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Rejected Research</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('reviewer.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Rejected Research
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
                            @if ($research->feedbacks->count() > 0)
                                <h4>Feedback</h4>
                                <div class="mb-3">
                                    @foreach ($research->feedbacks as $feedback)
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
                                    <p>No feedback available for this research.</p>
                                    <hr>
                                </div>
                            @endif
                            <!-- End Feedback Section -->

                            <h4>Rejected Research</h4>
                            <div class="mb-3">
                                <label for="title" class="form-label">Title:</label>
                                <input type="text" name="title" id="title" class="form-control"
                                    value="{{ $research->title }}" readonly>
                            </div>
                            <!-- Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label">Description:</label>
                                <div class="form-control" style="min-height: 100px; overflow-y: auto;"
                                    contenteditable="false">
                                    {!! $research->description !!}
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="research_category" class="form-label">Research Category:</label>
                                <input type="text" name="research_category" id="research_category" class="form-control"
                                    value="{{ $research->researchcategory->name ?? 'N/A' }}" readonly>
                            </div>
                            <!-- SDGs -->
                            <div class="mb-3">
                                <label for="sdg" class="form-label">SDGs:</label>
                                <textarea name="sdg" id="sdg" cols="30" rows="3" class="form-control" readonly>
@foreach ($research->sdg as $sdg)
{{ $sdg->name }}
@endforeach
</textarea>
                            </div>
                            <!-- SDG Sub Categories -->
                            <div class="mb-3">
                                <label for="sdg_sub_categories" class="form-label">SDG Targets:</label>
                                <textarea name="sdg_sub_categories" id="sdg_sub_categories" cols="30" rows="5" class="form-control" readonly>
    @if ($research->sdgSubCategories->isEmpty())
No SDG Targets available.
@else
@foreach ($research->sdgSubCategories as $subCategory)
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
                                @if($research->genderImpact)
                                    <div class="card border-primary mb-3">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <ul class="list-group mb-3">
                                                        <li class="list-group-item {{ $research->genderImpact->benefits_women ? 'list-group-item-success' : 'list-group-item-light' }}">
                                                            <i class="fas {{ $research->genderImpact->benefits_women ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' }} me-2"></i>
                                                            Benefits Women/Girls
                                                            @if($research->genderImpact->women_count)
                                                                <span class="badge bg-info ms-2">{{ $research->genderImpact->women_count }} mentioned</span>
                                                            @endif
                                                        </li>
                                                        <li class="list-group-item {{ $research->genderImpact->benefits_men ? 'list-group-item-success' : 'list-group-item-light' }}">
                                                            <i class="fas {{ $research->genderImpact->benefits_men ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' }} me-2"></i>
                                                            Benefits Men/Boys
                                                            @if($research->genderImpact->men_count)
                                                                <span class="badge bg-info ms-2">{{ $research->genderImpact->men_count }} mentioned</span>
                                                            @endif
                                                        </li>
                                                        <li class="list-group-item {{ $research->genderImpact->benefits_all ? 'list-group-item-success' : 'list-group-item-light' }}">
                                                            <i class="fas {{ $research->genderImpact->benefits_all ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' }} me-2"></i>
                                                            Benefits All Genders
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card h-100">
                                                        <div class="card-body">
                                                            <h6 class="card-subtitle mb-2 text-muted">Gender Equality Focus</h6>
                                                            <p class="card-text">
                                                                <span class="badge {{ $research->genderImpact->addresses_gender_inequality ? 'bg-success' : 'bg-secondary' }} p-2">
                                                                    <i class="fas {{ $research->genderImpact->addresses_gender_inequality ? 'fa-check' : 'fa-times' }} me-1"></i>
                                                                    {{ $research->genderImpact->addresses_gender_inequality ? 'Addresses Gender Inequality' : 'No Explicit Focus on Gender Inequality' }}
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @if($research->genderImpact->gender_notes)
                                                <div class="alert alert-info mt-3">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    {{ $research->genderImpact->gender_notes }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-secondary">
                                        <i class="fas fa-info-circle me-2"></i>No gender impact analysis available for this research.
                                    </div>
                                @endif
                            </div>


                            <div class="mb-3">
                                <label for="research_status" class="form-label">Research Status:</label>
                                <input type="text" name="research_status" id="research_status" class="form-control"
                                    value="{{ $research->status->status ?? 'N/A' }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="review_status" class="form-label">Review Status:</label>
                                <input type="text" name="review_status" id="review_status" class="form-control"
                                    value="{{ $research->reviewStatus->status ?? 'N/A' }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="is_publish" class="form-label">Is Published:</label>
                                <input type="text" name="is_publish" id="is_publish" class="form-control"
                                    value="{{ $research->is_publish == 1 ? 'Published' : 'Draft' }}" readonly>
                            </div>
                            <!-- File -->
                            <div class="mb-3">
                                <label for="file" class="form-label">File:</label>
                                @if ($research->researchfiles->isEmpty())
                                    <input type="text" name="file" id="file" class="form-control"
                                        value="No files available for this research." readonly>
                                @else
                                    @foreach ($research->researchfiles as $file)
                                        <div class="input-group">
                                            <!-- Display clickable filename as a link -->
                                            <a href="{{ route('research.file.download', $file->id) }}"
                                                class="form-control" target="_blank" rel="noopener noreferrer">
                                                <span>Download</span>
                                                {{ $file->original_filename ?? $research->title }}
                                            </a>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            @if ($research->file_link)
                                <div class="mb-3">
                                    <label for="file_link" class="form-label">Full Version File:</label>
                                    <a href="{{ $research->file_link }}" target="_blank">{{ $research->file_link }}</a>
                                </div>
                            @else
                                <p>Full Version File: No file link available.</p>
                            @endif
                            <div class="mb-3">
                                <label for="created_by" class="form-label">Created by:</label>
                                <input type="text" name="created_by" id="created_by" class="form-control"
                                    value="{{ $research->user->first_name }} {{ $research->user->last_name }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="created_at" class="form-label">Created at:</label>
                                <input type="text" name="created_at" id="created_at" class="form-control"
                                    value="{{ $research->created_at->format('M d, Y H:i') }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="updated_at" class="form-label">Updated at:</label>
                                <input type="text" name="updated_at" id="updated_at" class="form-control"
                                    value="{{ $research->updated_at->format('M d, Y H:i') }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!--end::Container-->
    </div>
    <!--end::App Content-->
@endsection
