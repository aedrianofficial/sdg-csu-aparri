@extends('layouts.admin')

@section('title', 'Project Gender Impact Analysis')

@section('content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Project Gender Impact Details</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.gender-impact.index') }}">Gender Impact Analysis</a></li>
                    <li class="breadcrumb-item active">Project Details</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        <!-- Project Information Card -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-project-diagram me-2"></i>Project Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4>{{ $item->title }}</h4>
                                <div class="mt-3">
                                    <strong>Contributor:</strong> {{ $item->user->name }} ({{ $item->user->email }})
                                </div>
                                <div class="mt-2">
                                    <strong>Created:</strong> {{ $item->created_at->format('F j, Y, g:i a') }}
                                </div>
                                <div class="mt-2">
                                    <strong>Last Updated:</strong> {{ $item->updated_at->format('F j, Y, g:i a') }}
                                </div>
                                <div class="mt-2">
                                    <strong>SDGs:</strong>
                                    <div class="d-flex flex-wrap mt-1">
                                        @foreach($item->sdg as $sdg)
                                            <div class="me-2 mb-2" data-bs-toggle="tooltip" title="{{ $sdg->name }}">
                                                <img src="{{ asset('assets/images/sdgs/E-WEB-Goal-' . str_pad($sdg->id, 2, '0', STR_PAD_LEFT) . '.png') }}" 
                                                    alt="SDG {{ $sdg->id }}" class="img-fluid" style="max-height: 40px;">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                @if($item->projectimg && $item->projectimg->image_path)
                                    <img src="{{ asset('storage/projectimages/' . $item->projectimg->image_path) }}" 
                                        alt="{{ $item->title }}" class="img-fluid rounded">
                                @else
                                    <div class="text-center p-4 bg-light rounded">
                                        <i class="fas fa-image fa-4x text-muted"></i>
                                        <p class="mt-2">No image available</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Description</h5>
                                <div class="p-3 bg-light rounded">
                                    {!! $item->description !!}
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Target Beneficiaries</h5>
                                <div class="p-3 bg-light rounded">
                                    {{ $item->target_beneficiaries ?? 'No specific target beneficiaries listed.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Gender Impact Analysis Card -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-venus-mars me-2"></i>Gender Impact Analysis</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3 h-100">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-3 text-muted">Beneficiary Assessment</h6>
                                        <ul class="list-group">
                                            <li class="list-group-item {{ $item->genderImpact->benefits_women ? 'list-group-item-success' : 'list-group-item-light' }}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>
                                                        <i class="fas {{ $item->genderImpact->benefits_women ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' }} me-2"></i>
                                                        Benefits Women/Girls
                                                    </span>
                                                    @if($item->genderImpact->women_count)
                                                        <span class="badge bg-info">{{ $item->genderImpact->women_count }} mentioned</span>
                                                    @endif
                                                </div>
                                            </li>
                                            <li class="list-group-item {{ $item->genderImpact->benefits_men ? 'list-group-item-success' : 'list-group-item-light' }}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>
                                                        <i class="fas {{ $item->genderImpact->benefits_men ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' }} me-2"></i>
                                                        Benefits Men/Boys
                                                    </span>
                                                    @if($item->genderImpact->men_count)
                                                        <span class="badge bg-info">{{ $item->genderImpact->men_count }} mentioned</span>
                                                    @endif
                                                </div>
                                            </li>
                                            <li class="list-group-item {{ $item->genderImpact->benefits_all ? 'list-group-item-success' : 'list-group-item-light' }}">
                                                <i class="fas {{ $item->genderImpact->benefits_all ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' }} me-2"></i>
                                                Benefits All Genders
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-3 h-100">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-3 text-muted">Gender Equality Focus</h6>
                                        <ul class="list-group">
                                            <li class="list-group-item {{ $item->genderImpact->addresses_gender_inequality ? 'list-group-item-success' : 'list-group-item-light' }}">
                                                <i class="fas {{ $item->genderImpact->addresses_gender_inequality ? 'fa-check-circle text-success' : 'fa-times-circle text-muted' }} me-2"></i>
                                                Addresses Gender Inequality
                                            </li>
                                        </ul>
                                        
                                        @if($item->genderImpact->addresses_gender_inequality)
                                            <div class="mt-3">
                                                <h6>Key Gender Equality Aspects:</h6>
                                                <ul>
                                                    <li>Mentions gender-specific needs or barriers</li>
                                                    <li>Focuses on gender-balanced participation</li>
                                                    <li>Addresses structural inequality</li>
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-3 text-muted">Gender Impact Notes</h6>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            {{ $item->genderImpact->gender_notes ?: 'No specific gender impact notes available.' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-3 text-muted">Recommendations for Enhanced Gender Impact</h6>
                                        <ul class="list-group">
                                            @if(!$item->genderImpact->addresses_gender_inequality)
                                                <li class="list-group-item">
                                                    <i class="fas fa-lightbulb text-warning me-2"></i>
                                                    <strong>Consider Gender Inequality:</strong> This project could be enhanced by explicitly addressing gender inequality issues.
                                                </li>
                                            @endif
                                            
                                            @if(!$item->genderImpact->benefits_women && !$item->genderImpact->benefits_men)
                                                <li class="list-group-item">
                                                    <i class="fas fa-lightbulb text-warning me-2"></i>
                                                    <strong>Specify Beneficiaries:</strong> Clarify which gender groups will benefit from this project.
                                                </li>
                                            @endif
                                            
                                            @if($item->genderImpact->benefits_women && !$item->genderImpact->benefits_men)
                                                <li class="list-group-item">
                                                    <i class="fas fa-lightbulb text-warning me-2"></i>
                                                    <strong>Consider Male Inclusion:</strong> While focusing on women is valuable, consider if men could also benefit from certain aspects.
                                                </li>
                                            @endif
                                            
                                            @if($item->genderImpact->benefits_men && !$item->genderImpact->benefits_women)
                                                <li class="list-group-item">
                                                    <i class="fas fa-lightbulb text-warning me-2"></i>
                                                    <strong>Consider Female Inclusion:</strong> While focusing on men is valuable, consider if women could also benefit from certain aspects.
                                                </li>
                                            @endif
                                            
                                            <li class="list-group-item">
                                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                                <strong>Gender-Disaggregated Data:</strong> Consider collecting and analyzing gender-disaggregated data to better understand impact.
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="row mt-4 mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.gender-impact.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                    <a href="{{ route('admin.projects.show', $item->id) }}" class="btn btn-primary">
                        <i class="fas fa-eye me-2"></i>View Complete Project
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltips.map(function(tooltip) {
            return new bootstrap.Tooltip(tooltip);
        });
    });
</script>
@endsection 