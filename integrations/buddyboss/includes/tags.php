<?php
/**
 * Tags
 *
 * @package     AutomatorWP\BuddyBoss\Tags
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Activity tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_buddyboss_get_activity_tags() {

    return array(
        'activity_id' => array(
            'label'     => __( 'Activity ID', 'automatorwp' ),
            'type'      => 'integer',
            'preview'   => 'The activity ID',
        ),
        'activity_url' => array(
            'label'     => __( 'Activity URL', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The activity URL',
        ),
        'activity_content' => array(
            'label'     => __( 'Activity content', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The activity content',
        ),
        'activity_author_id' => array(
            'label'     => __( 'Activity Author ID', 'automatorwp' ),
            'type'      => 'integer',
            'preview'   => 'The activity author ID',
        ),
    );

}

/**
 * Custom trigger tag replacement
 *
 * @since 1.0.0
 *
 * @param string    $replacement    The tag replacement
 * @param string    $tag_name       The tag name (without "{}")
 * @param stdClass  $trigger        The trigger object
 * @param int       $user_id        The user ID
 * @param string    $content        The content to parse
 * @param stdClass  $log            The last trigger log object
 *
 * @return string
 */
function automatorwp_buddyboss_get_trigger_activity_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {


    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Skip if trigger is not from this integration
    if( $trigger_args['integration'] !== 'buddyboss' ) {
        return $replacement;
    }

    switch( $tag_name ) {
        case 'activity_id':
            $replacement = automatorwp_get_log_meta( $log->id, 'activity_id', true );
            break;
        case 'activity_url':
            $activity_id = absint( automatorwp_get_log_meta( $log->id, 'activity_id', true ) );

            $replacement = bp_activity_get_permalink( $activity_id );
            break;
        case 'activity_content':
            $replacement = automatorwp_get_log_meta( $log->id, 'activity_content', true );
            break;
        case 'activity_author_id':
            $replacement = automatorwp_get_log_meta( $log->id, 'activity_author_id', true );
            break;
        case 'activity_comment_content':
            $replacement = automatorwp_get_log_meta( $log->id, 'activity_comment_content', true );
            break;
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_buddyboss_get_trigger_activity_tag_replacement', 10, 6 );

/**
 * Group tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_buddyboss_get_group_tags() {

    $groups_url =  get_option( 'home' ) . '/groups';

    if( function_exists( 'bp_get_groups_directory_permalink' ) ) {
        $groups_url = bp_get_groups_directory_permalink();
    }

    return array(
        'group_id' => array(
            'label'     => __( 'Group ID', 'automatorwp' ),
            'type'      => 'integer',
            'preview'   => '1',
        ),
        'group_name' => array(
            'label'     => __( 'Group name', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => __( 'My group', 'automatorwp' ),
        ),
        'group_description' => array(
            'label'     => __( 'Group description', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => __( 'My group description', 'automatorwp' ),
        ),
        'group_url' => array(
            'label'     => __( 'Group URL', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => $groups_url . '/my-group',
        ),
        'group_link' => array(
            'label'     => __( 'Group link', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => '<a href="' . $groups_url . '/my-group' . '">' . __( 'My group', 'automatorwp' ) . '</a>',
        ),
    );

}

/**
 * Custom trigger group tag replacement
 *
 * @since 1.0.0
 *
 * @param string    $replacement    The tag replacement
 * @param string    $tag_name       The tag name (without "{}")
 * @param stdClass  $trigger        The trigger object
 * @param int       $user_id        The user ID
 * @param string    $content        The content to parse
 * @param stdClass  $log            The last trigger log object
 *
 * @return string
 */
function automatorwp_buddyboss_get_trigger_group_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {


    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Skip if trigger is not from this integration
    if( $trigger_args['integration'] !== 'buddyboss' ) {
        return $replacement;
    }

    // Bail if groups module is not active
    if( ! function_exists( 'groups_get_group' ) ) {
        return $replacement;
    }

    $group_id = absint( automatorwp_get_log_meta( $log->id, 'group_id', true ) );

    // Bail if not group ID store
    if( $group_id === 0 ) {
        return $replacement;
    }

    $group = groups_get_group( $group_id );

    if( ! $group ) {
        return $replacement;
    }

    switch( $tag_name ) {
        case 'group_id':
            $replacement = $group_id;
            break;
        case 'group_name':
            $replacement = $group->name;
            break;
        case 'group_description':
            $replacement = $group->description;
            break;
        case 'group_url':
            $replacement = bp_get_group_permalink( $group );
            break;
        case 'group_link':
            $replacement = '<a href="' . bp_get_group_permalink( $group ) . '">' . $group->name . '</a>';
            break;
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_buddyboss_get_trigger_group_tag_replacement', 10, 6 );

/**
 * Invitation tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_buddyboss_get_invitation_tags() {

    return array(
        'inviter_id' => array(
            'label'     => __( 'Inviter ID', 'automatorwp' ),
            'type'      => 'integer',
            'preview'   => '1',
        ),
        'invited_id' => array(
            'label'     => __( 'Invited ID', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => '2',
        ),
    );

}

/**
 * Custom trigger invitation tag replacement
 *
 * @since 1.0.0
 *
 * @param string    $replacement    The tag replacement
 * @param string    $tag_name       The tag name (without "{}")
 * @param stdClass  $trigger        The trigger object
 * @param int       $user_id        The user ID
 * @param string    $content        The content to parse
 * @param stdClass  $log            The last trigger log object
 *
 * @return string
 */
function automatorwp_buddyboss_get_trigger_invitation_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {


    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Skip if trigger is not from this integration
    if( $trigger_args['integration'] !== 'buddyboss' ) {
        return $replacement;
    }

    switch( $tag_name ) {
        case 'inviter_id':
            $replacement = automatorwp_get_log_meta( $log->id, 'inviter_id', true );
            break;
        case 'invited_id':
            $replacement = automatorwp_get_log_meta( $log->id, 'invited_id', true );
            break;
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_buddyboss_get_trigger_invitation_tag_replacement', 10, 6 );