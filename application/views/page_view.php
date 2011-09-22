<div id="page_view" class="content">
<?php
if ($this->session->flashdata('error'))
{
    $this->load->view('error');
}
if ($this->session->flashdata('success'))
{
    $this->load->view('success');
}
?>
<div id="backtrack">
<?php foreach ($backtrack as $key => $value): ?>
    <?php echo anchor('page/view/'.$key, $value); ?>&nbsp;>&nbsp;
<? endforeach; ?>
</div>

<h1>
    <?=$title?>
    <div class="controls">
		<?=$controls?>
	</div>
</h1>

<div>
<?=$content?>
</div>


<div class="actors">
<?php if ($actors): ?>
    <h3>Actors</h3>
    <p>
        <?php $count = count($actors); foreach ($actors as $actor_id => $actor): ?>
        <?php echo anchor('profile/view/'.$actor['url'], $actor['name']); if ($count > 1) echo '&nbsp;|&nbsp;'; $count--;?>
        <?php endforeach; ?>
    </p>
<?php endif; ?>
</div>

</div>