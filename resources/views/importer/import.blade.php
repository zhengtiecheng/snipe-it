@extends('layouts/default')
<link rel="stylesheet" type="text/css" href="{{ asset('css/lib/jquery.fileupload.css') }}">

{{-- Hide importer until vue has rendered it, if we continue using vue for other things we should move this higher in the style --}}
<style>
[v-cloak] {
    display:none;
}
</style>
{{-- Page title --}}
@section('title')
{{ trans('general.import') }}
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="app">
    <importer inline-template v-cloak>
        <div class="row">
        <alert v-show="alert.visible" :alert-type="alert.type" v-on:hide="alert.visible = false">@{{ alert.message }}</alert>
            <errors :errors="importErrors"></errors>
            <div class="col-md-12">
                <div class="box">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3">
                                <!-- The fileinput-button span is used to style the file input field as button -->
                                <span class="btn btn-info fileinput-button">
                                    <span>Select Import File...</span>
                                    <!-- The file input field used as target for the file upload widget -->
                                    <input id="fileupload" type="file" name="files[]" data-url="/api/v1/imports" accept="text/csv">
                                </span>
                            </div>
                            <div class="col-md-9" v-show="progress.visible" style="padding-bottom:20px">
                                <div class="col-md-11">
                                    <div class="progress progress-striped-active" style="margin-top: 8px">
                                        <div class="progress-bar" :class="progress.currentClass" role="progressbar" :style="progressWidth">
                                            <span>@{{ progress.statusText }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" style="padding-top: 30px;">
                                <table class="table table-striped" id="upload-table">
                                    <thead>
                                        <th>File</th>
                                        <th>Created</th>
                                        <th>Size</th>
                                        <th></th>
                                    </thead>
                                    <tbody>
                                    	<template v-for="currentFile in files">
                                    		<tr>
                                    			<td>@{{ currentFile.file_path }}</td>
                                    			<td>@{{ currentFile.created_at }} </td>
                                    			<td>@{{ currentFile.filesize }}</td>
                                    			<td>
                                    			<button class="btn btn-sm btn-info" @click="toggleEvent(currentFile.id)">Process</button>
                                    				<button class="btn btn-danger" @click="deleteFile(currentFile)"><i class="fa fa-trash icon-white"></i></button>
                                    			</td>
                                    		</tr>
                                    			<import-file :key="currentFile.id" :file="currentFile" @alert="updateAlert(alert)">
                                    			</import-file>
                                    	</template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </importer>
</div>
@stop

@section('moar_scripts')
<script>
    new Vue({
        el: '#app'
    });
</script>
@endsection
