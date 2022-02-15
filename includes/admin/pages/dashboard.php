<?php
/**
 * Admin Dashboard Page
 *
 * @package     AutomatorWP\Admin\Dashboard
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       2.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Dashboard page
 *
 * @since  2.0.0
 */
function automatorwp_dashboard_page() {
    ?>
    <div class="wrap automatorwp-dashboard">

        <div id="icon-options-general" class="icon32"></div>
        <h1 class="wp-heading-inline"><?php _e( 'Dashboard', 'automatorwp' ); ?></h1>
        <hr class="wp-header-end">

        <div id="dashboard-widgets-wrap">
            <div id="dashboard-widgets" class="metabox-holder">

                <div id="normal-sortables" class="meta-box-sortables ui-sortable">

                    <?php // Logo ?>
                    <div class="automatorwp-dashboard-logo">
                        <img src="<?php echo AUTOMATORWP_URL . 'assets/img/automatorwp-brand-logo.svg' ?>" alt="AutomatorWP">
                        <strong class="automatorwp-dashboard-version">v<?php echo AUTOMATORWP_VER; ?></strong>
                    </div>

                    <?php // Welcome ?>
                    <?php automatorwp_dashboard_box( array(
                        'id' => 'welcome',
                        'title' => __( 'Welcome to AutomatorWP', 'automatorwp' ),
                        'content_cb' => 'automatorwp_dashboard_welcome_box',
                    ) ); ?>

                </div>

                <div id="postbox-container-1" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">

                        <?php // Getting started ?>
                        <?php automatorwp_dashboard_box( array(
                            'id' => 'docs',
                            'title' => __( 'Getting started', 'automatorwp' ),
                            'content_cb' => 'automatorwp_dashboard_docs_box',
                        ) ); ?>

                    </div>
                </div>

                <div id="postbox-container-2" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">

                        <?php // Advanced features ?>
                        <?php automatorwp_dashboard_box( array(
                            'id' => 'advanced',
                            'title' => __( 'Advanced features', 'automatorwp' ),
                            'content_cb' => 'automatorwp_dashboard_advanced_box',
                        ) ); ?>

                    </div>
                </div>

                <div id="postbox-container-3" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">

                        <?php // Team ?>
                        <?php automatorwp_dashboard_box( array(
                            'id' => 'team',
                            'title' => __( 'Meet the team', 'automatorwp' ),
                            'content_cb' => 'automatorwp_dashboard_team_box',
                        ) ); ?>

                    </div>
                </div>

                <div id="postbox-container-4" class="postbox-container">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">

                    <?php // Get involved ?>
                    <?php automatorwp_dashboard_box( array(
                            'id' => 'social',
                            'title' => __( 'Follow us', 'automatorwp' ),
                            'content_cb' => 'automatorwp_dashboard_social_box',
                    ) ); ?>

                    </div>
                </div>

            </div>
        </div>

    </div>
    <?php
}

/**
 * Dashboard page
 *
 * @since  2.0.0
 */
function automatorwp_dashboard_box( $args ) {

    $args = wp_parse_args( $args, array(
        'id' => '',
        'title' => '',
        'content' => '',
        'content_cb' => '',
    ) );

    ?>
        <div id="automatorwp-dashboard-<?php echo $args['id']; ?>" class="automatorwp-dashboard-box postbox">

            <div class="postbox-header">
                <h2 class="hndle"><?php echo $args['title']; ?></h2>
            </div>

            <div class="inside">

                <?php if( is_callable( $args['content_cb'] ) ) {
                    call_user_func( $args['content_cb'] );
                } else {
                    echo $args['content'];
                } ?>

            </div>

        </div>
    <?php

}

/**
 * Dashboard welcome box
 *
 * @since  2.0.0
 */
function automatorwp_dashboard_welcome_box() {
    ?>
    <div class="automatorwp-dashboard-columns">

        <div class="automatorwp-dashboard-column automatorwp-dashboard-main-video">
            <iframe width="560" height="315" src="https://www.youtube.com/embed/8CcRMWx9EtA" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>

        <div class="automatorwp-dashboard-column automatorwp-dashboard-videos-list">
            <h3><?php _e( 'More videos', 'automatorwp' ) ?></h3>
            <div class="automatorwp-dashboard-videos">
                <?php
                $videos = array(
                    array(
                        'id' => 'vWqRcEO8SgY',
                        'title' => 'Creating your first automation on WordPress',
                        'duration' => '1:53',
                    ),
                    array(
                        'id' => '-tlRUR1HKR0',
                        'title' => 'Register new users using Gravity Forms and AutomatorWP',
                        'duration' => '2:55',
                    ),
                    array(
                        'id' => '3YMMQ6QqrDo',
                        'title' => ' Connect automations from different sites through Webhooks',
                        'duration' => '2:55',
                    ),
                    array(
                        'id' => 'JHCWVbLqR0A',
                        'title' => 'Give access to site B when a purchase is made on site A (WooCommerce & LifterLMS)',
                        'duration' => '4:32',
                    ),
                );

                foreach( $videos as $video ) { ?>
                    <div class="automatorwp-dashboard-video">
                        <a href="https://www.youtube.com/watch?v=<?php echo $video['id']; ?>" target="_blank">
                            <div class="automatorwp-dashboard-video-image">
                                <img src="https://img.youtube.com/vi/<?php echo $video['id']; ?>/default.jpg" alt="">
                            </div>
                            <div class="automatorwp-dashboard-video-details">
                                <strong class="automatorwp-dashboard-video-title"><?php echo $video['title']; ?></strong>
                                <div class="automatorwp-dashboard-video-duration"><?php echo $video['duration']; ?></div>
                            </div>
                        </a>
                    </div>
                <?php }

                ?>
            </div>
            <div class="automatorwp-dashboard-more-videos">
                <a href="https://www.youtube.com/channel/UCDBAqLYtoCYYUe2K_kx9Crw/videos" target="_blank"><?php _e( 'View all videos', 'automatorwp' ); ?></a>
            </div>
        </div>

        <div class="automatorwp-dashboard-column automatorwp-dashboard-get-involved">
            <p><?php _e( 'AutomatorWP is a free and open-source plugin accessible to everyone just like WordPress. There are many ways you can help support AutomatorWP', 'automatorwp' ); ?></p>
            <ul>
                <li><a href="https://github.com/rubengc/AutomatorWP" target="_blank"><i class="dashicons dashicons-admin-tools"></i> <?php _e( 'Get involved with AutomatorWP development.', 'automatorwp' ); ?></a></li>
                <li><a href="https://translate.wordpress.org/projects/wp-plugins/automatorwp/" target="_blank"><i class="dashicons dashicons-translation"></i> <?php _e( 'Translate AutomatorWP into your language.', 'automatorwp' ); ?></a></li>
                <li><a href="https://wordpress.org/plugins/automatorwp/#reviews" target="_blank"><i class="dashicons dashicons-wordpress"></i> <?php _e( 'Review AutomatorWP on WordPress.org.', 'automatorwp' ); ?></a></li>
            </ul>
            <p><?php _e( 'Pro add-ons help to maintain the project and offer the most advanced features.', 'automatorwp' ); ?></p>
            <div class="automatorwp-dashboard-pricing-button">
                <a href="https://automatorwp.com/pricing/" target="_blank" class="button button-primary"><?php _e( 'View plans and pricing', 'automatorwp' ); ?></a>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Dashboard docs box
 *
 * @since  2.0.0
 */
function automatorwp_dashboard_docs_box() {
    ?>
    <ul>
        <li><a href="https://automatorwp.com/docs/getting-started/what-is-automatorwp/" target="_blank"><?php _e( 'What is AutomatorWP?', 'automatorwp' ); ?></a></li>
        <li><a href="https://automatorwp.com/docs/getting-started/automations/" target="_blank"><?php _e( 'Automations', 'automatorwp' ); ?></a></li>
        <li><a href="https://automatorwp.com/docs/getting-started/anonymous-automations/" target="_blank"><?php _e( 'Anonymous Automations', 'automatorwp' ); ?></a></li>
        <li><a href="https://automatorwp.com/docs/getting-started/triggers/" target="_blank"><?php _e( 'Triggers', 'automatorwp' ); ?></a></li>
        <li><a href="https://automatorwp.com/docs/getting-started/actions/" target="_blank"><?php _e( 'Actions', 'automatorwp' ); ?></a></li>
        <li><a href="https://automatorwp.com/docs/getting-started/filters/" target="_blank"><?php _e( 'Filters', 'automatorwp' ); ?></a></li>
        <li><a href="https://automatorwp.com/docs/getting-started/tags/" target="_blank"><?php _e( 'Tags', 'automatorwp' ); ?></a></li>
        <li><a href="https://automatorwp.com/docs/getting-started/logs/" target="_blank"><?php _e( 'Logs', 'automatorwp' ); ?></a></li>
    </ul>
    <?php
}

/**
 * Dashboard advanced box
 *
 * @since  2.0.0
 */
function automatorwp_dashboard_advanced_box() {
    ?>
    <ul>
        <li>
            <h3><?php _e( 'Features', 'automatorwp' ); ?></h3>
            <ul>
                <li><a href="https://automatorwp.com/docs/features/sequential-triggers/" target="_blank"><?php _e( 'Sequential triggers', 'automatorwp' ); ?></a></li>
                <li><a href="https://automatorwp.com/docs/features/redirect-users/" target="_blank"><?php _e( 'Redirect users', 'automatorwp' ); ?></a></li>
                <li><a href="https://automatorwp.com/docs/features/import-export-automations-through-url/" target="_blank"><?php _e( 'Import & Export automations through URL', 'automatorwp' ); ?></a></li>
            </ul>
        </li>
        <li>
            <h3><?php _e( 'Special actions', 'automatorwp' ); ?></h3>
            <ul>
                <li><a href="https://automatorwp.com/docs/special-actions/call-a-function/" target="_blank"><?php _e( 'Call a function', 'automatorwp' ); ?></a></li>
                <li><a href="https://automatorwp.com/docs/special-actions/run-a-wordpress-hook/" target="_blank"><?php _e( 'Run a WordPress hook', 'automatorwp' ); ?></a></li>
                <li><a href="https://automatorwp.com/docs/special-actions/multiple-posts-actions/" target="_blank"><?php _e( 'Multiple posts actions', 'automatorwp' ); ?></a></li>
            </ul>
        </li>
        <li>
            <h3><?php _e( 'Special tags', 'automatorwp' ); ?></h3>
            <ul>
                <li><a href="https://automatorwp.com/docs/special-tags/user-meta-tag/" target="_blank"><?php _e( 'User meta tag', 'automatorwp' ); ?></a></li>
                <li><a href="https://automatorwp.com/docs/special-tags/post-meta-tag/" target="_blank"><?php _e( 'Post meta tag', 'automatorwp' ); ?></a></li>
                <li><a href="https://automatorwp.com/docs/special-tags/date-tag/" target="_blank"><?php _e( 'Date tag', 'automatorwp' ); ?></a></li>
            </ul>
        </li>
    </ul>
    <?php
}

/**
 * Dashboard team box
 *
 * @since  2.0.0
 */
function automatorwp_dashboard_team_box() {
    ?>
    <ul id="contributors-list" class="contributors-list">
        <li>
            <a href="https://profiles.wordpress.org/rubengc/" target="_blank">
                <img alt="" src="https://secure.gravatar.com/avatar/103d0ec19ade3804009f105974fd4d05?s=64&amp;d=mm&amp;r=g" class="avatar avatar-32 photo" loading="lazy">
                <span>Ruben Garcia</span>
            </a>
        </li>
        <li>
            <a href="https://profiles.wordpress.org/eneribs/" target="_blank">
                <img alt="" src="https://secure.gravatar.com/avatar/7103ea44d40111ab67a22efe7ebd6f71?s=64&amp;d=mm&amp;r=g" class="avatar avatar-32 photo" loading="lazy">
                <span>Irene Berna</span>
            </a>
        </li>
        <li>
            <a href="https://profiles.wordpress.org/pacogon/" target="_blank">
                <img alt="" src="https://secure.gravatar.com/avatar/348f374779e7433ad6bf3930cb2a492e?s=64&amp;d=mm&amp;r=g" class="avatar avatar-32 photo" loading="lazy">
                <span>Paco González</span>
            </a>
        </li>
        <li>
            <a href="https://profiles.wordpress.org/dioni00/" target="_blank">
                <img alt="" src="https://secure.gravatar.com/avatar/6de68ad3863fdf3c92a194ba16546571?s=64&amp;d=mm&amp;r=g" class="avatar avatar-32 photo" loading="lazy">
                <span>Dionisio Sanchez</span>
            </a>
        </li>
        <li>
            <a href="https://profiles.wordpress.org/flabernardez/" target="_blank">
                <img alt="" src="https://secure.gravatar.com/avatar/fd626d9a8463260894f0f6f07a5cc71a?s=64&amp;d=mm&amp;r=g" class="avatar avatar-32 photo" loading="lazy">
                <span>Flavia Bernardez</span>
            </a>
        </li>
    </ul>
    <?php
}

/**
 * Dashboard involved box
 *
 * @since  2.0.0
 */
function automatorwp_dashboard_social_box() {
    ?>
    <p><?php _e( 'Follow us in your favorite social network!', 'automatorwp' ); ?></p>
    <ul class="automatorwp-dashboard-social-list">
        <li><a href="https://www.youtube.com/channel/UCDBAqLYtoCYYUe2K_kx9Crw" target="_blank"><i class="dashicons dashicons-youtube"></i> <?php _e( 'Subscribe to our YouTube channel.', 'automatorwp' ); ?></a></li>
        <li><a href="https://www.facebook.com/AutomatorWP/" target="_blank"><i class="dashicons dashicons-facebook"></i> <?php _e( 'Follow us on Facebook.', 'automatorwp' ); ?></a></li>
        <li><a href="https://www.facebook.com/groups/automatorwp" target="_blank"><i class="dashicons dashicons-facebook"></i> <?php _e( 'Join our Facebook community.', 'automatorwp' ); ?></a></li>
        <li><a href="https://twitter.com/AutomatorWP" target="_blank"><i class="dashicons dashicons-twitter"></i> <?php _e( 'Follow @AutomatorWP on Twitter.', 'automatorwp' ); ?></a></li>
        <li><a href="https://www.linkedin.com/company/65262548/" target="_blank"><i class="dashicons dashicons-linkedin"></i> <?php _e( 'Follow us on LinkedIn.', 'automatorwp' ); ?></a></li>
    </ul>
    <?php
}