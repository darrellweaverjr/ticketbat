    <!-- BEGIN EXAMPLE TABLE PORTLET-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject bold uppercase">  LIST </span>
                    </div>
                    <div class="actions">
                        <div class="btn-group">
                            @if(in_array('Other',Auth::user()->user_type->getACLs()['ACLS']['permission_types']))
                            <button id="btn_model_run" disabled="true" class="btn sbold bg-purple"> Run
                                <i class="fa fa-tablet"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover table-checkable" id="tb_model_commands">
                        <thead>
                            <tr>
                                <th width="2%"> </th>
                                <th width="38%"> Command </th>
                                <th width="60%"> Parameters </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($commands as $index=>$c)
                            <tr>
                                <td>
                                    <label class="mt-radio mt-radio-single mt-radio-outline">
                                        <input type="radio" name="radios" value="{{$c['command']}}" />
                                        <span></span>
                                    </label>
                                </td>
                                <td> {{$c['command']}} </td>
                                <td>
                                    @foreach($c['values'] as $k=>$v)
                                    <label class="control-label col-md-1">{{$k}}</label>
                                    <div class="col-md-1 show-error">
                                        <input type="text" name="{{$k}}" class="input-mini form-control" value="{{$v}}" />
                                    </div>
                                    @endforeach
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- END EXAMPLE TABLE PORTLET-->