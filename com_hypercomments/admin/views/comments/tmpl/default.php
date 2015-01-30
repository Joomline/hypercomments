<?php
/** @var $this HypercommentsViewComments */
defined( '_JEXEC' ) or die;// No direct access
/**
 * Hypercomments
 *
 * @version 	1.0
 * @author		Arkadiy Sedelnikov, JoomLine
 * @copyright	© 2015. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */
?>
<?php if (!empty( $this->sidebar )): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif; ?>

<?php if(!$this->params->get('widget_id', '')):?>
	<div class="hc_btn_settings e_hc_login"><?php echo JText::_('COM_HYPERCOMMENTS_LOGIN_GOOGLE') ?></div>
<?php else:?>
	<div class="hc_box e_box_comments">
		<div id="hc_adm_widget"></div>
	</div>
<?php endif;?>
</div>