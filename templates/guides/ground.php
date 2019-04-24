<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );//For security 

// Inherits $sections from ajax functions

?>


<h2>In Ground Planting Guide</h2>

<div class="planting-guide-sections">
    <?php 
    $hide = '';
    $count = count($sections);
    $i = 0;
    foreach($sections as $section => $options){ ?>
        <h3><?php echo $section; ?></h3>
        <div class="planting-guide-options <?php echo $hide; ?> section-<?php echo $options['id']; ?>" >
            <ul class="style-free">
                <li><input type="radio" name="section-<?php $options['id']; ?>"><?php echo $options['a-instructions'];?></li>
                <li><input type="radio" name="section-<?php $options['id']; ?>"><?php echo $options['b-instructions'];?></li>
            </ul>
            <?php
            $i++;
            if($i < $count){ ?>
                <a href="#" class="indppl-button" data-target="section-<?php echo next($sections)['id']; ?>">Next</a>
            <?php } else { ?>
                <a href="#" class="indppl-button" >Save</a>

            <?php } 
            ?>
        </div>
        <?php $hide = 'display-none'; ?>
    <?php } ?>
</div>