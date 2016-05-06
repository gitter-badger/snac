jQuery.fn.exists = function(){return this.length>0;}


/**
 * Set the search position (page number)
 * @param int start The starting position
 */
function setSearchPosition(start) {
    $('#start').val(start);
}

/**
 * Search with AJAX and update the display of search results
 *
 * @return boolean false to play nice with the browser
 */
function searchAndUpdate() {
    if ($("#searchbox").val() == "" || $("#searchbox").val().length < 2) {
        $("#search-results-box").html("");
    } else {
        $.post("?command=search", $("#search_form").serialize(), function (data) {
            //var previewWindow = window.open("", "Preview");
            //previewWindow.document.write(data);

            var html = "";
            html += "<h4 class='text-left'>Search Results</h4><div class='list-group text-left' style='margin-bottom:0px'>";
            if (data.results.length > 0) {
                for (var key in data.results) {
                    html += "<a href='?command=view&constellationid="+data.results[key].id+"' class='list-group-item'>"+data.results[key].nameEntry+"</a>";
                }
            } else {
                html += "<a href='#' class='list-group-item list-group-item-danger'>No results found.</a>";
            }
            html += "</div>";

            // Have pagination (total number of pages) and page (current page number) in data
            // ... use them to help stepping through the search for multiple pages.

            if (data.results.length > 0 && data.results.length < data.total) {
                var start = $('#start').val();
                var count = $('#count').val();
                var prev = (data.page - 1) * count;
                var next = (data.page + 1) * count;
                html += "<nav><ul class='pagination'>";
                var disabled = "";
                var goScript = " onClick='setSearchPosition("+prev+");searchAndUpdate();return false;'";
                if (data.page == 0) {
                    disabled = " class='disabled'";
                    goScript = "";
                }
                html += "<li"+disabled+"><a href='#' aria-label='Previous'"+goScript+"><span aria-hidden='true'>&laquo;</span></a></li>";
                for (var i = 0; i < data.pagination; i++) {
                    var active = '';
                    var goScript = " onClick='setSearchPosition("+(i * count)+");searchAndUpdate();return false;'";
                    if (i == data.page) {
                        active = " class='active'";
                        goScript = "";
                    }
                    html += "<li"+active+"><a href='#'"+goScript+">"+(i+1)+"</a></li>";
                }
                disabled = "";
                goScript = " onClick='setSearchPosition("+next+");searchAndUpdate();return false;'";
                if (data.page == data.pagination - 1) {
                    disabled = " class='disabled'";
                    goScript = "";
                }
                html += "<li"+disabled+"><a href='#' aria-label='Next'"+goScript+"><span aria-hidden='true'>&raquo;</span></a></li>";
                html += "</ul></nav>";
            }
            $("#search-results-box").html(html);
        });
    }
    return false;
}


/**
 * Only load this script once the document is fully loaded
 */
$(document).ready(function() {



    // Preview button
    if($('#searchbutton').exists()) {
        $('#searchbutton').click(function(){

            // Do the same thing as the auto-complete
            setSearchPosition(0); searchAndUpdate();

        });
    }

    var timeoutID = null;


    $('#searchbox').keyup(function() {
        clearTimeout(timeoutID);
        var $target = $(this);
        timeoutID = setTimeout(function() { setSearchPosition(0); searchAndUpdate(); }, 500);
    });
});