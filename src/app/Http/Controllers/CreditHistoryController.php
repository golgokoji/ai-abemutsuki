<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CreditHistory;

class CreditHistoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $histories = CreditHistory::where('user_id', '>' ,0)
        // $histories = CreditHistory::where('user_id', $user->id)
            ->orderByDesc('granted_at')
            ->paginate(20);
        return view('credit_history', compact('histories'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $history = CreditHistory::where('user_id', $user->id)->findOrFail($id);
        return view('credit_history_show', compact('history'));
    }
}
