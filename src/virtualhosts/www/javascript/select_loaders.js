/**
 * Replace a select that is linked to a Vocabulary search
 *
 * Replaces the select with a select2 object capable of making AJAX queries
 *
 * @param  JQuery selectItem The JQuery item to replace
 * @param  string idMatch    ID string for the object on the page
 * @param  string type       The type of the vocabulary term
 * @param  int    minLength  The minimum required length of the autocomplete search
 */
function vocab_select_replace(selectItem, idMatch, type, minLength) {
    if (minLength === undefined) {
        minLength = 4;
    }

        if(selectItem.attr('id').endsWith(idMatch)
            && !selectItem.attr('id').endsWith("ZZ")) {
                selectItem.select2({
                    ajax: {
                        url: "?command=vocabulary&type="+type,
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term,
                                page: params.page
                            };
                        },
                        processResults: function (data, page) {
                            return { results: data.results };
                        },
                        cache: true
                    },
                    width: '100%',
                    minimumInputLength: minLength,
                    allowClear: true,
                    theme: 'classic',
                    placeholder: 'Select'
                });
            }
}

/**
 * Replace a select that is linked to a Constellation Source search
 *
 * Replaces the select with a select2 object capable of making AJAX queries
 *
 * @param  JQuery selectItem The JQuery item to replace
 * @param  string idMatch    ID string for the object on the page
 */
function scm_source_select_replace(selectItem, idMatch) {
        if(selectItem.attr('id').endsWith(idMatch)
            && !selectItem.attr('id').endsWith("ZZ")) {
        		var icid = $("#constellationid").val();
        		var icversion = $("#version").val();
                selectItem.select2({
                    ajax: {
                        url: "?command=vocabulary&type=ic_sources&id="+icid+"&version="+icversion,
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term,
                                page: params.page
                            };
                        },
                        processResults: function (data, page) {
                            return { results: data.results };
                        },
                        cache: true
                    },
                    width: '100%',
                    minimumInputLength: 0,
                    allowClear: true,
                    theme: 'classic',
                    placeholder: 'Select'
                });
            }
}

/**
 * Replace all the selects that exist on the page when the page has finished loading
 */
$(document).ready(function() {

    // Use select2 to display the select dropdowns
    // rather than the HTML default
    $("select").each(function() {

        // Replace the subject selects
        vocab_select_replace($(this), "language_language_", "language_code", 1);

        // Replace the subject selects
        vocab_select_replace($(this), "language_script_", "script_code", 1);

        // Replace the subject selects
        vocab_select_replace($(this), "subject_", "subject", 4);

        // Replace the function selects
        vocab_select_replace($(this), "function_", "function", 4);

        // Replace the occupation selects
        vocab_select_replace($(this), "occupation_", "occupation", 4);

        // Replace the entityType select
        vocab_select_replace($(this), "entityType", "entity_type", 0);
    });
});
