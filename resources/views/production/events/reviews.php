<!-- BEGIN CONTACT US MODAL -->
<div id="modal_write_review" class="modal fade" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h3 class="modal-title">Write a review</h3>
            </div>
            <div class="modal-body mt-element-ribbon" style="margin:10px">
                <!-- BEGIN FORM-->
                <form method="post" id="form_write_review" class="form-horizontal">
                    <div class="form-body">
                        <div class="alert alert-danger display-hide">
                            <button class="close" data-close="alert"></button> You have some form errors. Please check below. </div>
                        <div class="alert alert-success display-hide">
                            <button class="close" data-close="alert"></button> Your form validation is successful! </div>
                        <div class="row" style="margin-top:-15px">
                          <div class="form-group">
                              <label class="control-label col-md-3">Rating:
                                  <span class="required">*</span>
                              </label>
                              <div class="col-md-9">
                                  <input name="show_id" type="hidden" value="0"/>
                                  <input name="rating" type="hidden" value="0"/>
                                  <a data-star="5" title="5 stars" class="rating-star ribbon ribbon-vertical-right ribbon-shadow ribbon-color-warning">
                                      <div class="ribbon-sub ribbon-bookmark"></div>
                                      <i class="fa fa-star fa-star-o"></i>
                                  </a>
                                  <a data-star="4" title="4 stars" class="rating-star ribbon ribbon-vertical-right ribbon-shadow ribbon-color-warning">
                                      <div class="ribbon-sub ribbon-bookmark"></div>
                                      <i class="fa fa-star fa-star-o"></i>
                                  </a>
                                  <a data-star="3" title="3 stars" class="rating-star ribbon ribbon-vertical-right ribbon-shadow ribbon-color-warning">
                                      <div class="ribbon-sub ribbon-bookmark"></div>
                                      <i class="fa fa-star fa-star-o"></i>
                                  </a>
                                  <a data-star="2" title="2 stars" class="rating-star ribbon ribbon-vertical-right ribbon-shadow ribbon-color-warning">
                                      <div class="ribbon-sub ribbon-bookmark"></div>
                                      <i class="fa fa-star fa-star-o"></i>
                                  </a>
                                  <a data-star="1" title="1 star" class="rating-star ribbon ribbon-vertical-right ribbon-shadow ribbon-color-warning">
                                      <div class="ribbon-sub ribbon-bookmark"></div>
                                      <i class="fa fa-star fa-star-o"></i>
                                  </a>
                              </div>
                          </div>
                        </div>
                        <div class="row" style="margin-top:15px">
                            <div class="form-group">
                                <label class="control-label col-md-3">Comment:
                                    <span class="required">*</span>
                                </label>
                                <div class="col-md-9 show-error">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-text-width"></i>
                                        </span>
                                        <textarea name="review" rows="4" class="form-control" placeholder="Write your comment here..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="modal-footer">
                                <button type="button" data-dismiss="modal" class="btn dark btn-outline" title="Close form.">Close</button>
                                <button type="button" id="btn_review_send" class="btn bg-green btn-outline" title="Send comment.">Post</button>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
        </div>
    </div>
</div>
<!-- END CONTACT US MODAL -->
