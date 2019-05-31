<table class="form-table">
    <tr>
        <th><label>URL to News Article</label></th><?php $mb->the_field('newsurl'); ?>
        <td><input  class="large-text" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="http://" /></td>
    </tr>
    <tr>
        <th><label>Team Members</label></th>
            <td>
                <?php
                $team = new MSDTeamDisplay;
                $team_members = $team->get_all_team_members();
                foreach ($team_members as $item):
                    $mb->the_field('team_members'); ?>
                    <input type="checkbox" name="<?php $mb->the_name(); ?>[]" value="<?php echo $item->ID; ?>"<?php $mb->the_checkbox_state($item->ID); ?>/> <?php echo $item->post_title; ?><br/>
                <?php endforeach; ?>
            </td>
    </tr>
</table>