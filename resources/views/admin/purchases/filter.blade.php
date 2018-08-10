<!-- BEGIN SEARCH MODAL-->
<div id="modal_model_search" class="modal fade" tabindex="1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content portlet">
            <div class="modal-header alert-block bg-grey-salsa">
                <h4 class="modal-title bold uppercase" style="color:white;"><center>Filter Panel</center></h4>
            </div>
            <div class="modal-body">
                <!-- BEGIN FORM-->
                <form method="post" action="{{$action}}" id="form_model_search">
                    <input type="hidden" name="_token" value="{{ Session::token() }}" />
                    <div class="form-body">
                        <div class="row" style="padding-right:40px">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Venue:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group">
                                            <select class="form-control" name="venue" style="width: 321px !important">
                                                <option selected value="">All</option>
                                                @foreach($search['venues'] as $index=>$v)
                                                <option @if($v->id==$search['venue']) selected @endif value="{{$v->id}}">{{$v->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Show:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group">
                                            <select class="form-control" name="show" style="width: 321px !important">
                                                <option selected value="">All</option>
                                                @foreach($search['shows'] as $index=>$s)
                                                    <option @if($s->id==$search['show']) selected @endif value="{{$s->id}}">{{$s->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Ticket:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group">
                                            <select class="form-control" name="ticket" style="width: 321px !important">
                                                <option selected value="">All</option>
                                                @foreach($search['tickets'] as $index=>$t)
                                                    <option @if($t->id==$search['ticket']) selected @endif value="{{$t->id}}">{{$t->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Show Time:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group input-large" id="show_times_date">
                                            <input type="text" class="form-control" name="showtime_start_date" value="{{$search['showtime_start_date']}}" readonly="true">
                                            <span class="input-group-addon"> to </span>
                                            <input type="text" class="form-control" name="showtime_end_date" value="{{$search['showtime_end_date']}}" readonly="true">
                                            <span class="input-group-btn">
                                                <button class="btn default date-range-toggle" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                                <button class="btn default" type="button" id="clear_show_times_date">
                                                    <i class="fa fa-remove"></i>
                                                </button>
                                            </span>
                                        </div>
                                        <div id="showtime_date_input" class="input-group input-large date form_datetime dtpicker">
                                            <input size="16" readonly="" class="form-control" type="text" name="showtime_date" value="{{$search['showtime_date']}}">
                                            <span class="input-group-btn">
                                                <button class="btn default date-set" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                                <button class="btn default" type="button" id="clear_onsale_date">
                                                    <i class="fa fa-remove"></i>
                                                </button>
                                            </span>
                                        </div>
                                        <div class="input-group">
                                            <input type="number" class="form-control input-large" name="showtime_id" value="{{$search['showtime_id']}}" placeholder="ID of the event time (showtime id)" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Channels:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group">
                                            <select class="form-control" name="channel" style="width: 321px !important">
                                                <option selected value="">All</option>
                                                @foreach($search['channels'] as $p)
                                                    <option @if(!empty($search['channel']) && $p==$search['channel']) selected @endif value="{{$p}}">{{$p}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">T.Type:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group">
                                            <select class="form-control" name="ticket_type" style="width: 321px !important">
                                                <option selected value="">All</option>
                                                @foreach($search['ticket_types'] as $index=>$tt)
                                                    <option @if(!empty($search['ticket_type']) && $index==$search['ticket_type']) selected @endif value="{{$index}}">{{$tt}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Status:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group">
                                            <select class="form-control" name="statu" style="width: 321px !important">
                                                <option selected value="">Not Voided</option>
                                                @foreach($search['status'] as $index=>$s)
                                                    <option @if(!empty($search['statu']) && $index==$search['statu']) selected @endif value="{{$index}}">{{$s}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Amount:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group input-large">
                                            <input type="number" class="form-control" name="start_amount" step="0.01" value="{{$search['start_amount']}}" placeholder="Start" />
                                            <span class="input-group-addon"> to </span>
                                            <input type="number" class="form-control" name="end_amount" step="0.01" value="{{$search['end_amount']}}" placeholder="End" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">User:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group input-large">
                                            <input type="text" class="form-control" name="first_name" value="{{$search['first_name']}}" placeholder="First name">
                                            <span class="input-group-addon">-</span>
                                            <input type="text" class="form-control" name="last_name" value="{{$search['last_name']}}" placeholder="Last name">
                                        </div>
                                        <div class="input-group input-large">
                                            <input type="text" class="form-control" name="user" value="{{$search['user']}}" placeholder="ID/Email of the user" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Customer:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group">
                                            <input type="text" class="form-control input-large" name="customer" value="{{$search['customer']}}" placeholder="ID/Email of the customer" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Order id:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group">
                                            <input type="number" class="form-control input-large" name="order_id" value="{{$search['order_id']}}" placeholder="ID of the order (purchase id)" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Invoice:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group">
                                            <input type="text" class="form-control input-large" name="invoice" value="{{$search['invoice']}}" placeholder="Invoice # (USAePay id)" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">AuthCode:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group">
                                            <input type="text" class="form-control input-large" name="authcode" value="{{$search['authcode']}}" placeholder="AuthCode of the transaction" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">RefNum:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group">
                                            <input type="text" class="form-control input-large" name="refnum" value="{{$search['refnum']}}" placeholder="RefNum of the transaction" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Trans.Date:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group input-large" id="sold_times_date">
                                            <input type="text" class="form-control" name="soldtime_start_date" value="{{$search['soldtime_start_date']}}" readonly="true">
                                            <span class="input-group-addon"></span>
                                            <input type="text" class="form-control" name="soldtime_end_date" value="{{$search['soldtime_end_date']}}" readonly="true">
                                            <span class="input-group-btn">
                                                <button class="btn default" type="button" id="clear_sold_times_date">
                                                    <i class="fa fa-remove"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @if(!empty($order))
                                <div class="form-group">
                                    <label class="control-label col-md-3">Order:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group">
                                            <select class="form-control" name="order" style="width: 321px !important">
                                                <option selected value="channel">Channel</option>
                                                <option @if($search['order']=='show') selected @endif value="show">Show</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="row" style="padding-right:40px">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Payment Type:</label>
                                    <div class="col-md-9 show-error">
                                        <div class="input-group mt-checkbox-inline">
                                            @foreach($search['payment_types'] as $index=>$p)
                                            @php if($p=='None') $p='Comp.' @endphp
                                            <label class="mt-checkbox">
                                                <input type="checkbox" @if(!empty($search['payment_type']) && in_array($index,$search['payment_type'])) checked="true" @endif name="payment_type[]" data-value="{{$p}}" value="{{$index}}" />{{$p}}
                                                <span></span>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>    
                        </div>
                        @if(!empty($printing))
                        <div class="row" style="padding:0 20px">
                            <label class="control-label">
                                <span class="required">Printing Settings</span>
                            </label><hr>
                            <div class="col-md-6">
                                <div class="form-group show-error">
                                    <div class="input-group mt-radio-list">
                                        <label class="mt-radio">
                                            <input type="radio" @if((!empty($search['mirror_type']) && $search['mirror_type']=='previous_period') || empty($search['mirror_type'])) checked="true" @endif name="mirror_type" value="previous_period" />Previous mirror type (default)
                                            <span></span>
                                        </label>
                                        <label class="mt-radio">
                                            <input type="radio" @if(!empty($search['mirror_type']) && $search['mirror_type']=='previous_year' ) checked="true" @endif name="mirror_type" value="previous_year" />Last years mirror period
                                            <span></span>
                                        </label>
                                        <input type="text" name="mirror_period" style="width:20px" @if(!empty($search['mirror_period'])) value="{{$search['mirror_period']}}" @else value="0" @endif />
                                        <label class="control-label">&nbsp;&nbsp;&nbsp;Qty of mirrors prior sold date period</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group show-error">
                                    <div class="input-group mt-checkbox-single">
                                        <label class="mt-checkbox">
                                            <input type="checkbox" @if(!empty($search['replace_chart'])) checked="true" @endif name="replace_chart" value="1" />
                                            <span></span> Replace sales table by chart
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group show-error">
                                    <div class="input-group mt-checkbox-single">
                                        <label class="mt-checkbox">
                                            <input type="checkbox" @if(!empty($search['coupon_report'])) checked="true" @endif name="coupon_report" value="1" />
                                            <span></span> Include Coupon's Report
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="modal-footer">
                                <button type="button" data-dismiss="modal" class="btn sbold dark btn-outline">Cancel</button>
                                <button type="submit" class="btn sbold grey-salsa">Search</button>
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