@php $page_title='Manifests' @endphp
@extends('layouts.admin')
@section('title', 'Manifests' )

@section('styles') 
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content') 
    <!-- BEGIN PAGE HEADER-->   
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{$page_title}} 
        <small> - List and view manifests.</small>
    </h1>
    <!-- END PAGE TITLE-->    
    <!-- BEGIN EXAMPLE TABLE PORTLET-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject bold uppercase"> {{strtoupper($page_title)}} EMAILS </span>
                    </div>
                    <div class="actions">                        
                        <div class="btn-group">
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable" id="tb_model">
                        <thead>
                            <tr>
                                <th width="88%"> Manifest </th>
                                <th width="10%"> Date </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($manifests as $index=>$me)
                            <tr>
                                <td class="search-item clearfix" width="88%"> 
                                    <div class="search-content col-md-2">                                        
                                        <h4 class="search-title sbold"><a>{{$show_times[$me[0]->show_time_id]->name}}</a></h4>
                                    </div>
                                    @foreach($me as $index2=>$m)
                                    <div class="search-content col-md-5" style="padding-left:35px;text-align:left">
                                        <div><h5 class="search-title">
                                            <a class="col-md-7 @if($index2==0) bg-green @else green @endif  btn green-sharp sbold uppercase">{{$m->manifest_type}}</a>
                                            <a class="col-md-2 btn blue-sharp btn-outline sbold uppercase">CSV</a>
                                            <a class="col-md-2 btn green-sharp btn-outline sbold uppercase">PDF</a>
                                        </h5></div><hr>
                                        <div><small><i>
                                        Purchases: <b>{{$m->num_purchases}}</b>, Tickets Sold: <b>{{$m->num_people}}</b>, Sent at: <b>{{date('l, m/d/Y g:ia',strtotime($m->created))}}</b><br>
                                        @php $emails = explode(',',$m->recipients) @endphp
                                        Receipts: (@foreach($emails as $e) <a href="mailto:{{$e}}" target="_top">{{$e}}</a> . @endforeach) 
                                        </i></small></div>
                                    </div>
                                    @endforeach 
                                </td>
                                <td width="10%"><center> {{date('l, m/d/Y g:ia',strtotime($show_times[$me[0]->show_time_id]->show_time))}} </center></td>
                            </tr>
                            @endforeach 
                        </tbody>
                    </table>
                </div>
            </div>            
        </div>
    </div>
    <!-- END EXAMPLE TABLE PORTLET-->   
    <!-- BEGIN UPDATE MODAL--> 
    <div id="modal_model_update" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:50% !important;">
            <div class="modal-content portlet">
                <div id="modal_model_update_header" class="modal-header alert-block bg-green">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center id="modal_model_update_title"></center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_update" class="form-horizontal">
                        <input name="id" type="hidden" value=""/>
                        <div class="form-body">
                            <div class="alert alert-danger display-hide">
                                <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                            <div class="alert alert-success display-hide">
                                <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Name
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="name" class="form-control" placeholder="My Band" /> </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Category
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <select class="form-control" name="category_id">
                                               
                                            </select>
                                        </div>    
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Image
                                        </label>
                                        <div class="col-md-9 show-error" >
                                            <center>
                                                <input type="hidden" name="image_url"/>
                                                <button type="button" id="btn_bands_upload_image" class="btn btn-block sbold dark btn-outline" >Upload New Image</button>
                                                <img name="image_url" alt="- No image -" src="" width="323px" height="270px" />
                                            </center>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Web Site
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="website" class="form-control" placeholder="https://www.myband.com" /> 
                                        </div> 
                                        <label class="col-md-3 control-label">
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <button type="button" id="btn_load_social_media" class="btn btn-block sbold dark btn-outline">Guess Media From WebSite</button>
                                        </div> 
                                        <label class="col-md-3 control-label">Youtube
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="youtube" class="form-control" placeholder="https://www.youtube.com/user/myband" /> 
                                        </div>
                                        <label class="col-md-3 control-label">Facebook
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="facebook" class="form-control" placeholder="https://www.facebook.com/myband" /> 
                                        </div>
                                        <label class="col-md-3 control-label">Twitter
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="twitter" class="form-control" placeholder="https://twitter.com/myband" /> 
                                        </div>
                                        <label class="col-md-3 control-label">My Space
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="my_space" class="form-control" placeholder="https://myspace.com/myband" /> 
                                        </div>
                                        <label class="col-md-3 control-label">Flickr
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="flickr" class="form-control" placeholder="https://flickr.com/myband" /> 
                                        </div>
                                        <label class="col-md-3 control-label">Instagram
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="instagram" class="form-control" placeholder="https://www.instagram.com/myband" /> 
                                        </div>
                                        <label class="col-md-3 control-label">SoundCloud
                                        </label>
                                        <div class="col-md-9 show-error">
                                            <input type="text" name="soundcloud" class="form-control" placeholder="https://soundcloud.com/myband" /> 
                                        </div>
                                    </div>
                                </div>
                            </div> 
                            <div class="row">
                                <label class="col-md-2 control-label">Short Descript.
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-10 show-error">
                                    <textarea name="short_description" class="form-control" rows="2"></textarea>
                                </div> 
                            </div>
                            <div class="row">
                                <label class="col-md-2 control-label">Description</label>
                                <div class="col-md-10 show-error">
                                    <textarea name="description" class="form-control" rows="5"></textarea>
                                </div>
                            </div>
<!--                            <div class="row">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th width="80%"> Show </th>
                                            <th width="20%"> Order </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td width="80%"> sdfdfdfdf </td>
                                            <td width="20%"> 1 </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>  -->
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                    <button type="button" id="btn_model_save" class="btn sbold bg-green">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END UPDATE MODAL--> 
@endsection

@section('scripts') 
<script src="/js/admin/manifests/index.js" type="text/javascript"></script>
@endsection