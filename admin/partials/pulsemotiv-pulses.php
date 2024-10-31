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
<div class="pulse_options">
    <div>
        <h3>Your pulses in draft status:</h3>
        <?php
        if (count($body->pulses) > 0) {
        echo '<div class="list-wrapper">';
        echo '<table class="pulses-table">';
        echo '<thead>';
        echo '<tr>';
        echo '<td>Pulse Name</td>';
        echo '<td colspan="2">Short Code</td>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($body->pulses as $key => $pulse) {
            echo '<tr>';
            echo '<td class='. $pulse->name .'>' . $pulse->name . '</td>';
            echo '<td><span class="pulseCode">[pulse id="' . $pulse->id . '" uniqueid="' . $pulse->uuid . '"]</span></td>';
            echo '<td class='. $pulse->name .'><button class="copyClick">Copy short code</button></td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        } else {
            echo 'No pulses are available in drafts';
        }
        ?>
    </div>
</div>
