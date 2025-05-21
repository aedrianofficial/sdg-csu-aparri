@extends('layouts.admin')

@section('title', 'Gender Impact Analysis Dashboard')

@section('content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Gender Impact Analysis Dashboard</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Gender Impact Analysis</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Gender Impact Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-box bg-light">
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Items with Gender Analysis</span>
                                        <span class="info-box-number display-6">{{ $stats['total'] }}</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-primary" style="width: 100%"></div>
                                        </div>
                                        <span class="progress-description">
                                            {{ $stats['projects_total'] }} Projects + {{ $stats['research_total'] }} Research
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-success">
                                    <div class="info-box-content">
                                        <span class="info-box-text text-white">Benefits Women/Girls</span>
                                        <span class="info-box-number text-white">{{ $stats['benefits_women']['count'] }}</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-white" style="width: {{ $stats['benefits_women']['percentage'] }}%"></div>
                                        </div>
                                        <span class="progress-description text-white">
                                            {{ $stats['benefits_women']['percentage'] }}% of total
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-info">
                                    <div class="info-box-content">
                                        <span class="info-box-text text-white">Benefits Men/Boys</span>
                                        <span class="info-box-number text-white">{{ $stats['benefits_men']['count'] }}</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-white" style="width: {{ $stats['benefits_men']['percentage'] }}%"></div>
                                        </div>
                                        <span class="progress-description text-white">
                                            {{ $stats['benefits_men']['percentage'] }}% of total
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-warning">
                                    <div class="info-box-content">
                                        <span class="info-box-text">Addresses Gender Inequality</span>
                                        <span class="info-box-number">{{ $stats['addresses_inequality']['count'] }}</span>
                                        <div class="progress">
                                            <div class="progress-bar bg-white" style="width: {{ $stats['addresses_inequality']['percentage'] }}%"></div>
                                        </div>
                                        <span class="progress-description">
                                            {{ $stats['addresses_inequality']['percentage'] }}% of total
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SDG Gender Impact -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-globe me-2"></i>Gender Impact by SDG</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>SDG</th>
                                        <th>Name</th>
                                        <th>Total Items</th>
                                        <th>Projects</th>
                                        <th>Research</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sdgStats as $stat)
                                    <tr>
                                        <td>
                                            <img src="{{ asset('assets/images/sdgs/E-WEB-Goal-' . str_pad($stat['sdg']->id, 2, '0', STR_PAD_LEFT) . '.png') }}" 
                                                alt="SDG {{ $stat['sdg']->id }}" class="img-fluid" style="max-height: 40px;">
                                        </td>
                                        <td>{{ $stat['sdg']->name }}</td>
                                        <td><span class="badge bg-primary">{{ $stat['total_count'] }}</span></td>
                                        <td><span class="badge bg-success">{{ $stat['project_count'] }}</span></td>
                                        <td><span class="badge bg-info">{{ $stat['research_count'] }}</span></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No SDG gender impact data available</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Projects with Gender Impact -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-project-diagram me-2"></i>Recent Projects with Gender Impact</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Women</th>
                                        <th>Men</th>
                                        <th>Gender Equality</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($projects as $project)
                                    <tr>
                                        <td>{{ Str::limit($project->title, 30) }}</td>
                                        <td>{!! $project->genderImpact->benefits_women ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                                        <td>{!! $project->genderImpact->benefits_men ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                                        <td>{!! $project->genderImpact->addresses_gender_inequality ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                                        <td>
                                            <a href="{{ route('admin.gender-impact.show', ['type' => 'project', 'id' => $project->id]) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No projects with gender impact data</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Research with Gender Impact -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-purple text-white">
                        <h5 class="mb-0"><i class="fas fa-book me-2"></i>Recent Research with Gender Impact</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Women</th>
                                        <th>Men</th>
                                        <th>Gender Equality</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($research as $item)
                                    <tr>
                                        <td>{{ Str::limit($item->title, 30) }}</td>
                                        <td>{!! $item->genderImpact->benefits_women ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                                        <td>{!! $item->genderImpact->benefits_men ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                                        <td>{!! $item->genderImpact->addresses_gender_inequality ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>' !!}</td>
                                        <td>
                                            <a href="{{ route('admin.gender-impact.show', ['type' => 'research', 'id' => $item->id]) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No research with gender impact data</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Section -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.gender-impact.export') }}" method="post">
                            @csrf
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-file-export me-2"></i>Export Gender Analysis Data</h5>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-download me-2"></i>Export to Excel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 