<?php

function journal_page_id(string $title, string $slug): int {
    $page = get_page_by_path($slug, OBJECT, 'page');
    if ($page) {
        return (int) $page->ID;
    }

    return (int) wp_insert_post([
        'post_type' => 'page',
        'post_title' => $title,
        'post_name' => $slug,
        'post_status' => 'publish',
    ]);
}

$home_id = journal_page_id('Home', 'home');
journal_page_id('About', 'about');
$blog_id = journal_page_id('Blog', 'blog');
journal_page_id('Contact', 'contact');

update_option('show_on_front', 'page');
update_option('page_on_front', $home_id);
update_option('page_for_posts', $blog_id);

$post_slug = 'why-i-started-documenting-my-life';
$existing_post = get_page_by_path($post_slug, OBJECT, 'post');

if (!$existing_post) {
    wp_insert_post([
        'post_type' => 'post',
        'post_title' => 'Why I Started Documenting My Life',
        'post_name' => $post_slug,
        'post_status' => 'publish',
        'post_content' => implode("\n\n", [
            'Documenting life makes the small lessons easier to notice. I started this journal to capture useful habits, honest experiments, and the practical details that usually disappear by the end of the week.',
            'The plan is simple: test one idea at a time, write down what worked, and turn the result into a tip someone else can use. Some posts will be about routines, some will be about tools, and some will be reflections after trying something new.',
            'If you are building your own life lab, start small. Pick one area, make one change, measure it for a few days, and keep the notes clear enough that your future self can learn from them.',
        ]),
    ]);
}

foreach ([
    'Life Tips' => 'life-tips',
    'Experiments' => 'experiments',
    'Reflections' => 'reflections',
] as $name => $slug) {
    if (!term_exists($slug, 'category')) {
        wp_insert_term($name, 'category', ['slug' => $slug]);
    }
}

echo "Starter content configured.\n";
