<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<meta name="viewport" content="width=610, initial-scale=1">
<style>
.rotate {
/* Safari */
-webkit-transform: rotate(-90deg);
/* Firefox */
-moz-transform: rotate(-90deg);
/* IE */
-ms-transform: rotate(-90deg);
/* Opera */
-o-transform: rotate(-90deg);
/* Internet Explorer */
filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
}
</style>
<body>
  <?php if($type && $type=='S'): ?>
        <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
        <div style='page-break-after:always;text-align:center;'>
                <div style='float:center;position:absolute;top:-23;left:38;'>
                      <div style='font-size:12px;'>$<?php echo e($ticket['price_each']); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo e($ticket['id']); ?>-<?php echo e($ticket['number']); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo e($ticket['ticket_type']); ?></div>   
                </div>
                <div style='text-align:center;width:90%;position:absolute;top:0;left:-20;'>
                        <div style='padding-top:2px;'><span style='font-size:22px'><?php echo e($ticket['show_name']); ?></span></div>
                        <div style='padding-top:2px;'><span style='font-size:12px'><?php if($ticket['package'] != 'None'): ?> (<?php echo e($ticket['package']); ?>) <?php endif; ?> at<br/></span><?php echo e($ticket['venue_name']); ?></div>
                        <div style='padding-top:3px;'><span style='font-size:12px'>on&nbsp;</span> <?php echo e(date('l, m/d/Y',strtotime($ticket['show_time']))); ?> <span style='font-size:12px'><?php if($ticket['time_alternative']): ?> - <?php else: ?> at <?php endif; ?></span><?php if($ticket['time_alternative']): ?> - <?php else: ?> <?php echo e(date('h:i a',strtotime($ticket['show_time']))); ?> <?php endif; ?></div>                     
                </div>
                <div class="rotate" style='font-size:12px;position:absolute;top:85;left:317;'>
                        <div style='padding-top:3px;font-size:12px;'>$<?php echo e($ticket['price_each']); ?></div>
                </div>
                <div class="rotate" style='font-size:12px;position:absolute;top:10;left:302;'>
                        <img src='<?php echo e($ticket['QRcode']); ?>' alt='TB<?php echo e($ticket['id']); ?><?php echo e($ticket['user_id']); ?><?php echo e($ticket['number']); ?>' width=70px height=70px/>
                </div>
                <div class="rotate" style='font-size:12px;position:absolute;top:-5;left:327;'>
                        <div style='padding-top:3px;font-size:12px;text-align:center;width:40px'><?php echo e($ticket['id']); ?>-<?php echo e($ticket['number']); ?></div>
                </div>
                <div style='width:80%;position:absolute;top:85;left:-20;font-size:12px;'>
                        <hr><div style='text-align:center;'><span><?php if(!($ticket['restrictions']=='None' || $ticket['restrictions']=='Inherit')): ?>RESTRICTIONS: <?php echo e($ticket['restrictions']); ?> years old to attend the event.<?php endif; ?></span></div>
                </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
  <?php elseif($type && $type=='C'): ?>
        <?php  $pages=array_chunk($tickets,5)  ?>
        <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
        <div  style='page-break-after:always;text-align:center;'>
        <?php  $top=0  ?>
              <?php $__currentLoopData = $page; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
              <div style="height:189px">
              <?php if($top!=0): ?>
                    <div style='width:100%;font-size:12px;'>
                           <hr style="border: 0 none;border-top:2px dashed #322f32">
                    </div>
              <?php endif; ?>
                    <div style='float:left;width:80%;'>
                            <div style='padding-top:2px;'><span style='font-size:12px'></span><?php echo e($ticket['show_name']); ?> (<?php echo e($ticket['ticket_type']); ?>)</div>
                            <div style='padding-top:2px;'><span style='font-size:12px'><?php if($ticket['package'] != 'None'): ?> (<?php echo e($ticket['package']); ?>) <?php endif; ?>  at<br/></span><?php echo e($ticket['venue_name']); ?></div>
                            <div style='padding-top:3px;'><span style='font-size:12px'>on </span> <?php echo e(date('l, m/d/Y',strtotime($ticket['show_time']))); ?> </div>
                            <div style='padding-top:3px;'><span style='font-size:12px'><?php if($ticket['time_alternative']): ?> - <?php else: ?> at <?php endif; ?></span><?php if($ticket['time_alternative']): ?> - <?php else: ?> <?php echo e(date('h:i a',strtotime($ticket['show_time']))); ?> <?php endif; ?></div>
                            <div style='padding-top:-5px;'><hr><span><?php echo e($ticket['customer_name']); ?></span></div><hr>
                            <div style='text-align:center;'><span><?php if(!($ticket['restrictions']=='None' || $ticket['restrictions']=='Inherit')): ?>RESTRICTIONS: <?php echo e($ticket['restrictions']); ?> years old to attend the event.<?php endif; ?></span></div>
                    </div>
                    <div style='float:right;width:20%;'>
                            <div style='padding-top:0px;'><img src='<?php echo e($ticket['QRcode']); ?>' alt='TB<?php echo e($ticket['id']); ?><?php echo e($ticket['user_id']); ?><?php echo e($ticket['number']); ?>' width=125px height=125px /></div>
                            <div style='padding-top:2px;text-align:left;padding-left:30px'><span style='font-size:12px;'>Paid: </span>$ <?php echo e($ticket['price_each']); ?> </div>
                            <div style='padding-top:2px;text-align:left;padding-left:30px'><span style='font-size:12px;'>Ticket: </span> <?php echo e($ticket['id']); ?>-<?php echo e($ticket['number']); ?> </div>
                    </div>
              <?php  $top=$top+1  ?>
                </div> 
              <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
  <?php else: ?>
  <?php  echo $tickets  ?>
  <?php endif; ?>