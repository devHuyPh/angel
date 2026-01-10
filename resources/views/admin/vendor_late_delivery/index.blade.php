@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    {!! $dataTable->renderTable() !!}
@endsection
