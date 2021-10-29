<?php
/**
 * Functions
 *
 * @package     AutomatorWP\LearnDash\Functions
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Retrieves the post ID.
 *
 * @since 1.0.0
 *
 * @param mixed $thing
 *
 * @return int|false
 */
function automatorwp_learndash_get_post_id( $thing ) {

    if( $thing instanceof WP_Post ) {
        return absint( $thing->ID );
    }

    if( is_numeric( $thing ) ) {

        if( absint( $thing ) === 0 ) {
            return false;
        } else {
            return absint( $thing );
        }
    }

    return false;
}

/**
 * Helper function to mark a quiz as completed
 *
 * @since 1.0.0
 *
 * @param int $user_id
 * @param int $quiz_id
 * @param int $course_id
 */
function automatorwp_learndash_mark_quiz_as_completed( $user_id = 0, $quiz_id = 0, $course_id = 0 ) {

    $user_meta      = get_user_meta( $user_id, '_sfwd-quizzes', true );
    $quiz_progress  = empty( $user_meta ) ? array() : $user_meta;

    $quiz_meta = get_post_meta( $quiz_id, '_sfwd-quiz', true );

    $quiz_data = array(
        'quiz'             => $quiz_id,
        'score'            => 0,
        'count'            => 0,
        'pass'             => true,
        'rank'             => '-',
        'time'             => time(),
        'pro_quizid'       => $quiz_meta['sfwd-quiz_quiz_pro'],
        'course'           => $course_id,
        'points'           => 0,
        'total_points'     => 0,
        'percentage'       => 0,
        'timespent'        => 0,
        'has_graded'       => false,
        'statistic_ref_id' => 0,
        'm_edit_by'        => 9999999,  // Manual Edit By ID.
        'm_edit_time'      => time(),   // Manual Edit timestamp.
    );

    $quiz_progress[] = $quiz_data;

    // Add the quiz entry to the user activity
    learndash_update_user_activity(
        array(
            'course_id'          => $course_id,
            'user_id'            => $user_id,
            'post_id'            => $quiz_id,
            'activity_type'      => 'quiz',
            'activity_action'    => 'insert',
            'activity_status'    => $quiz_data['pass'],
            'activity_started'   => $quiz_data['time'],
            'activity_completed' => $quiz_data['time'],
            'activity_meta'      => $quiz_data,
        )
    );

    // Update user quiz progress
    if ( ! empty( $quiz_progress ) ) {
        update_user_meta( $user_id, '_sfwd-quizzes', $quiz_progress );
    }

}

/**
 * Helper function to mark a topic as completed
 *
 * @since 1.0.0
 *
 * @param int $user_id
 * @param int $topic_id
 * @param int $course_id
 */
function automatorwp_learndash_mark_topic_as_completed( $user_id = 0, $topic_id = 0, $course_id = 0 ) {

    // Get all topic's quizzes
    $quizzes = learndash_get_lesson_quiz_list( $topic_id, $user_id, $course_id ); // learndash_get_lesson_quiz_list() works for topics too

    if( is_array( $quizzes ) ) {

        foreach( $quizzes as $quiz_data ) {
            // Mark quiz as completed
            automatorwp_learndash_mark_quiz_as_completed( $user_id, $quiz_data['post']->ID, $course_id );
        }

    }

    // Mark topic as completed
    learndash_process_mark_complete( $user_id, $topic_id, false, $course_id );

}

/**
 * Helper function to mark a lesson as completed
 *
 * @since 1.0.0
 *
 * @param int $user_id
 * @param int $lesson_id
 * @param int $course_id
 */
function automatorwp_learndash_mark_lesson_as_completed( $user_id = 0, $lesson_id = 0, $course_id = 0 ) {

    // Get all lesson topics
    $topics = learndash_get_topic_list( $lesson_id, $course_id );

    if( is_array( $topics ) ) {

        foreach( $topics as $topic ) {
            // Mark topic as completed
            automatorwp_learndash_mark_topic_as_completed( $user_id, $topic->ID, $course_id );
        }

    }

    // Get all lesson's quizzes
    $quizzes = learndash_get_lesson_quiz_list( $lesson_id, $user_id, $course_id );

    if( is_array( $quizzes ) ) {

        foreach( $quizzes as $quiz_data ) {
            // Mark quiz as completed
            automatorwp_learndash_mark_quiz_as_completed( $user_id, $quiz_data['post']->ID, $course_id );
        }

    }

    // Mark lesson as completed
    learndash_process_mark_complete( $user_id, $lesson_id, false, $course_id );

}

/**
 * Helper function to mark a course as completed
 *
 * @since 1.0.0
 *
 * @param int $user_id
 * @param int $course_id
 */
function automatorwp_learndash_mark_course_as_completed( $user_id = 0, $course_id = 0 ) {

    // Get all course lessons
    $lessons = learndash_get_lesson_list( $course_id, array( 'num' => 0 ) );

    if( is_array( $lessons ) ) {

        foreach( $lessons as $lesson ) {
            // Mark lesson as completed
            automatorwp_learndash_mark_lesson_as_completed( $user_id, $lesson->ID, $course_id );
        }

    }

    // Get all course quizzes
    $quizzes = learndash_get_course_quiz_list( $course_id, $user_id );

    if( is_array( $quizzes ) ) {

        foreach( $quizzes as $quiz_data ) {
            // Mark course quizzes as completed
            automatorwp_learndash_mark_quiz_as_completed( $user_id, $quiz_data['post']->ID, $course_id );
        }

    }

    // Mark course as completed
    $completed = learndash_process_mark_complete( $user_id, $course_id, false, $course_id );

}