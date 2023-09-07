<?php

/* Template Name: Blog Listing Dynamic */
get_header();
?>



<section class="blog_filter">
    <div class="container">
        <div class="filter_wrap">
            <div class="row g-0">
                <div class="col-md-4">
                    <div class="search_box">
                        <input type="text" id="blog-search" placeholder="Type Here for Search">
                        <button class="btn">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/Search.svg" alt="search icon">
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="drop_box">
                        <!-- <label for="sort-select">Sort by:</label> -->
                        <select name="sort" id="sort-select">
                            <option value="date">Date</option>
                            <option value="title">Title</option>
                        </select>
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="drop_box">
                        <!-- <label for="category-select">Filter by Category:</label> -->
                        <select name="category" id="category-select">
                            <option value="">Any</option>
                            <?php
                            $categories = get_categories(); // Get all categories
                            foreach ($categories as $category) {
                                echo '<option value="' . $category->slug . '">' . $category->name . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>




<section class="news news_blog">
    <div class="container">
        <div class="row">
            <?php
            // Get the sorting option from the URL parameter
            $sort_option = isset($_GET['sort']) ? sanitize_text_field($_GET['sort']) : 'date';

            // Get the category filter option from the URL parameter
            $category_filter = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';

            // Get the text-based filter query from the URL parameter
            $filter_query = isset($_GET['filter']) ? sanitize_text_field($_GET['filter']) : '';


            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $args = array(
                'post_type' => 'post',
                'posts_per_page' => 8,
                'paged' => $paged,
                // Add sorting based on the selected option
                'orderby' => ($sort_option === 'title') ? 'title' : 'date',
                'order' => ($sort_option === 'title') ? 'ASC' : 'DESC',
            );

            // Add category filter if a category is selected
            if (!empty($category_filter)) {
                $args['category_name'] = $category_filter;
            }


            // Add text-based filter query if a filter query is provided
            if (!empty($filter_query)) {
                $args['s'] = $filter_query;
            }


            $query = new WP_Query($args);



            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post();
            ?>
                    <div class="col-lg-3 col-sm-6">
                        <div class="news_block">
                            <div class="news_img">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('thumbnail'); ?>
                                <?php endif; ?>
                            </div>
                            <div class="news_cat">
                                <?php
                                $categories = get_the_category();
                                if (!empty($categories)) :
                                    $category = $categories[0];
                                    echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
                                endif;
                                ?>
                            </div>
                            <div class="news_title">
                                <a href="<?php the_permalink(); ?>">
                                    <h5><?php the_title(); ?></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php
                endwhile;
            else :
                ?>
                <div class="go-back" style="
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
">
                    <span> No posts found. </span>
                    <a class="btn_primary" href="<?php echo esc_url(get_permalink(get_page_by_title('blog-listing-dynamic'))); ?>">Go Back</a>
                </div>
            <?php
            endif;
            wp_reset_postdata();
            ?>

        </div>

        <div class="pagination">
            <?php
            // Pagination links
            echo paginate_links(array(
                'total' => $query->max_num_pages,
            ));
            ?>
        </div>

    </div>
</section>

<script>
    $ = jQuery;
    $(document).ready(function() {
        // Function to update the URL based on category, sort, and filter selection
        function updateURL() {
            var selectedCategory = $('#category-select').val();
            var selectedSort = $('#sort-select').val();
            var selectedFilter = $('#blog-search').val(); // Get the filter input value
            var baseUrl = window.location.href.split('?')[0];
            var newUrl = baseUrl + '?';

            if (selectedCategory) {
                newUrl += 'category=' + selectedCategory;
            }

            if (selectedSort) {
                if (newUrl.charAt(newUrl.length - 1) !== '?') {
                    newUrl += '&';
                }
                newUrl += 'sort=' + selectedSort;
            }

            if (selectedFilter) {
                if (newUrl.charAt(newUrl.length - 1) !== '?') {
                    newUrl += '&';
                }
                newUrl += 'filter=' + selectedFilter; // Add filter parameter
            }

            history.replaceState(null, null, newUrl);
        }

        // Event handler for category selection change
        $('#category-select').on('change', function() {
            updateURL();
            location.reload(); // Reload the browser
            // You can add additional code here to perform category-based filtering.
        });

        // Event handler for sorting selection change
        $('#sort-select').on('change', function() {
            updateURL();
            location.reload(); // Reload the browser
            // You can add additional code here to perform sorting.
        });

        // Event handler for filter input change
        $('#blog-search').on('input', function() {
            updateURL();
            location.reload(); // Reload the browser
            // You can add additional code here to perform text-based filtering.
        });

        // Function to set dropdown and filter values based on URL parameters
        function setDropdownAndFilterValuesFromURL() {
            var params = new URLSearchParams(window.location.search);
            var categoryParam = params.get('category');
            var sortParam = params.get('sort');
            var filterParam = params.get('filter');

            if (categoryParam) {
                $('#category-select').val(categoryParam);
            }

            if (sortParam) {
                $('#sort-select').val(sortParam);
            }

            if (filterParam) {
                $('#blog-search').val(filterParam);
            }
        }

        // Initialize dropdown and filter values based on the current URL
        setDropdownAndFilterValuesFromURL();
    });
</script>


<?php
get_footer();
