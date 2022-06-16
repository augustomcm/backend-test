<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InvestmentController extends Controller
{
    public function store(Request $req)
    {
        try {
            $data = $req->validate([
                'owner' => 'required|numeric',
                'amount' => 'required|numeric|gt:0',
                'creation_date' => 'required|date_format:Y-m-d|before_or_equal:' . now()->format('Y-m-d')
            ]);

            $owner = Owner::find($data['owner']);
            if(!$owner) throw new \InvalidArgumentException("Owner not found.");

            $investment = Investment::make($owner, $data['amount'], new \DateTime($data['creation_date']));
            $investment->save();

            return response()->json($investment, Response::HTTP_CREATED);
        }catch (\Exception $ex) {
            throw $ex;
        }
    }
}
