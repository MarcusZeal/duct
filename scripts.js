jQuery(document).ready(function($) {
    let owl = jQuery('.owl-carousel');
    owl.on('changed.owl.carousel', function(e) {
        $('.url').removeClass('active');
        $($('.people-familiar .url')[e.item.index]).addClass('active');
        $($('.people-unfamiliar .url')[e.item.index]).addClass('active');
    });

    
    $('.left-toggle').click(function() {
        let id = $(this).attr('id');
        $('.duct-toggle').hide();
        $('.duct-toggle').removeClass('active');
        $('#' + id + 'C').fadeIn(400);
        $('#' + id + 'C').addClass('active');
        $('.left-toggle').removeClass('active');
        $(this).addClass('active');
    })
	
    $('.owl-carousel').owlCarousel({
        items: 1,
        loop: false,
        center: true,
        margin: 10,
        URLhashListener: true,
        autoplayHoverPause: true,
        startPosition: 'URLHash'
    });
	
    $('.tag-nav:first .url').addClass('active');
	
    jQuery(".uct-card-button").click(function(e) {
        e.preventDefault();
        jQuery(this).addClass("loading");
        page = jQuery(this).attr("id");
        skip = jQuery('section.uct-cards').attr("skip");
        jQuery(this).attr("id", parseInt(page) + 1);
        jQuery.ajax({
            type: "get",
            url: uct.ajaxurl,
            data: {
                action: "load_uct_cards",
                page: page,
                skip: skip,
            },
            dataType: 'JSON',
            success: function(response) {
                if (response) {
                    jQuery(".uct-cards").append(response.cards);
                    if (!response.hasMore) {
                        jQuery('.uct-card-button').addClass("hidden");
                    }
                }
                jQuery('.uct-card-button').removeClass("loading");
            },
            error: function() {
                jQuery('.uct-card-button').removeClass("loading");
                alert("Failed to load more cards");
            }
        });
    });
	
	document.querySelector('.custom-select-wrapper').addEventListener('click', function() {
		this.querySelector('.custom-select').classList.toggle('open');
	})
	
	for (const option of document.querySelectorAll(".custom-option")) {
		option.addEventListener('click', function() {
			if (!this.classList.contains('selected')) {
				this.parentNode.querySelector('.custom-option.selected').classList.remove('selected');
				this.classList.add('selected');
				this.closest('.custom-select').querySelector('.custom-select__trigger span').textContent = this.textContent;
				let id = $(this).attr('id');
					$('.duct-toggle').hide();
					$('.duct-toggle').removeClass('active');
					$('#' + id + 'C').fadeIn(400);
					$('#' + id + 'C').addClass('active');
				
			}
		})
	}	
	
	
});