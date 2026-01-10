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
