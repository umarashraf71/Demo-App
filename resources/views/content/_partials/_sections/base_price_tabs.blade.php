@php $current_route = $request->route()->getName();
$pending_count = \App\Models\Price::where('status',0)->where('created_by',auth()->user()->id)->count();
$reverted_count = \App\Models\WorkFlowApproval::where(['created_by'=>auth()->user()->id,'status'=>4])->count();
 @endphp


<ul class="nav nav-tabs justify-content-center" role="tablist">
    @php $request_paramters = array_merge(request()->all()) @endphp
    @can('Create Base Pricing')
        <li class="nav-item">
            <a class="nav-link {{$current_route == 'price.create'?'active disabled':''}}" href="{{route('price.create', $request_paramters)}}" role="tab" aria-selected="false">Created &nbsp<span class="badge badge-light-primary" id="created_counter">{{$pending_count}}</span></a>
        </li>
    @endcan

    <li class="nav-item">
        <a class="nav-link {{$current_route == 'price.pending'?'active disabled':''}}" href="{{route('price.pending', $request_paramters)}}" role="tab" aria-selected="false">Pending</a>
    </li>
    <li class="nav-item">
        <a class="nav-link  {{$current_route == 'price.index'?'active disabled':''}}" href="{{route('price.index', $request_paramters)}}">Approved</a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{$current_route == 'price.rejected'?'active disabled':''}}" href="{{route('price.rejected', $request_paramters)}}" role="tab" aria-selected="false">Rejected</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{$current_route == 'price.reverted'?'active disabled':''}}" href="{{route('price.reverted', $request_paramters)}}" role="tab" aria-selected="false" >Reverted &nbsp<span class="badge badge-light-primary {{$reverted_count<1?'d-none':''}}" >{{$reverted_count}}</span></a>
    </li>
</ul>
