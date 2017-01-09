@php $page_title='Shows' @endphp
@extends('layouts.admin')
@section('title', 'Shows' )

@section('styles') 
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="/themes/admin/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
<link href="/themes/admin/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->
@endsection

@section('content') 
    <!-- BEGIN PAGE HEADER-->   
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{$page_title}} 
        <small> - List, add, edit and remove shows.</small>
    </h1>
    <!-- END PAGE TITLE-->    
    <!-- BEGIN EXAMPLE TABLE PORTLET-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject bold uppercase"> {{strtoupper($page_title)}} LIST </span>
                    </div>
                    <div class="actions">
                        <div class="btn-group">
                            <button id="btn_model_search" class="btn sbold grey-salsa" data-toggle="modal" data-target="#modal_model_search"> Search 
                                <i class="fa fa-search"></i>
                            </button>
                            <button id="btn_model_add" class="btn sbold bg-green" disabled="true"> Add 
                                <i class="fa fa-plus"></i>
                            </button>
                            <button id="btn_model_edit" class="btn sbold bg-yellow" disabled="true"> Edit 
                                <i class="fa fa-edit"></i>
                            </button>
                            <button id="btn_model_remove" class="btn sbold bg-red" disabled="true"> Remove 
                                <i class="fa fa-remove"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable" id="tb_model">
                        <thead>
                            <tr>
                                <th width="2%">
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="group-checkable" data-set="#tb_model .checkboxes" />
                                        <span></span>
                                    </label>
                                </th>
                                <th width="82%"> Name </th>
                                <th width="8%"> Category </th>
                                <th width="3%"> Featured </th>
                                <th width="5%"> Status </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shows as $index=>$s)
                            <tr>
                                <td width="2%">
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" id="{{$s->id}}" value="{{$s->name}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td class="search-item clearfix" width="82%"> 
                                    <div class="search-content col-md-3"> 
                                        @if(preg_match('/\/uploads\//',$s->image_url)) @php $s->image_url = env('IMAGE_URL_OLDTB_SERVER').$s->image_url @endphp @endif
                                        @if(preg_match('/\/s3\//',$s->image_url)) @php $s->image_url = env('IMAGE_URL_AMAZON_SERVER').str_replace('/s3/','/',$s->image_url) @endphp @endif
                                        <center style="color:red;"><i><b><a data-toggle="modal" href="#modal_details_{{$s->id}}"><img alt="- No image -" height="100px" width="200px" src="{{$s->image_url}}"/></a></b></i></center>
                                    </div>
                                    <div class="search-content col-md-9">
                                        <h4 class="search-title"><b><a data-toggle="modal" href="#modal_details_{{$s->id}}">{{$s->name}}</a></b> [<a href="https://www.ticketbat.com/event/{{$s->slug}}" target="_blank">{{$s->slug}}</a>]</h4>
                                        <small><i>
                                            @if($s->url)Web Site: <a href="{{$s->url}}" target="_blank">{{$s->url}} </a>@endif
                                            @if($s->googleplus)Google+: <a href="{{$s->googleplus}}" target="_blank">{{$s->googleplus}} </a>@endif
                                            @if($s->youtube)YouTube: <a href="{{$s->youtube}}" target="_blank">{{$s->youtube}} </a>@endif 
                                            @if($s->facebook)Facebook: <a href="{{$s->facebook}}" target="_blank">{{$s->facebook}} </a>@endif 
                                            @if($s->twitter)Twitter: <a href="{{$s->twitter}}" target="_blank">{{$s->twitter}} </a>@endif 
                                            @if($s->yelpbadge)MySpace: <a href="{{$s->yelpbadge}}" target="_blank">{{$s->yelpbadge}} </a>@endif 
                                            @if($s->instagram)Instagram: <a href="{{$s->instagram}}" target="_blank">{{$s->instagram}} </a>@endif 
                                        </i></small><br>
                                        @if($s->short_description) {{$s->short_description}} @else <i style="color:red"><b>- No short description -</b></i> @endif 
                                    </div>
                                </td>
                                <td width="8%"><center> {{$s->category}} </center></td>
                                <td width="3%"><center> <span class="label label-sm sbold
                                    @if($s->is_featured) label-success"> Yes 
                                    @else label-danger"> No 
                                    @endif
                                    </center></span> 
                                </td>
                                <td width="5%"><center> <span class="label label-sm sbold
                                    @if($s->is_active) label-success"> Active 
                                    @else label-danger"> Inactive 
                                    @endif
                                    </center></span> 
                                </td>
                            </tr>
                            <!-- BEGIN DETAILS MODAL--> 
                            <!--{{-- 
                            <div id="modal_details_{{$s->id}}" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog">
                                    <div class="modal-content portlet">
                                        <div id="modal_model_update_header" class="modal-header">
                                            <h4 class="modal-title bold uppercase"><center>{{$s->name}}</center></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="portlet light ">
                                                <div class="portlet-title">
                                                    <center style="color:red;"><i><b><img alt="- No image -" height="200px" width="500px" src="{{$s->image_url}}"/></b></i></center>
                                                </div>
                                                <div class="portlet-body">
                                                    <ul class="chats">
                                                        <li class="in">
                                                            <div class="avatar">Slug</div>
                                                            <div class="message">
                                                                <span class="arrow"> </span>
                                                                <span class="body"> <a href="https://www.ticketbat.com/event/{{$s->slug}}" target="_blank">{{$s->slug}}</a> </span>
                                                            </div>
                                                        </li>
                                                        <li class="in">
                                                            <div class="avatar">Location</div>
                                                            <div class="message">
                                                                <span class="arrow"> </span>
                                                                <span class="body"> 
                                                                    Venue: <b>{{$s->venue_name}}</b><br>
                                                                    Stage: <b>{{$s->stage_name}}</b><br>
                                                                    Restrictions: <b>{{$s->restrictions}}</b>
                                                                </span>
                                                            </div>
                                                        </li>
                                                        <li class="in">
                                                            <div class="avatar">Category</div>
                                                            <div class="message">
                                                                <span class="arrow"> </span>
                                                                <span class="body"> {{$s->category}} </span>
                                                            </div>
                                                        </li>
                                                        <li class="in">
                                                            <div class="avatar">Social Media</div>
                                                            <div class="message">
                                                                <span class="arrow"> </span>
                                                                <span class="body"> 
                                                                    @if($s->url)Web Site: <a href="{{$s->url}}" target="_blank">{{$s->url}} </a>@endif
                                                                    @if($s->googleplus)Google+: <a href="{{$s->googleplus}}" target="_blank">{{$s->googleplus}} </a>@endif
                                                                    @if($s->youtube)YouTube: <a href="{{$s->youtube}}" target="_blank">{{$s->youtube}} </a>@endif 
                                                                    @if($s->facebook)Facebook: <a href="{{$s->facebook}}" target="_blank">{{$s->facebook}} </a>@endif 
                                                                    @if($s->twitter)Twitter: <a href="{{$s->twitter}}" target="_blank">{{$s->twitter}} </a>@endif 
                                                                    @if($s->yelpbadge)MySpace: <a href="{{$s->yelpbadge}}" target="_blank">{{$s->yelpbadge}} </a>@endif 
                                                                    @if($s->instagram)Instagram: <a href="{{$s->instagram}}" target="_blank">{{$s->instagram}} </a>@endif 
                                                                    @if(!$s->url && !$s->googleplus && !$s->youtube && !$s->facebook && !$s->twitter && !$s->yelpbadge && !$s->instagram) <i style="color:red"><b>- No social media links -</b></i> @endif
                                                                </span>
                                                            </div>
                                                        </li>
                                                        <li class="in">
                                                            <div class="avatar">Sponsorship</div>
                                                            <div class="message">
                                                                <span class="arrow"> </span>
                                                                <span class="body"> 
                                                                    @if($s->sponsor)Sponsor: <b>{{$s->sponsor}}</b> <br>@endif
                                                                    @if($s->presented_by)Presented By: <b>{{$s->presented_by}}</b>@endif
                                                                    @if(!$s->sponsor && !$s->presented_by && !$s->sponsor_logo_id) <i style="color:red"><b>- No sponsorship -</b></i> @endif
                                                                </span>
                                                            </div>
                                                        </li>
                                                        <li class="in">
                                                            <div class="avatar">Feature</div>
                                                            <div class="message">
                                                                <span class="arrow"> </span>
                                                                <span class="body"> 
                                                                    Featured?: <b>@if($s->is_featured) Yes @else No @endif</b><br>
                                                                    Active?: <b>@if($s->is_active) Yes @else No @endif</b><br>
                                                                    Enable print tickets?: <b>@if($s->printed_tickets) Yes @else No @endif</b><br>
                                                                    On Sale: <b>@if($s->on_sale && $s->on_sale!='0000-00-00 00:00:00') {{date('m/d/Y g:ia',strtotime($s->on_sale))}} @else - @endif</b><br>
                                                                </span>
                                                            </div>
                                                        </li>
                                                        <li class="in">
                                                            <div class="avatar">Short Description</div>
                                                            <div class="message">
                                                                <span class="arrow"> </span>
                                                                <span class="body"> @if($s->short_description) {{$s->short_description}} @else <i style="color:red"><b>- No short description -</b></i> @endif </span>
                                                            </div>
                                                        </li>
                                                        <li class="in">
                                                            <div class="avatar">Full Description</div>
                                                            <div class="message">
                                                                <span class="arrow"> </span>
                                                                <span class="body"> @if($s->description) {{$s->description}} @else <i style="color:red"><b>- No description -</b></i> @endif </span>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div> 
                                            </div>
                                            <div class="row">
                                                <div class="modal-footer">
                                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}-->
                            <!-- END DETAILS MODAL--> 
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
        <div class="modal-dialog" style="width:1000px !important;">
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
                            <div class="tabbable-line">
                                <ul class="nav nav-tabs">
                                    <li class="active">
                                        <a href="#tab_model_update_general" data-toggle="tab" aria-expanded="false"> General </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_sponsor" data-toggle="tab" aria-expanded="true"> Sponsorship </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_reports" data-toggle="tab" aria-expanded="true"> Reports </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_checking" data-toggle="tab" aria-expanded="false"> Checking </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_passwords" data-toggle="tab" aria-expanded="false"> Passwords </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_showtimes" data-toggle="tab" aria-expanded="false"> Showtimes </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_tickets" data-toggle="tab" aria-expanded="false"> Tickets </a>
                                    </li>
                                    <li class="">
                                        <a href="#tab_model_update_bands" data-toggle="tab" aria-expanded="false"> Bands </a>
                                    </li>
                                    <li class="dropdown">
                                        <a href="#tab_model_update_multimedia" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> Multimedia
                                            <i class="fa fa-angle-down"></i>
                                        </a>
                                        <ul class="dropdown-menu pull-right">
                                            <li class="">
                                                <a href="#tab_model_update_images" data-toggle="tab"> Images </a>
                                            </li>
                                            <li class="">
                                                <a href="#tab_model_update_banners" data-toggle="tab"> Banners </a>
                                            </li>
                                            <li class="">
                                                <a href="#tab_model_update_videos" data-toggle="tab"> Videos </a>
                                            </li>
                                            <li class="">
                                                <a href="#tab_model_update_reviews" data-toggle="tab"> Reviews </a>
                                            </li>
                                            <li class="">
                                                <a href="#tab_model_update_awards" data-toggle="tab"> Awards </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="tab_model_update_general">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="control-label">
                                                    <span class="required"> General </span>
                                                </label><hr>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Name
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <input type="text" name="name" class="form-control" placeholder="My Show" /> 
                                                    </div>
                                                    <label class="control-label col-md-3">Slug
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <input type="text" name="slug" class="form-control" readonly="true" /> 
                                                    </div>
                                                    <label class="control-label col-md-3">Category
                                                    <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <select class="form-control" name="category_id">
                                                            @foreach($categories as $index=>$c)
                                                                @if($c->id_parent == 0)
                                                                    <option value="{{$c->id}}">{{$c->name}}</option>
                                                                    @foreach ($c->children()->get() as $children)
                                                                        <option value="{{$children->id}}">&nbsp;&nbsp;-&nbsp;&nbsp;{{$children->name}}</option>
                                                                        @foreach ($children->children()->get() as $niece)
                                                                            <option value="{{$niece->id}}">&nbsp;&nbsp;-&nbsp;&nbsp;-&nbsp;&nbsp;{{$niece->name}}</option>
                                                                        @endforeach
                                                                    @endforeach
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <label class="control-label col-md-3">Venue
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <select class="form-control" name="venue_id">
                                                            @foreach($venues as $index=>$v)
                                                            <option class="{{$v->restrictions}}" value="{{$v->id}}">{{$v->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div> 
                                                    <label class="control-label col-md-3">Stage
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <select class="form-control" name="stage_id">
                                                            @foreach($stages as $index=>$t)
                                                                <option style = "display:@if(isset($venues[0]) && $venues[0]->id == $t->venue_id) block @else none @endif ;"                         class="venue_{{$t->venue_id}}" value="{{$t->id}}">{{$t->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div> 
                                                    <label class="control-label col-md-3">Restriction
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <select class="form-control" name="restrictions">
                                                            @foreach($restrictions as $index=>$r)
                                                            @if(isset($venues[0]) && $venues[0]->restrictions == $r)
                                                                <option selected value="{{$r}}">{{$r}} - Venue default</option>
                                                            @else
                                                                <option value="{{$r}}">{{$r}} - WARNING: Not venue default</option>
                                                            @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <label class="control-label col-md-3">On sale</label>
                                                    <div class="col-md-9">
                                                        <div id="on_sale_date" class="input-group date form_datetime dtpicker">
                                                            <input size="16" readonly="" class="form-control" type="text" name="on_sale" value="{{date('Y-m-d H:i')}}">
                                                            <span class="input-group-btn">
                                                                <button class="btn default date-set" type="button">
                                                                    <i class="fa fa-calendar"></i>
                                                                </button>
                                                                <button class="btn default" type="button" id="clear_onsale_date">
                                                                    <i class="fa fa-remove"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <label class="control-label col-md-3">Cutoff Hours</label>
                                                    <div class="col-md-9">
                                                        <input type="text" value="1" name="cutoff_hours" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 "> 
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Status</label>
                                                    <div class="col-md-9">
                                                        <input type="hidden" name="is_active" value="0"/>
                                                        <input type="checkbox" class="make-switch" name="is_active" data-size="small" value="1" data-on-text="Active" data-off-text="Inactive" data-on-color="primary" data-off-color="danger">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="control-label">
                                                    <span class="required"> Social Media & Others </span>
                                                </label><hr>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Web Site
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <input type="text" name="url" class="form-control" placeholder="https://www.myshow.com" /> 
                                                    </div> 
                                                    <label class="col-md-3 control-label">
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <button type="button" id="btn_load_social_media" class="btn btn-block sbold dark btn-outline">Get Media From Web Site</button>
                                                    </div> 
                                                    <label class="col-md-3 control-label">Youtube
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <input type="text" name="youtube" class="form-control" placeholder="https://www.youtube.com/user/myshow" /> 
                                                    </div>
                                                    <label class="col-md-3 control-label">Facebook
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <input type="text" name="facebook" class="form-control" placeholder="https://www.facebook.com/myshow" /> 
                                                    </div>
                                                    <label class="col-md-3 control-label">Twitter
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <input type="text" name="twitter" class="form-control" placeholder="https://twitter.com/myshow" /> 
                                                    </div>
                                                    <label class="col-md-3 control-label">Google+
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <input type="text" name="googleplus" class="form-control" placeholder="https://googleplus.com/myshow" /> 
                                                    </div>
                                                    <label class="col-md-3 control-label">YelpBadge
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <input type="text" name="yelpbadge" class="form-control" placeholder="https://yelpbadge.com/myshow" /> 
                                                    </div>
                                                    <label class="col-md-3 control-label">Instagram
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <input type="text" name="instagram" class="form-control" placeholder="https://www.instagram.com/myshow" /> 
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Featured</label>
                                                    <div class="col-md-2">
                                                        <input type="hidden" name="is_featured" value="0"/>
                                                        <input type="checkbox" class="make-switch" name="is_featured" data-size="small" value="100" data-on-text="ON" data-off-text="OFF" data-on-color="primary" data-off-color="danger">
                                                    </div>
                                                    <label class="control-label col-md-4">Able Print Ticket</label>
                                                    <div class="col-md-3">
                                                        <input type="hidden" name="printed_tickets" value="0"/>
                                                        <input type="checkbox" class="make-switch input-large" name="printed_tickets" data-size="small" value="1" data-on-text="ON" data-off-text="OFF" data-on-color="primary" data-off-color="danger">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <label class="control-label">
                                            <span class="required"> Descriptions </span>
                                        </label><hr>
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
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_sponsor">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Image
                                                    </label>
                                                    <div class="col-md-9 show-error" >
                                                        <center>
                                                            <input type="hidden" name="sponsor_logo_id"/>
                                                            <button type="button" id="btn_sponsor_upload_image" class="btn btn-block sbold dark btn-outline" >Upload New Image</button>
                                                            <img name="sponsor_logo_id" alt="- No image -" src="" width="323px" height="270px" />
                                                        </center>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Sponsor
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <input type="text" name="sponsor" class="form-control"/> 
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Presented by
                                                    </label>
                                                    <div class="col-md-9 show-error">
                                                        <input type="text" name="presented_by" class="form-control"  /> 
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_reports">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label" style="padding-left:30px">Email for Individual Sales and Manifests Reports:
                                                    </label>
                                                    <div class="show-error" style="padding-left:30px">
                                                        <input type="text" name="emails" class="form-control" placeholder="abc@ticketbat.com,def@redmercuryent.com" /> 
                                                    </div>
                                                    <label class="control-label" style="padding-left:30px">Email for Daily Accounting: 
                                                    </label>
                                                    <div class="show-error" style="padding-left:30px">
                                                        <input type="text" name="accounting_email" class="form-control" placeholder="abc@ticketbat.com,def@redmercuryent.com" /> 
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                <label class="control-label col-md-9">Send individual order emails</label>
                                                <div class="col-md-3">
                                                    <input type="hidden" name="individual_emails" value="0"/>
                                                    <input type="checkbox" class="make-switch" name="individual_emails" data-size="small" value="1" data-on-text="ON" data-off-text="OFF" data-on-color="primary" data-off-color="danger">
                                                </div>
                                                <label class="control-label col-md-9">Send manifest emails</label>
                                                <div class="col-md-3">
                                                    <input type="hidden" name="manifest_emails" value="0"/>
                                                    <input type="checkbox" class="make-switch" name="manifest_emails" data-size="small" value="1" data-on-text="ON" data-off-text="OFF" data-on-color="primary" data-off-color="danger">
                                                </div>
                                                <label class="control-label col-md-9">Send daily sales emails</label>
                                                <div class="col-md-3">
                                                    <input type="hidden" name="daily_sales_emails" value="0"/>
                                                    <input type="checkbox" class="make-switch" name="daily_sales_emails" data-size="small" value="1" data-on-text="ON" data-off-text="OFF" data-on-color="primary" data-off-color="danger">
                                                </div>
                                                <label class="control-label col-md-9">Send financial report emails</label>
                                                <div class="col-md-3">
                                                    <input type="hidden" name="financial_report_emails" value="0"/>
                                                    <input type="checkbox" class="make-switch" name="financial_report_emails" data-size="small" value="1" data-on-text="ON" data-off-text="OFF" data-on-color="primary" data-off-color="danger">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_checking">
                                        <label class="control-label">
                                            <span class="required"> American Express Card Checking </span>
                                        </label><hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Date range:
                                                    </label>
                                                    <div class="input-group col-md-9" id="amex_only_date">
                                                        <input type="text" class="form-control" name="amex_only_start_date" readonly="true">
                                                        <span class="input-group-addon"> to </span>
                                                        <input type="text" class="form-control" name="amex_only_end_date" readonly="true">
                                                        <span class="input-group-btn">
                                                            <button class="btn default date-range-toggle" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                            <button class="btn default" type="button" id="clear_amex_only_date">
                                                                <i class="fa fa-remove"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="col-md-3 control-label">Ticket types:
                                                </label>
                                                <div class="col-md-9 mt-checkbox-inline ticket_types_lists">
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_passwords">
                                        <div class="row" style="padding-right:20px">
                                            <input type="button" value=" + " style="font-size:18px"  class="btn sbold bg-green pull-right" id="btn_model_password_add" /> 
                                        </div>
                                        <div class="row table-responsive" style="padding-left:20px;max-height:400px;overflow-y: auto;">
                                            <table class="table table-striped table-hover table-bordered" >
                                                <thead>
                                                    <tr>
                                                        <th> Password </th>
                                                        <th> Date Start </th>
                                                        <th> Date End </th>
                                                        <th> Ticket Types </th>
                                                        <th> </th>
                                                        <th> </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tb_show_passwords">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_showtimes">
                                        
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_tickets">
                                        <div class="row" style="padding-right:20px">
                                            <input type="button" value=" + " style="font-size:18px"  class="btn sbold bg-green pull-right" id="btn_model_ticket_add" /> 
                                        </div>
                                        <div class="row table-responsive" style="padding-left:20px;max-height:400px;overflow-y: auto;">
                                            <table class="table table-striped table-hover table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th> Ticket Type </th>
                                                        <th> Package </th>
                                                        <th> Retail Price </th>
                                                        <th> P.Fee($) </th>
                                                        <th> P.Fee(%) </th>
                                                        <th> Comm.(%) </th>
                                                        <th> Default? </th>
                                                        <th> Max Tickets </th>
                                                        <th> Status </th>
                                                        <th> </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tb_show_tickets">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_bands">
                                        
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_images">
                                        
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_banners">
                                        
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_videos">
                                        
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_reviews">
                                        
                                    </div>
                                    <div class="tab-pane" id="tab_model_update_awards">
                                        
                                    </div>
                                </div>
                            </div> 
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
    <!-- BEGIN SEARCH MODAL--> 
    <div id="modal_model_search" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:400px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Search Panel</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" action="/admin/shows" id="form_model_search">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label for="venue" class="col-md-5"> <span>Venue:</span> </label>
                                    <select class="table-group-action-input form-control input-inline input-small input-sm col-md-7" name="venue" style="width:200px !important">
                                        <option selected value="">All</option>
                                        @foreach($venues as $index=>$v)
                                        <option @if($v->id==$venue) selected @endif value="{{$v->id}}">{{$v->name}}</option>
                                        @endforeach
                                    </select>
                                </div>    
                                <div class="form-group">
                                    <label for="showtime" class="col-md-5"> <span>Show Time:</span> </label>
                                    <select class="table-group-action-input form-control input-inline input-small input-sm col-md-7" name="showtime" style="width:100px !important">
                                        <option @if($showtime=='A') selected @endif value="A">All</option>
                                        <option @if($showtime=='P') selected @endif value="P">Passed</option>
                                        <option @if($showtime=='U') selected @endif value="U">Upcoming</option>
                                    </select>
                                </div>    
                                <div class="form-group">
                                    <label for="status" class="col-md-5"> <span>Status:</span> </label>
                                    <select class="table-group-action-input form-control input-inline input-small input-sm col-md-7" name="status" style="width:90px !important">
                                        <option @if($status==1) selected @endif value="1">Active</option>
                                        <option @if($status==0) selected @endif value="0">Inactive</option>
                                    </select>
                                </div>    
                                <div class="form-group">
                                    <label for="onlyerrors" class="col-md-5"> <span>Only With Error:</span> </label>
                                    <select class="table-group-action-input form-control input-inline input-small input-sm col-md-7" name="onlyerrors" style="width:65px !important">
                                        <option @if($onlyerrors==0) selected @endif value="0">No</option>
                                        <option @if($onlyerrors==1) selected @endif value="1">Yes</option>
                                    </select>
                                </div>   
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline" onclick="$('#form_model_search').trigger('reset')">Cancel</button>
                                    <button type="submit" class="btn sbold grey-salsa" onclick="$('#modal_model_search').modal('hide'); swal({
                                                                                                    title: 'Searching information',
                                                                                                    text: 'Please, wait.',
                                                                                                    type: 'info',
                                                                                                    showConfirmButton: false
                                                                                                });" >Search</button>
                                </div>
                            </div>
                        </div>
                    </form> 
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END SEARCH MODAL--> 
    <!-- BEGIN ADD/EDIT PASSWORD MODAL--> 
    <div id="modal_model_show_passwords" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:500px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Add/Edit Password</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_show_passwords">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                        <input type="hidden" name="show_id" value="" />
                        <input type="hidden" name="id" value="" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Password
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <input type="text" name="password" class="form-control" required="true" /> 
                                    </div>
                                    <label class="control-label col-md-3">Date range:
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group" id="show_passwords_date">
                                            <input type="text" class="form-control" name="start_date" readonly="true" required="true">
                                            <span class="input-group-addon"> to </span>
                                            <input type="text" class="form-control" name="end_date" readonly="true" required="true">
                                            <span class="input-group-btn">
                                                <button class="btn default date-range-toggle" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                    <label class="col-md-3 control-label">Ticket types
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-9 ticket_types_lists">
                                    </div> 
                                </div>  
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline" onclick="$('#form_model_show_passwords').trigger('reset')">Cancel</button>
                                    <button type="button" id="submit_model_show_passwords" class="btn sbold grey-salsa">Save</button>
                                </div>
                            </div>
                        </div>
                    </form> 
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END ADD/EDIT PASSWORD MODAL--> 
    <!-- BEGIN ADD/EDIT TICKET MODAL--> 
    <div id="modal_model_show_tickets" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="width:500px !important;">
            <div class="modal-content portlet">
                <div class="modal-header alert-block bg-grey-salsa">
                    <h4 class="modal-title bold uppercase" style="color:white;"><center>Add/Edit Ticket</center></h4>
                </div>
                <div class="modal-body">
                    <!-- BEGIN FORM-->
                    <form method="post" id="form_model_show_tickets">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                        <input type="hidden" name="show_id" value="" />
                        <input type="hidden" name="id" value="" />
                        <div class="form-body">
                            <div class="row">
                                <div class="form-group">
                                    <label class="control-label col-md-5">Ticket Type
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-7 show-error">
                                        <select class="form-control" name="ticket_type">
                                            @foreach($ticket_types as $index=>$tt)
                                                <option value="{{$index}}">{{$tt}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label class="control-label col-md-5">Package
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-7 show-error">
                                        <select class="form-control" name="package_id">
                                            @foreach($packages as $index=>$p)
                                                <option value="{{$p->id}}">{{$p->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label class="col-md-5 control-label">Retail Price
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-7 show-error">
                                        <input type="text" value="0" name="retail_price" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 "> 
                                    </div> 
                                    <label class="col-md-5 control-label">Amount (0 unlimited)
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-7 show-error">
                                        <input type="text" value="0" name="max_tickets" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 "> 
                                    </div> 
                                    <label class="col-md-5 control-label">Proccessing Fee ($)
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-7 show-error">
                                        <input type="text" value="0" name="processing_fee" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 "> 
                                    </div> 
                                    <label class="col-md-5 control-label">Proccessing Fee (%)
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-7 show-error">
                                        <input type="text" value="0" name="percent_pf" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 "> 
                                    </div> 
                                    <label class="col-md-5 control-label">Commission (%)
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-7 show-error">
                                        <input type="text" value="0" name="percent_commission" width="100px" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 0 "> 
                                    </div> 
                                    <label class="control-label col-md-5">Make default ticket
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-7">
                                        <input type="hidden" name="is_default" value="0"/>
                                        <input type="checkbox" class="make-switch" name="is_default" data-size="small" value="1" data-on-text="Default" data-off-text="Not Default" data-on-color="primary" data-off-color="danger">
                                    </div>
                                    <label class="control-label col-md-5">Status
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-7">
                                        <input type="hidden" name="is_active" value="0"/>
                                        <input type="checkbox" class="make-switch" name="is_active" data-size="small" value="1" data-on-text="Active" data-off-text="Inactive" data-on-color="primary" data-off-color="danger">
                                    </div>
                                </div>  
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline" onclick="$('#form_model_show_tickets').trigger('reset')">Cancel</button>
                                    <button type="button" id="submit_model_show_tickets" class="btn sbold grey-salsa">Save</button>
                                </div>
                            </div>
                        </div>
                    </form> 
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END ADD/EDIT TICKET MODAL--> 
@endsection

@section('scripts') 
<script src="/themes/admin/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js" type="text/javascript"></script>
<script src="/themes/admin/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script src="/js/admin/shows/index.js" type="text/javascript"></script>
@endsection