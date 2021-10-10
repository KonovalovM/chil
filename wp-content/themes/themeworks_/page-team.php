<?php
/*
 * Template Name: Team Page
 * The template for displaying team members.
 *
 * @package themeworks
 */

get_header(); ?>

<section id="primary" class="content-area team-page">
    <main id="main" class="site-main" role="main">

        <?php while ( have_posts() ) : the_post(); ?>
            <?php do_action( 'tw_above_entry_hook' ); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <?php do_action( 'tw_above_title_hook' ); ?>
                <header class="entry-header">
                    <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                </header><!-- .entry-header -->
                <?php do_action( 'tw_below_title_hook' ); ?>
                <?php do_action( 'tw_above_content_hook' ); ?>
                <div class="entry-content">
                    <?php the_content(); ?>

                    <div class="team-grid">

                    <?php
                        $blogusers = get_users();
                        foreach ( $blogusers as $user ) {
                            if ( $user->staff == '1' ) {
                                echo '<figure class="col three-col">' . "\n\t";
                                echo '<a href="' . esc_url( home_url( '/?author=' ) ) . $user->ID . '" class="team-member-image">' . get_avatar( $user->ID, 512 ) . '</a>' . "\n";
                                echo '<figcaption>' . "\n\t";
                                echo '<h3 class="team-member-name">' . $user->display_name . '</h3>' . "\n";
                                if ( ! empty( $user->title ) ) {
                                    echo '<p class="team-member-title">' . $user->title . '</p>' . "\n";
                                }
                                if ( ! empty( $user->description ) ) {
                                    echo '<p class="team-member-description">' . $user->description . '</p>' . "\n";
                                }
                                if ( ! empty( $user->user_url ) ) {
                                    echo '<a href=" ' . $user->user_url . '" target="_blank" class="team-member-link"><span class="genericon genericon-link"></span></a>' . "\n";
                                }
                                $fields = themeworks_user_profile_fields();
                                foreach ( $fields as $field ) {
                                    if ( 'text' == $field['type'] && 'title' != $field['id'] ) {
                                        if ( ! empty( $user->$field['id'] ) ) {
                                            echo '<a href=" ' . $user->$field['id'] . '" target="_blank" class="team-member-link"><span class="genericon genericon-' . $field['id'] . '"></span></a>';
                                        }
                                    }
                                }
                                echo '</figcaption>';
                                echo '</figure>';
                            }
                        }
                    ?>

                    </div><!-- .team-grid -->

                </div><!-- .entry-content -->
                <?php do_action( 'tw_below_content_hook' ); ?>
                <?php do_action( 'tw_above_meta_hook' ); ?>
                <footer class="entry-footer">
                    <?php edit_post_link( __( 'Edit', 'themeworks' ), '<span class="edit-link">', '</span>' ); ?>
                </footer><!-- .entry-footer -->
                <?php do_action( 'tw_below_meta_hook' ); ?>
            </article><!-- #post-## -->
            <?php do_action( 'tw_below_entry_hook' ); ?>
        <?php endwhile; // end of the loop. ?>

    </main><!-- #main -->
</section><!-- #primary -->

<?php get_footer(); ?>