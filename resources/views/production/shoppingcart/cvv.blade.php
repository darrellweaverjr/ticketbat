<!-- BEGIN TERMS MODAL -->
<div id="modal_cvv" class="modal fade" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h3 class="modal-title">Information about CVV</h3>
            </div>
            <div class="modal-body">
                <div data-always-visible="1" data-rail-visible1="1">
                    <div class="row" style="padding:20px">
                        <div class="col-xs-12 col-sm-6">
                            <strong>Visa&reg;, Mastercard&reg;, and Discover&reg; cardholders:</strong>
                            <p>Turn your card over and look at the signature box. You should see either the entire 16-digit credit card number or
                                just the last four digits followed by a special 3-digit code. This 3-digit code is your CVV number / Card Security Code.</p>
                        </div>
                        <div class="col-xs-12  col-sm-6"><img height="125" src="{{config('app.theme')}}img/cvc_1.png" width="240" align="left" border="0" alt="cvv"></div>
                    </div>
                    <div class="row" style="padding:20px">
                        <div class="col-xs-12 col-sm-6">
                            <strong>American Express&reg; cardholders:</strong>
                            <p>Look for the 4-digit code printed on the front of your card just above and to the right of your main credit card number.<br>
                                This 4-digit code is your Card Identification Number (CID). The CID is the four-digit code printed just above the Account Number.</p>
                        </div>
                        <div class="col-xs-12 col-sm-6"><img height="133" src="{{config('app.theme')}}img/cvc_2.jpg" width="200" align="right" border="0" alt="cid"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn dark btn-outline">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- END TERMS MODAL -->