define([],function(){return function isLinkOnlyHashChange(link){if(window.location.hostname===link.hostname&&window.location.pathname===link.pathname){return window.location.href!==link.href}return false}});