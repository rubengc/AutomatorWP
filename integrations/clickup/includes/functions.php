<?php
/**
 * Functions
 *
 * @package     AutomatorWP\ClickUp\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Helper function to get the ClickUp url
 *
 * @since 1.0.0
 *
 * @return string
 */
function automatorwp_clickup_get_url() {

    return 'https://api.clickup.com/api/v2';

}

/**
 * Helper function to get the ClickUp API parameters
 *
 * @since 1.0.0
 *
 * @return array|false
 */
function automatorwp_clickup_get_api() {

    $url = automatorwp_clickup_get_url();
    $token = automatorwp_clickup_get_option( 'token', '' );

    if( empty( $token ) ) {
        return false;
    }

    return array(
        'url' => $url,
        'token' => $token,
    );

}

/**
 * Get teams from ClickUp
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_clickup_get_teams( ) {

    $teams = array();

    $api = automatorwp_clickup_get_api();

    if( ! $api ) {
        return $options;
    }

    $response = wp_remote_get( $api['url'] . '/team', array(
        'headers' => array(
            'Authorization' => $api['token'],
            'Accept' => 'application/json',
            'Content-Type'  => 'application/json'
        )
    ) );
    
    $response = json_decode( wp_remote_retrieve_body( $response ), true  );
    
    foreach ( $response['teams'] as $team ){

        $teams[] = array(
            'id'    => $team['id'],
            'name'  => $team['name'],
        );
        
    }

    return $teams;

}

/**
 * Get team from ClickUp
 *
 * @since 1.0.0
 * 
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_clickup_options_cb_team( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any team', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );
    
    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }
    
        foreach( $value as $team_id ) {

            // Skip option none
            if( $team_id === $none_value ) {
                continue;
            }

            $options[$team_id] = automatorwp_clickup_get_team_name( $team_id );
        }
    }

    return $options;

}

/**
* Get the team name
*
* @since 1.0.0
* 
* @param string $team_id
*
* @return array
*/
function automatorwp_clickup_get_team_name( $team_id ) {

    $api = automatorwp_clickup_get_api();
    
    if( ! $api ) {
        return $options;
    }

    $response = wp_remote_get( 'https://api.clickup.com/api/v2/team/' . $team_id, array(
        'headers' => array(
            'Authorization' => $api['token'],
        ),
    ) );

    $response = json_decode( wp_remote_retrieve_body( $response ), true  );
    
    if ( isset ( $response['error']['code'] ) === 404 || !isset ( $response['team']['name'] )  ){
        return;
    }
    
    return $response['team']['name'];
}

/**
 * Get spaces from ClickUp
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_clickup_get_spaces( $team_id ) {

    $spaces = array();

    $api = automatorwp_clickup_get_api();

    if( ! $api ) {
        return $options;
    }

    //$response = wp_remote_get( $api['url'] . '/team/9008180486/space', array(
    $response = wp_remote_get( $api['url'] . '/team/' . $team_id . '/space', array(
        'headers' => array(
            'Authorization' => $api['token'],
            'Accept' => 'application/json',
            'Content-Type'  => 'application/json'
        )
    ) );
    
    $response = json_decode( wp_remote_retrieve_body( $response ), true  );
    
    foreach ( $response['spaces'] as $space ){

        $spaces[] = array(
            'id'    => $space['id'],
            'name'  => $space['name'],
        );
        
    }

    return $spaces;

}

/**
 * Get space from ClickUp
 *
 * @since 1.0.0
 * 
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_clickup_options_cb_space( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any space', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    $team_id = ct_get_object_meta( $field->object_id, 'team', true );    
    
    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }
    
        foreach( $value as $space_id ) {

            // Skip option none
            if( $space_id === $none_value ) {
                continue;
            }

            $options[$space_id] = automatorwp_clickup_get_space_name( $space_id );
        }
    }

    return $options;

}

/**
* Get the space name
*
* @since 1.0.0
* 
* @param string $space_id
*
* @return array
*/
function automatorwp_clickup_get_space_name( $space_id ) {

    $api = automatorwp_clickup_get_api();

    if( ! $api ) {
        return $options;
    }

    $response = wp_remote_get( 'https://api.clickup.com/api/v2/space/' . $space_id, array(
        'headers' => array(
            'Authorization' => $api['token'],
        ),
    ) );

    $response = json_decode( wp_remote_retrieve_body( $response ), true  );
    
    if ( isset ( $response['error']['code'] ) === 404 || !isset ( $response['name'] )  ){
        return;
    }
    
    return $response['name'];
}

/**
 * Get folders from ClickUp
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_clickup_get_folders( $space_id ) {

    $folders = array();

    $folders[] = array(
        'id'    => 0,
        'name'  => __( 'Folderless lists', 'automatorwp' ),
    );

    $api = automatorwp_clickup_get_api();

    if( ! $api ) {
        return $options;
    }

    $response = wp_remote_get( $api['url'] . '/space/'. $space_id . '/folder', array(
        'headers' => array(
            'Authorization' => $api['token'],
            'Accept' => 'application/json',
            'Content-Type'  => 'application/json'
        )
    ) );
    
    $response = json_decode( wp_remote_retrieve_body( $response ), true  );
    
    if ( empty( $response['folders'] ) ) {
        return $folders;
    }

    foreach ( $response['folders'] as $folder ) {

        $folders[] = array(
            'id'    => $folder['id'],
            'name'  => $folder['name'],
        );
        
    }

    return $folders;

}

/**
 * Get folder from ClickUp
 *
 * @since 1.0.0
 * 
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_clickup_options_cb_folder( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any folder', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    $space_id = ct_get_object_meta( $field->object_id, 'space', true );   
    
    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }
    
        foreach( $value as $folder_id ) {

            // Skip option none
            if( $folder_id === $none_value ) {
                continue;
            }

            $options[$folder_id] = automatorwp_clickup_get_folder_name( $folder_id );
        }
    }

    return $options;

}

/**
* Get the folder name
*
* @since 1.0.0
* 
* @param string $folder_id
*
* @return array
*/
function automatorwp_clickup_get_folder_name( $folder_id ) {

    $api = automatorwp_clickup_get_api();

    if( ! $api ) {
        return $options;
    }

    if ( $folder_id === 0 ) {
        return __( 'Folderless lists', 'automatorwp' );
    }

    $response = wp_remote_get( 'https://api.clickup.com/api/v2/folder/' . $folder_id, array(
        'headers' => array(
            'Authorization' => $api['token'],
        ),
    ) );

    $response = json_decode( wp_remote_retrieve_body( $response ), true  );
    
    if ( isset ( $response['error']['code'] ) === 404 ){
        return;
    }
    
    return $response['name'];
}


/**
 * Get lists from ClickUp
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_clickup_get_lists( $folder_id ) {

    $lists = array();

    $api = automatorwp_clickup_get_api();

    if( ! $api ) {
        return $options;
    }

    $response = wp_remote_get( $api['url'] . '/folder/'. $folder_id . '/list', array(
        'headers' => array(
            'Authorization' => $api['token'],
        )
    ) );
    
    $response = json_decode( wp_remote_retrieve_body( $response ), true  );
    
    if ( empty( $response['lists'] ) ) {
        return $lists;
    }

    foreach ( $response['lists'] as $list ) {

        $lists[] = array(
            'id'    => $list['id'],
            'name'  => $list['name'],
        );
        
    }

    return $lists;

}

/**
 * Get list from ClickUp
 *
 * @since 1.0.0
 * 
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_clickup_options_cb_list( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any list', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    $folder_id = ct_get_object_meta( $field->object_id, 'folder', true );   
    
    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }
    
        foreach( $value as $list_id ) {

            // Skip option none
            if( $list_id === $none_value ) {
                continue;
            }

            $options[$list_id] = automatorwp_clickup_get_list_name( $list_id );
        }
    }

    return $options;

}

/**
* Get the list name
*
* @since 1.0.0
* 
* @param string $list_id
*
* @return array
*/
function automatorwp_clickup_get_list_name( $list_id ) {

    $api = automatorwp_clickup_get_api();

    if( ! $api ) {
        return $options;
    }

    $response = wp_remote_get( 'https://api.clickup.com/api/v2/list/' . $list_id, array(
        'headers' => array(
            'Authorization' => $api['token'],
        ),
    ) );

    $response = json_decode( wp_remote_retrieve_body( $response ), true  );
    
    if ( isset ( $response['error']['code'] ) === 404 ){
        return;
    }
    
    return $response['name'];
}


/**
 * Get tasks from ClickUp
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_clickup_get_tasks( $list_id ) {

    $tasks = array();

    $api = automatorwp_clickup_get_api();

    if( ! $api ) {
        return $options;
    }

    $response = wp_remote_get( $api['url'] . '/list/'. $list_id . '/task', array(
        'headers' => array(
            'Authorization' => $api['token'],
        )
    ) );
    
    $response = json_decode( wp_remote_retrieve_body( $response ), true  );
    
    if ( empty( $response['tasks'] ) ) {
        return $tasks;
    }

    foreach ( $response['tasks'] as $task ) {

        $tasks[] = array(
            'id'    => $task['id'],
            'name'  => $task['name'],
        );
        
    }

    return $tasks;

}

/**
 * Get task from ClickUp
 *
 * @since 1.0.0
 * 
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_clickup_options_cb_task( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any task', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    $list_id = ct_get_object_meta( $field->object_id, 'list', true );   
    
    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }
    
        foreach( $value as $task_id ) {

            // Skip option none
            if( $task_id === $none_value ) {
                continue;
            }

            $options[$task_id] = automatorwp_clickup_get_task_name( $task_id );
        }
    }

    return $options;

}

/**
* Get the task name
*
* @since 1.0.0
* 
* @param string $task_id
*
* @return array
*/
function automatorwp_clickup_get_task_name( $task_id ) {

    $api = automatorwp_clickup_get_api();

    if( ! $api ) {
        return $options;
    }

    $response = wp_remote_get( 'https://api.clickup.com/api/v2/task/' . $task_id, array(
        'headers' => array(
            'Authorization' => $api['token'],
        ),
    ) );

    $response = json_decode( wp_remote_retrieve_body( $response ), true  );
    
    if ( isset ( $response['error']['code'] ) === 404 ) {
        return;
    }

    if ( !isset ( $response['name'] ) ) {
        return;
    }

    return $response['name'];
}

/**
 * Create list to ClickUp
 *
 * @since 1.0.0
 * 
 * @param string    $list       The list name to create
 * @param double    $space_id   Space ID
 * @param double    $folder_id  Folder ID
 */
function automatorwp_clickup_create_list( $list, $space_id, $folder_id ) {

    $api = automatorwp_clickup_get_api();

    if( ! $api ) {
        return;
    }

    if ( $folder_id === 0 ) {
        $url_request = $api['url'] . '/space/' . $space_id . '/list';
    } else {
        $url_request = $api['url'] . '/folder/' . $folder_id . '/list';
    }

    $response = wp_remote_post( $url_request, array(
        'headers' => array(
            'Authorization' => $api['token'],
            'Accept' => 'application/json',
            'Content-Type'  => 'application/json'
        ),
        'body' => json_encode( array(
            'name'     => $list,
        ) )
    ) );
 
    return $response['response']['code'];
}

/**
 * Add comment to a task
 *
 * @since 1.0.0
 * 
 * @param double    $task_id        Space ID
 * @param string    $comment_text   Comment text
 * 
 * @return int
 */
function automatorwp_clickup_add_comment( $task_id, $comment_text ) {

    $api = automatorwp_clickup_get_api();

    if( ! $api ) {
        return;
    }

    $response = wp_remote_post( $api['url'] . '/task/' . $task_id . '/comment', array(
        'headers' => array(
            'Authorization' => $api['token'],
            'Accept' => 'application/json',
            'Content-Type'  => 'application/json'
        ),
        'body' => json_encode( array(
            'comment_text'     => $comment_text,
        ) )
    ) );
 
    return $response['response']['code'];
}

/**
 * Add tag to a task
 *
 * @since 1.0.0
 * 
 * @param double    $task_id    Space ID
 * @param string    $tag        Tag
 * 
 * @return int
 */
function automatorwp_clickup_add_tag( $task_id, $tag ) {

    $api = automatorwp_clickup_get_api();

    if( ! $api ) {
        return;
    }

    $response = wp_remote_post( $api['url'] . '/task/' . $task_id . '/tag/' . $tag, array(
        'headers' => array(
            'Authorization' => $api['token'],
            'Accept' => 'application/json',
            'Content-Type'  => 'application/json'
        )
    ) );
 
    return $response['response']['code'];
}

/**
 * Remove tag from a task
 *
 * @since 1.0.0
 * 
 * @param double    $task_id    Space ID
 * @param string    $tag        Tag
 * 
 * @return int
 */
function automatorwp_clickup_remove_tag( $task_id, $tag ) {

    $api = automatorwp_clickup_get_api();

    if( ! $api ) {
        return;
    }

    $response = wp_remote_request( $api['url'] . '/task/' . $task_id . '/tag/' . $tag, array(
        'method' => 'DELETE',
        'headers' => array(
            'Authorization' => $api['token'],
            'Accept' => 'application/json',
            'Content-Type'  => 'application/json'
        )
    ) );
 
    return $response['response']['code'];
}

/**
 * Add comment to a task
 *
 * @since 1.0.0
 * 
 * @param double    $list_id            List ID
 * @param string    $name_task          Name task
 * @param string    $description_task   Description task
 * 
 * @return int
 */
function automatorwp_clickup_create_task( $list_id, $name_task, $description_task = '' ) {

    $api = automatorwp_clickup_get_api();

    if( ! $api ) {
        return;
    }

    $response = wp_remote_post( $api['url'] . '/list/' . $list_id . '/task', array(
        'headers' => array(
            'Authorization' => $api['token'],
            'Accept' => 'application/json',
            'Content-Type'  => 'application/json'
        ),
        'body' => json_encode( array(
            'name'          => $name_task,
            'description'   => $description_task
        ) )
    ) );
 
    return $response['response']['code'];
}

/**
 * Remove a task
 *
 * @since 1.0.0
 * 
 * @param double    $task_id    Task ID
 * 
 * @return int
 */
function automatorwp_clickup_remove_task( $task_id ) {

    $api = automatorwp_clickup_get_api();

    if( ! $api ) {
        return;
    }

    $response = wp_remote_request( $api['url'] . '/task/' . $task_id, array(
        'method' => 'DELETE',
        'headers' => array(
            'Authorization' => $api['token'],
            'Accept' => 'application/json',
            'Content-Type'  => 'application/json'
        )
    ) );
 
    return $response['response']['code'];
}