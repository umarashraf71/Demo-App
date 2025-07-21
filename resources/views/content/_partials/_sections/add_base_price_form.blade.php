<div class="row">
    <div class="col-2 pe-0">
        <label class="form-label" >Source Type</label>
        <select id="source_type" data-placeholder="Source Type"  class="select2 form-select"  onchange="getData(this.value,'suppliers');getCps(this.value)" required>
            <option value="" selected disabled>Source Type</option>
            <option value="0" >All</option>
            @foreach($types as $type)
                <option {{old('id')==$type->id?'selected':''}} value="{{$type->id}}">{{ucfirst($type->name)}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-2 pe-0 ">
        <label class="form-label" for="modalPermissionName">Supplier</label>
        <select name="supplier" data-placeholder=" Supplier" class="select2 form-select" id="suppliers" onchange="getData(this.value,'collection_points')" required>
            <option value="" selected disabled>Supplier</option>
        </select>
    </div>
    <div class="col-2 pe-0">
        <label class="form-label" for="collection_points">Collection Point</label>
        <select name="collection_point" data-placeholder="Collection Point" class="select2 form-select" id="collection_points" required>
            <option value="" selected disabled>Collection Point</option>
            <option value="0" >All</option>
            @foreach($collection_points as $cp)
                <option value="{{$cp->id}}" >{{$cp->name}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-2 pe-0">
        <label class="form-label " for="pric">Price (Rs.)</label>
        <input name="price" id="pric" value="{{old('price')}}" type="number" class="form-control" required min="1" max="10000" maxlength="7" step="0.01" placeholder="Base Price"/>
    </div>
    <div class="col-2 pe-0">
        <label class="form-label" for="volume">Expected Vol. (Ltr.)</label>
        <input name="volume" id="volume" value="{{old('volume')}}" type="number" class="form-control" onkeypress="return event.charCode >= 48 && event.charCode <= 57" required min="1" max="100000" step="1" placeholder="Expected Volume"/>
    </div>
    <div class="col-1 pe-0">
        <label class="form-label" for="volume">W.E.F</label>
        <input type="date" name="wef" id="wef" class="form-control search-input">
    </div>
    <div class="col-1 pe-0">
        <br>
        <button type="submit" class="btn btn-primary btn-md d-flex sbt_btn" onclick="submitForm()"> Add </button>
    </div>
</div>
@section('page-script')
<script>
    function getData(id,input_id){
        if(input_id=='suppliers'){
            $('#collection_point,#suppliers').html(`<option value="" selected="" disabled="">Select Option</option>`)
        }else if(input_id == 'collection_points') {
            $('#collection_points').html(`<option value="" selected="" disabled="">Select Option</option>`)
        }
        $.ajax('{{route('price.fetch.data')}}', {
            type: 'get',
            data: { id: id, type: input_id,area_office:$('#area_office_search').val(),plant:$('#plant_search').val()},
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
    function getCps(id){
        $('#collection_points').html(`<option value="" selected="" disabled="">Select Option</option>`)
        $.ajax('{{route('price.cp.source.type')}}', {
            type: 'get',
            data: { source_type: id, area_office:$('#area_office_search').val()},
            success: function (data) {
                if(data.success== true){
                    $('#collection_points').html(data.data);
                }
            },
            error: function (jqXhr, textStatus, errorMessage) {
                alert('Error Occurred')
            }
        });
    }

    function submitForm(){

        let plant  = $('#plant_search').val();
        let department  = $('#department_search').val();
        let section  = $('#section_search').val();
        let area_office  = $('#area_office_search').val() && $('#area_office_search').val()!=0?$('#area_office_search').val():null;
        let source_type  = $('#source_type').val() && $('#source_type').val()!=0?$('#source_type').val():null;
        let supplier  = $('#suppliers').val() && $('#suppliers').val()!=0?$('#suppliers').val():null;
        let collection_point  = $('#collection_points').val() && $('#collection_points').val()!=0?$('#collection_points').val():null;
        let price  = $('#pric').val();
        let volume  = $('#volume').val();
        let wef  = $('#wef').val();

        if(area_office == null && plant == null){
            showAlert('error','Area office or plant is required');
            return;
        }

        if(!source_type && !supplier && !collection_point && !area_office){
            showAlert('error','Minimum 1 field is required');
            return;
        }

        if(!price || price<1){
            showAlert('error','Price field is required');
            $('#pric').addClass('is-invalid');
            return;
        }
        if(!volume || volume<1){
            showAlert('error','Expected Volume field is required');
            $('#volume').addClass('is-invalid')
            return;
        }
        if(wef === null || wef == '')
        {
            showAlert('error','WEF field is required');
            $('#wef').addClass('is-invalid')
            return;
        }
        $('.sbt_btn').toggleClass("btn-danger","btn-primary");
        // $('.spinner').toggleClass("d-none","d-inline");

        $('#volume,#pric,#source_type,#plant_search').removeClass('is-invalid')
        $.ajax('{{route('price.add')}}', {
            type: 'get',
            data: {plant: plant, department: department, section:section, area_office:area_office, source_type:source_type,
                supplier:supplier, collection_point:collection_point, price:price, volume:volume, wef:wef},
            success: function (response) {
                if(response.success == true){
                    showAlert('success',response.message);
                    $('#price_table').find('tbody').prepend(response.data);
                    $('#created_counter').text(parseInt($('#created_counter').text())+1);
                    $('#sbt_tr').show();
                    $('#empty_tr').hide();

                }else{
                    showAlert('error',response.message);
                }
                $('.sbt_btn').toggleClass("btn-danger","btn-primary");
                // $('.spinner').toggleClass("d-none","d-inline");
            },
            error: function (jqXhr, textStatus, errorMessage) {
                alert('Error Occurred')
            }
        });
    }


</script>
@endsection
