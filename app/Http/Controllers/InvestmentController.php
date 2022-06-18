<?php

namespace App\Http\Controllers;

use App\Models\GainCalculatingService;
use App\Models\Investment;
use App\Models\Owner;
use App\Models\WithdrawalService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    public function show(Investment $investment)
    {
        try {
            $expectedBalance = $investment->calculateExpectedBalance();

            return response()->json([
                'initial_amount' => $investment->getAmount(),
                'expected_balance' => $expectedBalance
            ]);

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function withdraw(Request $req, Investment $investment, WithdrawalService $withdrawalService)
    {
        try {
            $data = $req->validate([
                'date' => [
                    'required',
                    'date_format:Y-m-d',
                    'after_or_equal:' . $investment->getCreationDate()->format('Y-m-d'),
                    'before_or_equal:' . now()->format('Y-m-d')
                ]
            ]);

            $amountWithdrawn = $withdrawalService->withdrawInvestment($investment, new \DateTime($data['date']));

            return response()->json([
                'amount_withdrawn' => $amountWithdrawn
            ]);

        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
