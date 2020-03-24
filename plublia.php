<?php
/**
*Plugin Name: Plublia 
*Description: Mostrar os Versiculos
**/

function plublia_register_plugin_styles() {
	wp_register_style ('plublia_css', plugins_url('css/style.css', __FILE__));
	wp_enqueue_style ('plublia_css' );
}

function plublia_transformador( $content ) {
	$original_content = $content ; // preservar o original ...
	$script = '
<style>
#tooltip {
     text-align: center;
     color: #fff;
     background: #111;
     position: absolute;
     z-index: 100;
     padding: 15px;
}
 #tooltip:after 
/* triangle decoration */
 {
     width: 0;
     height: 0;
     border-left: 10px solid transparent;
     border-right: 10px solid transparent;
     border-top: 10px solid #111;
     content: "";
     position: absolute;
     left: 50%;
     bottom: -10px;
     margin-left: -10px;
}
 #tooltip.top:after {
     border-bottom: 10px solid #111;
     top: -20px;
     bottom: auto;
}
 #tooltip.left:after {
     left: 10px;
     margin: 0;
}
 #tooltip.right:after {
     right: 10px;
     left: auto;
     margin: 0;
}
abbr.link-plublia {
     text-decoration: none;
     border-bottom: 1px dotted;
}
abbr.link-plublia:hover {
     text-decoration: none;
     border-bottom: 2px dotted;
}
</style>
<script>
jQuery( function() {
    var targets = $( "[rel~=tooltip]" ),
        target  = false,
        tooltip = false,
        title   = false;
 
    targets.bind( "mouseenter", function()
    {
        target  = $( this );
        tip     = target.attr( "title" );
        tooltip = $( "<div id=\"tooltip\"></div>" );
 
        if( !tip || tip == "" )
            return false;
 
        target.removeAttr( "title" );
        tooltip.css( "opacity", 0 )
               .html( tip )
               .appendTo( "body" );
 
        var init_tooltip = function()
        {
            if( $( window ).width() < tooltip.outerWidth() * 1.5 )
                tooltip.css( "max-width", $( window ).width() / 2 );
            else
                tooltip.css( "max-width", 340 );
 
            var pos_left = target.offset().left + ( target.outerWidth() / 2 ) - ( tooltip.outerWidth() / 2 ),
                pos_top  = target.offset().top - tooltip.outerHeight() - 20;
 
            if( pos_left < 0 )
            {
                pos_left = target.offset().left + target.outerWidth() / 2 - 20;
                tooltip.addClass( "left" );
            }
            else
                tooltip.removeClass( "left" );
 
            if( pos_left + tooltip.outerWidth() > $( window ).width() )
            {
                pos_left = target.offset().left - tooltip.outerWidth() + target.outerWidth() / 2 + 20;
                tooltip.addClass( "right" );
            }
            else
                tooltip.removeClass( "right" );
 
            if( pos_top < 0 )
            {
                var pos_top  = target.offset().top + target.outerHeight();
                tooltip.addClass( "top" );
            }
            else
                tooltip.removeClass( "top" );
 
            tooltip.css( { left: pos_left, top: pos_top } )
                   .animate( { top: "+=10", opacity: 1 }, 50 );
        };
 
        init_tooltip();
        $( window ).resize( init_tooltip );
 
        var remove_tooltip = function()
        {
            tooltip.animate( { top: "-=10", opacity: 0 }, 50, function()
            {
                $( this ).remove();
            });
 
            target.attr( "title", tip );
        };
 
        target.bind( "mouseleave", remove_tooltip );
        tooltip.bind( "click", remove_tooltip );
    });
});
</script>
	';
	$count = 0;
	$carrega_biblia = file_get_contents(plugin_dir_url( __FILE__ ) . "biblias/pt_nvi.json");
	$biblia_ler = json_decode($carrega_biblia, true);
	$equivalencias = file_get_contents(plugin_dir_url( __FILE__ ) . "equivalencia.json");
	$equival_decode = json_decode($equivalencias, true);
	$resultado = str_replace('<p>', '', preg_replace_callback('/(\d{1,2}\s)*?([A-Za-zÊúíôçóâêãáé]+\s\d+:\d+)(-?\d{1,2})?/', function ($matchis) use ($equival_decode, $biblia_ler) {
	preg_match_all('/(?!\s)(\d+:\d+)/', $matchis[0], $capvers_nosplit);
	list($cap_split, $vers_split) = split(':', $capvers_nosplit[0][0]);
	preg_match_all('/(\d+)-+?(\d+)/', $matchis[0], $vers_de_ate);
	list($vers_de, $vers_ate) = split('-', $vers_de_ate[0][0]);
			preg_match_all('/(\d )?([A-Za-zÊúíôçóâêãáé])+/', $matchis[0], $match_versiculo);
				preg_match_all('/(\d )?([A-Za-zÊúíôçóâêãáé])+/', $match_versiculo[0][0], $matchez); 
				if (isset($equival_decode[$match_versiculo[0][0]])) {
					$pertence_biblia = true;
				} else {
					$pertence_biblia = false;
				}
			$count;
			$count++;
	$vers_de--;
	$vers_ate--;
	$cap_split--;
	$vers_split--;
if ($vers_de_ate[0][0]) {
	$conta_vers = 0;
	for ($i = $vers_de; $i <= $vers_ate; $i++) {
		if ($conta_vers >= 3) {
			$mostra_versiculo = $mostra_versiculo . '<sup>' . ($i+1) . '</sup>' . htmlentities($biblia_ler[$equival_decode[$match_versiculo[0][0]]]["chapters"][$cap_split][$i]) . ' (...)';
			break;
		} else {
			$mostra_versiculo = $mostra_versiculo . '<sup>' . ($i+1) . '</sup>' . htmlentities($biblia_ler[$equival_decode[$match_versiculo[0][0]]]["chapters"][$cap_split][$i]) . ' ';
		}
		$conta_vers++;
	}
} else {
	$mostra_versiculo = '<sup>' . ($vers_split+1) . '</sup>' . htmlentities($biblia_ler[$equival_decode[$match_versiculo[0][0]]]["chapters"][$cap_split][$vers_split]) . ' ';
}
			if ($pertence_biblia == true) {
				return '<abbr class="link-plublia" title="' . $matchis[0] . ' - ' . $mostra_versiculo . '" rel="tooltip">' . $matchis[0] . '</abbr>'; 
			} else {
				return $matchis[0];
			}
	}, $original_content));
	$resultado = $script . $resultado;
        // Returns the content.
	return $resultado;
}
function add_ss_plublia() {
	wp_register_script(
		'jquery_plublia_script', 
		plugin_dir_url( __FILE__ ) . 'js/jquery-3.4.1.min.js', 
		array('jquery')
	);

	wp_enqueue_style( 'plublia-tooltip', plugin_dir_url( __FILE__ ) . 'css/style.css',false,'1.1','all');
	wp_enqueue_script('jquery_plublia_script');
}

add_action( 'wp_head', 'add_ss_plublia');
add_action('wp_enqueue_scripts', 'plublia_register_plugin_styles');
add_filter( 'the_content', 'plublia_transformador', 20 );

?>
