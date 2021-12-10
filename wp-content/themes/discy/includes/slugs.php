<?php if (isset($category_id) && $category_id > 0?$category_id:0) {
	$recent_questions_slug   = discy_options("recent_questions_slug");
	$most_answers_slug       = discy_options("most_answers_slug");
	$question_bump_slug      = discy_options("question_bump_slug");
	$answers_slug            = discy_options("answers_slug");
	$most_visit_slug         = discy_options("most_visit_slug");
	$most_vote_slug          = discy_options("most_vote_slug");
	$no_answers_slug         = discy_options("no_answers_slug");
	$recent_posts_slug       = discy_options("recent_posts_slug");
	$posts_visited_slug      = discy_options("posts_visited_slug");
	$random_slug             = discy_options("random_slug");
	$question_new_slug       = discy_options("question_new_slug");
	$question_sticky_slug    = discy_options("question_sticky_slug");
	$question_polls_slug     = discy_options("question_polls_slug");

	$recent_questions_slug_2 = discy_options("recent_questions_slug_2");
	$most_answers_slug_2     = discy_options("most_answers_slug_2");
	$question_bump_slug_2    = discy_options("question_bump_slug_2");
	$answers_slug_2          = discy_options("answers_slug_2");
	$most_visit_slug_2       = discy_options("most_visit_slug_2");
	$most_vote_slug_2        = discy_options("most_vote_slug_2");
	$no_answers_slug_2       = discy_options("no_answers_slug_2");
	$recent_posts_slug_2     = discy_options("recent_posts_slug_2");
	$posts_visited_slug_2    = discy_options("posts_visited_slug_2");
	$random_slug_2           = discy_options("random_slug_2");
	$question_new_slug_2     = discy_options("question_new_slug_2");
	$question_sticky_slug_2  = discy_options("question_sticky_slug_2");
	$question_polls_slug_2   = discy_options("question_polls_slug_2");
}else {
	$recent_questions_slug   = discy_post_meta("recent_questions_slug");
	$most_answers_slug       = discy_post_meta("most_answers_slug");
	$question_bump_slug      = discy_post_meta("question_bump_slug");
	$answers_slug            = discy_post_meta("answers_slug");
	$most_visit_slug         = discy_post_meta("most_visit_slug");
	$most_vote_slug          = discy_post_meta("most_vote_slug");
	$no_answers_slug         = discy_post_meta("no_answers_slug");
	$recent_posts_slug       = discy_post_meta("recent_posts_slug");
	$posts_visited_slug      = discy_post_meta("posts_visited_slug");
	$random_slug             = discy_post_meta("random_slug");
	$question_new_slug       = discy_post_meta("question_new_slug");
	$question_sticky_slug    = discy_post_meta("question_sticky_slug");
	$question_polls_slug     = discy_post_meta("question_polls_slug");

	$recent_questions_slug_2 = discy_post_meta("recent_questions_slug_2");
	$most_answers_slug_2     = discy_post_meta("most_answers_slug_2");
	$question_bump_slug_2    = discy_post_meta("question_bump_slug_2");
	$answers_slug_2          = discy_post_meta("answers_slug_2");
	$most_visit_slug_2       = discy_post_meta("most_visit_slug_2");
	$most_vote_slug_2        = discy_post_meta("most_vote_slug_2");
	$no_answers_slug_2       = discy_post_meta("no_answers_slug_2");
	$recent_posts_slug_2     = discy_post_meta("recent_posts_slug_2");
	$posts_visited_slug_2    = discy_post_meta("posts_visited_slug_2");
	$random_slug_2           = discy_post_meta("random_slug_2");
	$question_new_slug_2     = discy_post_meta("question_new_slug_2");
	$question_sticky_slug_2  = discy_post_meta("question_sticky_slug_2");
	$question_polls_slug_2   = discy_post_meta("question_polls_slug_2");
}

$recent_questions_slug   = ($recent_questions_slug != ""?$recent_questions_slug:"recent-questions");
$most_answers_slug       = ($most_answers_slug != ""?$most_answers_slug:"most-answers");
$question_bump_slug      = ($question_bump_slug != ""?$question_bump_slug:"question-bump");
$answers_slug            = ($answers_slug != ""?$answers_slug:"answers");
$most_visit_slug         = ($most_visit_slug != ""?$most_visit_slug:"most-visit");
$most_vote_slug          = ($most_vote_slug != ""?$most_vote_slug:"most-vote");
$random_slug             = ($random_slug != ""?$random_slug:"random");
$question_new_slug       = ($question_new_slug != ""?$question_new_slug:"new");
$question_sticky_slug    = ($question_sticky_slug != ""?$question_sticky_slug:"sticky");
$question_polls_slug     = ($question_polls_slug != ""?$question_polls_slug:"polls");
$no_answers_slug         = ($no_answers_slug != ""?$no_answers_slug:"no-answers");
$recent_posts_slug       = ($recent_posts_slug != ""?$recent_posts_slug:"recent-posts");
$posts_visited_slug      = ($posts_visited_slug != ""?$posts_visited_slug:"posts-visited");

$recent_questions_slug_2 = ($recent_questions_slug_2 != ""?$recent_questions_slug_2:"recent-questions-time");
$most_answers_slug_2     = ($most_answers_slug_2 != ""?$most_answers_slug_2:"most-answers-time");
$question_bump_slug_2    = ($question_bump_slug_2 != ""?$question_bump_slug_2:"question-bump-time");
$answers_slug_2          = ($answers_slug_2 != ""?$answers_slug_2:"answers-time");
$most_visit_slug_2       = ($most_visit_slug_2 != ""?$most_visit_slug_2:"most-visit-time");
$most_vote_slug_2        = ($most_vote_slug_2 != ""?$most_vote_slug_2:"most-vote-time");
$random_slug_2           = ($random_slug_2 != ""?$random_slug_2:"random-time");
$question_new_slug_2     = ($question_new_slug_2 != ""?$question_new_slug_2:"new-time");
$question_sticky_slug_2  = ($question_sticky_slug_2 != ""?$question_sticky_slug_2:"sticky-time");
$question_polls_slug_2   = ($question_polls_slug_2 != ""?$question_polls_slug_2:"polls-time");
$no_answers_slug_2       = ($no_answers_slug_2 != ""?$no_answers_slug_2:"no-answers-time");
$recent_posts_slug_2     = ($recent_posts_slug_2 != ""?$recent_posts_slug_2:"recent-posts-time");
$posts_visited_slug_2    = ($posts_visited_slug_2 != ""?$posts_visited_slug_2:"posts-visited-time");
?>