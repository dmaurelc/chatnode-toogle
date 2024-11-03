<?php
/*
Plugin Name: ChatNode Toggle
Description: Allows you to customize the ChatNode Toggle for Wordpress.
Version: 1.0
Author: Daniel M.
Author URI: https://www.dmaurel.cl/
*/

if (!defined('ABSPATH')) {
    exit;
}

function chatnode_enqueue_chatbot_script() {
	
	if ('on' === get_option('chatnode_enqueue_script', 'off')) {
        $chatbot_id = get_option('chatnode_chatbot_id');
		$toggle_position = get_option('chatnode_toggle_position', 'right');
        $color_1 = get_option('chatnode_color_1');
        $color_2 = get_option('chatnode_color_2');
        $tooltip_text = get_option('chatnode_tooltip_text', 'Your CTA');
        ?>
        <script>
        function waitForElementToDisplay(selector, time) {
            if (document.querySelector(selector) != null) {
                addTooltip(selector);
                return;
            } else {
                setTimeout(function () {
                    waitForElementToDisplay(selector, time);
                }, time);
            }
        }

        function addTooltip(selector) {
            var element = document.querySelector(selector);
            if (element) {
                element.setAttribute('data-tooltip', '<?php echo esc_js($tooltip_text); ?>');
                element.classList.add('tooltip');

                setTimeout(function () {
                    element.classList.add('show-tooltip');
                }, 2000);

                element.addEventListener('click', function () {
                    element.classList.remove('show-tooltip');
                    element.classList.add('hide-tooltip');
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            waitForElementToDisplay('#toggle-btn-<?php echo esc_js($chatbot_id); ?>', 500);
        });
        </script>
        <script src="https://lkjink.com/embed.js" data-chatbot-id="<?php echo esc_attr($chatbot_id); ?>" data-color-1="<?php echo esc_attr($color_1); ?>" data-color-2="<?php echo esc_attr($color_2); ?>"></script>
        <style>
		.tooltip[data-tooltip]:before, .tooltip[data-tooltip]:after {
            transition: opacity 0.3s, transform 0.3s;
            <?php if ($toggle_position === 'left') : ?>
                margin-left: 10px;
				left: 100% !important;
            <?php else : ?>
                right: 100%;
            <?php endif; ?>
        }
        .tooltip[data-tooltip]:before {
			content: attr(data-tooltip);
			position: absolute;
			top: 50%;
			transform: translateY(-50%);
			right: 100%;
			margin-right: 10px; /* Ajustado para acomodar el caret */
			padding: 5px 10px;
			min-width: max-content;
			text-align: center;
			background-color: black;
			color: white;
			border-radius: 5px;
			font-size: 14px;
			z-index: 1000;
			opacity: 0;
			transition: opacity 0.3s;
		}

		.tooltip[data-tooltip]:after {
			content: '';
			position: absolute;
			top: 50%;
			transform: translateY(-50%);
			right: 100%;
			border-width: 10px;
			border-style: solid;
			transition: opacity 0.3s;
			opacity: 0;
			<?php if ($toggle_position === 'left') : ?>
                margin-left: -8px;
                border-color: transparent black transparent transparent;
				left: 100% !important;
            <?php else : ?>
                margin-right: -8px;
                border-color: transparent transparent transparent black;
				right: 100%;
            <?php endif; ?>
		}
			
		.tooltip.show-tooltip[data-tooltip]:before,
		.tooltip.show-tooltip[data-tooltip]:after {
			opacity: 1;
			transform: translateY(-50%);
		}

		.tooltip.hide-tooltip[data-tooltip]:before,
		.tooltip.hide-tooltip[data-tooltip]:after {
			opacity: 0;
			transform: translateY(-60%); /* Ligeramente hacia arriba para la animación de desvanecimiento */
		}
        
        /* Más estilos para el tooltip... */

        #toggle-btn-<?php echo esc_js($chatbot_id); ?> {
            bottom: 28px !important;
            <?php echo $toggle_position; ?>: 28px !important;
            width: 60px !important;
            height: 60px !important;
        }
			
        #chatbot-84469dea83db146f-chat.open {
            height: auto;
            top: 40px;
            opacity: 1;
            transform: translateY(0%);
            <?php if ($toggle_position === 'left') : ?>
				left: 10px !important;
            <?php else : ?>
                right: 10px;
            <?php endif; ?>
            bottom: 96px;
        }

		@media (max-width: 768px) {
			#chatbot-9c29a4d3e096eacf-chat.open {
				height: calc(100vh - 114px);
				opacity: 1;
				transform: translateY(0%);
				left: 20px;
				bottom: 20px;
				right: 20px;
				top: 20% !important;
				border-radius: 16px;
			}
		}
        </style>
        <?php
    }
}
add_action('wp_head', 'chatnode_enqueue_chatbot_script');

function chatnode_register_settings() {
    add_option('chatnode_enqueue_script', 'off');
    add_option('chatnode_chatbot_id', '');
    add_option('chatnode_color_1', '');
    add_option('chatnode_color_2', '');
    add_option('chatnode_tooltip_text', 'Hello!');
	register_setting('chatnode_options', 'chatnode_enqueue_script', [
        'type' => 'string',
        'description' => 'Chatbot ID',
        'sanitize_callback' => 'chatnode_sanitize_switcher',
        'default' => 'on',
    ]);
    register_setting('chatnode_options', 'chatnode_chatbot_id', [
        'type' => 'string',
        'description' => 'Chatbot ID',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => null,
        'required' => false,
    ]);
	register_setting('chatnode_options', 'chatnode_toggle_position', [
        'type' => 'string',
        'description' => 'Toggle Position',
        'sanitize_callback' => 'chatnode_sanitize_toggle_position',
        'default' => 'right',
    ]);
    register_setting('chatnode_options', 'chatnode_color_1', [
        'type' => 'string',
        'description' => 'Icon Color',
        'sanitize_callback' => 'sanitize_hex_color',
        'default' => null,
        'required' => false,
    ]);
    register_setting('chatnode_options', 'chatnode_color_2', [
        'type' => 'string',
        'description' => 'Toggle Background',
        'sanitize_callback' => 'sanitize_hex_color',
        'default' => null,
        'required' => false,
    ]);
    register_setting('chatnode_options', 'chatnode_tooltip_text', [
        'type' => 'string',
        'description' => 'Tooltip Text',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => 'Ask me!',
    ]);
}
add_action('admin_init', 'chatnode_register_settings');

function chatnode_sanitize_switcher($input) {
    return $input === 'on' ? 'on' : 'off';
}

function chatnode_sanitize_toggle_position($input) {
    return in_array($input, ['left', 'right']) ? $input : 'right';
}

function chatnode_options_page() {
    ?>
    <div class="wrap">
        <h1>ChatNode Toogle</h1>
        <form method="post" action="options.php">
            <?php settings_fields('chatnode_options'); ?>
            <table class="form-table">
                
				<tr valign="top">
                <th scope="row">Enable Chatbot:</th>
                <td>
                    <label>
                        <input type="checkbox" name="chatnode_enqueue_script" <?php checked(get_option('chatnode_enqueue_script', 'off'), 'on'); ?> />
                        Activate to show ChatNode
                    </label>
                </td>
                </tr>
				
				<tr valign="top">
                <th scope="row">Chatbot ID:</th>
                <td><input type="password" name="chatnode_chatbot_id" id="chatnode-id" value="<?php echo esc_attr(get_option('chatnode_chatbot_id')); ?>" placeholder="Your Chatbot ID"  /></td>
                </tr>
				
				<tr valign="top">
                <th scope="row">Toggle Position:</th>
                <td>
                    <label>
                        <input type="radio" name="chatnode_toggle_position" value="left" <?php checked(get_option('chatnode_toggle_position'), 'left'); ?> />
                        Left
                    </label>
                    <label>
                        <input type="radio" name="chatnode_toggle_position" value="right" <?php checked(get_option('chatnode_toggle_position'), 'right'); ?> />
                        Right
                    </label>
                </td>
                </tr>
                 
                <tr valign="top">
                <th scope="row">Icon Color:</th>
                <td><input type="text" name="chatnode_color_1" class="chatnode-color-picker" value="<?php echo esc_attr(get_option('chatnode_color_1')); ?>"  /></td>
                </tr>
                
                <tr valign="top">
                <th scope="row">Toggle Background:</th>
                <td><input type="text" name="chatnode_color_2" class="chatnode-color-picker" value="<?php echo esc_attr(get_option('chatnode_color_2')); ?>"  /></td>
                </tr>


                <tr valign="top">
                <th scope="row">Tooltip:</th>
                <td><input type="text" name="chatnode_tooltip_text" value="<?php echo esc_attr(get_option('chatnode_tooltip_text')); ?>" placeholder="Your CTA"  /></td>
                </tr>
		
            </table>
            
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
    // Incluir CSS y JavaScript del color picker.
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    
    // Inicializar color picker.
    add_action('admin_footer', 'chatnode_initialize_color_picker');
}

function chatnode_initialize_color_picker() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('.chatnode-color-picker').wpColorPicker();
    });
    </script>
    <?php
}

add_action('admin_menu', function() {
    add_options_page('Configuración del ChatNode Toggle', 'ChatNode Toggle', 'manage_options', 'chatnode-toggle', 'chatnode_options_page');
});

// Agrega el enlace de configuración en la página de plugins.
function chatnode_add_settings_link($links) {
    $settings_link = '<a href="options-general.php?page=chatnode-toggle">' . __('Settings', 'wpturbo') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}

$plugin_basename = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin_basename", 'chatnode_add_settings_link');

function chatnode_uninstall_plugin() {
    delete_option('chatnode_chatbot_id');
    delete_option('chatnode_color_1');
    delete_option('chatnode_color_2');
    delete_option('chatnode_tooltip_text');
}

register_uninstall_hook(__FILE__, 'chatnode_uninstall_plugin');