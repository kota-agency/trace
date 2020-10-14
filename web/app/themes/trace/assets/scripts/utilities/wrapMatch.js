function wrapMatch(elem, term) {

    const filter = new RegExp(term, "\sig");

    $(elem).html($(elem).html().replace(filter, (match) => {
        return "<span class='wrap-match'>" + match + "</span>";
    }));
}


export default wrapMatch;
