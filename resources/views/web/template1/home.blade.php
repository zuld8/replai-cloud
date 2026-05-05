<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<head>
    <title>{{$page}} - {{$name}} </title>
    <x-meta-component></x-meta-component>
    <style>
        {!! $details->parent->template->css ?? '' !!}   
    </style>
</head>
{!! $app->html !!}
</html>