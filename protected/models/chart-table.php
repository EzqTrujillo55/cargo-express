<?php
//dump($new_data);
if (!isset($new_data)){
	$new_data='';
}
?>

<table class="table top30 table-hover table-striped">
 <thead>
  <tr>
   <th class="text-muted"><?php echo Driver::t("Date")?></th>
   <th class="text-muted"><?php echo Driver::t("Successful Tasks")?></th>
   <th class="text-muted"><?php echo Driver::t("Cancelled Tasks")?></th>
   <th class="text-muted"><?php echo Driver::t("Failed Tasks")?></th>
   <th class="text-muted"><?php echo Driver::t("Total Tasks")?></th>
  </tr>
 </thead>
 <tbody>
 <?php if (is_array($new_data) && count($new_data)>=1):?>
 <?php unset($new_data[0])?>
 <?php foreach ($new_data as $val):?>
 <?php   
   $total = 0;
   if (isset($val['successful'])){
   	  $total+=$val['successful'];
   }
   if (isset($val['cancelled'])){
   	  $total+=$val['cancelled'];
   }
   if (isset($val['failed'])){
   	  $total+=$val['failed'];
   }   
 ?>
  <tr>
    <td><?php echo Driver::prettyDate($val['date'],false)?></td>
    <td><?php echo isset($val['successful'])?$val['successful']:''?></td>
    <td><?php echo isset($val['cancelled'])?$val['cancelled']:''?></td>
    <td><?php echo isset($val['failed'])?$val['failed']:''?></td>
    <td><?php echo $total?></td>
  </tr>
  <?php endforeach;?>
  <?php endif;?>
 </tbody>
</table>