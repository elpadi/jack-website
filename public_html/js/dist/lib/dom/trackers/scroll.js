define(["jquery"],function(jquery){var fns=[];$(window).on("scroll",function(e){var scrollTop=document.body.scrollTop;for(var i in fns)fns[i](scrollTop)});return function onScroll(fn){fn(document.body.scrollTop);fns.push(fn)}});