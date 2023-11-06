<?php
/**
 * Database
 *
 * @package     AutomatorWP\Classes\Database
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Database {

    /**
     * Posts table name
     *
     * @since 1.0.0
     *
     * @var string $posts
     */
    public $posts = '';

    /**
     * Post meta table name
     *
     * @since 1.0.0
     *
     * @var string $postmeta
     */
    public $postmeta = '';

    /**
     * Users table name
     *
     * @since 1.0.0
     *
     * @var string $users
     */
    public $users = '';

    /**
     * User meta table name
     *
     * @since 1.0.0
     *
     * @var string $user
     */
    public $usermeta = '';

    /**
     * Automations table name
     *
     * @since 1.0.0
     *
     * @var string $automations
     */
    public $automations = '';

    /**
     * Automations meta table name
     *
     * @since 1.0.0
     *
     * @var string $automations_meta
     */
    public $automations_meta = '';

    /**
     * Triggers table name
     *
     * @since 1.0.0
     *
     * @var string $triggers
     */
    public $triggers = '';

    /**
     * Triggers meta table name
     *
     * @since 1.0.0
     *
     * @var string $triggers_meta
     */
    public $triggers_meta = '';

    /**
     * Actions table name
     *
     * @since 1.0.0
     *
     * @var string $actions
     */
    public $actions = '';

    /**
     * Actions meta table name
     *
     * @since 1.0.0
     *
     * @var string $actions_meta
     */
    public $actions_meta = '';

    /**
     * Logs table name
     *
     * @since 1.0.0
     *
     * @var string $logs
     */
    public $logs = '';

    /**
     * Logs meta table name
     *
     * @since 1.0.0
     *
     * @var string $logs_meta
     */
    public $logs_meta = '';

}