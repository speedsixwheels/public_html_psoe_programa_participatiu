<?php get_header(); while ( have_posts() ) : the_post(); ?>
<div class="max-w-[760px] mx-auto px-4 py-10">
  <h1 class="text-3xl font-extrabold"><?php the_title(); ?></h1>
  <div class="mt-4 prose max-w-none"><?php the_content(); ?></div>
  <?php comments_template(); ?>
</div>
<?php endwhile; get_footer(); ?>
