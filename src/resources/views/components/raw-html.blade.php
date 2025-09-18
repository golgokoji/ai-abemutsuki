@if(!empty($html))
    {!! is_string($html) ? $html : json_encode($html) !!}
@endif
