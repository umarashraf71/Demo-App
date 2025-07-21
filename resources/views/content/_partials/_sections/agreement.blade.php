<div class="row">
    <div class="col-lg-4 col-md-4 col-sm-4 mb-1">
        <label class="form-label" for="agreement_period_from">Agreement From</label>
        <input type="text" id="agreement_period_from"
               class="form-control flatpickr-basic"
               placeholder="From: (YYYY-MM-DD)"
               name="agreement_period_from"
               value="{{ old('agreement_period_from') }}" />
        @error('agreement_period_from')
             <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 mb-1">
        <label class="form-label" for="agreement_period_to">Agreement To</label>

        <input type="text" id="agreement_period_to"
               class="form-control flatpickr-basic"
               placeholder="To: (YYYY-MM-DD)"
               name="agreement_period_to"
               value="{{ old('agreement_period_to') }}" />
        @error('agreement_period_to')
             <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 mb-1">
        <label class="form-label" for="agreement_period_wef">Effective From</label>
        <input type="text" id="agreement_period_wef"
               class="form-control flatpickr-basic"
               placeholder="W.E.F: (YYYY-MM-DD)"
               name="agreement_period_wef"
               value="{{ old('agreement_period_wef') }}" />
        @error('agreement_period_wef')
             <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

</div>
