@php
 $current_route = $request->route()->getName();
$plants = \App\Models\Plant::select('name','id')->get();
$departments = [];
if(request('plant')){
    $departments  = \App\Models\Department::select('name','id')->where('plant_id',request('plant'))->get();
}
$sections = [];
if(request('department')){
    $sections  = \App\Models\Section::select('name','id')->where('dept_id',request('department'))->get();
}
$zones = [];
if(request('section')){
    $zones  = \App\Models\Zone::select('name','id')->where('section_id',request('section'))->get();
}
$area_offices = [];
if(request('zone')){
    $area_offices  = \App\Models\AreaOffice::select('name','id')->where('zone_id',request('zone'))->get();
}
@endphp
<form method="get">
    <div class="row mt-2 ms-5">
        <div class="col-2 ">
            <label class="form-label">Plant</label>
            <div >
                <select name="plant" class="form-control" id="plant_search" onchange="getDropdownData('department_search',this.value)">
                    <option value="" selected="" disabled="">Plant</option>
                    @foreach($plants as $plant)
                        <option value="{{$plant->id}}" {{request('plant')==$plant->id?'selected':''}}>{{$plant->name}}</option>
                    @endforeach
                 </select>
            </div>
        </div>
        <div class="col-2 ps-0 pe-0">
            <label class="form-label">Department</label>
            <div>
                <select name="department" id="department_search" class=" form-control" onchange="getDropdownData('section_search',this.value)">
                    <option value="" selected="" disabled="">Department</option>
                    @foreach($departments as $data)
                        <option value="{{$data->id}}"  {{request('department')==$data->id?'selected':''}}>{{$data->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-2  pe-0">
            <label class="form-label">Section</label>
            <div>
                <select name="section" id="section_search" class=" form-control" onchange="getDropdownData('zone_search',this.value)">
                    <option value="" selected="" disabled="">Section</option>
                    @foreach($sections as $data)
                        <option value="{{$data->id}}"  {{request('section')==$data->id?'selected':''}}>{{$data->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-2 pe-0">
            <label class="form-label">Zone</label>
            <div >
                <select name="zone" class="form-control" id="zone_search" onchange="getDropdownData('area_office_search',this.value)">
                    <option value="" selected="" disabled="">Zone</option>
                    @foreach($zones as $data)
                        <option value="{{$data->id}}"  {{request('zone')==$data->id?'selected':''}}>{{$data->name}}</option>
                    @endforeach
                </select>

            </div>
        </div>
        <div class="col-2  pe-0">
            <label class="form-label">Area Office</label>
            <div >
                <select name="area_office" id="area_office_search" class="form-control ">
                    <option value="" selected="" disabled="">Area Office</option>
                    @foreach($area_offices as $data)
                        <option value="{{$data->id}}"  {{request('area_office')==$data->id?'selected':''}}>{{$data->name}}</option>
                    @endforeach
                </select>

            </div>
        </div>

        <div class="col-2">
            <br>
            <input type="submit" value="search"  class="btn btn-sm btn-primary">&nbsp
            @if (count(Input::all()))
               <a href="{{route($current_route)}}" >reset</a>
            @endif
        </div>
    </div>
</form>
<script>
   function getDropdownData(input_id,id){
      if(input_id=='department_search'){
          $('#department_search,#section_search,#zone_search,#area_office_search').html(`<option value="" selected="" disabled="">Select Option</option>`)
      }else if(input_id=='department_search'){
          $('#section_search,#zone_search,#area_office_search').html(`<option value="" selected="" disabled="">Select Option</option>`)
      }else if(input_id=='section_search'){
          $('#zone_search,#area_office_search').html(`<option value="" selected="" disabled="">Select Option</option>`)
      }else if(input_id=='area_office_search'){
          $('#area_office_search').html(`<option value="" selected="" disabled="">Select Option</option>`)
      }

       $.ajax('{{route('price.get.dropdown.data')}}', {
           type: 'get',
           data: { id: id, type: input_id},
           success: function (data) {
               if(data.success== true){
                   $('#'+input_id).html(data.data);
               }
           },
           error: function (jqXhr, textStatus, errorMessage) {
               alert('Error Occurred')
           }
       });

    }


</script>
