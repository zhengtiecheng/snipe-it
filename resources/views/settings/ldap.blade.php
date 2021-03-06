@extends('layouts/default')

{{-- Page title --}}
@section('title')
    Update LDAP/AD Settings
    @parent
@stop

@section('header_right')
    <a href="{{ route('settings.index') }}" class="btn btn-default"> {{ trans('general.back') }}</a>
@stop


{{-- Page content --}}
@section('content')

    <style>
        .checkbox label {
            padding-right: 40px;
        }
    </style>

    @if ((!function_exists('ldap_connect')) || (!function_exists('ldap_set_option')) || (!function_exists('ldap_bind')))
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-12">
                    <div class="alert alert-danger">
                       It doesn't look like the LDAP extension is installed or enabled on this server. :(
                    </div>
                </div>
            </div>
        </div>

    @else


    {{ Form::open(['method' => 'POST', 'files' => true, 'class' => 'form-horizontal', 'role' => 'form' ]) }}
    <!-- CSRF Token -->
    {{csrf_field()}}

    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">


            <div class="panel box box-default">
                <div class="box-header with-border">
                    <h4 class="box-title">
                        <i class="fa fa-sitemap"></i> LDAP/AD
                    </h4>
                </div>
                <div class="box-body">


                    <div class="col-md-11 col-md-offset-1">

                        <!-- Enable LDAP -->
                        <div class="form-group {{ $errors->has('ldap_integration') ? 'error' : '' }}">
                            <div class="col-md-3">
                                {{ Form::label('ldap_integration', trans('admin/settings/general.ldap_integration')) }}
                            </div>
                            <div class="col-md-9">
                                {{ Form::checkbox('ldap_enabled', '1', Input::old('ldap_enabled', $setting->ldap_enabled),array('class' => 'minimal')) }}
                                {{ trans('admin/settings/general.ldap_enabled') }}
                                {!! $errors->first('ldap_enabled', '<span class="alert-msg">:message</span>') !!}
                            </div>
                        </div>

                        <!-- AD Flag -->
                        <div class="form-group">
                            <div class="col-md-3">
                                {{ Form::label('is_ad', trans('admin/settings/general.ad')) }}
                            </div>
                            <div class="col-md-9">
                                {{ Form::checkbox('is_ad', '1', Input::old('is_ad', $setting->is_ad),array('class' => 'minimal')) }}
                                {{ trans('admin/settings/general.is_ad') }}
                                {!! $errors->first('is_ad', '<span class="alert-msg">:message</span>') !!}
                            </div>
                        </div>

                        <!-- LDAP Password Sync -->
                        <div class="form-group">
                            <div class="col-md-3">
                                {{ Form::label('is_ad', trans('admin/settings/general.ldap_pw_sync')) }}
                            </div>
                            <div class="col-md-9">
                                {{ Form::checkbox('ldap_pw_sync', '1', Input::old('ldap_pw_sync', $setting->ldap_pw_sync),array('class' => 'minimal')) }}
                                {{ trans('general.yes') }}
                                <p class="help-block">{{ trans('admin/settings/general.ldap_pw_sync_help') }}</p>
                                {!! $errors->first('ldap_pw_sync', '<span class="alert-msg">:message</span>') !!}
                            </div>
                        </div>

                        <!-- AD Domain -->
                        <div class="form-group {{ $errors->has('ad_domain') ? 'error' : '' }}">
                            <div class="col-md-3">
                                {{ Form::label('ad_domain', trans('admin/settings/general.ad_domain')) }}
                            </div>
                            <div class="col-md-9">
                                @if (config('app.lock_passwords')===true)
                                    {{ Form::text('ad_domain', Input::old('ad_domain', $setting->ad_domain), array('class' => 'form-control', 'disabled'=>'disabled','placeholder' => 'example.com')) }}
                                @else
                                    {{ Form::text('ad_domain', Input::old('ad_domain', $setting->ad_domain), array('class' => 'form-control','placeholder' => 'example.com')) }}
                                @endif
                                <p class="help-block">{{ trans('admin/settings/general.ad_domain_help') }}</p>
                                {!! $errors->first('ad_domain', '<span class="alert-msg">:message</span>') !!}
                            </div>
                        </div><!-- AD Domain -->

                        <!-- LDAP Server -->
                        <div class="form-group {{ $errors->has('ldap_server') ? 'error' : '' }}">
                            <div class="col-md-3">
                                {{ Form::label('ldap_server', trans('admin/settings/general.ldap_server')) }}
                            </div>
                            <div class="col-md-9">
                                @if (config('app.lock_passwords')===true)
                                    {{ Form::text('ldap_server', Input::old('ldap_server', $setting->ldap_server), array('class' => 'form-control', 'disabled'=>'disabled','placeholder' => 'ldap://ldap.example.com')) }}
                                @else
                                    {{ Form::text('ldap_server', Input::old('ldap_server', $setting->ldap_server), array('class' => 'form-control','placeholder' => 'ldap://ldap.example.com')) }}
                                @endif
                                <p class="help-block">{{ trans('admin/settings/general.ldap_server_help') }}</p>
                                {!! $errors->first('ldap_server', '<span class="alert-msg">:message</span>') !!}
                            </div>
                        </div><!-- LDAP Server -->

                        <!-- Start TLS -->
                        <div class="form-group">
                            <div class="col-md-3">
                                {{ Form::label('ldap_tls', trans('admin/settings/general.ldap_tls')) }}
                            </div>
                            <div class="col-md-9">
                                {{ Form::checkbox('ldap_tls', '1', Input::old('ldap_tls', $setting->ldap_tls),array('class' => 'minimal')) }}
                                {{ trans('admin/settings/general.ldap_tls_help') }}
                                {!! $errors->first('ldap_tls', '<span class="alert-msg">:message</span>') !!}
                            </div>
                        </div>

                        <!-- Ignore LDAP Certificate -->
                        <div class="form-group {{ $errors->has('ldap_server_cert_ignore') ? 'error' : '' }}">
                            <div class="col-md-3">
                                {{ Form::label('ldap_server_cert_ignore', trans('admin/settings/general.ldap_server_cert')) }}
                            </div>
                            <div class="col-md-9">
                                {{ Form::checkbox('ldap_server_cert_ignore', '1', Input::old('ldap_server_cert_ignore', $setting->ldap_server_cert_ignore),array('class' => 'minimal')) }}
                                {{ trans('admin/settings/general.ldap_server_cert_ignore') }}
                                {!! $errors->first('ldap_server_cert_ignore', '<span class="alert-msg">:message</span>') !!}
                                <p class="help-block">{{ trans('admin/settings/general.ldap_server_cert_help') }}</p>
                            </div>
                        </div>

                        <!-- LDAP Username -->
                        <div class="form-group {{ $errors->has('ldap_uname') ? 'error' : '' }}">
                            <div class="col-md-3">
                                {{ Form::label('ldap_uname', trans('admin/settings/general.ldap_uname')) }}
                            </div>
                            <div class="col-md-9">
                                @if (config('app.lock_passwords')===true)
                                    {{ Form::text('ldap_uname', Input::old('ldap_uname', $setting->ldap_uname), array('class' => 'form-control', 'disabled'=>'disabled','placeholder' => 'binduser@example.com')) }}
                                @else
                                    {{ Form::text('ldap_uname', Input::old('ldap_uname', $setting->ldap_uname), array('class' => 'form-control','placeholder' => 'binduser@example.com')) }}
                                @endif
                                {!! $errors->first('ldap_uname', '<span class="alert-msg">:message</span>') !!}
                            </div>
                        </div>

                        <!-- LDAP pword -->
                        <div class="form-group {{ $errors->has('ldap_pword') ? 'error' : '' }}">
                            <div class="col-md-3">
                                {{ Form::label('ldap_pword', trans('admin/settings/general.ldap_pword')) }}
                            </div>
                            <div class="col-md-9">
                                @if (config('app.lock_passwords'))
                                    {{ Form::password('ldap_pword', array('class' => 'form-control', 'disabled'=>'disabled','placeholder' => 'binduserpassword')) }}
                                @else
                                    {{ Form::password('ldap_pword', array('class' => 'form-control','placeholder' => 'binduserpassword')) }}
                                @endif
                                {!! $errors->first('ldap_pword', '<span class="alert-msg">:message</span>') !!}
                            </div>
                        </div>

                        <!-- LDAP basedn -->
                        <div class="form-group {{ $errors->has('ldap_basedn') ? 'error' : '' }}">
                            <div class="col-md-3">
                                {{ Form::label('ldap_basedn', trans('admin/settings/general.ldap_basedn')) }}
                            </div>
                            <div class="col-md-9">
                                @if (config('app.lock_passwords')===true)
                                    {{ Form::text('ldap_basedn', Input::old('ldap_basedn', $setting->ldap_basedn), array('class' => 'form-control', 'disabled'=>'disabled','placeholder' => 'cn=users/authorized,dc=example,dc=com')) }}
                                @else
                                    {{ Form::text('ldap_basedn', Input::old('ldap_basedn', $setting->ldap_basedn), array('class' => 'form-control','placeholder' => 'cn=users/authorized,dc=example,dc=com')) }}
                                @endif
                                {!! $errors->first('ldap_basedn', '<span class="alert-msg">:message</span>') !!}
                            </div>
                        </div>

                        <!-- LDAP filter -->
                        <div class="form-group {{ $errors->has('ldap_filter') ? 'error' : '' }}">
                            <div class="col-md-3">
                                {{ Form::label('ldap_filter', trans('admin/settings/general.ldap_filter')) }}
                            </div>
                            <div class="col-md-9">
                                @if (config('app.lock_passwords')===true)
                                    {{ Form::text('ldap_filter', Input::old('ldap_filter', $setting->ldap_filter), array('class' => 'form-control', 'disabled'=>'disabled','placeholder' => '&(cn=*)')) }}
                                @else
                                    {{ Form::text('ldap_filter', Input::old('ldap_filter', $setting->ldap_filter), array('class' => 'form-control','placeholder' => '&(cn=*)')) }}
                                @endif
                                {!! $errors->first('ldap_filter', '<span class="alert-msg">:message</span>') !!}
                            </div>
                        </div>

                        <!-- LDAP  username field-->
                        <div class="form-group {{ $errors->has('ldap_username_field') ? 'error' : '' }}">
                            <div class="col-md-3">
                                {{ Form::label('ldap_username_field', trans('admin/settings/general.ldap_username_field')) }}
                            </div>
                            <div class="col-md-9">
                                @if (config('app.lock_passwords')===true)
                                    {{ Form::text('ldap_username_field', Input::old('ldap_username_field', $setting->ldap_username_field), array('class' => 'form-control', 'disabled'=>'disabled','placeholder' => 'samaccountname')) }}
                                @else
                                    {{ Form::text('ldap_username_field', Input::old('ldap_username_field', $setting->ldap_username_field), array('class' => 'form-control','placeholder' => 'samaccountname')) }}
                                @endif
                                {!! $errors->first('ldap_username_field', '<span class="alert-msg">:message</span>') !!}
                            </div>
                        </div>

                        <!-- LDAP Last Name Field -->
                        <div class="form-group {{ $errors->has('ldap_lname_field') ? 'error' : '' }}">
                            <div class="col-md-3">
                                {{ Form::label('ldap_lname_field', trans('admin/settings/general.ldap_lname_field')) }}
                            </div>
                            <div class="col-md-9">
                                @if (config('app.lock_passwords')===true)
                                    {{ Form::text('ldap_lname_field', Input::old('ldap_lname_field', $setting->ldap_lname_field), array('class' => 'form-control', 'disabled'=>'disabled','placeholder' => 'sn')) }}
                                @else
                                    {{ Form::text('ldap_lname_field', Input::old('ldap_lname_field', $setting->ldap_lname_field), array('class' => 'form-control','placeholder' => 'sn')) }}
                                @endif
                                {!! $errors->first('ldap_lname_field', '<span class="alert-msg">:message</span>') !!}
                            </div>
                        </div>

                        <!-- LDAP First Name field -->
                        <div class="form-group {{ $errors->has('ldap_fname_field') ? 'error' : '' }}">
                            <div class="col-md-3">
                                {{ Form::label('ldap_fname_field', trans('admin/settings/general.ldap_fname_field')) }}
                            </div>
                            <div class="col-md-9">
                                @if (config('app.lock_passwords')===true)
                                    {{ Form::text('ldap_fname_field', Input::old('ldap_fname_field', $setting->ldap_fname_field), array('class' => 'form-control', 'disabled'=>'disabled','placeholder' => 'givenname')) }}
                                @else
                                    {{ Form::text('ldap_fname_field', Input::old('ldap_fname_field', $setting->ldap_fname_field), array('class' => 'form-control','placeholder' => 'givenname')) }}
                                @endif
                                {!! $errors->first('ldap_fname_field', '<span class="alert-msg">:message</span>') !!}
                            </div>
                        </div>

                        <!-- LDAP Auth Filter Query -->
                        <div class="form-group {{ $errors->has('ldap_auth_filter_query') ? 'error' : '' }}">
                            <div class="col-md-3">
                                {{ Form::label('ldap_auth_filter_query', trans('admin/settings/general.ldap_auth_filter_query')) }}
                            </div>
                            <div class="col-md-9">
                                @if (config('app.lock_passwords')===true)
                                    {{ Form::text('ldap_auth_filter_query', Input::old('ldap_auth_filter_query', $setting->ldap_auth_filter_query), array('class' => 'form-control', 'disabled'=>'disabled','placeholder' => '"uid="')) }}
                                @else
                                    {{ Form::text('ldap_auth_filter_query', Input::old('ldap_auth_filter_query', $setting->ldap_auth_filter_query), array('class' => 'form-control','placeholder' => '"uid="')) }}
                                @endif
                                {!! $errors->first('ldap_auth_filter_query', '<span class="alert-msg">:message</span>') !!}
                            </div>
                        </div>

                        <!-- LDAP Version -->
                        <div class="form-group {{ $errors->has('ldap_version') ? 'error' : '' }}">
                            <div class="col-md-3">
                                {{ Form::label('ldap_version', trans('admin/settings/general.ldap_version')) }}
                            </div>
                            <div class="col-md-9">
                                @if (config('app.lock_passwords')===true)
                                    {{ Form::text('ldap_version', Input::old('ldap_version', $setting->ldap_version), array('class' => 'form-control', 'disabled'=>'disabled','placeholder' => '3')) }}
                                @else
                                    {{ Form::text('ldap_version', Input::old('ldap_version', $setting->ldap_version), array('class' => 'form-control','placeholder' => '3')) }}
                                @endif
                                {!! $errors->first('ldap_version', '<span class="alert-msg">:message</span>') !!}
                            </div>
                        </div>

                        <!-- LDAP active flag -->
                        <div class="form-group {{ $errors->has('ldap_active_flag') ? 'error' : '' }}">
                            <div class="col-md-3">
                                {{ Form::label('ldap_active_flag', trans('admin/settings/general.ldap_active_flag')) }}
                            </div>
                            <div class="col-md-9">
                                @if (config('app.lock_passwords')===true)
                                    {{ Form::text('ldap_active_flag', Input::old('ldap_active_flag', $setting->ldap_active_flag), array('class' => 'form-control', 'disabled'=>'disabled','placeholder' => '')) }}
                                @else
                                    {{ Form::text('ldap_active_flag', Input::old('ldap_active_flag', $setting->ldap_active_flag), array('class' => 'form-control','placeholder' => '')) }}
                                @endif
                                {!! $errors->first('ldap_active_flag', '<span class="alert-msg">:message</span>') !!}
                            </div>
                        </div>

                        <!-- LDAP emp number -->
                        <div class="form-group {{ $errors->has('ldap_emp_num') ? 'error' : '' }}">
                            <div class="col-md-3">
                                {{ Form::label('ldap_emp_num', trans('admin/settings/general.ldap_emp_num')) }}
                            </div>
                            <div class="col-md-9">
                                @if (config('app.lock_passwords')===true)
                                    {{ Form::text('ldap_emp_num', Input::old('ldap_emp_num', $setting->ldap_emp_num), array('class' => 'form-control', 'disabled'=>'disabled','placeholder' => '')) }}
                                @else
                                    {{ Form::text('ldap_emp_num', Input::old('ldap_emp_num', $setting->ldap_emp_num), array('class' => 'form-control','placeholder' => '')) }}
                                @endif
                                {!! $errors->first('ldap_emp_num', '<span class="alert-msg">:message</span>') !!}
                            </div>
                        </div>

                        <!-- LDAP email -->
                        <div class="form-group {{ $errors->has('ldap_email') ? 'error' : '' }}">
                            <div class="col-md-3">
                                {{ Form::label('ldap_email', trans('admin/settings/general.ldap_email')) }}
                            </div>
                            <div class="col-md-9">
                                @if (config('app.lock_passwords')===true)
                                    {{ Form::text('ldap_email', Input::old('ldap_email', $setting->ldap_email), array('class' => 'form-control', 'disabled'=>'disabled','placeholder' => '')) }}
                                @else
                                    {{ Form::text('ldap_email', Input::old('ldap_email', $setting->ldap_email), array('class' => 'form-control','placeholder' => '')) }}
                                @endif
                                {!! $errors->first('ldap_email', '<span class="alert-msg">:message</span>') !!}
                            </div>
                        </div>


                        <!-- LDAP test -->
                        <div class="form-group {{ $errors->has('ldap_email') ? 'error' : '' }}">
                            <div class="col-md-3">
                                Test LDAP Connection
                            </div>
                            <div class="col-md-9">
                                <div id="ldaptestrow">
                                    <div class="col-md-8">
                                        <a class="btn btn-default btn-sm pull-left" id="ldaptest" style="margin-right: 10px;"> Test LDAP</a>
                                        <span id="ldaptesticon"></span>
                                        <span id="ldaptestresult"></span>
                                        <span id="ldapteststatus"></span>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>

                </div> <!--/.box-body-->
                <div class="box-footer">
                    <div class="text-left col-md-6">
                        <a class="btn btn-link text-left" href="{{ route('settings.index') }}">{{ trans('button.cancel') }}</a>
                    </div>
                    <div class="text-right col-md-6">
                        <button type="submit" class="btn btn-success"><i class="fa fa-check icon-white"></i> {{ trans('general.save') }}</button>
                    </div>

                </div>
            </div> <!-- /box -->




        </div> <!-- /.col-md-8-->
    </div> <!-- /.row-->

    {{Form::close()}}
   @endif

@stop

@section('moar_scripts')
    <script>
        $("#ldaptest").click(function(){
            $("#ldaptestrow").removeClass('success');
            $("#ldaptestrow").removeClass('danger');
            $("#ldapteststatus").html('');
            $("#ldaptesticon").html('<i class="fa fa-spinner spin"></i>');
            $.ajax({
                url: '{{ route('api.settings.ldaptest') }}',
                type: 'GET',
                data: {},
                dataType: 'json',

                success: function (data) {
                    // console.dir(data);
                    //console.log(data.responseJSON.message);
                    $("#ldaptesticon").html('');
                    $("#ldaptestrow").addClass('success');
                    $("#ldapteststatus").html('<i class="fa fa-check text-success"></i> It worked!');
                },

                error: function (data) {
                    console.dir(data);
                    console.log(data.responseJSON.message);
                    $("#ldaptesticon").html('');
                    $("#ldaptestrow").addClass('danger');
                    $("#ldaptesticon").html('<i class="fa fa-exclamation-triangle text-danger"></i>');
                    $('#ldapteststatus').text(data.responseJSON.message);
                }


            });
        });



    </script>
@stop
