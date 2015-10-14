var Knp = Knp || {};
Knp.Translator = function($, baseUrl, config) {

    config = $.parseJSON(config);

    var selector = config.tag;

    $.each(config.keys, function() {
        selector += "[" + this + "]";
    });

    $(document).keydown(function(e) {

        var $active = $(document.activeElement);
        if(e.which == 13 || e.which == 27) {
            if($active.is(selector)) {
                $active.blur();
                e.preventDefault();
            }
        } else if(e.which == 40 || e.which == 38) {

            var $translations = $(selector);
            if($translations.length) {
                var index = -1;
                if($active) {
                    $active.blur();
                    index = $translations.index($active);
                }

                if(e.which == 40) {
                    index = (index+1)%$translations.length;
                } else {
                    index--;
                    if(index < 0) {
                        index = $translations.length-1;
                    }
                }

                $active = $($translations[index]);
                $active.attr('contenteditable', 'true');
                $active.focus();
            }
        }
    });

    var timer = false;
    var delay = 400;
    $(document).on('click mouseenter mouseleave blur', selector, function(e) {
        var $this = $(this);

        var data = {};

        $.each(config.keys, function(key) {
            data[key] = $this.attr(this);
        });

        if(e.type == "mouseenter") {
            timer = setTimeout(function () {
                $this.attr('contenteditable', 'true');
                $this.focus();
            }, delay);
        }
        else if(e.type == "mouseleave") {
            clearTimeout(timer);
        } else if(e.type == 'click') {
            if($this.attr('contenteditable')) {
                e.preventDefault();
                e.stopPropagation();
            }
        }else {
            $this.attr('contenteditable', null);
            data.value = $this.text();
            $.ajax({
                data: data,
                url: baseUrl,
                cache: false,
                method: 'PUT',
                dataType: 'json'

            }).done(function( data ) {
                $this.text(data.translation);
            });
        }
    });
};