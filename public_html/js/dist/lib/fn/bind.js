define(["lib/object/defineProperty"],function(defineProperty){if(!("bind"in Function.prototype)){function bind(fn,thisArg){var args=Array.prototype.slice.call(arguments,2);return function(){return fn.apply(thisArg,args.concat(Array.prototype.slice.call(arguments,0)))}}defineProperty(Function.prototype,"bind",function(thisArg){return bind(this,thisArg)});return bind}return function(fn,thisArg){return fn.bind(thisArg)}});