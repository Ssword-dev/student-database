import { default as getESMJQuery } from "./esm-jquery";

const metadataElementTagnameList = [
    'link',
    'meta',
    'style',
    'script',
];

const projectRootURI = new URL(`${window.location.origin}/${window.location.pathname.split('/')[1]}`);

const state = {
    currentPageURI = window.location.pathname
};

function serializeElement(el) {
  return {
    tag: el.tagName.toLowerCase(),
    attrs: Array.from(el.attributes).map(a => [a.name, a.value]),
    children: Array.from(el.childNodes).map(child => 
      child.nodeType === document.TEXT_NODE ? child.textContent : serializeElement(child)
    ),
  };
}

function captureCurrentState(){
    const container = document.querySelector('div[data-edge-app-container]');
    const metadataElements = document.querySelectorAll(metadataElementTagnameList.join(','));
    const metadataElementList = Array.from(metadataElements).map(serializeElement);
    return {
        appContainer: container,
        metadataElements: metadataElementList,
    };
}

function captureCurrentPageState(){
    const container = document.querySelector('div[data-edge-app-container]');
    return {
        content: container.innerHTML
    };
}

const partialContentCache = Object.create(null);

// suspended content means the content is not being displayed
// but saved for reuse.
const suspendedContentCache = Object.create(null);
const $ = getESMJQuery();

function preloadPage(pageUri){
    if (partialContentCache[pageUri]) {
        return;
    }
    
    $
        .ajax({
            async: true,
            'headers': {
                'Accept': 'application/x-edge-partial-content'
            },
            url: new URL(pageUri, projectRootURI)
        })
        .done(function(partialContent){
            partialContentCache[pageUri] = partialContent;
        });
}

function gotoPage(pageUri) {
    $(function() {
        const container = document.querySelector('div[data-edge-app-container]');
        if (suspendedContentCache[pageUri]){
            
            container.innerHTML = suspendedContentCache[pageUri];
        }

        else {
            
        }
    })
}