<?php echo $this->Html->link('Add data', 'add'); ?>
<br>
<br>
<?php foreach($results as $result): ?>

	id: <?php echo $result['Mensaje']['_id']; ?> [<?php echo $this->Html->link('edit','edit/'.$result['Post']['_id']); ?>] [<?php echo $this->Html->link('delete','delete/'.$result['Post']['_id']); ?>]<br>
	title: <?php echo $result['Mensaje']['sesion']; ?><br>
	body: <?php echo $result['Mensaje']['body']; ?><br>
	hoge: <?php echo $result['Mensaje']['comentario']; ?><br>

<hr>
<?php endforeach; ?>
