<!DOCTYPE html>

<html lang="en" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:v="urn:schemas-microsoft-com:vml">

<head>
    <title>{{$details->parent->name ?? 'Promosi Email'}}</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <style>
        {!! $details->parent->template->css ?? '' !!}   
    </style>
</head>
{!! $details->parent->template->html ?? '' !!}

</html>