
@forelse($cgt_grades as $key => $v)

<div class="col-md-4">
  <div class="card">
      <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <div>
                <span class="badge bg-primary">CGT Grade</span>
              </div>
              <div class="mt-2">
                <h2 class="counter word-wrap">{{ $v->grade_name }}</h2>
              </div>
            </div>
            <div id="service-chart-01"></div>
          </div>
          <div class="d-flex justify-content-between mt-2">
            <div>
              <span>Pallet Count</span>
            </div>
            <div>
              <span>{{ $inventory_summary[$key]->pallet_count ?? '0' }}</span>
            </div>
          </div>
          <div class="d-flex justify-content-between mt-2">
            <div>
              <span>Rolls</span>
            </div>
            <div>
              <span>{{ $inventory_summary[$key]->total_rolls ?? '0' }}</span>
            </div>
          </div>
          @if ($inv_type == 'yards')

            <div class="d-flex justify-content-between mt-2">
              <div>
                <span>Yards</span>
              </div>
              <div>
                <span>{{ $inventory_summary[$key]->total_yards ?? '0' }}</span>
              </div>
            </div>

          @else

            <div class="d-flex justify-content-between mt-2">
              <div>
                <span>Weight</span>
              </div>
              <div>
                <span>{{ $inventory_summary[$key]->total_weight ?? '0' }}</span>
              </div>
            </div>

          @endif
         {{-- <div class="d-flex justify-content-between mt-2">
         <div>
         <span>Yards</span>
         </div>
         <div>
         <span>' + (item ? item.yards : 'N/A') + '</span>
         </div>
         </div> --}}
      </div>
    </div>
</div>

@empty

    <div class="col-sm-12 text-center">

      <h5>No record found...</h5>>

    </div>

@endforelse
