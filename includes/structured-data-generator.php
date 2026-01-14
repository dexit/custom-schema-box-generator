<?php
/**
 * Structured Data Generator
 *
 * Provides methods to generate JSON-LD for various Google Rich Result types.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class StructuredDataGenerator {
    /**
     * Generate JSON-LD for a given type.
     *
     * @param string $type The schema type (e.g., 'Article', 'Breadcrumb').
     * @return array|null Associative array representing JSON-LD, or null if unsupported.
     */
    public static function generate( $type ) {
        $type = strtolower( $type );
        switch ( $type ) {
            case 'article':
                return self::article();
            case 'book':
                return self::book();
            case 'breadcrumb':
                return self::breadcrumb();
            case 'carousel':
                return self::carousel();
            case 'course':
                return self::course();
            case 'dataset':
                return self::dataset();
            case 'discussionforumposting':
                return self::discussion_forum();
            case 'quiz': // Aligned with features array
                return self::education_qa();
            case 'employeraggregaterating':
                return self::employer_aggregate_rating();
            case 'factcheck':
                return self::fact_check();
            case 'event':
                return self::event();
            case 'faqpage':
                return self::faq();
            case 'imageobject':
                return self::image_metadata();
            case 'jobposting':
                return self::job_posting();
            case 'localbusiness':
                return self::local_business();
            case 'mathsolver':
                return self::math_solver();
            case 'movie':
                return self::movie();
            case 'organization':
                return self::organization();
            case 'product':
                return self::product();
            case 'profilepage':
                return self::profile_page();
            case 'qapage':
                return self::qa_page();
            case 'recipe':
                return self::recipe();
            case 'review':
                return self::review();
            case 'softwareapplication':
                return self::software_app();
            case 'speakablespecification':
                return self::speakable();
            case 'subscription':
                return self::subscription();
            case 'vacationrental':
                return self::vacation_rental();
            case 'videoobject':
                return self::video();
            case 'medicalcondition':
                return self::medical_condition();
            default:
                return null;
        }
    }

    private static function medical_condition() {
        return apply_filters( 'csg_sd_medical_condition', array(
            '@context'          => 'https://schema.org',
            '@type'             => 'MedicalCondition',
            'name'              => get_the_title(),
            'description'       => get_the_excerpt(),
            'possibleTreatment' => array(
                '@type' => 'MedicalTherapy',
                'name'  => 'See a Doctor',
            ),
        ) );
    }

    private static function article() {
        return apply_filters( 'csg_sd_article', array(
            '@context'      => 'https://schema.org',
            '@type'         => 'Article',
            'headline'      => get_the_title(),
            'description'   => get_the_excerpt(),
            'image'         => self::get_image(),
            'datePublished' => get_the_date( 'c' ),
            'dateModified'  => get_the_modified_date( 'c' ),
            'author'        => array(
                '@type' => 'Person',
                'name'  => get_the_author(),
                'url'   => get_author_posts_url( get_the_author_meta( 'ID' ) ),
            ),
            'publisher' => self::get_organization_data(),
        ) );
    }

    private static function book() {
        return apply_filters( 'csg_sd_book', array(
            '@context' => 'https://schema.org',
            '@type'    => 'Book',
            'name'     => get_the_title(),
            'author'   => array(
                '@type' => 'Person',
                'name'  => get_the_author(),
            ),
            'image' => self::get_image(),
        ) );
    }

    private static function breadcrumb() {
        $items = array(
            array(
                '@type'    => 'ListItem',
                'position' => 1,
                'name'     => 'Home',
                'item'     => home_url(),
            )
        );

        if ( is_singular() ) {
            $items[] = array(
                '@type'    => 'ListItem',
                'position' => 2,
                'name'     => get_the_title(),
                'item'     => get_permalink(),
            );
        }

        return apply_filters( 'csg_sd_breadcrumb', array(
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $items,
        ) );
    }

    private static function carousel() {
        // Typically used on archives or with specific items. Returning ItemList.
        return apply_filters( 'csg_sd_carousel', array(
            '@context' => 'https://schema.org',
            '@type'    => 'ItemList',
            'itemListElement' => array(),
        ) );
    }

    private static function course() {
        return apply_filters( 'csg_sd_course', array(
            '@context'    => 'https://schema.org',
            '@type'       => 'Course',
            'name'        => get_the_title(),
            'description' => get_the_excerpt(),
            'provider'    => self::get_organization_data(),
        ) );
    }

    private static function dataset() {
        return apply_filters( 'csg_sd_dataset', array(
            '@context'    => 'https://schema.org',
            '@type'       => 'Dataset',
            'name'        => get_the_title(),
            'description' => get_the_excerpt(),
            'license'     => 'https://creativecommons.org/licenses/by/4.0/',
        ) );
    }

    private static function discussion_forum() {
        return apply_filters( 'csg_sd_discussion_forum', array(
            '@context' => 'https://schema.org',
            '@type'    => 'DiscussionForumPosting',
            'headline' => get_the_title(),
            'author'   => array(
                '@type' => 'Person',
                'name'  => get_the_author(),
            ),
            'datePublished' => get_the_date( 'c' ),
        ) );
    }

    private static function education_qa() {
        return apply_filters( 'csg_sd_education_qa', array(
            '@context' => 'https://schema.org',
            '@type'    => 'Quiz',
            'name'     => get_the_title(),
            'hasPart'  => array(),
        ) );
    }

    private static function employer_aggregate_rating() {
        return apply_filters( 'csg_sd_employer_aggregate_rating', array(
            '@context' => 'https://schema.org',
            '@type'    => 'EmployerAggregateRating',
            'itemReviewed' => self::get_organization_data(),
            'ratingValue'  => '4.5',
            'ratingCount'  => '10',
        ) );
    }

    private static function fact_check() {
        return apply_filters( 'csg_sd_fact_check', array(
            '@context'      => 'https://schema.org',
            '@type'         => 'ClaimReview',
            'url'           => get_permalink(),
            'claimReviewed' => get_the_title(),
            'reviewRating'  => array(
                '@type'       => 'Rating',
                'ratingValue' => '3',
                'bestRating'  => '5',
                'alternateName' => 'Mostly True',
            ),
        ) );
    }

    private static function event() {
        return apply_filters( 'csg_sd_event', array(
            '@context'  => 'https://schema.org',
            '@type'     => 'Event',
            'name'      => get_the_title(),
            'startDate' => get_the_date( 'c' ),
            'location'  => array(
                '@type' => 'VirtualLocation',
                'url'   => get_permalink(),
            ),
            'image' => self::get_image(),
        ) );
    }

    private static function faq() {
        return apply_filters( 'csg_sd_faq', array(
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => array(),
        ) );
    }

    private static function image_metadata() {
        return apply_filters( 'csg_sd_image_metadata', array(
            '@context'   => 'https://schema.org',
            '@type'      => 'ImageObject',
            'contentUrl' => self::get_image(),
            'creator'    => array(
                '@type' => 'Person',
                'name'  => get_the_author(),
            ),
            'creditText' => get_bloginfo( 'name' ),
        ) );
    }

    private static function job_posting() {
        return apply_filters( 'csg_sd_job_posting', array(
            '@context'   => 'https://schema.org',
            '@type'      => 'JobPosting',
            'title'      => get_the_title(),
            'datePosted' => get_the_date( 'c' ),
            'hiringOrganization' => self::get_organization_data(),
        ) );
    }

    private static function local_business() {
        return apply_filters( 'csg_sd_local_business', array(
            '@context' => 'https://schema.org',
            '@type'    => 'LocalBusiness',
            'name'     => get_bloginfo( 'name' ),
            'image'    => self::get_image(),
            'url'      => home_url(),
            'telephone' => '',
        ) );
    }

    private static function math_solver() {
        return apply_filters( 'csg_sd_math_solver', array(
            '@context' => 'https://schema.org',
            '@type'    => 'MathSolver',
            'name'     => get_the_title(),
        ) );
    }

    private static function movie() {
        return apply_filters( 'csg_sd_movie', array(
            '@context' => 'https://schema.org',
            '@type'    => 'Movie',
            'name'     => get_the_title(),
            'image'    => self::get_image(),
        ) );
    }

    private static function organization() {
        return apply_filters( 'csg_sd_organization', self::get_organization_data() );
    }

    private static function product() {
        return apply_filters( 'csg_sd_product', array(
            '@context'    => 'https://schema.org',
            '@type'       => 'Product',
            'name'        => get_the_title(),
            'image'       => self::get_image(),
            'description' => get_the_excerpt(),
        ) );
    }

    private static function profile_page() {
        return apply_filters( 'csg_sd_profile_page', array(
            '@context'   => 'https://schema.org',
            '@type'      => 'ProfilePage',
            'mainEntity' => array(
                '@type' => 'Person',
                'name'  => get_the_author(),
                'image' => get_avatar_url( get_the_author_meta( 'ID' ) ),
            ),
        ) );
    }

    private static function qa_page() {
        return apply_filters( 'csg_sd_qa_page', array(
            '@context'   => 'https://schema.org',
            '@type'      => 'QAPage',
            'mainEntity' => array(
                '@type' => 'Question',
                'name'  => get_the_title(),
                'suggestedAnswer' => array(),
            ),
        ) );
    }

    private static function recipe() {
        return apply_filters( 'csg_sd_recipe', array(
            '@context' => 'https://schema.org',
            '@type'    => 'Recipe',
            'name'     => get_the_title(),
            'image'    => self::get_image(),
            'author'   => array(
                '@type' => 'Person',
                'name'  => get_the_author(),
            ),
        ) );
    }

    private static function review() {
        return apply_filters( 'csg_sd_review', array(
            '@context' => 'https://schema.org',
            '@type'    => 'Review',
            'itemReviewed' => array(
                '@type' => 'Thing',
                'name'  => get_the_title(),
            ),
            'author' => array(
                '@type' => 'Person',
                'name'  => get_the_author(),
            ),
            'reviewRating' => array(
                '@type' => 'Rating',
                'ratingValue' => '5',
            ),
        ) );
    }

    private static function software_app() {
        return apply_filters( 'csg_sd_software_app', array(
            '@context' => 'https://schema.org',
            '@type'    => 'SoftwareApplication',
            'name'     => get_the_title(),
            'operatingSystem'     => 'Windows, OSX, Linux',
            'applicationCategory' => 'BusinessApplication',
            'offers' => array(
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'USD',
            ),
        ) );
    }

    private static function speakable() {
        return apply_filters( 'csg_sd_speakable', array(
            '@context'  => 'https://schema.org',
            '@type'     => 'WebPage',
            'speakable' => array(
                '@type' => 'SpeakableSpecification',
                'xpath' => array(
                    '/html/head/title',
                    '/html/body/h1',
                ),
            ),
        ) );
    }

    private static function subscription() {
        return apply_filters( 'csg_sd_subscription', array(
            '@context'            => 'https://schema.org',
            '@type'               => 'NewsArticle',
            'headline'            => get_the_title(),
            'isAccessibleForFree' => 'False',
            'hasPart' => array(
                '@type' => 'WebPageElement',
                'isAccessibleForFree' => 'False',
                'cssSelector' => '.paywall',
            ),
        ) );
    }

    private static function vacation_rental() {
        return apply_filters( 'csg_sd_vacation_rental', array(
            '@context' => 'https://schema.org',
            '@type'    => 'VacationRental',
            'name'     => get_the_title(),
            'image'    => self::get_image(),
        ) );
    }

    private static function video() {
        return apply_filters( 'csg_sd_video', array(
            '@context'     => 'https://schema.org',
            '@type'        => 'VideoObject',
            'name'         => get_the_title(),
            'description'  => get_the_excerpt(),
            'thumbnailUrl' => self::get_image(),
            'uploadDate'   => get_the_date( 'c' ),
        ) );
    }

    // Helpers
    private static function get_image() {
        $url = wp_get_attachment_url( get_post_thumbnail_id() );
        return $url ? $url : get_site_icon_url();
    }

    private static function get_organization_data() {
        return array(
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => get_bloginfo( 'name' ),
            'url'      => home_url(),
            'logo'     => array(
                '@type' => 'ImageObject',
                'url'   => get_site_icon_url(),
            ),
        );
    }


}
?>
