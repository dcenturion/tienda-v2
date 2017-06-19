					var tpj=jQuery;
					var revapi116;
					tpj(document).ready(function() {
						if(tpj("#rev_slider_4_1").revolution == undefined){
							revslider_showDoubleJqueryError("#rev_slider_4_1");
						}else{
							revapi116 = tpj("#rev_slider_4_1").show().revolution({
								sliderType:"standard",
								jsFileLocation:"../revolution/js/",
								sliderLayout:"fullscreen",
								dottedOverlay:"none",
								delay:9000,
	                            navigation: {
	                                keyboardNavigation: "on",
	                                keyboard_direction: "horizontal",
	                                mouseScrollNavigation: "off",
	                                onHoverStop: "off",
	                                touch: {
	                                    touchenabled: "on",
	                                    swipe_threshold: 75,
	                                    swipe_min_touches: 1,
	                                    swipe_direction: "horizontal",
	                                    drag_block_vertical: false
	                                },
	                                arrows: {
	                                    style: "erinyen",
	                                    enable: false,
	                                    hide_onmobile: true,
	                                    hide_onleave: false,
	                                    tmp: '<div class="tp-title-wrap">	<span class="tp-arr-titleholder">{{title}}</span>    <span class="tp-arr-imgholder"></span> </div>',
	                                    left: {
	                                        h_align: "left",
	                                        v_align: "bottom",
	                                        h_offset: 10,
	                                        v_offset: 340
	                                    },
	                                    right: {
	                                        h_align: "right",
	                                        v_align: "bottom",
	                                        h_offset: 10,
	                                        v_offset: 340
	                                    }
	                                },
	                                tabs: {
	                                    style: "zeus",
	                                    enable: true,
	                                    width: "100",
	                                    height: "auto",
	                                    min_width: 50,
	                                    wrapper_padding: 0,
	                                    wrapper_color: "#FFF",
	                                    wrapper_opacity: "0.6",
	                                    tmp: '<span class="tp-tab-title">{{title}}</span>',
	                                    visibleAmount: 4,
	                                    hide_onmobile: true,
	                                    hide_under: 992,
	                                    hide_onleave: false,
	                                    hide_delay: 200,
	                                    direction: "horizontal",
	                                    span: false,
	                                    position: "inner",
	                                    space: 0,
	                                    h_align: "left",
	                                    v_align: "bottom",
	                                    h_offset: 150,
	                                    v_offset: 120
	                                }
	                            },
								viewPort: {
									enable:true,
									outof:"pause",
									visible_area:"80%"
								},
								parallax:{
								   type:"mouse",
								   levels:[5,10,15,20,25,30,35,40,45,50,55,60,65,70,75,80,85],
								   origo:"enterpoint",
								   speed:400,
								   bgparallax:"on",
								   disable_onmobile:"off"
								},

								gridwidth:1170,
								gridheight:600,
								lazyType:"none",
								shadow:0,
								spinner:"spinner0",
								stopLoop:"off",
								stopAfterLoops:-1,
								stopAtSlide:-1,
								shuffle:"off",
								autoHeight:"off",
								hideThumbsOnMobile:"off",
								hideSliderAtLimit:0,
								hideCaptionAtLimit:0,
								hideAllCaptionAtLilmit:0,
								debugMode:false,
								fallbacks: {
									simplifyAll:"off",
									nextSlideOnWindowFocus:"off",
									disableFocusListener:false,
								}
							});
						}
					});	/*ready*/
