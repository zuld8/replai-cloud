<?php

namespace App\Supports;

class MimeTypes
{
    public const TYPE_MAP = [
        'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        'video' => ['video/mp4', 'video/ogg', 'video/webm'],
        'audio' => ['audio/mpeg', 'audio/wav', 'audio/ogg'],
        'pdf'   => ['application/pdf'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'doc'  => ['application/msword'],
        'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation'],
        'ppt'  => ['application/vnd.ms-powerpoint'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        'xls'  => ['application/vnd.ms-excel'],
        'txt' => ['text/plain'],
        'json' => ['application/json'],
        'html' => ['text/html'],
        'css' => ['text/css'],
        'js' => ['application/javascript'],
        'php' => ['application/x-httpd-php'],
        'file'  => ['application/zip', 'application/octet-stream', 'application/x-rar-compressed'],
    ];
}
