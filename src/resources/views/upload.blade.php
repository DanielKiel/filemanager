@extends('layouts.material')

@section('content')

    <dion-fileupload :api="{{json_encode(route('filemanager.upload',['directory' => 'uploads', 'uploadParam' => 'upload' ]))}}"></dion-fileupload>

@endsection