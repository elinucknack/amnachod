jQuery(function() {
    if (navigator.userAgent.match(/iPhone|iPad|iPod/i)) {
        jQuery("#right-menu .divider > span").on("click", function(event) {
            var children = event.target.parentNode.children;
            for (var i = 0; i < children.length; i++) {
                if (children[i].localName == "ul") {
                    var obj = jQuery(children[i]);
                    if (obj.css("display") == "none") obj.show();
                    else obj.hide();
                }
            }
        });
    } else {
        jQuery("#right-menu li").addClass("hoverable");
    }
});
