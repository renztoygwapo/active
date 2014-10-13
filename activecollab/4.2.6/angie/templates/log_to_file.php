<?php if(isset($this) && $this instanceof Logger) { ?>
===================================================================================
| Logged on <?php echo date('F j, Y, g:i a') ?> (<?php echo array_var($_SERVER, 'REMOTE_ADDR') ?>)
===================================================================================

<?php $counter = 0; ?>
<?php foreach($this->getEntries() as $log_entry) { ?>
<?php $counter++; ?>
Log entry #<?php echo $counter ?>: <?php echo (isset($log_entry['label']) ? $log_entry['label'] : '') ?>
-----------------------------------------------------------------------------------
<?php echo isset($log_entry['content']) ? $log_entry['content'] : '' ?>

<?php } // foreach ?>

<?php } // if ?>