<?php

return [
    "api_key"      => env("FISH_AUDIO_API_KEY", ""),
    "api_base"     => "https://api.fish.audio",
    "model"        => "s1",
    "reference_id" => "9ae776f976914160856436e1ab2b2b88",
    "format"       => "mp3",

    "latency"      => "normal",
    "mp3_bitrate"  => 128,
    "opus_bitrate" => 32,
    "normalize"    => true,
    "chunk_length" => 200,

    "temperature"  => 0.7,
    "top_p"        => 0.7,
    "prosody"      => [
        "volume" => 0
    ],
];
