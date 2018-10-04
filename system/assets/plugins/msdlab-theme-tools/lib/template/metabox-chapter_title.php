<?php global $wpalchemy_media_access; ?>
<div class="msdlab_meta" id="chaptertitle_metabox">
    <ul>
        <li>
            <?php $mb->the_field('chapter_title'); ?>
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" />
        </li>
    </ul>
</div>