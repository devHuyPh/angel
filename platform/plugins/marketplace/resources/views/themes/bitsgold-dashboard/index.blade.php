@extends(MarketplaceHelper::viewPath('vendor-dashboard.layouts.master'))

@section('content')
  @include(MarketplaceHelper::viewPath('vendor-dashboard.partials.report-filters'))

  <section
    class="ps-dashboard report-chart-content"
    id="report-chart"
  >
    @include(MarketplaceHelper::viewPath('vendor-dashboard.partials.dashboard-content'))
  </section>
@stop



@push('footer')
  <script>
    'use strict';

    var BotbleVariables = BotbleVariables || {};
    BotbleVariables.languages = BotbleVariables.languages || {};
    BotbleVariables.languages.reports = {!! json_encode(trans('plugins/ecommerce::reports.ranges'), JSON_HEX_APOS) !!}
  </script>
@endpush
