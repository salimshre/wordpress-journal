<?php

define('STATIC_BASE_URL_VALUE', rtrim((string) getenv('STATIC_BASE_URL'), '/'));
define('STATIC_OUT_DIR', '/static');

function static_url(string $path = ''): string {
    $path = '/' . ltrim($path, '/');
    return (STATIC_BASE_URL_VALUE ?: '') . ($path === '/' ? '/' : $path);
}

function ensure_dir(string $path): void {
    if (!is_dir($path)) {
        mkdir($path, 0775, true);
    }
}

function clean_dir(string $dir): void {
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
        return;
    }

    $items = array_diff(scandir($dir), ['.', '..']);
    foreach ($items as $item) {
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            clean_dir($path);
            rmdir($path);
        } else {
            unlink($path);
        }
    }
}

function write_page(string $path, string $title, string $body, string $description = ''): void {
    $target = rtrim(STATIC_OUT_DIR, '/') . '/' . trim($path, '/');
    if (str_ends_with($target, '/') || $path === '') {
        $target .= 'index.html';
    } elseif (!str_ends_with($target, '.html')) {
        $target .= '/index.html';
    }

    ensure_dir(dirname($target));
    $site_title = get_bloginfo('name');
    $description = $description ?: get_bloginfo('description');
    $html_title = esc_html($title . ' | ' . $site_title);

    $html = '<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>' . $html_title . '</title>
  <meta name="description" content="' . esc_attr(wp_strip_all_tags($description)) . '">
  <link rel="stylesheet" href="' . esc_url(static_url('assets/site.css')) . '">
</head>
<body>
  <header class="site-header">
    <a class="brand" href="' . esc_url(static_url('/')) . '">My Life Lab</a>
    <nav>
      <a href="' . esc_url(static_url('/')) . '">Home</a>
      <a href="' . esc_url(static_url('blog/')) . '">Blog</a>
      <a href="' . esc_url(static_url('about/')) . '">About</a>
      <a href="' . esc_url(static_url('contact/')) . '">Contact</a>
    </nav>
  </header>
  <main>' . $body . '</main>
  <footer class="site-footer">
    <p>Public lessons from private journaling. Sensitive details stay offline.</p>
  </footer>
</body>
</html>';

    file_put_contents($target, $html);
}

function post_card(WP_Post $post): string {
    $cats = get_the_category($post->ID);
    $cat = $cats ? $cats[0]->name : 'Journal';
    $excerpt = get_the_excerpt($post) ?: wp_trim_words(wp_strip_all_tags($post->post_content), 28);

    return '<article class="post-card">
      <p class="eyebrow">' . esc_html($cat) . '</p>
      <h2><a href="' . esc_url(static_url($post->post_name . '/')) . '">' . esc_html(get_the_title($post)) . '</a></h2>
      <p>' . esc_html($excerpt) . '</p>
    </article>';
}

clean_dir(STATIC_OUT_DIR);
ensure_dir(STATIC_OUT_DIR . '/assets');

$css = ':root{color-scheme:light;--bg:#f7f5ef;--ink:#1d1b18;--muted:#68635b;--line:#d8d1c4;--paper:#fffdf8;--accent:#176b5f;--accent-2:#8b3d2e}*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--ink);font-family:Inter,Segoe UI,Arial,sans-serif;line-height:1.65}.site-header{display:flex;align-items:center;justify-content:space-between;gap:24px;padding:18px max(24px,calc((100vw - 1080px)/2));border-bottom:1px solid var(--line);background:rgba(255,253,248,.92);position:sticky;top:0;backdrop-filter:blur(10px)}.brand{font-weight:800;color:var(--ink);text-decoration:none;font-size:1.15rem}nav{display:flex;gap:18px;flex-wrap:wrap}nav a{color:var(--muted);text-decoration:none;font-weight:650}.hero,.content,.archive{max-width:1080px;margin:0 auto;padding:56px 24px}.hero{display:grid;grid-template-columns:minmax(0,1.15fr) minmax(280px,.85fr);gap:36px;align-items:end}.hero h1{font-size:clamp(2.2rem,5vw,4.7rem);line-height:1.02;margin:0 0 18px}.hero p,.lead{font-size:1.16rem;color:var(--muted);max-width:720px}.panel{border-left:4px solid var(--accent);padding:20px 22px;background:var(--paper)}.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.post-card{background:var(--paper);border:1px solid var(--line);border-radius:8px;padding:22px}.post-card h2{font-size:1.25rem;line-height:1.25;margin:6px 0 10px}.post-card a{color:var(--ink);text-decoration:none}.post-card a:hover{color:var(--accent)}.eyebrow{color:var(--accent-2);font-size:.78rem;text-transform:uppercase;letter-spacing:.08em;font-weight:800;margin:0}.article{max-width:760px;margin:0 auto;padding:56px 24px}.article h1{font-size:clamp(2rem,4vw,3.4rem);line-height:1.08;margin:0 0 12px}.article-meta{color:var(--accent-2);font-weight:800;text-transform:uppercase;font-size:.8rem;letter-spacing:.08em}.article p,.article li{font-size:1.08rem}.article ul{padding-left:1.25rem}.site-footer{max-width:1080px;margin:24px auto 0;padding:28px 24px 46px;border-top:1px solid var(--line);color:var(--muted)}@media(max-width:760px){.site-header{align-items:flex-start;flex-direction:column}.hero{grid-template-columns:1fr;padding-top:34px}.grid{grid-template-columns:1fr}}';
file_put_contents(STATIC_OUT_DIR . '/assets/site.css', $css);

$posts = get_posts([
    'post_type' => 'post',
    'post_status' => 'publish',
    'numberposts' => -1,
    'orderby' => 'date',
    'order' => 'DESC',
]);

$cards = implode("\n", array_map('post_card', $posts));

write_page('', 'Home', '<section class="hero">
  <div>
    <p class="eyebrow">Life tips and experiments</p>
    <h1>My Life Lab</h1>
    <p>Practical lessons from years of journaling, converted into public-safe notes about focus, routines, workouts, study systems, and small experiments.</p>
  </div>
  <div class="panel">
    <p>No private diary details are published here. The site keeps the patterns, lessons, and experiments that can help someone else build a better day.</p>
  </div>
</section>
<section class="archive">
  <p class="eyebrow">Latest posts</p>
  <div class="grid">' . $cards . '</div>
</section>');

write_page('blog/', 'Blog', '<section class="archive">
  <p class="eyebrow">All posts</p>
  <h1>Blog</h1>
  <p class="lead">Life tips and experiments from My Life Lab.</p>
  <div class="grid">' . $cards . '</div>
</section>');

foreach (['about', 'contact'] as $slug) {
    $page = get_page_by_path($slug, OBJECT, 'page');
    if ($page) {
        write_page($slug . '/', get_the_title($page), '<article class="article"><h1>' . esc_html(get_the_title($page)) . '</h1>' . apply_filters('the_content', $page->post_content) . '</article>', wp_strip_all_tags($page->post_content));
    }
}

foreach ($posts as $post) {
    setup_postdata($post);
    $cats = get_the_category($post->ID);
    $cat = $cats ? $cats[0]->name : 'Journal';
    $body = '<article class="article">
      <p class="article-meta">' . esc_html($cat) . '</p>
      <h1>' . esc_html(get_the_title($post)) . '</h1>
      ' . apply_filters('the_content', $post->post_content) . '
    </article>';
    write_page($post->post_name . '/', get_the_title($post), $body, get_the_excerpt($post));
}
wp_reset_postdata();

$robots = "User-agent: *\nAllow: /\n";
file_put_contents(STATIC_OUT_DIR . '/robots.txt', $robots);
file_put_contents(STATIC_OUT_DIR . '/.nojekyll', '');

echo 'Static site generated in ' . STATIC_OUT_DIR . "\n";
