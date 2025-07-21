@php
    use App\Models\Bank;
    $banks = Bank::where('status', 1)->get();
    $segment1 = request()->segment(1);
@endphp


<div class="col-md-6 col-12">
    <div class="mb-1">
        <label class="form-label">Bank</label>
        <select name="bank_id" class="form-control select2">
            <option value="" disabled selected>Bank</option>
            @foreach ($banks as $bank)
                <option value="{{ $bank->id }}"
                    {{ old('bank_id', $bank_detail->bank_id) == $bank->id ? 'selected' : '' }}>( {{ $bank->short_name }}
                    )
                    {{ $bank->name }}</option>
            @endforeach
        </select>
        @error('bank_id')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>
<div class="col-md-6 col-12">
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12 mb-1 pe-0">
            <label class="form-label" for="column-own-7">Branch Code</label>
            <input type="number" id="column-own-8"  class="form-control" placeholder="Code"
                name="bank_branch_code" value="{{ old('bank_branch_code', $bank_detail->bank_branch_code) }}" />
            @error('bank_branch_code')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-lg-7 col-md-7 col-sm-12  offset-lg-1 offset-md-1 ps-0">
            <label class="form-label" for="column-own-5">Branch Name / Address</label>
            <input type="text" id="column-own-7" class="form-control" placeholder="Branch Name / Address"
                name="bank_address" value="{{ old('bank_address', $bank_detail->bank_address) }}" />
            @error('bank_address')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
<div class="col-md-6 col-12">
    <div class="mb-1">
        <label class="form-label" for="column-own-5">Account Title</label><span class="text-danger">*</span>
        <input type="text" id="column-own-5" class="form-control" placeholder="Title" name="bank_account_title"
            value="{{ old('bank_account_title', $bank_detail->bank_account_title) }}" />
        @error('bank_account_title')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>
<div class="col-md-6 col-12">
    <div class="mb-1">
        <label class="form-label" for="column-own-9">IBAN# </label><span class="text-danger">*</span>
        <input type="text" id="column-own-9" class="form-control" placeholder="IBAN#" name="bank_account_no"
            value="{{ old('bank_account_no', $bank_detail->bank_account_no) }}" />
        @error('bank_account_no')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>
