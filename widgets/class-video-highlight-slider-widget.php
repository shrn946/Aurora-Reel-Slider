<?php
namespace EVHS\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Video_Highlight_Slider_Widget extends Widget_Base {
	public function get_name() { return 'evhs-video-highlight-slider'; }
	public function get_title() { return esc_html__( 'Aurora Reel Slider', 'evhs' ); }
	public function get_icon() { return 'eicon-slider-video'; }
	public function get_categories() { return array( 'general' ); }
	public function get_keywords() { return array( 'video', 'slider', 'swiper', 'highlight', 'carousel' ); }
	public function get_style_depends() { return array( 'evhs-swiper', 'evhs-video-slider' ); }
	public function get_script_depends() { return array( 'evhs-swiper', 'evhs-video-slider' ); }

	protected function register_controls() {
		$this->start_controls_section( 'section_content', array(
			'label' => esc_html__( 'Content', 'evhs' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );

		$repeater = new Repeater();

		$repeater->add_control( 'video_source', array(
			'label'   => esc_html__( 'Video Source', 'evhs' ),
			'type'    => Controls_Manager::CHOOSE,
			'options' => array(
				'upload' => array( 'title' => esc_html__( 'Upload', 'evhs' ), 'icon' => 'eicon-upload' ),
				'url'    => array( 'title' => esc_html__( 'URL', 'evhs' ), 'icon' => 'eicon-link' ),
			),
			'default' => 'url',
			'toggle'  => false,
		) );

		$repeater->add_control( 'video_file', array(
			'label'      => esc_html__( 'Upload Video', 'evhs' ),
			'type'       => Controls_Manager::MEDIA,
			'media_type' => 'video',
			'condition'  => array( 'video_source' => 'upload' ),
		) );

		$repeater->add_control( 'video_url', array(
			'label'       => esc_html__( 'Video URL', 'evhs' ),
			'type'        => Controls_Manager::URL,
			'placeholder' => 'https://example.com/video.mp4',
			'dynamic'     => array( 'active' => true ),
			'condition'   => array( 'video_source' => 'url' ),
		) );

		$repeater->add_control( 'title', array(
			'label'       => esc_html__( 'Title', 'evhs' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__( 'Momentum Sprint', 'evhs' ),
			'label_block' => true,
		) );

		$repeater->add_control( 'subtitle', array(
			'label'       => esc_html__( 'Subtitle', 'evhs' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__( 'Training Highlight', 'evhs' ),
			'label_block' => true,
		) );

		$repeater->add_control( 'enable_audio_icon', array(
			'label'        => esc_html__( 'Enable Audio Icon', 'evhs' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => '',
		) );

		$this->add_control( 'videos', array(
			'label'       => esc_html__( 'Videos', 'evhs' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'title_field' => '{{{ title }}}',
			'default'     => $this->get_default_videos(),
		) );

		$this->end_controls_section();

		$this->start_controls_section( 'section_slider_settings', array(
			'label' => esc_html__( 'Slider Settings', 'evhs' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );

		$this->add_control( 'autoplay', array(
			'label'        => esc_html__( 'Autoplay Slides', 'evhs' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => '',
		) );

		$this->add_control( 'autoplay_delay', array(
			'label'     => esc_html__( 'Autoplay Delay', 'evhs' ),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 3500,
			'condition' => array( 'autoplay' => 'yes' ),
		) );

		$this->add_control( 'speed', array(
			'label'   => esc_html__( 'Transition Speed', 'evhs' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 1000,
		) );
		$this->add_responsive_control( 'space_between', array(
			'label'      => esc_html__( 'Slide Spacing', 'evhs' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 0, 'max' => 160 ) ),
			'default'    => array( 'size' => 90 ),
			'tablet_default' => array( 'size' => 50 ),
			'mobile_default' => array( 'size' => 24 ),
		) );

		$this->end_controls_section();

		$this->register_style_controls();
	}

	private function register_style_controls() {
		$this->start_controls_section( 'section_cards_style', array(
			'label' => esc_html__( 'Video Cards', 'evhs' ),
			'tab' => Controls_Manager::TAB_STYLE,
		) );
		$this->add_responsive_control( 'slider_height', array(
			'label' => esc_html__( 'Slider Height', 'evhs' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range' => array( 'px' => array( 'min' => 250, 'max' => 900 ) ),
			'default' => array( 'size' => 570 ),
			'tablet_default' => array( 'size' => 500 ),
			'mobile_default' => array( 'size' => 430 ),
			'selectors' => array( '{{WRAPPER}} .evhs-video-slider' => 'height: {{SIZE}}{{UNIT}};' ),
		) );
		$this->add_responsive_control( 'card_width', array(
			'label' => esc_html__( 'Card Width', 'evhs' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range' => array( 'px' => array( 'min' => 160, 'max' => 700 ) ),
			'default' => array( 'size' => 400 ),
			'tablet_default' => array( 'size' => 320 ),
			'mobile_default' => array( 'size' => 260 ),
			'selectors' => array( '{{WRAPPER}} .evhs-slide' => 'width: {{SIZE}}{{UNIT}};' ),
		) );
		$this->add_responsive_control( 'card_height', array(
			'label' => esc_html__( 'Card Height', 'evhs' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range' => array( 'px' => array( 'min' => 180, 'max' => 800 ) ),
			'default' => array( 'size' => 480 ),
			'tablet_default' => array( 'size' => 420 ),
			'mobile_default' => array( 'size' => 360 ),
			'selectors' => array( '{{WRAPPER}} .evhs-slide' => 'height: {{SIZE}}{{UNIT}};' ),
		) );
		$this->add_responsive_control( 'active_lift', array(
			'label' => esc_html__( 'Active Card Lift', 'evhs' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range' => array( 'px' => array( 'min' => -180, 'max' => 0 ) ),
			'default' => array( 'size' => -90 ),
			'tablet_default' => array( 'size' => -75 ),
			'mobile_default' => array( 'size' => -65 ),
			'selectors' => array( '{{WRAPPER}} .evhs-wrapper' => '--evhs-active-lift: {{SIZE}}{{UNIT}};' ),
		) );
		$this->add_responsive_control( 'slider_top_spacing', array(
			'label' => esc_html__( 'Top Spacing', 'evhs' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range' => array( 'px' => array( 'min' => 0, 'max' => 160 ) ),
			'default' => array( 'size' => 60 ),
			'selectors' => array( '{{WRAPPER}} .evhs-video-slider' => 'margin-top: {{SIZE}}{{UNIT}};' ),
		) );
		$this->add_responsive_control( 'slider_bottom_spacing', array(
			'label' => esc_html__( 'Bottom Spacing', 'evhs' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range' => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
			'default' => array( 'size' => 60 ),
			'selectors' => array( '{{WRAPPER}} .evhs-video-slider' => 'margin-bottom: {{SIZE}}{{UNIT}};' ),
		) );
		$this->add_control( 'card_border_radius', array(
			'label' => esc_html__( 'Border Radius', 'evhs' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%' ),
			'selectors' => array(
				'{{WRAPPER}} .evhs-slide' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} .evhs-video-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} .evhs-video-box::before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} .evhs-video, {{WRAPPER}} .evhs-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			),
		) );
		$this->add_group_control( Group_Control_Border::get_type(), array(
			'name' => 'card_border',
			'selector' => '{{WRAPPER}} .evhs-video-box',
		) );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), array(
			'name' => 'card_shadow',
			'selector' => '{{WRAPPER}} .evhs-slide.swiper-slide-active .evhs-video-box, {{WRAPPER}} .evhs-slide:hover .evhs-video-box',
		) );
		$this->add_control( 'inactive_brightness', array(
			'label' => esc_html__( 'Inactive Brightness', 'evhs' ),
			'type' => Controls_Manager::SLIDER,
			'range' => array( 'px' => array( 'min' => 0.1, 'max' => 1, 'step' => 0.05 ) ),
			'default' => array( 'size' => 0.2 ),
			'selectors' => array( '{{WRAPPER}} .evhs-wrapper' => '--evhs-inactive-brightness: {{SIZE}};' ),
		) );
		$this->add_control( 'overlay_color', array(
			'label' => esc_html__( 'Overlay Start Color', 'evhs' ),
			'type' => Controls_Manager::COLOR,
			'default' => 'rgba(41,121,255,0.42)',
			'selectors' => array( '{{WRAPPER}} .evhs-wrapper' => '--evhs-overlay-start-color: {{VALUE}};' ),
		) );
		$this->add_control( 'overlay_end_color', array(
			'label' => esc_html__( 'Overlay End Color', 'evhs' ),
			'type' => Controls_Manager::COLOR,
			'default' => 'rgba(155,81,224,0)',
			'selectors' => array( '{{WRAPPER}} .evhs-wrapper' => '--evhs-overlay-end-color: {{VALUE}};' ),
		) );
		$this->add_control( 'overlay_gradient_direction', array(
			'label' => esc_html__( 'Gradient Direction', 'evhs' ),
			'type' => Controls_Manager::SELECT,
			'default' => 'to top',
			'options' => array(
				'to top' => esc_html__( 'Bottom to Top', 'evhs' ),
				'to bottom' => esc_html__( 'Top to Bottom', 'evhs' ),
				'to right' => esc_html__( 'Left to Right', 'evhs' ),
				'to left' => esc_html__( 'Right to Left', 'evhs' ),
				'to top right' => esc_html__( 'Bottom-Left to Top-Right', 'evhs' ),
				'to top left' => esc_html__( 'Bottom-Right to Top-Left', 'evhs' ),
				'to bottom right' => esc_html__( 'Top-Left to Bottom-Right', 'evhs' ),
				'to bottom left' => esc_html__( 'Top-Right to Bottom-Left', 'evhs' ),
			),
			'selectors' => array( '{{WRAPPER}} .evhs-wrapper' => '--evhs-overlay-direction: {{VALUE}}; --evhs-inactive-overlay-direction: {{VALUE}};' ),
		) );
		$this->add_control( 'inactive_overlay_start_color', array(
			'label' => esc_html__( 'Inactive Overlay Start', 'evhs' ),
			'type' => Controls_Manager::COLOR,
			'default' => 'rgba(0,198,255,0.30)',
			'selectors' => array( '{{WRAPPER}} .evhs-wrapper' => '--evhs-inactive-overlay-start-color: {{VALUE}};' ),
		) );
		$this->add_control( 'inactive_overlay_end_color', array(
			'label' => esc_html__( 'Inactive Overlay End', 'evhs' ),
			'type' => Controls_Manager::COLOR,
			'default' => 'rgba(123,97,255,0.08)',
			'selectors' => array( '{{WRAPPER}} .evhs-wrapper' => '--evhs-inactive-overlay-end-color: {{VALUE}};' ),
		) );
		$this->add_control( 'inactive_overlay_opacity', array(
			'label' => esc_html__( 'Inactive Overlay Opacity', 'evhs' ),
			'type' => Controls_Manager::SLIDER,
			'range' => array(
				'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.05 ),
			),
			'default' => array( 'size' => 1 ),
			'selectors' => array( '{{WRAPPER}} .evhs-wrapper' => '--evhs-inactive-overlay-opacity: {{SIZE}};' ),
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_card_text_style', array(
			'label' => esc_html__( 'Card Text', 'evhs' ),
			'tab' => Controls_Manager::TAB_STYLE,
		) );
		$this->add_responsive_control( 'card_text_content_alignment', array(
			'label' => esc_html__( 'Content Align', 'evhs' ),
			'type' => Controls_Manager::CHOOSE,
			'options' => array(
				'flex-start' => array( 'title' => esc_html__( 'Left', 'evhs' ), 'icon' => 'eicon-h-align-left' ),
				'center' => array( 'title' => esc_html__( 'Center', 'evhs' ), 'icon' => 'eicon-h-align-center' ),
				'flex-end' => array( 'title' => esc_html__( 'Right', 'evhs' ), 'icon' => 'eicon-h-align-right' ),
			),
			'default' => 'center',
			'selectors' => array( '{{WRAPPER}} .evhs-info-box' => 'align-items: {{VALUE}};' ),
		) );
		$this->add_responsive_control( 'card_text_align', array(
			'label' => esc_html__( 'Text Align', 'evhs' ),
			'type' => Controls_Manager::CHOOSE,
			'options' => array(
				'left' => array( 'title' => esc_html__( 'Left', 'evhs' ), 'icon' => 'eicon-text-align-left' ),
				'center' => array( 'title' => esc_html__( 'Center', 'evhs' ), 'icon' => 'eicon-text-align-center' ),
				'right' => array( 'title' => esc_html__( 'Right', 'evhs' ), 'icon' => 'eicon-text-align-right' ),
			),
			'default' => 'center',
			'selectors' => array( '{{WRAPPER}} .evhs-info-box' => 'text-align: {{VALUE}};' ),
		) );
		$this->add_responsive_control( 'card_text_vertical_position', array(
			'label' => esc_html__( 'Vertical Position', 'evhs' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => array( 'px', '%' ),
			'range' => array(
				'px' => array( 'min' => -200, 'max' => 200 ),
				'%'  => array( 'min' => -50, 'max' => 50 ),
			),
			'default' => array( 'size' => 20, 'unit' => 'px' ),
			'selectors' => array( '{{WRAPPER}} .evhs-info-box' => 'bottom: {{SIZE}}{{UNIT}};' ),
		) );
		$this->add_responsive_control( 'card_text_horizontal_position', array(
			'label' => esc_html__( 'Horizontal Position', 'evhs' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => array( '%', 'px' ),
			'range' => array(
				'%' => array( 'min' => 0, 'max' => 100 ),
				'px' => array( 'min' => -200, 'max' => 200 ),
			),
			'default' => array( 'size' => 50, 'unit' => '%' ),
			'selectors' => array( '{{WRAPPER}} .evhs-info-box' => 'left: {{SIZE}}{{UNIT}};' ),
		) );
		$this->add_responsive_control( 'card_text_width', array(
			'label' => esc_html__( 'Text Area Width', 'evhs' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => array( '%', 'px' ),
			'range' => array(
				'%' => array( 'min' => 30, 'max' => 100 ),
				'px' => array( 'min' => 120, 'max' => 800 ),
			),
			'default' => array( 'size' => 100, 'unit' => '%' ),
			'selectors' => array( '{{WRAPPER}} .evhs-info-box' => 'width: {{SIZE}}{{UNIT}};' ),
		) );
		$this->add_control( 'card_title_color', array(
			'label' => esc_html__( 'Title Color', 'evhs' ),
			'type' => Controls_Manager::COLOR,
			'default' => '#ffffff',
			'selectors' => array( '{{WRAPPER}} .evhs-info-title' => 'color: {{VALUE}};' ),
		) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'card_title_typography',
			'selector' => '{{WRAPPER}} .evhs-info-title',
		) );
		$this->add_group_control( Group_Control_Text_Shadow::get_type(), array(
			'name' => 'card_title_text_shadow',
			'selector' => '{{WRAPPER}} .evhs-info-title',
		) );
		$this->add_control( 'card_subtitle_color', array(
			'label' => esc_html__( 'Subtitle Color', 'evhs' ),
			'type' => Controls_Manager::COLOR,
			'default' => 'rgba(255,255,255,0.7)',
			'selectors' => array( '{{WRAPPER}} .evhs-info-subtitle' => 'color: {{VALUE}};' ),
		) );
		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name' => 'card_subtitle_typography',
			'selector' => '{{WRAPPER}} .evhs-info-subtitle',
		) );
		$this->add_group_control( Group_Control_Text_Shadow::get_type(), array(
			'name' => 'card_subtitle_text_shadow',
			'selector' => '{{WRAPPER}} .evhs-info-subtitle',
		) );
		$this->end_controls_section();
	}

	private function get_default_videos() {
		return array(
			array( 'video_source' => 'url', 'video_url' => array( 'url' => '' ), 'title' => 'Momentum Sprint', 'subtitle' => 'Training Highlight' ),
			array( 'video_source' => 'url', 'video_url' => array( 'url' => '' ), 'title' => 'Arena Energy', 'subtitle' => 'Matchday Feature' ),
			array( 'video_source' => 'url', 'video_url' => array( 'url' => '' ), 'title' => 'Peak Performance', 'subtitle' => 'Team Showcase' ),
		);
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$space = $settings['space_between']['size'] ?? 90;
		$space_tablet = $settings['space_between_tablet']['size'] ?? 50;
		$space_mobile = $settings['space_between_mobile']['size'] ?? 24;
		?>
		<div class="evhs-wrapper evhs-loading">
			<div class="evhs-video-slider swiper" data-autoplay="<?php echo esc_attr( $settings['autoplay'] ); ?>" data-autoplay-delay="<?php echo esc_attr( $settings['autoplay_delay'] ?? 3500 ); ?>" data-speed="<?php echo esc_attr( $settings['speed'] ?? 1000 ); ?>" data-space-desktop="<?php echo esc_attr( $space ); ?>" data-space-tablet="<?php echo esc_attr( $space_tablet ); ?>" data-space-mobile="<?php echo esc_attr( $space_mobile ); ?>" role="region" aria-label="<?php echo esc_attr__( 'Video highlight slider', 'evhs' ); ?>" tabindex="0">
				<div class="swiper-wrapper">
					<?php foreach ( $settings['videos'] as $item ) :
						$video_url = '';
						if ( 'upload' === ( $item['video_source'] ?? '' ) && ! empty( $item['video_file']['url'] ) ) {
							$video_url = $item['video_file']['url'];
						} elseif ( ! empty( $item['video_url']['url'] ) ) {
							$video_url = $item['video_url']['url'];
						}
						?>
						<div class="evhs-slide swiper-slide">
							<div class="evhs-video-box">
								<?php if ( $video_url ) : ?>
									<video class="evhs-video" src="<?php echo esc_url( $video_url ); ?>" playsinline muted preload="metadata"></video>
								<?php endif; ?>
								<?php if ( 'yes' === ( $item['enable_audio_icon'] ?? '' ) ) : ?>
									<button type="button" class="evhs-sound-toggle" aria-label="<?php echo esc_attr__( 'Enable sound', 'evhs' ); ?>" data-muted="true">
										<span class="evhs-sound-icon" aria-hidden="true"><svg viewBox="0 0 24 24" width="12" height="12" focusable="false"><path fill="currentColor" d="M14 5.23v13.54c0 .71-.86 1.07-1.36.57L8.31 15H5a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1h3.31l4.33-4.34c.5-.5 1.36-.14 1.36.57zM18.71 8.29a1 1 0 0 1 0 1.42L17.41 11l1.3 1.29a1 1 0 1 1-1.42 1.42L16 12.41l-1.29 1.3a1 1 0 0 1-1.42-1.42l1.3-1.29-1.3-1.29a1 1 0 1 1 1.42-1.42L16 9.59l1.29-1.3a1 1 0 0 1 1.42 0z"/></svg></span>
									</button>
								<?php endif; ?>
								<div class="evhs-info-box">
									<?php if ( ! empty( $item['title'] ) ) : ?><h3 class="evhs-info-title"><?php echo esc_html( $item['title'] ); ?></h3><?php endif; ?>
									<?php if ( ! empty( $item['subtitle'] ) ) : ?><div class="evhs-info-subtitle"><?php echo esc_html( $item['subtitle'] ); ?></div><?php endif; ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}
}
