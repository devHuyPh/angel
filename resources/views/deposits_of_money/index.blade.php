@extends('plugins/marketplace::themes.bitsgold-dashboard.layouts.master')

@section('content')
    <div id="app">
        <div class="table-wrapper">
            <div class="card">
                <div class="card-header">
                    <div class="w-100 justify-content-between d-flex flex-wrap align-items-center gap-1">
                        <div class="d-flex flex-wrap flex-md-nowrap align-items-center gap-1">
                            <div class="table-search-input">
                                <label>
                                    <input type="search" class="form-control input-sm" placeholder=" {{ trans('core/base::layouts.search') }}"
                                        style="min-width: 120px">
                                </label>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <a href="{{route('deposit.create')}}" class="btn action-item btn-primary" tabindex="0"
                                aria-controls="botble-marketplace-tables-vendor-withdrawal-table" type="button"
                                aria-haspopup="dialog" aria-expanded="false">
                                <span data-action="create">
                                    <svg
                                        class="icon  svg-icon-ti-ti-plus" xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M12 5l0 14"></path>
                                        <path d="M5 12l14 0"></path>
                                    </svg> {{ trans('core/base::layouts.add') }}
                                </span>
                            </a>

                            <button class="btn" type="button" data-bb-toggle="dt-buttons"
                                data-bb-target=".buttons-reload" tabindex="0"
                                aria-controls="botble-marketplace-tables-vendor-withdrawal-table">
                                <svg class="icon icon-left svg-icon-ti-ti-refresh" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path>
                                    <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path>
                                </svg>
                                 {{ trans('core/base::layouts.reload') }}

                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-table">
                    <div class="table-responsive">
                        <div id="botble-marketplace-tables-vendor-withdrawal-table_wrapper"
                            class="dataTables_wrapper dt-bootstrap5 no-footer">
                            <table
                                class="table card-table table-vcenter table-striped table-hover dataTable no-footer dtr-inline"
                                id="botble-marketplace-tables-vendor-withdrawal-table"
                                aria-describedby="botble-marketplace-tables-vendor-withdrawal-table_info">
                                <thead>
                                    <tr>
                                        <th title="ID" width="20"
                                            class="text-center no-column-visibility column-key-0 sorting sorting_desc"
                                            tabindex="0"
                                            aria-controls="botble-marketplace-tables-vendor-withdrawal-table"
                                            rowspan="1" colspan="1" style="width: 20px;" aria-sort="descending"
                                            aria-label="IDorderby asc">ID</th>
                                        <th title="Amount" class="column-key-1 sorting" tabindex="0"
                                            aria-controls="botble-marketplace-tables-vendor-withdrawal-table"
                                            rowspan="1" colspan="1" aria-label="Amountorderby asc">
                                            {{ trans('core/base::layouts.amount') }}
                                        </th>
                                        <th title="Fee" class="column-key-2 sorting" tabindex="0"
                                            aria-controls="botble-marketplace-tables-vendor-withdrawal-table"
                                            rowspan="1" colspan="1" aria-label="Feeorderby asc">
                                            {{ trans('core/base::layouts.currency') }}
                                        </th>
                                        <th title="Status" width="100" class="text-center column-key-3 sorting"
                                            tabindex="0"
                                            aria-controls="botble-marketplace-tables-vendor-withdrawal-table"
                                            rowspan="1" colspan="1" style="width: 100px;"
                                            aria-label="Statusorderby asc">
                                            {{ trans('core/base::layouts.status') }}
                                        </th>
                                        <th title="Created At" width="100" class="column-key-4 sorting" tabindex="0"
                                            aria-controls="botble-marketplace-tables-vendor-withdrawal-table"
                                            rowspan="1" colspan="1" style="width: 100px;"
                                            aria-label="Created Atorderby asc">
                                            {{ trans('core/base::layouts.created_at') }}
                                        </th>
                                        <th title="Operations" class="text-nowrap text-center sorting_disabled"
                                            rowspan="1" colspan="1" aria-label="Operations">
                                            {{ trans('core/base::layouts.operations') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($deposits->isEmpty())
                                        <tr class="odd">
                                            <td valign="top" colspan="8" class="dataTables_empty text-center">
                                                {{ trans('core/base::layouts.no_data_found') }}
                                            </td>
                                        </tr>
                                    @endif
                                    @foreach ($deposits as $deposit)
                                    <tr class="odd">
                                        <td class="text-center no-column-visibility column-key-0 sorting_1 dtr-control">
                                        {{$deposit->id}}    
                                        </td>
                                        <td class="   column-key-1">{{ format_price($deposit->amount) }}</td>
                                        <td class="   column-key-2">{{ $deposit->currency }}</td>
                                        <td class="text-center column-key-3">
                                            @php
                                                $statusClasses = [
                                                    0 => 'bg-warning text-warning-fg',
                                                    1 => 'bg-success text-success-fg',
                                                    2 => 'bg-danger text-danger-fg'
                                                ];
                                                $statusTexts = [
                                                    0 => 'pending',
                                                    1 => 'success',
                                                    2 => 'failed'
                                                ];
                                            @endphp
                                        
                                            <span class="badge {{ $statusClasses[$deposit->status] ?? 'bg-secondary text-secondary-fg' }}">
                                                {{ trans('core/base::layouts.'.$statusTexts[$deposit->status]) }}
                                            </span>
                                        </td>

                                        <td class="    text-nowrap column-key-4">{{ date_format($deposit->created_at, 'H:i d-m-Y') }}</td>
                                        <td class="  text-nowrap text-center column-key-5">
                                            <div class="table-actions">
                                                <a class="btn btn-icon btn-sm btn-success" data-bs-toggle="tooltip"
                                                    data-bs-original-title="Show"
                                                    href="{{route('deposit.show', $deposit->id)}}">
                                                    <svg class="icon  svg-icon-ti-ti-eye"
                                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                                                        <path
                                                            d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6">
                                                        </path>
                                                    </svg> </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                            <div class="card-footer d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2"
                                style>
                                <p>{{trans('core/base::layouts.show_from')}} 
                                    {{ $deposits->firstItem() }} 
                                    {{trans('core/base::layouts.to')}} 
                                    {{ $deposits->lastItem() }} 
                                    {{trans('core/base::layouts.in')}} 
                                    {{ $deposits->total() }} 
                                    {{trans('core/base::layouts.records')}}</p>

                                {{ $deposits->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection