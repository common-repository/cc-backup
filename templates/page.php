<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap">
    <form method="post" action="options.php">
        <h2><?= $header; ?></h2>
        <div id="poststuff">
            <div id="post-body" class="metabox-holder">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <form method="post">
                            <?php $table->display(); ?>
                        </form>
                    </div>
                </div>
            </div>
            <br class="clear" />
        </div>
        <form method="post" action="<?= $url; ?>">
            <?php
                echo $input;
                wp_nonce_field( $action );
                submit_button( $button );
            ?>
        </form>
    </form>
</div>