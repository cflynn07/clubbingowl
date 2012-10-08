You have a new reservation request for: "<?= $gla_name ?>"

<?php if($auto_approve): ?>
The request was automatically approved.
<?php else: ?>
The request requires a response.
<?php endif; ?>