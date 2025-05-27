@if(isset($genderImpact) && $genderImpact)
<div class="card card-info card-outline mt-4">
    <div class="card-header">
        <h5 class="card-title m-0">
            <i class="fas fa-venus-mars"></i> Gender Impact Analysis
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Target Beneficiaries -->
            @if($genderImpact->target_beneficiaries)
            <div class="col-12 mb-3">
                <div class="info-box bg-light">
                    <span class="info-box-icon bg-info">
                        <i class="fas fa-users"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Target Beneficiaries</span>
                        <span class="info-box-number">{{ $genderImpact->target_beneficiaries }}</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gender Benefits -->
            <div class="col-md-6 mb-3">
                <h6><i class="fas fa-check-circle"></i> Gender Benefits</h6>
                <ul class="list-unstyled">
                    @if($genderImpact->benefits_men)
                        <li><i class="fas fa-male text-primary"></i> <strong>Benefits Men:</strong> <span class="badge badge-success">Yes</span></li>
                    @else
                        <li><i class="fas fa-male text-muted"></i> <strong>Benefits Men:</strong> <span class="badge badge-secondary">No</span></li>
                    @endif
                    
                    @if($genderImpact->benefits_women)
                        <li><i class="fas fa-female text-danger"></i> <strong>Benefits Women:</strong> <span class="badge badge-success">Yes</span></li>
                    @else
                        <li><i class="fas fa-female text-muted"></i> <strong>Benefits Women:</strong> <span class="badge badge-secondary">No</span></li>
                    @endif
                    
                    @if($genderImpact->benefits_all)
                        <li><i class="fas fa-users text-info"></i> <strong>Benefits All Genders:</strong> <span class="badge badge-success">Yes</span></li>
                    @else
                        <li><i class="fas fa-users text-muted"></i> <strong>Benefits All Genders:</strong> <span class="badge badge-secondary">No</span></li>
                    @endif
                </ul>
            </div>

            <!-- Gender Inequality -->
            <div class="col-md-6 mb-3">
                <h6><i class="fas fa-balance-scale"></i> Gender Equality Impact</h6>
                @if($genderImpact->addresses_gender_inequality)
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> This initiative addresses gender inequality
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> This initiative does not specifically address gender inequality
                    </div>
                @endif
            </div>

            <!-- Beneficiary Counts -->
            @if($genderImpact->men_count || $genderImpact->women_count)
            <div class="col-12 mb-3">
                <h6><i class="fas fa-chart-bar"></i> Beneficiary Statistics</h6>
                <div class="row">
                    @if($genderImpact->men_count)
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-primary">
                                <i class="fas fa-male"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Men Beneficiaries</span>
                                <span class="info-box-number">{{ number_format($genderImpact->men_count) }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($genderImpact->women_count)
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-danger">
                                <i class="fas fa-female"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Women Beneficiaries</span>
                                <span class="info-box-number">{{ number_format($genderImpact->women_count) }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                
                @if($genderImpact->men_count && $genderImpact->women_count)
                <div class="mt-3">
                    <h6><i class="fas fa-chart-pie"></i> Gender Distribution</h6>
                    @php
                        $total = $genderImpact->men_count + $genderImpact->women_count;
                        $menPercentage = round(($genderImpact->men_count / $total) * 100, 1);
                        $womenPercentage = round(($genderImpact->women_count / $total) * 100, 1);
                    @endphp
                    <div class="progress mb-2">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $menPercentage }}%" 
                             aria-valuenow="{{ $menPercentage }}" aria-valuemin="0" aria-valuemax="100">
                            Men: {{ $menPercentage }}%
                        </div>
                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $womenPercentage }}%" 
                             aria-valuenow="{{ $womenPercentage }}" aria-valuemin="0" aria-valuemax="100">
                            Women: {{ $womenPercentage }}%
                        </div>
                    </div>
                    <small class="text-muted">
                        Total Beneficiaries: {{ number_format($total) }} 
                        ({{ number_format($genderImpact->men_count) }} men, {{ number_format($genderImpact->women_count) }} women)
                    </small>
                </div>
                @endif
            </div>
            @endif

            <!-- Gender Notes -->
            @if($genderImpact->gender_notes)
            <div class="col-12">
                <h6><i class="fas fa-sticky-note"></i> Additional Notes</h6>
                <div class="alert alert-light">
                    <p class="mb-0">{{ $genderImpact->gender_notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@else
<div class="card card-secondary card-outline mt-4">
    <div class="card-header">
        <h5 class="card-title m-0">
            <i class="fas fa-venus-mars"></i> Gender Impact Analysis
        </h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No gender impact analysis data is available for this item.
        </div>
    </div>
</div>
@endif 