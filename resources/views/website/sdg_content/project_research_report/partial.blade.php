<div class="col-lg-8">
  <!-- Reports Section -->
  <div class="content-header">
      <div class="container">
          <div class="row mb-2">
              <div class="col-sm-6">
                  <h1 class="m-0">Reports</h1>
              </div>
              <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="{{ route('website.sdg_report_main2') }}">Show
                              All Reports</a></li>
                      <li class="breadcrumb-item active">Home</li>
                  </ol>
              </div>
          </div>
      </div>
  </div>

  <div class="content">
      <div class="container">

          @if ($reports !== null && count($reports) > 0)
              <div class="row">
                  @foreach ($reports as $report)
                      <div class="col-lg-6 mb-4">
                          <div class="card card-success card-outline h-100 d-flex flex-column post">
                              <div class="card-header">
                                  <h5 class="card-title m-0 text-truncate" title="{{ $report->title }}">
                                      {{ Str::limit($report->title, 30) }}
                                  </h5>
                              </div>
                              <div class="card-body d-flex flex-column">
                                  <a href="{{ route('website.display_single_report2', $report->id) }}">
                                      <img src="{{ $report->reportimg->image }}" class="card-img-top"
                                          alt="" style="height: 200px; object-fit: cover;">
                                  </a>
                                  <div class="post-meta mt-3">
                                      <ul class="list-unstyled">
                                          <li><i class="ion-calendar"></i>
                                              {{ date('d M Y', strtotime($report->created_at)) }}</li>
                                          <li>
                                              @foreach ($report->sdg as $report_sdgs)
                                                  <i
                                                      class="ion-pricetags">{{ $report_sdgs->name }}&nbsp;</i>
                                              @endforeach
                                          </li>
                                      </ul>
                                  </div>
                                  <a href="{{ route('website.display_single_report2', $report->id) }}"
                                      class="btn btn-success mt-auto continue-reading">Continue
                                      Reading</a>
                              </div>
                          </div>
                      </div>
                  @endforeach
              </div>
          @else
              <h2 class="text-center text-danger mt-5">No Reports added</h2>
          @endif
      </div>
  </div>

  <!-- Projects Section -->
  <div class="content-header">
      <div class="container">
          <div class="row mb-2">
              <div class="col-sm-6">
                  <h1 class="m-0">Projects</h1>
              </div>
              <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="{{ route('website.sdg_project_main2') }}">Show
                              All Projects</a></li>
                      <li class="breadcrumb-item active">Home</li>
                  </ol>
              </div>
          </div>
      </div>
  </div>

  <div class="content">
      <div class="container">
          @if ($projects !== null && count($projects) > 0)
              <div class="row">
                  @foreach ($projects as $project)
                      <div class="col-lg-6 mb-4">
                          <div class="card card-primary card-outline h-100 d-flex flex-column post">
                              <div class="card-header">
                                  <h5 class="card-title m-0 text-truncate" title="{{ $project->title }}">
                                      {{ Str::limit($project->title, 30) }}
                                  </h5>
                              </div>
                              <div class="card-body d-flex flex-column">
                                  <a href="{{ route('website.display_single_project2', $project->id) }}">
                                      <img src="{{ $project->projectimg->image }}" class="card-img-top"
                                          alt="" style="height: 200px; object-fit: cover;">
                                  </a>
                                  <div class="post-meta mt-3">
                                      <ul class="list-unstyled">
                                          <li><i class="ion-calendar"></i>
                                              {{ date('d M Y', strtotime($project->created_at)) }}</li>
                                          <li>
                                              @foreach ($project->sdg as $project_sdgs)
                                                  <i
                                                      class="ion-pricetags">{{ $project_sdgs->name }}&nbsp;</i>
                                              @endforeach
                                          </li>
                                      </ul>
                                  </div>
                                  <a href="{{ route('website.display_single_project2', $project->id) }}"
                                      class="btn btn-primary mt-auto continue-reading">Continue
                                      Reading</a>
                              </div>
                          </div>
                      </div>
                  @endforeach
              </div>
          @else
              <h2 class="text-center text-danger mt-5">No Projects added</h2>
          @endif
      </div>
  </div>

  <!-- Research Section -->
  <div class="content-header">
      <div class="container">
          <div class="row mb-2">
              <div class="col-sm-6">
                  <h1 class="m-0">Research</h1>
              </div>
              <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a
                              href="{{ route('website.sdg_research_main2') }}">Show All Research</a></li>
                      <li class="breadcrumb-item active">Home</li>
                  </ol>
              </div>
          </div>
      </div>
  </div>

  <div class="content">
      <div class="container">
          @if ($research !== null && count($research) > 0)
              <div class="row">
                  @foreach ($research as $singleResearch)
                      <div class="col-lg-6 mb-4">
                          <div class="card card-secondary card-outline h-100 d-flex flex-column post">
                              <div class="card-header">
                                  <h5 class="card-title m-0 text-truncate"
                                      title="{{ $singleResearch->title }}">
                                      {{ Str::limit($singleResearch->title, 30) }}
                                  </h5>
                              </div>
                              <div class="card-body d-flex flex-column">
                                  <div class="post-meta mt-3">
                                      <ul class="list-unstyled">
                                          <li><i class="ion-calendar"></i>
                                              {{ date('d M Y', strtotime($singleResearch->created_at)) }}
                                          </li>
                                          <li>
                                              @foreach ($singleResearch->sdg as $singleResearch_sdgs)
                                                  <i
                                                      class="ion-pricetags">{{ $singleResearch_sdgs->name }}&nbsp;</i>
                                              @endforeach
                                          </li>
                                          <li>
                                              <i class="ion-pricetags"></i>
                                              {{ $singleResearch->researchcategory->name ?? 'No Category' }}
                                          </li>
                                      </ul>
                                  </div>
                                  <a href="{{ route('website.display_single_research2', $singleResearch->id) }}"
                                      class="btn btn-secondary mt-auto continue-reading">Continue
                                      Reading</a>
                              </div>
                          </div>
                      </div>
                  @endforeach
              </div>
          @else
              <h2 class="text-center text-danger mt-5">No Research added</h2>
          @endif
      </div>
  </div>
</div>
<div class="col-lg-4">
  <div class="card card-widget card-danger card-outline">
      <div class="card-header">
          <h5 class="card-title m-0 text-truncate" data-year="sdg">
              <i class="fas fa-globe"></i> Sustainable Development Goals
          </h5>
      </div>
      <div class="card-footer p-0">
          <ul class="nav flex-column">
              @foreach ($sdgs as $singleSdg)
                  <li class="nav-item">
                      <a href="{{ route('website.display_sdg_content', $singleSdg->id) }}"
                          class="nav-link">
                          {{ $singleSdg->name }}

                          @php
                              // Set badge color based on the total count
                              $badgeColor = 'bg-primary'; // Default color

                              if ($singleSdg->total_count == 0) {
                                  $badgeColor = 'bg-danger';
                              } elseif ($singleSdg->total_count >= 1) {
                                  $badgeColor = 'bg-warning';
                              } elseif ($singleSdg->total_count >= 10) {
                                  $badgeColor = 'bg-primary';
                              } elseif ($singleSdg->total_count >= 20) {
                                  $badgeColor = 'bg-success';
                              }
                          @endphp

                          <span class="float-right badge {{ $badgeColor }}">
                              {{ $singleSdg->total_count }}
                          </span>
                      </a>
                  </li>
              @endforeach
          </ul>
      </div>
  </div>
</div>