<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.pulsemotiv.com/
 * @since      1.0.0
 *
 * @package    Pulsemotiv
 * @subpackage Pulsemotiv/admin/partials
 */
?>
<div class="pulsemotiv4wp">
    <div class="row">
        <div class="col">
            <div class="main">
                <h3>Pulsemotiv Integration for Wordpress</h3>
                <hr />
                <div class="formWrapper">
	                <?php
	                settings_errors();
	                ?>
                    <form class="apiKeyForm" action="<?php echo admin_url('options.php')?>" method="post">
	                    <?php settings_fields('pulse4wp_settings'); ?>

                        <h3>
		                    Pulsemotiv API Key Settings
                        </h3>

                        <div class="pulse-api-status">
                            <div class="api-status-wrapper">
                            <div>API Key Status:</div>
	                        <?php if ($api_valid) {
		                        ?>
                                <div class="status valid"><span>Valid</span></div>
		                        <?php
	                        } else {
		                        ?>
                                <div class="status invalid"><span>Invalid</span></div>
		                        <?php
	                        } ?>
                            </div>
                        </div>
                        <div class="pulse-api-key-inputWrapper">
                            <div class="wrapper-div">
                                <div class="label-wrapper">
                                    <label for="pulse_api_key">API Key:</label>
                                </div>
                                <div class="input-wrapper">
                                    <input class="pulse4wp-apikey-input" id="pulse_api_key" name="pulse4wp[api_key]" type="text"
                                   value="<?php echo esc_attr($obfuscated_api_key) ?>" placeholder="Enter a Valid API Key">
                                </div>
                            </div>
                        </div>
	                    <?php
	                    $otherAttributes = array('class' => 'pulse_api_key_submit');
	                    submit_button('submit', $otherAttributes); ?>
                    </form>
                </div>
                <hr />
                <?php
                if (! empty($options['api_key']) && $api_valid) {
                    include 'pulsemotiv-pulses.php';
                }
                ?>
            </div>
        </div>
    </div>
</div>
