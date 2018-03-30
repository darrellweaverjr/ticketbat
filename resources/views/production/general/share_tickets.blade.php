<!-- BEGIN SHARE TICKETS MODAL -->
<div id="modal_share_tickets" class="modal fade" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h3 class="modal-title">Share tickets</h3>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    To send tickets to someone, choose the number of tickets you would like to assign them and then click "New Person".<br>
                    In the new fields that appear, please enter the first name, last name and email address of the person to whom you would like to send the tickets. 
                </div>
                <div class="form-group" title="Select the quantity of tickets and then add a person.">
                    <label class="col-md-4 control-label text-right">Assign</label>
                    <div class="col-md-3">
                        <select class="form-control" id="share_tickets_availables">
                            <option>1 ticket(s) available(s)</option>
                        </select>
                    </div>
                    <button type="button" id="new_person_share" class="btn btn-primary">to a new person</button>
                </div>
                <!-- BEGIN FORM-->
                <form method="post" id="form_share_tickets" class="form-horizontal">
                    <input type="hidden" name="purchases_id" value="">
                    <div class="form-body">
                        <table class="table table-striped table-bordered table-hover table-header-fixed">
                            <thead>
                                <tr class="uppercase">
                                    <th width="20%">First Name</th>
                                    <th width="20%">Last Name</th>
                                    <th width="20%">Email</th>
                                    <th width="8%">Qty</th>
                                    <th width="27%">Comment</th>
                                    <th width="3%">Delete</th>
                                </tr>
                            </thead>
                            <tbody id="tb_shared_tickets_body">
                            </tbody>
                        </table>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn dark btn-outline">Close</button>
                <button type="button" id="btn_share_tickets" class="btn bg-green btn-outline" title="Share your tickets now.">Save</button>
            </div>
        </div>
    </div>
</div>
<!-- END SHARE TICKETS MODAL -->