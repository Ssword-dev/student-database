// jquery entry caller.
function createESMJQuery(){
    if (!('jQuery') in globalThis){
        throw new Error('The normal jquery globals also has to be loaded for esm-jquery to work, this may be because there is no current jquery script loaded before the calling script\'s execution, or createESMJQuery has been called multiple times in the same document.');
    }

    const $ = jQuery;
    $.noConflict(true);

    return $;
}

let $;

/**
 * @returns {JQueryStatic}
 */
function getESMJQuery () {
    if (!$){
        $ = createESMJQuery();
    }

    return $;
}

export default getESMJQuery;