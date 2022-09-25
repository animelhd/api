@extends('admin.layout')

@section('styles')
    <!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/forms/theme-checkbox-radio.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/table/datatable/dt-global_style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/table/datatable/custom_dt_custom.css')}}">
    <!-- END PAGE LEVEL CUSTOM STYLES -->
@endsection

@section('content')
<div class="row layout-spacing">
	<div class="col-lg-12">
		@include('admin.inc.flash-message')
		<div class="statbox widget box box-shadow">
			<div class="widget-content widget-content-area">
				<a href="{{ route('admin.servers.create') }}">
					<button class="btn btn-success mb-2">{{ __('Add') }}</button>
				</a>
				<div class="table-responsive mb-4">
					<table id="style-3" class="table style-3 table-hover">
						<thead>
							<tr>
								<th>{{ __('Title') }}</th>
                                <th>{{ __('Embed') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-center">{{ __('Options') }}</th>
							</tr>
						</thead>
						<tbody>
							@forelse($servers as $server)
                                <tr>
                                    <td>{{ $server->title }}</td>
                                    <td>{{ $server->embed }}</td>
                                    <td><span class="badge outline-badge-{{ $server->type == 0 ? 'success' : ( $server->type == 1 ? 'info' : 'warning' ) }}">{{ $server->type == 0 ? __('Direct Link') : ($server->type == 1 ? __('Iframe') : __('Generated')) }}</span></td>
                                    <td><span class="badge outline-badge-{{ $server->status == 0 ? 'danger' : ( $server->status == 1 ? 'success' : ( $server->status == 2 ? 'info' : 'warning' ) )  }}">{{ $server->status == 0 ? __('Offline') : ( $server->status == 1 ? __('Online') : ( $server->status == 2 ? __('Only Web') : __('Only App') ) )  }}</span></td>
                                    <td class="text-center">
									<ul class="table-controls">
										<li>
											<a href="{{ route('admin.servers.edit',[$server->id]) }}" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{__('Edit')}}">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 p-1 br-6 mb-1">
													<path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
												</svg>
											</a>
										</li>
										<li>
											<a
												class="bs-tooltip"
												data-toggle="tooltip"
												data-placement="top"
												title=""
												data-original-title="{{__('Delete')}}"
				                                onclick="event.preventDefault();
				                                            document.getElementById('delete_server_{{$server->id}}').submit();"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash p-1 br-6 mb-1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a>
				                            </div>
				                            <form id="delete_server_{{$server->id}}" action="{{ route('admin.servers.destroy',[$server->id]) }}" method="POST" style="display: none;">
				                            	@method('DELETE')
				                                @csrf
                            				</form>
										</li>
									</ul>
								</td>
                                </tr>
                            @empty
                            @endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
    
@endsection

@section('aditionals')
	<script src="{{ asset('plugins/table/datatable/datatables.js') }}"></script>
	<script>
        $('#style-3').DataTable({
        	"oLanguage": {
                "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                "sInfo": "Mostrando página _PAGE_ de _PAGES_",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "Buscar...",
               "sLengthMenu": "Resultados :  _MENU_",
            },
            "ordering": false
        });
    </script>
@endsection