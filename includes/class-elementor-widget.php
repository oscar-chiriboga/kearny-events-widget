<?php
namespace Kearny_Events;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor widget for Kearny Events.
 */
class Elementor_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'kearny_events';
    }

    public function get_title() {
        return __( 'Kearny Events', 'kearny-events' );
    }

    public function get_icon() {
        return 'eicon-calendar';
    }

    public function get_categories() {
        return [ 'kearny-events', 'general' ];
    }

    public function get_keywords() {
        return [ 'events', 'calendar', 'tribe', 'upcoming', 'kearny' ];
    }

    public function get_style_depends() {
        return [ 'kearny-events-widget' ];
    }

    public function get_script_depends() {
        return [ 'kearny-events-widget' ];
    }

    protected function register_controls() {

        // ----- Content: Query -----
        $this->start_controls_section(
            'section_query',
            [ 'label' => __( 'Events Query', 'kearny-events' ) ]
        );

        $this->add_control(
            'count',
            [
                'label'   => __( 'Number of Events', 'kearny-events' ),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'min'     => 1,
                'max'     => 24,
                'default' => 8,
            ]
        );

        $this->add_control(
            'category',
            [
                'label'       => __( 'Category Slug (optional)', 'kearny-events' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'default'     => '',
                'description' => __( 'Leave blank to show all categories. Use the slug from Events → Categories.', 'kearny-events' ),
            ]
        );

        $this->end_controls_section();

        // ----- Content: Layout -----
        $this->start_controls_section(
            'section_layout',
            [ 'label' => __( 'Layout', 'kearny-events' ) ]
        );

        $this->add_control(
            'layout_mode',
            [
                'label'       => __( 'Layout Mode', 'kearny-events' ),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'default'     => 'carousel',
                'options'     => [
                    'grid'     => __( 'Grid (wraps to rows)', 'kearny-events' ),
                    'carousel' => __( 'Carousel (horizontal scroll)', 'kearny-events' ),
                ],
                'description' => __( 'Carousel is great when you have lots of events — users can swipe or use arrow buttons.', 'kearny-events' ),
            ]
        );

        $this->add_control(
            'show_arrows',
            [
                'label'        => __( 'Show Arrow Buttons', 'kearny-events' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'default'      => 'yes',
                'return_value' => 'yes',
                'description'  => __( 'Arrows show on desktop. On touch devices, users swipe instead.', 'kearny-events' ),
                'condition'    => [ 'layout_mode' => 'carousel' ],
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label'          => __( 'Columns / Cards Visible', 'kearny-events' ),
                'type'           => \Elementor\Controls_Manager::SELECT,
                'default'        => '4',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options'        => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ],
                'description'    => __( 'In Grid mode: columns per row. In Carousel mode: cards visible before scrolling.', 'kearny-events' ),
                'selectors'      => [
                    '{{WRAPPER}} .kearny-events' => '--kearny-columns: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'image_ratio',
            [
                'label'   => __( 'Image Aspect Ratio', 'kearny-events' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => '3/2',
                'options' => [
                    '4/3'  => '4:3 (standard)',
                    '16/9' => '16:9 (wide)',
                    '1/1'  => '1:1 (square)',
                    '3/2'  => '3:2 (photo)',
                    '3/4'  => '3:4 (portrait)',
                ],
                'selectors' => [
                    '{{WRAPPER}} .kearny-events' => '--kearny-image-ratio: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ----- Content: Display Fields -----
        $this->start_controls_section(
            'section_display',
            [ 'label' => __( 'Display Fields', 'kearny-events' ) ]
        );

        $this->add_control(
            'show_image',
            [
                'label'        => __( 'Show Featured Image', 'kearny-events' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'default'      => 'yes',
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'show_date',
            [
                'label'        => __( 'Show Date', 'kearny-events' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'default'      => 'yes',
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'show_venue',
            [
                'label'        => __( 'Show Venue', 'kearny-events' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'default'      => '',
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'show_excerpt',
            [
                'label'        => __( 'Show Excerpt', 'kearny-events' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'default'      => 'yes',
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'excerpt_length',
            [
                'label'     => __( 'Excerpt Word Count', 'kearny-events' ),
                'type'      => \Elementor\Controls_Manager::NUMBER,
                'min'       => 5,
                'max'       => 60,
                'default'   => 20,
                'condition' => [ 'show_excerpt' => 'yes' ],
            ]
        );

        $this->end_controls_section();

        // ----- Content: Submit Event Button (top) -----
        $this->start_controls_section(
            'section_submit_button',
            [ 'label' => __( 'Submit Event Button', 'kearny-events' ) ]
        );

        $this->add_control(
            'show_submit_button',
            [
                'label'        => __( 'Show Submit Button', 'kearny-events' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'default'      => 'yes',
                'return_value' => 'yes',
                'description'  => __( 'Displays a "Submit an Event" button above the grid, linking to the Community Events submission form.', 'kearny-events' ),
            ]
        );

        $this->add_control(
            'submit_button_text',
            [
                'label'     => __( 'Button Text', 'kearny-events' ),
                'type'      => \Elementor\Controls_Manager::TEXT,
                'default'   => 'Submit an Event',
                'condition' => [ 'show_submit_button' => 'yes' ],
            ]
        );

        $this->add_control(
            'submit_button_url',
            [
                'label'       => __( 'Button URL', 'kearny-events' ),
                'type'        => \Elementor\Controls_Manager::URL,
                'placeholder' => __( '/events/community/add/', 'kearny-events' ),
                'default'     => [ 'url' => '/events/community/add/' ],
                'condition'   => [ 'show_submit_button' => 'yes' ],
            ]
        );

        $this->add_control(
            'submit_button_align',
            [
                'label'     => __( 'Alignment', 'kearny-events' ),
                'type'      => \Elementor\Controls_Manager::CHOOSE,
                'default'   => 'right',
                'options'   => [
                    'left'   => [ 'title' => __( 'Left', 'kearny-events' ),   'icon' => 'eicon-text-align-left' ],
                    'center' => [ 'title' => __( 'Center', 'kearny-events' ), 'icon' => 'eicon-text-align-center' ],
                    'right'  => [ 'title' => __( 'Right', 'kearny-events' ),  'icon' => 'eicon-text-align-right' ],
                ],
                'condition' => [ 'show_submit_button' => 'yes' ],
            ]
        );

        $this->add_control(
            'submit_logged_in_only',
            [
                'label'        => __( 'Show Only to Logged-In Users', 'kearny-events' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'default'      => '',
                'return_value' => 'yes',
                'description'  => __( 'If enabled, guests will not see the Submit button.', 'kearny-events' ),
                'condition'    => [ 'show_submit_button' => 'yes' ],
            ]
        );

        $this->end_controls_section();

        // ----- Content: More Events Button -----
        $this->start_controls_section(
            'section_button',
            [ 'label' => __( 'More Events Button', 'kearny-events' ) ]
        );

        $this->add_control(
            'show_button',
            [
                'label'        => __( 'Show Button', 'kearny-events' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'default'      => 'yes',
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label'     => __( 'Button Text', 'kearny-events' ),
                'type'      => \Elementor\Controls_Manager::TEXT,
                'default'   => 'View All Events',
                'condition' => [ 'show_button' => 'yes' ],
            ]
        );

        $this->add_control(
            'button_url',
            [
                'label'       => __( 'Button URL', 'kearny-events' ),
                'type'        => \Elementor\Controls_Manager::URL,
                'placeholder' => __( '/events', 'kearny-events' ),
                'default'     => [ 'url' => '/events' ],
                'condition'   => [ 'show_button' => 'yes' ],
            ]
        );

        $this->add_control(
            'empty_message',
            [
                'label'   => __( 'Empty State Message', 'kearny-events' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'No upcoming events. Check back soon!',
            ]
        );

        $this->end_controls_section();

        // ----- Content: Placeholder Colors -----
        $this->start_controls_section(
            'section_placeholder',
            [
                'label'     => __( 'Placeholder Card Colors', 'kearny-events' ),
                'condition' => [ 'show_image' => 'yes' ],
            ]
        );

        $this->add_control(
            'placeholder_color_1',
            [
                'label'   => __( 'Color 1', 'kearny-events' ),
                'type'    => \Elementor\Controls_Manager::COLOR,
                'default' => '#1e4d8c',
            ]
        );
        $this->add_control(
            'placeholder_color_2',
            [
                'label'   => __( 'Color 2', 'kearny-events' ),
                'type'    => \Elementor\Controls_Manager::COLOR,
                'default' => '#145c38',
            ]
        );
        $this->add_control(
            'placeholder_color_3',
            [
                'label'   => __( 'Color 3', 'kearny-events' ),
                'type'    => \Elementor\Controls_Manager::COLOR,
                'default' => '#4a2880',
            ]
        );
        $this->add_control(
            'placeholder_color_4',
            [
                'label'   => __( 'Color 4', 'kearny-events' ),
                'type'    => \Elementor\Controls_Manager::COLOR,
                'default' => '#8c3d00',
            ]
        );
        $this->add_control(
            'placeholder_color_5',
            [
                'label'   => __( 'Color 5', 'kearny-events' ),
                'type'    => \Elementor\Controls_Manager::COLOR,
                'default' => '#6b1212',
            ]
        );

        $this->end_controls_section();

        // ----- Style: Card -----
        $this->start_controls_section(
            'section_style_card',
            [
                'label' => __( 'Card', 'kearny-events' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_bg',
            [
                'label'     => __( 'Card Background', 'kearny-events' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .kearny-events__card' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_gap',
            [
                'label'     => __( 'Grid Gap (px)', 'kearny-events' ),
                'type'      => \Elementor\Controls_Manager::NUMBER,
                'min'       => 0,
                'max'       => 80,
                'default'   => 32,
                'selectors' => [
                    '{{WRAPPER}} .kearny-events__grid'  => 'gap: {{VALUE}}px;',
                    '{{WRAPPER}} .kearny-events__track' => 'gap: {{VALUE}}px;',
                ],
            ]
        );

        $this->end_controls_section();

        // ----- Style: Title -----
        $this->start_controls_section(
            'section_style_title',
            [
                'label' => __( 'Title', 'kearny-events' ),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => __( 'Title Color', 'kearny-events' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .kearny-events__title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .kearny-events__title',
            ]
        );

        $this->end_controls_section();

        // ----- Style: Button -----
        $this->start_controls_section(
            'section_style_button',
            [
                'label'     => __( 'Button', 'kearny-events' ),
                'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [ 'show_button' => 'yes' ],
            ]
        );

        $this->add_control(
            'button_bg',
            [
                'label'     => __( 'Background', 'kearny-events' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .kearny-events__button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_color',
            [
                'label'     => __( 'Text Color', 'kearny-events' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .kearny-events__button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ----- Style: Submit Button -----
        $this->start_controls_section(
            'section_style_submit_button',
            [
                'label'     => __( 'Submit Button', 'kearny-events' ),
                'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [ 'show_submit_button' => 'yes' ],
            ]
        );

        $this->add_control(
            'submit_button_style',
            [
                'label'        => __( 'Style', 'kearny-events' ),
                'type'         => \Elementor\Controls_Manager::SELECT,
                'default'      => 'outlined',
                'options'      => [
                    'outlined' => __( 'Outlined', 'kearny-events' ),
                    'filled'   => __( 'Filled', 'kearny-events' ),
                ],
                'selectors'    => [
                    '{{WRAPPER}} .kearny-events__submit-button' => 'background: {{VALUE}};',
                ],
                'prefix_class' => 'kearny-submit-style-',
            ]
        );

        $this->add_control(
            'submit_button_bg',
            [
                'label'     => __( 'Background', 'kearny-events' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .kearny-events__submit-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'submit_button_color',
            [
                'label'     => __( 'Text & Border Color', 'kearny-events' ),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .kearny-events__submit-button' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $button_url = '';
        if ( ! empty( $settings['button_url']['url'] ) ) {
            $button_url = $settings['button_url']['url'];
        }

        $submit_button_url = '/events/community/add/';
        if ( ! empty( $settings['submit_button_url']['url'] ) ) {
            $submit_button_url = $settings['submit_button_url']['url'];
        }

        echo Events_Renderer::render( [
            'count'                 => isset( $settings['count'] ) ? absint( $settings['count'] ) : 8,
            'columns'               => isset( $settings['columns'] ) ? absint( $settings['columns'] ) : 4,
            'category'              => isset( $settings['category'] ) ? sanitize_title( $settings['category'] ) : '',
            'show_image'            => ( 'yes' === ( $settings['show_image']   ?? 'yes' ) ),
            'show_date'             => ( 'yes' === ( $settings['show_date']    ?? 'yes' ) ),
            'show_venue'            => ( 'yes' === ( $settings['show_venue']   ?? '' ) ),
            'show_excerpt'          => ( 'yes' === ( $settings['show_excerpt'] ?? 'yes' ) ),
            'excerpt_length'        => isset( $settings['excerpt_length'] ) ? absint( $settings['excerpt_length'] ) : 20,
            'image_ratio'           => $settings['image_ratio'] ?? '4/3',
            'show_button'           => ( 'yes' === ( $settings['show_button'] ?? 'yes' ) ),
            'button_text'           => $settings['button_text'] ?? 'View All Events',
            'button_url'            => $button_url ?: '/events',
            'empty_message'         => $settings['empty_message'] ?? 'No upcoming events. Check back soon!',
            'show_submit_button'    => ( 'yes' === ( $settings['show_submit_button'] ?? 'yes' ) ),
            'submit_button_text'    => $settings['submit_button_text'] ?? 'Submit an Event',
            'submit_button_url'     => $submit_button_url,
            'submit_button_align'   => $settings['submit_button_align'] ?? 'right',
            'submit_logged_in_only' => ( 'yes' === ( $settings['submit_logged_in_only'] ?? '' ) ),
            'layout_mode'           => $settings['layout_mode'] ?? 'carousel',
            'show_arrows'           => ( 'yes' === ( $settings['show_arrows'] ?? 'yes' ) ),
            'placeholder_colors'    => array_filter( [
                $settings['placeholder_color_1'] ?? '#1a5fa8',
                $settings['placeholder_color_2'] ?? '#1a7a4a',
                $settings['placeholder_color_3'] ?? '#6b3fa0',
                $settings['placeholder_color_4'] ?? '#b85c00',
                $settings['placeholder_color_5'] ?? '#8b1a1a',
            ] ),
        ] );
    }
}
