@if(isset($genderImpact) && $genderImpact)
<div class="gender-impact-summary mt-2">
    <div class="d-flex align-items-center mb-2">
        <i class="fas fa-venus-mars text-info mr-2"></i>
        <strong>Gender Impact:</strong>
    </div>
    
    <div class="row">
        <!-- Quick Benefits Overview -->
        <div class="col-md-8">
            <div class="d-flex flex-wrap">
                @if($genderImpact->benefits_men)
                    <span class="badge badge-primary mr-1 mb-1">
                        <i class="fas fa-male"></i> Men
                    </span>
                @endif
                @if($genderImpact->benefits_women)
                    <span class="badge badge-danger mr-1 mb-1">
                        <i class="fas fa-female"></i> Women
                    </span>
                @endif
                @if($genderImpact->benefits_all)
                    <span class="badge badge-info mr-1 mb-1">
                        <i class="fas fa-users"></i> All Genders
                    </span>
                @endif
                @if($genderImpact->addresses_gender_inequality)
                    <span class="badge badge-success mr-1 mb-1">
                        <i class="fas fa-balance-scale"></i> Addresses Inequality
                    </span>
                @endif
            </div>
        </div>
        
        <!-- Beneficiary Count -->
        <div class="col-md-4">
            @if($genderImpact->men_count || $genderImpact->women_count)
                @php
                    $total = ($genderImpact->men_count ?? 0) + ($genderImpact->women_count ?? 0);
                @endphp
                <small class="text-muted">
                    <i class="fas fa-users"></i> {{ number_format($total) }} beneficiaries
                </small>
            @endif
        </div>
    </div>
    
    @if($genderImpact->target_beneficiaries)
    <div class="mt-2">
        <small class="text-muted">
            <i class="fas fa-bullseye"></i> <strong>Target:</strong> {{ Str::limit($genderImpact->target_beneficiaries, 100) }}
        </small>
    </div>
    @endif
</div>
@endif 