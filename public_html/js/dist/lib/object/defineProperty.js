define([],function(){var supportsDefineProperty="defineProperty"in Object&&"defineProperties"in Object;function defineProperty(target,name,value){if(supportsDefineProperty){Object.defineProperty(target,name,{value:value,configurable:true,enumerable:false,writable:true})}else{target[name]=value}}return defineProperty});