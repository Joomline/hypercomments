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

?>
<hc>
<post>
    <title><![CDATA[<?php echo $this->data['title']; ?>]]></title>
    <url><?php echo $this->data['link']; ?></url>
    <xid><?php echo $this->data['object_group'].'_'.$this->data['object_id']; ?></xid>
    <stream_id/>
    <comments>
        <?php foreach($this->data['comments'] as $v) : ?>
            <comment>
                <id><?php echo $v['id']; ?></id>
                <parent_id><?php echo $v['parent']; ?></parent_id>
                <root_id><?php echo $v['root']; ?></root_id>
                <text><![CDATA[<?php echo $v['comment']; ?>]]></text>
                <nick><?php echo $v['username']; ?></nick>
                <avatar><?php echo $v['avatar']; ?></avatar>
                <time><?php echo $v['date']; ?></time>
                <ip><?php echo $v['ip']; ?></ip>
                <email><?php echo $v['email']; ?></email>
                <account_id><?php echo $v['userid']; ?></account_id>
                <files/>
                <vote_up><?php echo $v['isgood']; ?></vote_up>
                <vote_dn><?php echo $v['ispoor']; ?></vote_dn>
                <topic>false</topic>
                <param/>
                <hc_comment>false</hc_comment>
                <category/>
            </comment>
        <?php endforeach; ?>
    </comments>
</post>
</hc>
