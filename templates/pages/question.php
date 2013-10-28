<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="widget">
				<div class="widget-header">
					<h3><?php echo $title; ?></h3>
				</div>
				<div class="widget-content">
					<div id="tabs">
						<ul>
							<li><a href="#tabs-1"><?php echo $text_theroy; ?></a></li>
							<li><a href="#tabs-2"><?php echo $text_question; ?></a></li>
							<li><a href="#tabs-3"><?php echo $text_answer; ?></a></li>
							<li><a href="#tabs-4"><?php echo $text_solution; ?></a></li>
						</ul>
						<div id="tabs-1">
							<?php if ($theory) echo $theory->render(); ?>
						</div>
						<div id="tabs-2">
							<?php if ($question) echo $question->render(); ?>
						</div>
						<div id="tabs-3">
							<?php if ($answer) echo $answer->render(); ?>
						</div>
						<div id="tabs-4">
							<?php if ($solution) echo $solution->render(); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(function() {
		$( "#tabs" ).tabs();
	});
</script>
