      <article id="article-<?php the_ID() ?>" class="article">
        <header class="article-header">
          <?php if (has_post_thumbnail() and !is_singular()): ?>
            <div class="featured-image">
              <a href="<?php the_permalink() ?>" title="<?php the_title_attribute() ?>"><?php the_post_thumbnail() ?></a>
            </div>
          <?php endif; ?>
          <h1 class="article-title"><?php if(!is_singular()): ?><a href="<?php the_permalink() ?>" title="<?php the_title_attribute() ?>"><?php endif; the_title() ?><?php if(!is_singular()): ?></a><?php endif; ?></h1>
          <div class="article-info">
            <span class="date"><?php the_date('d.m.Y') ?></span> -- 

      <?php
        // print list of categories, tags and author
	$categories_list = get_the_category_list(__( ', '));
	$tag_list = get_the_tag_list('', __(', '));
	if ( '' != $tag_list ) {
		$utility_text = '<span>Gepostet in: %1$s</span> -- <span>Tags: %2$s</span> -- <span>Autor:<a href="%6$s">%5$s</a></span>';
	} elseif ( '' != $categories_list ) {
		$utility_text = '<span>Gepostet in %1$s</span> -- <span>Autor: <a href="%6$s">%5$s</a></span>';
	} else {
		$utility_text = '</span>Autor: <a href="%6$s">%5$s</a></span>';
	}

	printf(
		$utility_text,
		$categories_list,
		$tag_list,
		esc_url( get_permalink() ),
		the_title_attribute( 'echo=0' ),
		get_the_author(),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) )
	);
      ?>
            -- <span class="comments"><?php comments_popup_link(__('Leave a comment'), __('1 Comment'), __('% Comments')) ?></span> 
          </div>
        </header>
        <div class="article-content">
          <?php (is_single()) ? the_content() : the_excerpt() ?>
        </div>
      </article>
