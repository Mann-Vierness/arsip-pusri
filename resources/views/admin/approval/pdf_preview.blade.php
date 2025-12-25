@extends('layouts.admin')

@section('title', 'Preview PDF Dokumen')

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">Preview PDF - {{ strtoupper($type) }} ({{ $document->nomor ?? $document->NOMOR_SK ?? $document->NO }})</h4>
    <div class="card">
        <div class="card-body">
            <iframe src="{{ $pdfUrl }}" width="100%" height="700px" style="border: none;"></iframe>
        </div>
    </div>
    <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">Kembali</a>
</div>
@endsection
