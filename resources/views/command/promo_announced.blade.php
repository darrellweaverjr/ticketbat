@if($format == 'announced')
    @foreach($promos['announced'] as $a)                
        <table class="mcnImageBlock" style="min-width:100%;" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tbody class="mcnImageBlockOuter">
                <tr>
                    <td style="padding:9px" class="mcnImageBlockInner" valign="top">
                    <table class="mcnImageContentContainer" style="min-width:100%;" align="left" border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tbody>
                            <tr>
                                <td class="mcnImageContent" style="padding-right: 9px; padding-left: 9px; padding-top: 0; padding-bottom: 0; text-align:center;" valign="top"><a href="/event/{{$a->slug}}" title="" class="" target="_blank"> <img alt="" src="{{$a->image_url}}" style="max-width:581px; padding-bottom: 0; display: inline !important; vertical-align: bottom;" class="mcnImage" align="middle" width="564"> </a></td>
                            </tr>
                        </tbody>
                    </table></td>
                </tr>
            </tbody>
        </table>
        <table class="mcnTextBlock" style="min-width:100%;" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tbody class="mcnTextBlockOuter">
                <tr>
                    <td class="mcnTextBlockInner" style="padding-top:9px;" valign="top">
                    <table style="max-width:100%; min-width:100%;" class="mcnTextContentContainer" align="left" border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tbody>
                            <tr>

                                <td class="mcnTextContent" style="padding-top:0; padding-right:18px; padding-bottom:9px; padding-left:18px;" valign="top">
                                <div style="text-align: left;">
                                    <span style="color:#4ea9df"> {{$a->show_time}} <a href="/event/{{$a->slug}}" target="_blank"><img src="https://gallery.mailchimp.com/ab5ce1d711eb623068ea21c6e/images/e352074a-bfae-4202-aad8-43a70726934c.jpg" style="width: 137px; height: 40px; margin: 0px; float: right;" height="40" align="none" width="137"></a></span>
                                    <br>
                                    <span style="color:#FFFFFF">@if($a->presented_by) {{$a->presented_by}} Presents @endif {{$a->name}}</span>
                                </div></td>
                            </tr>
                        </tbody>
                    </table><</td>
                </tr>
            </tbody>
        </table>
        <table class="mcnDividerBlock" style="min-width:100%;" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tbody class="mcnDividerBlockOuter">
                <tr>
                    <td class="mcnDividerBlockInner" style="min-width: 100%; padding: 10px 18px 25px;">
                    <table class="mcnDividerContent" style="min-width: 100%;border-top: 2px solid #333333;" border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tbody>
                            <tr>
                                <td><span></span></td>
                            </tr>
                        </tbody>
                    </table></td>
                </tr>
            </tbody>
        </table>                 
    @endforeach
@elseif($format == 'week')
    @foreach($promos['week'] as $w)                   
        <table class="mcnCaptionBlock" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tbody class="mcnCaptionBlockOuter">
                <tr>
                    <td class="mcnCaptionBlockInner" style="padding:9px;" valign="top">
                    <table class="mcnCaptionRightContentOuter" border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tbody>
                            <tr>
                                <td class="mcnCaptionRightContentInner" style="padding:0 9px ;" valign="top">
                                <table class="mcnCaptionRightImageContentContainer" align="left" border="0" cellpadding="0" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td class="mcnCaptionRightImageContent" valign="top"><img alt="" src="/{{$w->image_url}}" style="max-width:200px;" class="mcnImage" width="132"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="mcnCaptionRightTextContentContainer" align="right" border="0" cellpadding="0" cellspacing="0" width="396">
                                    <tbody>
                                        <tr>
                                            <td class="mcnTextContent" valign="top">
                                                <span style="font-size:14px"><span style="color: #4EA9DF;"> {{$w->show_time}} </span></span>
                                                <br>
                                                <span style="color:#FFFFFF"> {{$w->name}} &#64; {{$w->venue}} </span>
                                                <br>
                                                <a href="/event/{{$w->slug}}" target="_blank"><img src="https://gallery.mailchimp.com/ab5ce1d711eb623068ea21c6e/images/e352074a-bfae-4202-aad8-43a70726934c.jpg" style="width: 137px; height: 40px; margin: 0px;" height="40" align="none" width="137"></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="mcnDividerBlock" style="min-width:100%;" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tbody class="mcnDividerBlockOuter">
                <tr>
                    <td class="mcnDividerBlockInner" style="min-width: 100%; padding: 10px 18px 25px;">
                    <table class="mcnDividerContent" style="min-width: 100%;border-top: 2px solid #333333;" border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tbody>
                            <tr>
                                <td><span></span></td>
                            </tr>
                        </tbody>
                    </table></td>
                </tr>
            </tbody>
        </table>                  
    @endforeach
@else

@endif

























