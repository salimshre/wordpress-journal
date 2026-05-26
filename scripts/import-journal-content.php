<?php

function journal_term_id(string $name, string $slug): int {
    $term = get_term_by('slug', $slug, 'category');
    if ($term) {
        return (int) $term->term_id;
    }

    $created = wp_insert_term($name, 'category', ['slug' => $slug]);
    return is_wp_error($created) ? 0 : (int) $created['term_id'];
}

function journal_post(string $title, string $slug, string $category_slug, string $excerpt, array $paragraphs): void {
    $category = get_term_by('slug', $category_slug, 'category');
    $content = '';

    foreach ($paragraphs as $block) {
        if (is_array($block)) {
            $content .= "<ul>\n";
            foreach ($block as $item) {
                $content .= '<li>' . esc_html($item) . "</li>\n";
            }
            $content .= "</ul>\n\n";
        } else {
            $content .= '<p>' . esc_html($block) . "</p>\n\n";
        }
    }

    $post_data = [
        'post_type' => 'post',
        'post_title' => $title,
        'post_name' => $slug,
        'post_status' => 'publish',
        'post_excerpt' => $excerpt,
        'post_content' => trim($content),
    ];

    $existing = get_page_by_path($slug, OBJECT, 'post');
    if ($existing) {
        $post_data['ID'] = $existing->ID;
        $post_id = wp_update_post($post_data, true);
    } else {
        $post_id = wp_insert_post($post_data, true);
    }

    if (!is_wp_error($post_id) && $category) {
        wp_set_post_terms((int) $post_id, [(int) $category->term_id], 'category');
    }
}

function journal_page(string $title, string $slug, array $paragraphs): int {
    $content = '';
    foreach ($paragraphs as $paragraph) {
        $content .= '<p>' . esc_html($paragraph) . "</p>\n\n";
    }

    $existing = get_page_by_path($slug, OBJECT, 'page');
    $data = [
        'post_type' => 'page',
        'post_title' => $title,
        'post_name' => $slug,
        'post_status' => 'publish',
        'post_content' => trim($content),
    ];

    if ($existing) {
        $data['ID'] = $existing->ID;
        return (int) wp_update_post($data);
    }

    return (int) wp_insert_post($data);
}

$life_tips = journal_term_id('Life Tips', 'life-tips');
$experiments = journal_term_id('Experiments', 'experiments');

$placeholder = get_page_by_path('why-i-started-documenting-my-life', OBJECT, 'post');
if ($placeholder) {
    wp_delete_post($placeholder->ID, true);
}

$home_id = journal_page('Home', 'home', [
    'My Life Lab is a public collection of practical lessons from years of daily notes, routines, study logs, workouts, and small personal experiments.',
    'The private details stay private. What appears here is the useful part: simple habits, tested systems, and honest notes on what helped create more focus, discipline, and energy.',
]);

journal_page('About', 'about', [
    'This blog turns long-term journaling into clear life tips and experiments. The goal is to share what can help another person without exposing private diary details.',
    'Most posts come from repeated patterns: waking up better, reducing distraction, building a workout habit, studying in blocks, and reviewing the day with enough honesty to improve tomorrow.',
]);

$blog_id = journal_page('Blog', 'blog', [
    'Browse practical life tips and experiments from My Life Lab.',
]);

journal_page('Contact', 'contact', [
    'For now, this page is a simple placeholder for future contact details.',
]);

update_option('show_on_front', 'page');
update_option('page_on_front', $home_id);
update_option('page_for_posts', $blog_id);

$posts = [
    [
        'A Morning Reset for Days That Start Slowly',
        'morning-reset-for-slow-days',
        'life-tips',
        'A simple reset routine for mornings when the alarm fails, the body feels heavy, or the day starts without momentum.',
        [
            'Many journal entries repeat the same pattern: the day becomes harder when the morning begins with delay, phone use, or lying in bed too long. The useful lesson is not to expect perfect mornings. The better approach is to keep a short reset routine ready.',
            'The reset is simple: get out of bed, wash up, drink water or tea, open the room to light, and start one small task before checking entertainment. The first task should be easy enough that resistance has no time to grow.',
            'A slow morning does not have to become a wasted day. Treat the first thirty minutes as recovery time, then restart the day with one visible action.',
            [
                'Do not negotiate with the bed after the first alarm.',
                'Keep the first task physical: wash, clean, walk, stretch, or prepare the desk.',
                'Use a short task to rebuild trust with yourself before planning the whole day.',
            ],
        ],
    ],
    [
        'The 10-Minute Start Rule',
        'the-10-minute-start-rule',
        'life-tips',
        'A practical rule for starting study, reading, or work when the task feels too large.',
        [
            'A common pattern in the journals is that long plans often failed, but short starts often grew into useful sessions. Ten minutes is small enough to begin even when energy is low.',
            'The rule is to commit to only ten minutes of the task. Read one page, solve one question, clean one section, or write one note. After ten minutes, you may stop, but very often the mind has already crossed the hardest part: starting.',
            'This works because motivation usually follows movement. Waiting to feel ready wastes energy. Starting small creates the feeling of readiness.',
            [
                'Set a timer for ten minutes.',
                'Remove the phone or browser before the timer starts.',
                'Continue only if the next ten minutes feels possible.',
            ],
        ],
    ],
    [
        'My Pomodoro Study Experiment',
        'pomodoro-study-experiment',
        'experiments',
        'An experiment with focused study blocks, short breaks, and small reset actions between sessions.',
        [
            'The study logs show repeated testing of timed focus blocks. The most useful version was not complicated: a focused work block, a short break, and then another block before the mind cooled down.',
            'A good experiment format is thirty minutes of work followed by five minutes of break. During the break, avoid entertainment. Use the break for water, stretching, breathing, or a few pushups. The point is to recover without opening a new distraction loop.',
            'The result is better when the task is chosen before the timer starts. A timer cannot fix unclear work. Write the exact target first, then start.',
            [
                'Before the timer: choose one topic or one question set.',
                'During the timer: no switching, no checking, no extra tabs.',
                'After the timer: write one line about what was completed.',
            ],
        ],
    ],
    [
        'What Workout Tracking Taught Me About Discipline',
        'workout-tracking-discipline',
        'experiments',
        'Lessons from repeated workout logs and twenty-one-day challenge formats.',
        [
            'Workout entries appear again and again in the journals because exercise became more than fitness. It became a way to train action when the mind felt lazy, fearful, or unfocused.',
            'The useful experiment was tracking the workout, not just doing it. Writing down the session made the habit visible. It also made missed days harder to ignore and successful days easier to repeat.',
            'A good workout log does not need many details. Track the date, time, place, main exercises, energy before, energy after, and one lesson. That is enough to show whether the habit is building strength or just creating noise.',
            [
                'Track consistency before intensity.',
                'Notice how energy changes after the session.',
                'Use a challenge period, such as twenty-one days, to make progress visible.',
            ],
        ],
    ],
    [
        'Reducing Digital Distraction by Adding Friction',
        'reducing-digital-distraction-by-adding-friction',
        'life-tips',
        'A practical way to make bad digital habits harder and useful actions easier.',
        [
            'The journals repeatedly connect distraction with lost focus, weak starts, and late recovery. The strongest solution was not willpower alone. It was adding friction to distractions.',
            'Friction means making the unwanted action less automatic. Block distracting sites, move apps away from the home screen, keep the phone outside the bed area, or use a simpler device for reading and notes. Small barriers work because many bad habits depend on speed.',
            'At the same time, remove friction from good actions. Keep study material ready, prepare the desk before sleeping, and make the first useful action easy to reach.',
            [
                'Block or hide the sites that create repeated loops.',
                'Do not keep entertainment within arm reach of the bed.',
                'Prepare the next study or work task before the break starts.',
            ],
        ],
    ],
    [
        'A Better Daily Journal Template',
        'better-daily-journal-template',
        'life-tips',
        'A clean journal format that keeps useful lessons without turning every post into private diary material.',
        [
            'After years of logs, the most useful journal format became simple: track time, actions, energy, and lessons. Details are useful privately, but public writing should keep only the pattern and the takeaway.',
            'A daily template should help review behavior without making the journal heavy. The goal is not to record everything. The goal is to catch the few moments that changed the direction of the day.',
            'The best entries answer three questions: What moved me forward? What pulled me backward? What will I change tomorrow?',
            [
                'Morning: wake time, first action, main target.',
                'Day: focused blocks, workout or movement, distractions noticed.',
                'Night: lesson, mistake, and one adjustment for tomorrow.',
            ],
        ],
    ],
    [
        'How to Turn a Bad Day Into Data',
        'turn-a-bad-day-into-data',
        'life-tips',
        'A way to review lazy, distracted, or low-energy days without getting stuck in self-blame.',
        [
            'Some days in a long journal are messy: late starts, postponed alarms, unfinished tasks, or too much passive screen time. The useful move is to turn the bad day into data.',
            'Instead of writing only that the day was wasted, look for the trigger. Was it poor sleep, unclear work, too much comfort, no plan, or one distraction that opened the door to more? Once the trigger is visible, the next day can be designed differently.',
            'This keeps the review practical. The point is not to attack yourself. The point is to find the first broken link in the chain and fix that link.',
            [
                'Name the trigger without drama.',
                'Write the smallest prevention step.',
                'Restart with one action the same evening if possible.',
            ],
        ],
    ],
    [
        'Weekly Planning Without Overthinking',
        'weekly-planning-without-overthinking',
        'experiments',
        'A lightweight weekly planning experiment for study, fitness, and personal projects.',
        [
            'The journals show a clear problem with heavy planning: too much planning can become another way to avoid action. The better experiment is to make weekly planning short and concrete.',
            'Choose three outcomes for the week: one study or work outcome, one health outcome, and one personal project outcome. Then choose the first action for each. That is enough to begin.',
            'Review the plan at the end of the week. Do not only ask whether everything was completed. Ask which environment, time, or habit made completion easier.',
            [
                'Pick three outcomes only.',
                'Write the first action for each outcome.',
                'Review what actually helped, then repeat it next week.',
            ],
        ],
    ],
    [
        'Learning From People Without Copying Everything',
        'learning-from-people-without-copying-everything',
        'life-tips',
        'A lesson about observing smart behavior, asking better questions, and adapting what works.',
        [
            'One useful theme in the notes is observation: notice how focused people learn, ask questions, take notes, and use their time. The lesson is not to copy someone completely. The lesson is to identify the behavior that creates the result.',
            'For example, a better learner does not simply write everything down. They listen, select the important point, ask when confused, and turn the lesson into their own words. That is a transferable behavior.',
            'Use people as examples, not excuses. If someone has a useful habit, extract the habit and test it in your own routine.',
            [
                'Observe the behavior, not only the outcome.',
                'Ask what makes the behavior repeatable.',
                'Test one borrowed habit for a week before judging it.',
            ],
        ],
    ],
    [
        'The Small Action Recovery Plan',
        'small-action-recovery-plan',
        'experiments',
        'An experiment for recovering momentum after distraction, tiredness, or fear of starting.',
        [
            'A repeated lesson from the journals is that recovery matters more than perfection. The day is not lost when focus breaks. It is lost when there is no recovery plan.',
            'The small action recovery plan is a list of actions that take less than five minutes and return you to motion. Examples include cleaning the desk, opening the notebook, walking outside, doing a short stretch, or writing the next task on paper.',
            'The experiment is to use one recovery action immediately after noticing drift. The faster the recovery, the less power the drift has.',
            [
                'Keep a written list of five-minute recovery actions.',
                'Use one action before opening entertainment.',
                'Measure success by how quickly you restart, not by never drifting.',
            ],
        ],
    ],
];

foreach ($posts as $post) {
    journal_post($post[0], $post[1], $post[2], $post[3], $post[4]);
}

echo "Journal-derived public content imported.\n";
