if(typeof define!=="function"){var define=require("amdefine")(module,require)}define(["require","../../components/underscore/underscore","../../components/js-signals/dist/signals","../functions","../utils","../dom"],function(require){var _=require("../../components/underscore/underscore");var signals=require("../../components/js-signals/dist/signals");var utils=require("../utils");var dom=require("../dom");function BulkLoader(){}BulkLoader.signalKeys=["loaded","loadProgress","finished"];BulkLoader.prototype.signals={};_.each(BulkLoader.signalKeys,function(key){BulkLoader.prototype.signals[key]=new signals.Signal});BulkLoader.prototype.sources=[];BulkLoader.prototype.count=0;BulkLoader.prototype.processedCount=0;BulkLoader.prototype.loadedCount=0;BulkLoader.prototype.simultaneousDownloads=5;BulkLoader.prototype.percentLoaded=0;BulkLoader.prototype.itemsTagName="";BulkLoader.prototype.setPercentage=function(percentage){this.percentLoaded+=percentage;this.signals.loadProgress.dispatch(this.percentLoaded)};BulkLoader.prototype.applyPercentageIncrease=function(){var increase=Math.floor(this.loadedCount/this.count)-this.percentLoaded;increase>0&&this.setPercentage(this.percentLoaded+increase)};BulkLoader.prototype.startLoading=function(){utils.repeatCall(_.bind(this.loadNextItem,this),this.simultaneousDownloads)};BulkLoader.prototype.loadNextItem=function(){dom.loadElement(this.itemsTagName,this.sources[this.processedCount],_.bind(this.onItemLoaded,this));this.processedCount++};BulkLoader.prototype.onItemLoaded=function($element){this.loadedCount++;this.signals.loaded.dispatch($element,this.loadedCount);this.applyPercentageIncrease();this.loadedCount<this.count&&this.loadNextItem()};return{Loader:BulkLoader}});