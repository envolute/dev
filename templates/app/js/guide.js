jQuery(function(){var e=70,u=20,i=e+u,t=jQuery("#guide-menu").offset().top-i;jQuery("#footer").outerHeight(!0)+u;jQuery("#guide-menu").affix({offset:{top:t}});var r=jQuery("#guide-menu");r.length&&(visibleHeight(r,i),jQuery(window).resize(function(){visibleHeight(r,i)})),jQuery("#guide-menu").length&&jQuery("body").scrollspy({target:"#guide-menu",offset:parseInt(jQuery(window).height()/2)}),gotoElement("#guide-menu a",e)});