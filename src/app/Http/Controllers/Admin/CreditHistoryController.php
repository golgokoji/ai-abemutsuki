<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreditHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditHistoryController extends Controller
{
    public function index()
    {
        // 管理者のみアクセス可（Gateやmiddlewareで制御推奨）
        if (!Auth::user() || !Auth::user()->is_admin) {
            abort(403, '管理者のみ閲覧可能です');
        }
        $histories = CreditHistory::orderByDesc('granted_at')->paginate(50);
        return view('admin.credit_histories', compact('histories'));
    }
}
