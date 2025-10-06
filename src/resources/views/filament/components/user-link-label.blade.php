@if($url && $userId)
    <div style="margin-top:-0.5em;margin-bottom:1em;">
        <a href="{{ $url }}" target="_blank" style="color:#2563eb;text-decoration:underline;font-weight:600;">
            ユーザー詳細（ID: {{ $userId }}）
        </a>
    </div>
@endif