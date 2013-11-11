<div class="container">
	<div class="row">
		<div class="col-md-12">
			<form class="admin-search-form" method="get" id="form-block-search">
				<div class="widget">
					<div class="widget-header">
						<h3><?php echo $text_search; ?></h3>
					</div>
					<div class="widget-content">
						<div class="row">
							<div class="col-md-6">
								<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_title; ?></div>
								<div class="col-md-9 col-sm-9 ">
									<input type="text" class="form-control" name="search_title" value="<?php echo htmlspecialchars($form->getValue('search_title')); ?>" />
									<?php echo $form->getHtmlErrorDiv('search_title'); ?>
								</div>
							</div>
							<div class="col-md-6 visible-xs">
								<hr class="separator-2column" />
							</div>
							<div class="col-md-6">
								<div class="col-md-3 col-sm-3 title-2column"><?php echo $text_category; ?></div>
								<div class="col-md-9 col-sm-9 ">
									<select class="form-control" name="search_category">
										<option value=""></option>
										<?php foreach ($categories as $value => $text) { ?>
											<option value="<?php echo $value; ?>" <?php if ($form->getValue('search_category') == $value) echo 'selected="selected"'; ?>><?php echo $text; ?></option>
										<?php } ?>
									</select>
									<?php echo $form->getHtmlErrorDiv('search_category'); ?>
								</div>
							</div>
						</div>
						<hr class="separator-2column" />
						<div class="align-right">
							<button type="submit" class="btn btn-primary" name="form-block-search-submit"><?php echo $text_search; ?></button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="widget">
				<div class="widget-header">
					<h3><?php echo $text_search_results; ?></h3>
				</div>
				<div class="widget-content">
					<div class="pagination">
						<?php echo $pagination->getPageLinks(); ?>
					</div>
					<form action="<?php echo $this->url->getUrl('administrator/Blocks', 'delete'); ?>" method="post">
						<table class="table">
							<tr>
								<th></th>
								<th nowrap="nowrap"><?php echo $text_title; ?> <?php echo $pagination->getSortUrls('title'); ?></th>
								<th nowrap="nowrap"><?php echo $text_category; ?> <?php echo $pagination->getSortUrls('category_name'); ?></th>
								<th></th>
							</tr>
							<?php foreach ($questions as $question) { ?>
							<tr>
								<td class="select"><input type="checkbox" name="selected[]" value="<?php echo $question->id; ?>" /></td>
								<td><?php echo $question->title; ?></td>
								<td><?php echo $question->getCategoryName(); ?></td>
								<td>
									<a href="<?php echo $this->url->getUrl('administrator/BlockQuestion', 'edit', [$question->id]); ?>" class="btn btn-primary"><i class="icon-edit-sign" title="<?php echo $text_edit; ?>"></i></a>
								</td>
							</tr>
							<?php } ?>
						</table>
						<button type="submit" class="btn btn-primary" name="form-questions-list-submit" onclick="return deleteSelected();"><?php echo $text_delete_selected; ?></button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
