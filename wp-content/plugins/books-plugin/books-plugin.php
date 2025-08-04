<?php
/*
Plugin Name: Books Plugin
Description: Registers a custom post type and REST API for books.
Version: 1.0
Author: Your Name
*/

// Register custom post type
function books_plugin_register_post_type() {
    register_post_type('book', [
        'labels' => [
            'name' => 'Books',
            'singular_name' => 'Book',
        ],
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'author', 'custom-fields'],
        'menu_icon' => 'dashicons-book',
    ]);
}
add_action('init', 'books_plugin_register_post_type');

// REST API route
add_action('rest_api_init', function () {
    register_rest_route('books/v1', '/list', [
        'methods' => 'GET',
        'callback' => 'get_books_list',
    ]);
});

// Callback function to return books data
function get_books_list() {
    $books = get_posts([
        'post_type' => 'book',
        'numberposts' => 5,
    ]);

    $data = [];

    foreach ($books as $book) {
        $data[] = [
            'id'        => $book->ID,
            'title'     => get_the_title($book->ID),
            'content'   => apply_filters('the_content', $book->post_content),
            'author'    => get_post_meta($book->ID, 'author', true),
            'publisher' => get_post_meta($book->ID, 'publisher', true),
            'created_at' => get_the_date('', $book->ID),
        ];
    }

    return $data;
}
