<table class="form-table">
    <tbody>
    <?php $mb->the_field('attribute'); ?>
    <tr valign="top">
        <th scope="row"><label for="<?php $mb->the_name(); ?>">Attribute to:</label></th>
        <td>
            <p><input class="large-text" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="ex: Director of Marketing" /></p>
        </td>
    </tr>
    </tbody>
</table>