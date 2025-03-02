<?php

use Illuminate\Support\Facades\DB;

function myEvent() {
    return DB::table('event')->where('id_event', 1)->get()->first();
}

function mySetting() {
    return DB::table('setting')->get()->first();
}

function decode_phone($number) {
    $chars = ['+', '(', ')', '-', ' '];
    $number = str_replace($chars, '', $number);
    if (substr($number, 0, 1) === '0') {
        $number = '62' . substr($number, 1);
    }
    return $number;
}