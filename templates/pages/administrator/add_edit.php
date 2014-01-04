<div class="container">
	<div class="row">
		<div class="col-md-12">
			<form class="admin-form" method="post" id="form-question">
				<div class="widget">
					<div class="widget-header">
						<h3><?php
						  if ($is_add_page) echo $text_add_header;
						  else echo $text_update_header;
						?></h3>
					</div>
					<div class="widget-content">
						<hr class="separator-2column" />
						<div class="row">
							<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_title; ?></div>
							<div class="col-md-9 col-sm-9 ">
								<input type="text" class="form-control" name="title" value="<?php echo $question->title; ?>" />
								<?php echo $form->getHtmlErrorDiv('title'); ?>
							</div>
						</div>
						<hr class="separator-2column" />
						<div class="row">
							<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_number; ?></div>
							<div class="col-md-9 col-sm-9 ">
								<input type="text" class="form-control" name="number" value="<?php echo $question->number; ?>" />
								<?php echo $form->getHtmlErrorDiv('number'); ?>
							</div>
						</div>
						<hr class="separator-2column" />
						<div class="row">
							<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_sub_number; ?></div>
							<div class="col-md-9 col-sm-9 ">
								<select class="form-control" name="sub_number">
									<option value=""></option>
									<?php for ($i=1; $i<=26; $i++) { ?>
										<option value="<?php echo $i; ?>" <?php if ($i == $question->sub_number) echo 'selected="selected"'; ?>><?php echo chr(0x60+$i); ?></option>
									<?php } ?>
								</select>
								<?php echo $form->getHtmlErrorDiv('sub_number'); ?>
							</div>
						</div>
						<hr class="separator-2column" />
						<div class="row">
							<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_category; ?></div>
							<div class="col-md-9 col-sm-9 ">
								<select name="category" class="form-control">
									<option value=""></option>
									<?php foreach ($categories as $value => $text) { ?>
										<option value="<?php echo $value; ?>" <?php if ($value == $question->getCategoryId()) echo 'selected="selected"'; ?>><?php echo $text; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<hr class="separator-2column" />
						<div class="row">
							<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_type; ?></div>
							<div class="col-md-9 col-sm-9 ">
								<select name="type" class="form-control">
									<?php foreach ($types as $value => $text) { ?>
										<option value="<?php echo $value; ?>" <?php if ($value == $question->type) echo 'selected="selected"'; ?>><?php echo $text; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<hr class="separator-2column" />
						<div class="row">
							<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_theory_tag; ?></div>
							<div class="col-md-9 col-sm-9 ">
								<input type="text" class="form-control" name="theory" value="<?php echo $form->getValue('theory') ? $form->getValue('theory') : $question->getTheoryTag() ; ?>" />
								<?php echo $form->getHtmlErrorDiv('theory'); ?>
							</div>
						</div>
						<hr class="separator-2column" />
						<div class="row">
							<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_question_tag; ?></div>
							<div class="col-md-9 col-sm-9 ">
								<input type="text" class="form-control" name="question" value="<?php echo $form->getValue('question') ? $form->getValue('question') : $question->getQuestionTag(); ?>" />
								<?php echo $form->getHtmlErrorDiv('question'); ?>
							</div>
						</div>
						<hr class="separator-2column" />
						<div class="row">
							<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_answer_tag; ?></div>
							<div class="col-md-9 col-sm-9 ">
								<input type="text" class="form-control" name="answer" value="<?php echo $form->getValue('answer') ? $form->getValue('answer') : $question->getAnswerTag(); ?>" />
								<?php echo $form->getHtmlErrorDiv('answer'); ?>
							</div>
						</div>
						<hr class="separator-2column" />
						<div class="row">
							<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_solution_tag; ?></div>
							<div class="col-md-9 col-sm-9 ">
								<input type="text" class="form-control" name="solution" value="<?php echo $form->getValue('solution') ? $form->getValue('solution') : $question->getSolutionTag(); ?>" />
								<?php echo $form->getHtmlErrorDiv('solution'); ?>
							</div>
						</div>
						<hr class="separator-2column" />
						<div class="row">
							<div class="col-md-12 align-center">
								<button class="btn btn-primary" type="submit" name="form-question-submit"><?php
								  if ($is_add_page) echo $text_add_button;
								  else echo $text_update_button;
								?></button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	<?php echo $form->getJavascriptValidation(); ?>

	$(function() {
		$( "#tabs" ).tabs();
	});
</script>
