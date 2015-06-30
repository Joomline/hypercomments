<?php
// No direct access
defined('_JEXEC') or die;
/**
 * Hypercomments
 *
 * @version 	1.0
 * @author		Arkadiy Sedelnikov, JoomLine
 * @copyright	© 2015. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */
$user = JFactory::getUser();
$canDelete = $user->authorise('core.delete', 'com_hypercomments');
$widget_id = $this->config->get('widget_id');
JHtml::_('jquery.framework');
JFactory::getDocument()->addStyleSheet(JUri::root().'administrator/components/com_hypercomments/assets/css/hypercomments.css');
JFactory::getDocument()->addScript(JUri::root().'administrator/components/com_hypercomments/assets/js/hypercomments.js');
?>
<script type="text/javascript">
    var liveSite = '<?php echo JUri::root(); ?>';
    function deleteXmlFile(filename)
    {
        alert(filename);
        if (confirm ('<?php echo JText::_('DELETE_QUERY_STRING'); ?>'))
        {
            jQuery(':checkbox').removeAttr('checked');
            jQuery('input[name=task]').val('file.delete');
            jQuery('#adminForm').append('<input type="hidden" name="cid[]" value="'+filename+'"/>').submit();
        }
    }

    Joomla.submitbutton = function(task)
    {
        if (task == 'file.upload')
        {
            uploadXMLfiles('<?php echo $this->url; ?>', '<?php echo $widget_id; ?>', '<?php echo JUri::root(); ?>');
        }
        else{
            Joomla.submitform(task);
        }
    }
</script>
<form action="<?php echo JRoute::_('index.php?option=com_hypercomments&view=jcomments'); ?>" method="post" name="adminForm" id="adminForm">
    <div  class="row-fluid">
        <div id="filter-bar" class="btn-toolbar">

            <div class="btn-group pull-right hidden-phone">
                <label for="limit" class="element-invisible"><?php echo JText::_( 'JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC' );?></label>
                <?php echo $this->pagination->getLimitBox(); ?>
            </div>
        </div>
        <div class="clearfix"></div>
        <?php if (!empty( $this->sidebar )): ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
        <?php else : ?>
        <div id="j-main-container">
        <?php endif; ?>

        <table class="table table-striped span12" id="articleList">
            <thead>
            <tr>
                <th width="1%">
                    <input type="checkbox" name="checkall-toggle" value=""
                           title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
                </th>
                <th width="39%">
                    <?php echo JText::_('COM_HYPERCOMMENTS_LIST_FILE'); ?>
                </th>
                <th width="40%" class="status">
                    <?php echo JText::_('COM_HYPERCOMMENTS_LIST_FILE_STATUS'); ?>
                </th>
                <th width="20%">
                    <?php echo JText::_('COM_HYPERCOMMENTS_LIST_DELETE'); ?>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($this->items as $i => $item) : ?>
                <tr class="row<?php echo $i % 2; ?>">

                    <td class="center hidden-phone">
                        <?php if($item->status == 'new') echo JHtml::_('grid.id', $i, $item->filename); ?>
                    </td>
                    <td class="nowrap has-context">
                        <a href="<?php echo $this->url.$item->filename; ?>" target="_blank">
                            <?php echo $this->escape($item->filename); ?>
                        </a>
                    </td>
                    <td class="nowrap small hidden-phone">
                        <?php echo ($item->status == 'new') ? JText::_('COM_HYPERCOMMENTS_FILE_STATUS_NEW') : JText::_('COM_HYPERCOMMENTS_FILE_STATUS_SENT'); ?>
                    </td>
                    <td class="nowrap small hidden-phone">
                        <?php if ($canDelete) : ?>
                            <a class="btn btn-danger" onclick="deleteXmlFile('<?php echo $item->filename; ?>');">X</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php echo $this->pagination->getListFooter(); ?>
        </div>

        <div>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="folder" value="<?php echo $this->folder ?>"/>
            <input type="hidden" name="boxchecked" value="0"/>
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </div>
</form>