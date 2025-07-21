<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QaLabTest;
use App\Http\Resources\QaLabTestResource;
use App\Traits\HttpResponseTrait;
use Illuminate\Http\Request;
use Validator;

class QaLabTestController extends Controller
{
    use HttpResponseTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search_by' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors()->first());
        }
        if ($request->search_by == 'mcc')
            $tests = QaLabTestResource::collection(QaLabTest::where('apply_test', 'all', [1])->get());
        else if ($request->search_by == 'mmt')
            $tests = QaLabTestResource::collection(QaLabTest::where('apply_test', 'all', [2])->get());
        else if ($request->search_by == 'ao')
            $tests = QaLabTestResource::collection(QaLabTest::where('apply_test', 'all', [3])->get());
        else if ($request->search_by == 'plant')
            $tests = QaLabTestResource::collection(QaLabTest::where('apply_test', 'all', [4])->get());

        return $this->response(['tests' => $tests]);
    }
}
