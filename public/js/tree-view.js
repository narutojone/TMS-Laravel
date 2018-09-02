$.fn.extend({
    treed: function (o) {

        var openedClass = 'glyphicon-minus-sign';
        var closedClass = 'glyphicon-plus-sign';

        if (typeof o != 'undefined'){
            if (typeof o.openedClass != 'undefined'){
                openedClass = o.openedClass;
            }
            if (typeof o.closedClass != 'undefined'){
                closedClass = o.closedClass;
            }
        };

        /* initialize each of the top levels */
        var tree = $(this);
        tree.addClass("tree");
        tree.find('li').has("ul").each(function () {
            var branch = $(this);
            branch.prepend("");
            branch.addClass('branch');
            branch.on('click', function (e) {
                if (this == e.target) {
                    $(this).children().children().toggle();
                }
            })
            branch.children().children().toggle();
        });

        /* fire event to open branch if the li contains an anchor instead of text */
        tree.find('.branch>span.folder-name').each(function () {
            $(this).on('click', function (e) {
                $(this).closest('li').click();
                e.preventDefault();
            });
        });

    }
});
