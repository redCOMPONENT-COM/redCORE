<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Templates
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('rdropdown.init');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');

$action = JRoute::_('index.php?option=com_redcore&view=oauth_clients');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
?>
<form action="<?php echo $action; ?>" name="adminForm" class="adminForm" id="adminForm" method="post">
	<?php
	echo RLayoutHelper::render(
		'searchtools.default',
		array(
			'view' => $this,
			'options' => array(
				'filtersHidden' => false,
				'searchField' => 'search_oauth_clients',
				'searchFieldSelector' => '#filter_search_oauth_clients',
				'limitFieldSelector' => '#list_oauth_clients_limit',
				'activeOrder' => $listOrder,
				'activeDirection' => $listDirn
			)
		)
	);
	?>
	<hr/>
	<div class="row-fluid">
		<table class="table table-striped table-hover" id="oauthClientsList">
			<thead>
			<tr>
				<th class="hidden-xs">
					<input type="checkbox" name="checkall-toggle" value=""
					       title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
				</th>
				<th class="nowrap">
					<?php echo JHtml::_('rgrid.sort', 'COM_REDCORE_OAUTH_CLIENTS_CLIENT_ID', 'oc.client_id', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('rgrid.sort', 'COM_REDCORE_OAUTH_CLIENTS_CLIENT_SECRET', 'oc.client_secret', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('rgrid.sort', 'COM_REDCORE_OAUTH_CLIENTS_REDIRECT_URI', 'oc.redirect_uri', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap hidden-xs">
					<?php echo JHtml::_('rgrid.sort', 'COM_REDCORE_OAUTH_CLIENTS_GRANT_TYPES', 'oc.grant_types', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap hidden-xs">
					<?php echo JHtml::_('rgrid.sort', 'COM_REDCORE_OAUTH_CLIENTS_SCOPE', 'oc.scope', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap">
					<?php echo JHtml::_('rgrid.sort', 'COM_REDCORE_OAUTH_CLIENTS_USER', 'oc.user_id', $listDirn, $listOrder); ?>
				</th>
			</tr>
			</thead>
			<?php if ($this->items): ?>
				<tbody>
				<?php foreach ($this->items as $i => $item): ?>
					<?php
					$canChange = 1;
					$canEdit = 1;
					$canCheckin = 1;
					$grantTypes = explode(' ', $item->grant_types);
					$scopes = explode(' ', $item->scope);
					?>
					<tr>
						<td>
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td>
							<a href="<?php echo JRoute::_('index.php?option=com_redcore&task=oauth_client.edit&id=' . $item->id); ?>">
							<?php echo $item->client_id; ?>
							</a>
						</td>
						<td style="word-break:break-all; word-wrap:break-word;">
							<?php echo $item->client_secret; ?>
						</td>
						<td style="word-break:break-all; word-wrap:break-word;">
							<?php echo $item->redirect_uri; ?>
						</td>
						<td>
							<ul class="list-unstyled ctypes">
								<?php foreach ($grantTypes as $grantType): ?>
									<li><?php echo $grantType; ?></li>
								<?php endforeach; ?>
							</ul>
						</td>
						<td>
							<ul class="list-unstyled ctypes">
								<?php foreach ($scopes as $key => $scope): ?>
									<?php if ($key < 5): ?>
										<li><?php echo $scope; ?></li>
									<?php else : ?>
										<li>...</li>
										<?php break; ?>
									<?php endif; ?>
								<?php endforeach; ?>
							</ul>
						</td>
						<td>
							<?php echo $item->name; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			<?php endif; ?>
		</table>
		<?php echo $this->pagination->getListFooter(); ?>
	</div>

	<div>
		<input type="hidden" name="task" value="">
		<input type="hidden" name="boxchecked" value="0">
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
